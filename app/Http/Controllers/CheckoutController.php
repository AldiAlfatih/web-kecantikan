<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductShade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('keranjang')
                ->withErrors(['cart' => 'Keranjang masih kosong.']);
        }

        $items = [];
        $total = 0;

        foreach ($cart as $item) {
            $product = Product::find($item['product_id'] ?? null);
            $shade   = ProductShade::find($item['shade_id'] ?? null);

            // kalau data rusak, skip
            if (!$product || !$shade) {
                continue;
            }

            $qty = max(1, (int) ($item['qty'] ?? 1));

            // clamp qty sesuai stok shade (biar tampil aman)
            $shadeStock = (int) ($shade->stock ?? 0);
            if ($shadeStock <= 0) {
                $qty = 1;
            } else {
                $qty = min($qty, $shadeStock);
            }

            $subtotal = (int) $product->price * $qty;

            $items[] = [
                'product'  => $product,
                'shade'    => $shade,
                'qty'      => $qty,
                'subtotal' => $subtotal,
            ];

            $total += $subtotal;
        }

        if (empty($items)) {
            return redirect()->route('keranjang')
                ->withErrors(['cart' => 'Keranjang tidak valid / item tidak ditemukan.']);
        }

        $shipping = 0;
        $grandTotal = $total;

        return view('checkout.index', compact('user', 'items', 'total', 'shipping', 'grandTotal'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:120',
            'email'           => 'required|email|max:120',
            'phone'           => 'required|string|max:30',

            'delivery_method' => 'required|in:courier,pickup',
            'method'          => 'required|in:transfer,cod,store',

            'address'         => 'nullable|string|max:500',
            'bank'            => 'nullable|string|max:120',
            'payment_proof'   => 'nullable|image|max:2048',
        ], [
            'payment_proof.image' => 'Bukti transfer harus berupa gambar (JPG/PNG).',
            'payment_proof.max'   => 'Ukuran gambar maksimal 2MB.',
        ]);

        // validasi tambahan sesuai pilihan
        if ($request->delivery_method === 'courier' && empty($request->address)) {
            return back()->withErrors(['address' => 'Alamat wajib diisi jika memilih Diantar Kurir.'])->withInput();
        }

        if ($request->method === 'transfer') {
            if (empty($request->bank)) {
                return back()->withErrors(['bank' => 'Silakan pilih bank / e-wallet.'])->withInput();
            }
            if (!$request->hasFile('payment_proof')) {
                return back()->withErrors(['payment_proof' => 'Bukti transfer wajib diupload jika metode Transfer.'])->withInput();
            }
        }

        if ($request->method === 'store' && $request->delivery_method !== 'pickup') {
            return back()->withErrors(['method' => 'Metode "Bayar di Toko" hanya tersedia untuk Pick Up.'])->withInput();
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('keranjang')
                ->withErrors(['cart' => 'Keranjang kosong.']);
        }

        DB::beginTransaction();

        try {
            // status order
            $orderStatus = ($request->method === 'transfer')
                ? 'menunggu_verifikasi'
                : 'diproses';

            $order = Order::create([
                'user_id'          => Auth::id(),
                'order_code'       => 'ORD-' . strtoupper(Str::random(8)),
                'total_price'      => 0,
                'status'           => $orderStatus,

                'receiver_name'    => $request->name,
                'receiver_email'   => $request->email,
                'receiver_phone'   => $request->phone,
                'delivery_method'  => $request->delivery_method,
                'shipping_address' => $request->delivery_method === 'courier' ? $request->address : null,
                'shipping_note'    => null,
            ]);

            $total = 0;

            foreach ($cart as $item) {
                $productId = (int)($item['product_id'] ?? 0);
                $shadeId   = (int)($item['shade_id'] ?? 0);
                $qty       = max(1, (int)($item['qty'] ?? 1));

                $product = Product::find($productId);
                if (!$product) {
                    throw new \Exception("Produk tidak ditemukan.");
                }

                // ✅ kunci row shade agar aman dari race condition
                $shade = ProductShade::where('id', $shadeId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (!$shade) {
                    throw new \Exception("Shade tidak valid untuk produk '{$product->name}'.");
                }

                $stock = (int) ($shade->stock ?? 0);
                if ($stock <= 0) {
                    throw new \Exception("Shade '{$shade->shade_name}' sedang HABIS.");
                }
                if ($qty > $stock) {
                    throw new \Exception("Qty melebihi stok shade '{$shade->shade_name}'. Stok tersedia: {$stock}");
                }

                $subtotal = (int) $product->price * $qty;

                OrderItem::create([
                    'order_id'         => $order->id,
                    'product_id'       => $product->id,
                    'product_shade_id' => $shade->id,
                    'price'            => (int) $product->price,
                    'qty'              => $qty,
                    'subtotal'         => $subtotal,
                ]);

                // ✅ paling aman: stok shade dipotong saat checkout (semua metode)
                // kalau transfer ditolak, nanti admin bisa kembalikan stok
                $shade->decrement('stock', $qty);

                $total += $subtotal;
            }

            $order->update(['total_price' => $total]);

            $proofPath = null;
            if ($request->method === 'transfer' && $request->hasFile('payment_proof')) {
                $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
            }

            $paymentStatus = match ($request->method) {
                'transfer' => 'waiting_verification',
                'cod'      => 'pending',
                'store'    => 'pending',
                default    => 'pending',
            };

            $note = null;
            if ($request->method === 'transfer') {
                $note = 'Bank/E-Wallet: ' . ($request->bank ?? '-');
            } elseif ($request->method === 'store') {
                $note = 'Bayar langsung di toko (Pick Up).';
            }

            Payment::create([
                'order_id'      => $order->id,
                'method'        => $request->method,
                'status'        => $paymentStatus,
                'payment_proof' => $proofPath,
                'note'          => $note,
            ]);

            session()->forget('cart');

            DB::commit();

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Checkout berhasil!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['checkout' => $e->getMessage()])->withInput();
        }
    }
}