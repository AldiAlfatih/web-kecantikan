<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-3 border rounded">
            <div class="font-semibold mb-2">Info Order</div>
            <div>Kode: <b>{{ $order->order_code }}</b></div>
            <div>User: <b>{{ $order->user?->name ?? '-' }}</b></div>
            <div>Status Order: <b>{{ $order->status }}</b></div>
            <div>Total: <b>Rp {{ number_format($order->total_price, 0, ',', '.') }}</b></div>
        </div>

        <div class="p-3 border rounded">
            <div class="font-semibold mb-2">Pengiriman</div>
            <div>Nama: <b>{{ $order->receiver_name ?? '-' }}</b></div>
            <div>Email: <b>{{ $order->receiver_email ?? '-' }}</b></div>
            <div>HP: <b>{{ $order->receiver_phone ?? '-' }}</b></div>
            <div>Metode: <b>{{ $order->delivery_method ?? '-' }}</b></div>
            <div>Alamat: <b>{{ $order->shipping_address ?? '-' }}</b></div>
            <div>Catatan: <b>{{ $order->shipping_note ?? '-' }}</b></div>
        </div>
    </div>

    <div class="p-3 border rounded">
        <div class="font-semibold mb-2">Pembayaran</div>
        <div>Metode: <b>{{ $order->payment?->method ?? '-' }}</b></div>
        <div>Status: <b>{{ $order->payment?->status ?? '-' }}</b></div>
        <div>Catatan Admin: <b>{{ $order->payment?->note ?? '-' }}</b></div>

        @if($order->payment?->payment_proof)
            <div class="mt-3">
                <div class="font-semibold mb-2">Bukti Transfer</div>
                <a href="{{ Storage::disk('public')->url($order->payment->payment_proof) }}" target="_blank">
                    <img
                        src="{{ Storage::disk('public')->url($order->payment->payment_proof) }}"
                        alt="Bukti Transfer"
                        style="max-height: 220px; border-radius: 8px;"
                    />
                </a>
            </div>
        @else
            <div class="mt-2 text-sm text-gray-500">Tidak ada bukti transfer.</div>
        @endif
    </div>

    <div class="p-3 border rounded">
        <div class="font-semibold mb-2">Item yang dibeli</div>

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Produk</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Shade</th>
                        <th style="text-align:right; padding:8px; border-bottom:1px solid #ddd;">Harga</th>
                        <th style="text-align:center; padding:8px; border-bottom:1px solid #ddd;">Qty</th>
                        <th style="text-align:right; padding:8px; border-bottom:1px solid #ddd;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $it)
                        <tr>
                            <td style="padding:8px; border-bottom:1px solid #eee;">
                                {{ $it->product?->name ?? ('Produk #' . $it->product_id) }}
                            </td>
                            <td style="padding:8px; border-bottom:1px solid #eee;">
                                {{ $it->shade?->shade_name ?? '-' }}
                            </td>
                            <td style="padding:8px; border-bottom:1px solid #eee; text-align:right;">
                                Rp {{ number_format($it->price, 0, ',', '.') }}
                            </td>
                            <td style="padding:8px; border-bottom:1px solid #eee; text-align:center;">
                                {{ $it->qty }}
                            </td>
                            <td style="padding:8px; border-bottom:1px solid #eee; text-align:right;">
                                Rp {{ number_format($it->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:8px;">Tidak ada item.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>