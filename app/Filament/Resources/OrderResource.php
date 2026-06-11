<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductShade;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([

                Tables\Columns\TextColumn::make('order_code')
                    ->label('Kode')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Item')
                    ->counts('items')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR', locale: 'id'),

                /**
                 * ✅ STATUS ORDER (dropdown) - SINKRON 2 ARAH
                 */
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status Order')
                    ->options([
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'diproses'            => 'Diproses',
                        'dikirim'             => 'Dikirim',
                        'selesai'             => 'Selesai',
                        'dibatalkan'          => 'Dibatalkan',
                    ])
                    ->selectablePlaceholder(false)
                    ->rules(['required'])
                    ->updateStateUsing(function (Order $record, $state) {

                        DB::transaction(function () use ($record, $state) {

                            $order = Order::query()
                                ->whereKey($record->id)
                                ->lockForUpdate()
                                ->firstOrFail();

                            $order->loadMissing(['payment', 'items']);

                            $payment = $order->payment;

                            // Kalau tidak punya payment, ya update order saja (kasus jarang)
                            if (!$payment) {
                                $order->update(['status' => $state]);
                                return;
                            }

                            /**
                             * 1) Jika admin set ORDER = DIBATALKAN
                             *    => payment otomatis REJECTED (sinkron 2 arah)
                             */
                            if ($state === 'dibatalkan') {
                                $order->update(['status' => 'dibatalkan']);

                                if ($payment->status !== 'rejected') {
                                    $payment->update([
                                        'status'      => 'rejected',
                                        'verified_at' => null,
                                        'verified_by' => auth()->id(),
                                    ]);
                                }
                                return;
                            }

                            /**
                             * 2) Jika PAYMENT sudah REJECTED
                             *    => order harus dibatalkan, admin tidak boleh set status lain
                             */
                            if ($payment->status === 'rejected') {
                                // Paksa tetap dibatalkan
                                $order->update(['status' => 'dibatalkan']);

                                Notification::make()
                                    ->title('Tidak bisa ubah status')
                                    ->body('Payment Ditolak, order harus Dibatalkan.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            /**
                             * 3) Jika transfer belum verified
                             *    => order tidak boleh maju ke diproses/dikirim/selesai
                             */
                            if ($payment->method === 'transfer' && $payment->status !== 'verified') {
                                if (in_array($state, ['diproses', 'dikirim', 'selesai'], true)) {
                                    Notification::make()
                                        ->title('Tidak bisa ubah status')
                                        ->body('Transfer belum Terverifikasi. ACC pembayaran dulu.')
                                        ->danger()
                                        ->send();
                                    return; // tidak update apa-apa
                                }
                            }

                            // Update order sesuai pilihan admin
                            $order->update(['status' => $state]);
                        });

                        Notification::make()
                            ->title('Status order diperbarui')
                            ->success()
                            ->send();

                        // IMPORTANT: return $state biar dropdown Filament update
                        return $state;
                    }),

                /**
                 * ✅ STATUS PAYMENT (dropdown) - SINKRON 2 ARAH
                 * verified => cek stok + potong stok (sekali) + order jadi diproses (minimal)
                 * rejected => order dibatalkan
                 */
                Tables\Columns\SelectColumn::make('payment.status')
                    ->label('Status Payment')
                    ->options([
                        'waiting_verification' => 'Menunggu Verifikasi',
                        'pending'              => 'Pending',
                        'verified'             => 'Terverifikasi',
                        'rejected'             => 'Ditolak',
                    ])
                    ->selectablePlaceholder(false)
                    ->disabled(fn (Order $record) => !$record->payment)
                    ->updateStateUsing(function (Order $record, $state) {

                        if (!$record->payment) return $state;

                        try {
                            DB::transaction(function () use ($record, $state) {

                                $order = Order::query()
                                    ->whereKey($record->id)
                                    ->lockForUpdate()
                                    ->firstOrFail();

                                $order->loadMissing(['payment', 'items']);
                                $payment = $order->payment;

                                /**
                                 * A) Jika admin set PAYMENT = REJECTED
                                 *    => ORDER otomatis DIBATALKAN
                                 */
                                if ($state === 'rejected') {
                                    $payment->update([
                                        'status'      => 'rejected',
                                        'verified_at' => null,
                                        'verified_by' => auth()->id(),
                                    ]);

                                    $order->update(['status' => 'dibatalkan']);
                                    return;
                                }

                                /**
                                 * B) Jika admin set PAYMENT = VERIFIED
                                 *    => cek & potong stok (sekali)
                                 *    => order minimal DIPROSES (atau mau auto dikirim/selesai boleh)
                                 */
                                if ($state === 'verified') {

                                    // Jika sudah verified sebelumnya, jangan potong stok lagi
                                    if ($payment->status !== 'verified') {

                                        // cek stok (prioritas shade)
                                        foreach ($order->items as $it) {
                                            $qty = (int) $it->qty;

                                            if (!empty($it->product_shade_id)) {
                                                $shade = ProductShade::query()
                                                    ->whereKey($it->product_shade_id)
                                                    ->lockForUpdate()
                                                    ->firstOrFail();

                                                if ((int) $shade->stock < $qty) {
                                                    throw new \Exception("Stok shade '{$shade->shade_name}' tidak cukup.");
                                                }
                                            } else {
                                                $product = Product::query()
                                                    ->whereKey($it->product_id)
                                                    ->lockForUpdate()
                                                    ->firstOrFail();

                                                if ((int) $product->stock < $qty) {
                                                    throw new \Exception("Stok produk '{$product->name}' tidak cukup.");
                                                }
                                            }
                                        }

                                        // potong stok (sekali)
                                        foreach ($order->items as $it) {
                                            $qty = (int) $it->qty;

                                            if (!empty($it->product_shade_id)) {
                                                ProductShade::query()
                                                    ->whereKey($it->product_shade_id)
                                                    ->lockForUpdate()
                                                    ->firstOrFail()
                                                    ->decrement('stock', $qty);
                                            } else {
                                                Product::query()
                                                    ->whereKey($it->product_id)
                                                    ->lockForUpdate()
                                                    ->firstOrFail()
                                                    ->decrement('stock', $qty);
                                            }
                                        }
                                    }

                                    $payment->update([
                                        'status'      => 'verified',
                                        'verified_at' => now(),
                                        'verified_by' => auth()->id(),
                                    ]);

                                    // Setelah verified, order minimal diproses kalau sebelumnya masih menunggu_verifikasi / dibatalkan
                                    if (in_array($order->status, ['menunggu_verifikasi'], true)) {
                                        $order->update(['status' => 'diproses']);
                                    }

                                    /**
                                     * 🔥 Kalau kamu MAU verified otomatis jadi DIKIRIM / SELESAI:
                                     * contoh:
                                     * $order->update(['status' => 'dikirim']);
                                     * atau
                                     * $order->update(['status' => 'selesai']);
                                     */
                                    return;
                                }

                                /**
                                 * C) Selain itu (pending / waiting_verification)
                                 *    => update payment saja
                                 *    (opsional) kalau order dibatalkan karena sebelumnya rejected,
                                 *    kamu mau tetap dibatalkan atau balikin? aku biarkan tetap.
                                 */
                                $payment->update([
                                    'status'      => $state,
                                    'verified_at' => null,
                                    'verified_by' => null,
                                ]);

                                // Jika payment kembali ke waiting/pending dan order kebetulan dibatalkan,
                                // aku tidak paksa balik (biar admin yang tentukan).
                            });

                            Notification::make()
                                ->title('Status payment diperbarui')
                                ->success()
                                ->send();

                            return $state;

                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Gagal update payment')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();

                            // balik ke status lama supaya UI tidak misleading
                            return $record->payment->status;
                        }
                    }),

                Tables\Columns\TextColumn::make('payment.method')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'transfer' => 'Transfer',
                        'cod'      => 'COD',
                        'store'    => 'Bayar di Toko',
                        null       => '-',
                        default    => $state,
                    })
                    ->placeholder('-'),

                Tables\Columns\ImageColumn::make('payment.payment_proof')
                    ->label('Bukti')
                    ->disk('public')
                    ->visibility('public')
                    ->height(56)
                    ->width(56)
                    ->square()
                    ->url(fn (Order $record) => $record->payment?->payment_proof
                        ? Storage::disk('public')->url($record->payment->payment_proof)
                        : null
                    )
                    ->openUrlInNewTab()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status Order')
                    ->options([
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'diproses'            => 'Diproses',
                        'dikirim'             => 'Dikirim',
                        'selesai'             => 'Selesai',
                        'dibatalkan'          => 'Dibatalkan',
                    ]),

                SelectFilter::make('payment_status')
                    ->label('Filter Status Payment')
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) return $query;
                        return $query->whereHas('payment', fn ($q) => $q->where('status', $data['value']));
                    })
                    ->options([
                        'waiting_verification' => 'Menunggu Verifikasi',
                        'pending'              => 'Pending',
                        'verified'             => 'Terverifikasi',
                        'rejected'             => 'Ditolak',
                    ]),
            ])
            ->actions([

                /**
                 * ✅ ACC pembayaran (transfer)
                 * muncul kalau:
                 * - order masih menunggu_verifikasi
                 * - payment transfer
                 * - ada bukti
                 * - payment status pending/waiting
                 */
                Action::make('verify')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->iconButton()
                    ->tooltip('ACC Pembayaran')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record) =>
                        $record->status === 'menunggu_verifikasi'
                        && $record->payment?->method === 'transfer'
                        && !empty($record->payment?->payment_proof)
                        && in_array($record->payment?->status, ['pending', 'waiting_verification'], true)
                    )
                    ->action(function (Order $record) {
                        // cukup “set payment verified” (logic stok & sinkron sudah aman juga lewat dropdown)
                        // tapi untuk tombol ini, kita eksekusi versi aman yang lengkap:
                        try {
                            DB::transaction(function () use ($record) {

                                $order = Order::query()->whereKey($record->id)->lockForUpdate()->firstOrFail();
                                $order->loadMissing(['payment', 'items']);

                                if (!$order->payment) throw new \Exception('Payment tidak ditemukan.');

                                if ($order->payment->status === 'verified') {
                                    return;
                                }

                                // cek stok
                                foreach ($order->items as $it) {
                                    $qty = (int) $it->qty;

                                    if (!empty($it->product_shade_id)) {
                                        $shade = ProductShade::query()->whereKey($it->product_shade_id)->lockForUpdate()->firstOrFail();
                                        if ((int) $shade->stock < $qty) throw new \Exception("Stok shade '{$shade->shade_name}' tidak cukup.");
                                    } else {
                                        $product = Product::query()->whereKey($it->product_id)->lockForUpdate()->firstOrFail();
                                        if ((int) $product->stock < $qty) throw new \Exception("Stok produk '{$product->name}' tidak cukup.");
                                    }
                                }

                                // potong stok
                                foreach ($order->items as $it) {
                                    $qty = (int) $it->qty;

                                    if (!empty($it->product_shade_id)) {
                                        ProductShade::query()->whereKey($it->product_shade_id)->lockForUpdate()->firstOrFail()
                                            ->decrement('stock', $qty);
                                    } else {
                                        Product::query()->whereKey($it->product_id)->lockForUpdate()->firstOrFail()
                                            ->decrement('stock', $qty);
                                    }
                                }

                                $order->payment->update([
                                    'status'      => 'verified',
                                    'verified_at' => now(),
                                    'verified_by' => auth()->id(),
                                ]);

                                if ($order->status === 'menunggu_verifikasi') {
                                    $order->update(['status' => 'diproses']);
                                }
                            });

                            Notification::make()->title('Pembayaran di-ACC')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Gagal ACC')->body($e->getMessage())->danger()->send();
                        }
                    }),

                /**
                 * ❌ Tolak pembayaran (transfer)
                 * muncul kalau:
                 * - order menunggu_verifikasi / diproses (opsional)
                 * - payment transfer
                 * - ada bukti
                 * - payment belum rejected & belum verified
                 */
                Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->iconButton()
                    ->tooltip('Tolak Pembayaran')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record) =>
                        in_array($record->status, ['menunggu_verifikasi', 'diproses'], true)
                        && $record->payment?->method === 'transfer'
                        && !empty($record->payment?->payment_proof)
                        && in_array($record->payment?->status, ['pending', 'waiting_verification'], true)
                    )
                    ->action(function (Order $record) {
                        try {
                            DB::transaction(function () use ($record) {
                                $order = Order::query()->whereKey($record->id)->lockForUpdate()->firstOrFail();
                                $order->loadMissing(['payment']);

                                if (!$order->payment) throw new \Exception('Payment tidak ditemukan.');

                                $order->payment->update([
                                    'status'      => 'rejected',
                                    'verified_at' => null,
                                    'verified_by' => auth()->id(),
                                ]);

                                $order->update(['status' => 'dibatalkan']);
                            });

                            Notification::make()->title('Pembayaran ditolak')->danger()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Gagal menolak')->body($e->getMessage())->danger()->send();
                        }
                    }),

                /**
                 * 🚚 Kirim
                 * muncul kalau:
                 * - order diproses
                 * - dan kalau transfer: payment harus verified
                 */
                Action::make('ship')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->iconButton()
                    ->tooltip('Kirim Pesanan')
                    ->requiresConfirmation()
                    ->visible(function (Order $record) {
                        if ($record->status !== 'diproses') return false;

                        $method = $record->payment?->method;
                        $pstat  = $record->payment?->status;

                        if ($method === 'transfer' && $pstat !== 'verified') return false;
                        if ($pstat === 'rejected') return false;

                        return true;
                    })
                    ->action(function (Order $record) {
                        $record->update(['status' => 'dikirim']);
                        Notification::make()->title('Pesanan dikirim')->success()->send();
                    }),

                Action::make('finish')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->iconButton()
                    ->tooltip('Pesanan selesai')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record) => $record->status === 'dikirim')
                    ->action(function (Order $record) {
                        $record->update(['status' => 'selesai']);
                        Notification::make()->title('Pesanan selesai')->success()->send();
                    }),

                Action::make('detail')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->iconButton()
                    ->tooltip('Detail Order')
                    ->modalHeading('Detail Order')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(function (Order $record) {
                        $order = $record->load([
                            'user',
                            'payment',
                            'items.product',
                            'items.shade',
                        ]);

                        return view('filament.orders.detail', ['order' => $order]);
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
        ];
    }
}