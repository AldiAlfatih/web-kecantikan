<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use App\Services\RecommendationEngine;

class HomeController extends Controller
{
    public function index(RecommendationEngine $engine)
    {
        // =========================
        // 1) BEST SELLER (GUEST)
        // =========================
        // Ambil 3 produk terlaris dari order_items (qty terbanyak)
        $bestSellerProductIds = OrderItem::query()
            ->selectRaw('product_id, SUM(qty) as total_qty')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(3)
            ->pluck('product_id');

        $bestSellers = Product::query()
            ->whereIn('id', $bestSellerProductIds)
            ->get()
            // urutkan sesuai urutan pluck (biar tampil sesuai ranking)
            ->sortBy(fn ($p) => array_search($p->id, $bestSellerProductIds->toArray()))
            ->values();

        // =========================
        // 2) USER BELUM LOGIN
        // =========================
        if (!Auth::check()) {
            return view('index', [
                'isLogin' => false,
                'bestSellers' => $bestSellers,
                // biar blade aman walau tidak dipakai
                'recommendedProducts' => collect(),
                'otherProducts' => collect(),
            ]);
        }

        // =========================
        // 3) USER LOGIN
        // =========================
        $user = Auth::user()->load('pcaProfile');
        $pca  = $user->pcaProfile;

        $recommendedProducts = collect();

        // jika PCA lengkap, ambil rekomendasi dari engine
        if ($pca && $pca->skin_tone_level && $pca->undertone) {
            $profile = [
                'skin_tone_level' => (int) $pca->skin_tone_level,
                'undertone'       => strtolower($pca->undertone),
            ];

            // Engine return ProductShade collection
            $shades = $engine->recommend($profile);

            // Ambil product unik (1 produk 1 rekomendasi)
            $productIds = $shades
                ->groupBy('product_id')
                ->keys()
                ->take(3)
                ->values();

            $recommendedProducts = Product::query()
                ->whereIn('id', $productIds)
                ->get()
                ->sortBy(fn ($p) => array_search($p->id, $productIds->toArray()))
                ->values();
        }

        // =========================
        // 4) PRODUK LAINNYA (6 item)
        // =========================
        $excludeIds = $recommendedProducts->pluck('id')->toArray();

        $otherProducts = Product::query()
            ->when(count($excludeIds) > 0, fn ($q) => $q->whereNotIn('id', $excludeIds))
            ->latest('id')
            ->limit(6)
            ->get();

        return view('index', [
            'isLogin' => true,
            'bestSellers' => $bestSellers,
            'recommendedProducts' => $recommendedProducts,
            'otherProducts' => $otherProducts,
        ]);
    }
}