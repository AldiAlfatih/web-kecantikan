<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductShade;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $items = [];
        $total = 0;

        foreach ($cart as $item) {
            $product = Product::find($item['product_id'] ?? null);

            $shade = null;
            if (!empty($item['shade_id'])) {
                $shade = ProductShade::find($item['shade_id']);
            }

            if (!$product) {
                continue;
            }

            // kalau shade hilang/tidak valid, skip item biar aman
            if (!$shade) {
                continue;
            }

            $qty = (int) ($item['qty'] ?? 1);
            $qty = max(1, $qty);

            // ✅ clamp qty biar tidak lebih dari stok shade
            $shadeStock = (int) ($shade->stock ?? 0);
            if ($shadeStock <= 0) {
                // kalau stok habis, item ini sebaiknya ditandai/skip
                // sementara: biarkan tampil, tapi qty jadi 1
                $qty = 1;
            } else {
                $qty = min($qty, $shadeStock);
            }

            $subtotal = $product->price * $qty;

            $items[] = [
                'product'     => $product,
                'qty'         => $qty,
                'shade'       => $shade,
                'shade_text'  => $item['shade'] ?? null,
                'subtotal'    => $subtotal,
                'shade_stock' => $shadeStock,
            ];

            $total += $subtotal;
        }

        return view('keranjang', compact('items', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
            'shade_id'   => 'required|exists:product_shades,id',
        ], [
            'shade_id.required' => 'Silakan pilih shade terlebih dahulu.',
            'shade_id.exists'   => 'Shade tidak valid.',
        ]);

        // pastikan shade memang milik product
        $shade = ProductShade::where('id', $request->shade_id)
            ->where('product_id', $request->product_id)
            ->firstOrFail();

        $stock = (int) ($shade->stock ?? 0);

        // ✅ CEK STOK (ini yang kamu butuh)
        if ($stock <= 0) {
            return back()->with('error', 'Shade yang kamu pilih sedang HABIS.');
        }

        $qty = (int) $request->qty;
        $qty = max(1, $qty);

        // ✅ kalau qty melebihi stok, tolak atau clamp
        if ($qty > $stock) {
            return back()->with('error', 'Jumlah melebihi stok. Stok tersedia: ' . $stock);
        }

        $cart = session()->get('cart', []);

        // ✅ OPTIONAL: kalau produk+shade sama sudah ada di cart, gabungkan qty
        $found = false;
        foreach ($cart as $i => $it) {
            if ((int)$it['product_id'] === (int)$request->product_id && (int)$it['shade_id'] === (int)$shade->id) {
                $newQty = (int)$it['qty'] + $qty;

                if ($newQty > $stock) {
                    return back()->with('error', 'Qty di keranjang melebihi stok. Stok tersedia: ' . $stock);
                }

                $cart[$i]['qty'] = $newQty;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart[] = [
                'product_id' => (int) $request->product_id,
                'qty'        => $qty,
                'shade_id'   => (int) $shade->id,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('keranjang')->with('success', 'Produk berhasil ditambahkan');
    }

    public function update(Request $request)
    {
        $request->validate([
            'index' => 'required|integer|min:0',
            'qty'   => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (!isset($cart[$request->index])) {
            return redirect()->route('keranjang');
        }

        $item = $cart[$request->index];

        // ambil shade untuk cek stok
        $shade = ProductShade::find($item['shade_id'] ?? null);
        if (!$shade) {
            return redirect()->route('keranjang')->with('error', 'Shade tidak valid.');
        }

        $stock = (int) ($shade->stock ?? 0);

        if ($stock <= 0) {
            // stok habis -> jangan bisa update qty
            $cart[$request->index]['qty'] = 1;
            session()->put('cart', $cart);

            return redirect()->route('keranjang')->with('error', 'Shade ini sudah HABIS.');
        }

        $qty = (int) $request->qty;
        $qty = max(1, $qty);

        if ($qty > $stock) {
            return redirect()->route('keranjang')->with('error', 'Qty melebihi stok. Stok tersedia: ' . $stock);
        }

        $cart[$request->index]['qty'] = $qty;

        session()->put('cart', $cart);

        return redirect()->route('keranjang');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'index' => 'required|integer|min:0',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->index])) {
            unset($cart[$request->index]);
            $cart = array_values($cart);
        }

        session()->put('cart', $cart);

        return redirect()->route('keranjang');
    }
}