<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductShade;

class TryOnController extends Controller
{
    /**
     * Landing page Virtual Try-On.
     * Tampilkan daftar produk yang bisa di-try-on (punya shade).
     */
    public function index()
    {
        $products = Product::query()
            ->where('is_active', true)
            ->whereHas('shades', fn ($q) => $q->where('stock', '>', 0))
            ->with(['shades' => fn ($q) => $q->where('stock', '>', 0)->take(1)])
            ->latest()
            ->get();

        return redirect()->route('produk')
            ->with('info', 'Pilih produk yang ingin dicoba, lalu klik "Virtual Try-On".');
    }

    public function show(Product $product, ProductShade $shade)
    {
        // Ambil semua shades milik product (sekali saja)
        $product->load('shades');

        // Pastikan shade benar-benar milik product ini
        $activeShade = $product->shades->firstWhere('id', $shade->id);

        if (! $activeShade) {
            abort(404);
        }

        // Gambar produk
        $image = !empty($product->image)
            ? asset('storage/' . $product->image)
            : asset('assets/image/1.png');

        return view('virtual_tryon', [
            'product'     => $product,
            'shades'      => $product->shades,
            'activeShade' => $activeShade,
            'image'       => $image,
        ]);
    }
}
