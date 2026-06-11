<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\RecommendationEngine;

class ProductController extends Controller
{
    public function index(Request $request, RecommendationEngine $engine)
    {
        $search   = trim((string) $request->query('search', ''));
        $brand    = trim((string) $request->query('brand', ''));
        $category = trim((string) $request->query('category', ''));
        $skinType = trim((string) $request->query('skin_type', ''));
        $min      = $request->query('min');
        $max      = $request->query('max');

        // mapping label untuk UI
        $skinTypeOptions = [
            'normal'    => 'Normal',
            'berminyak' => 'Berminyak',
            'kering'    => 'Kering',
            'kombinasi' => 'Kombinasi',
            'sensitif'  => 'Sensitif',
        ];

        $query = Product::query()->where('is_active', true);

        // SEARCH
        $query->when($search, function ($q) use ($search) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('skin_type', 'like', "%{$search}%");
            });
        });

        // FILTERS
        $query->when($brand, fn ($q) => $q->where('brand', $brand));
        $query->when($category, fn ($q) => $q->where('category', $category));

        $query->when($skinType, function ($q) use ($skinTypeOptions, $skinType) {
            if (array_key_exists($skinType, $skinTypeOptions)) {
                $q->where('skin_type', $skinType);
            }
        });

        if (is_numeric($min)) {
            $query->where('price', '>=', (int) $min);
        }

        if (is_numeric($max)) {
            $query->where('price', '<=', (int) $max);
        }

        // DATA GRID PRODUK
        $products = (clone $query)
            ->latest()
            ->paginate(9)
            ->appends($request->query());

        // DROPDOWN FILTER
        $brands = Product::query()
            ->where('is_active', true)
            ->select('brand')
            ->whereNotNull('brand')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');

        $categories = Product::query()
            ->where('is_active', true)
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $skinTypes = Product::query()
            ->where('is_active', true)
            ->select('skin_type')
            ->whereNotNull('skin_type')
            ->distinct()
            ->orderBy('skin_type')
            ->pluck('skin_type');

        /**
         * ==========================================
         * PRODUK POPULER BULAN INI
         * ==========================================
         */
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        $popularProductIds = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select('order_items.product_id', DB::raw('SUM(order_items.qty) as total_qty'))
            ->where('products.is_active', true)
            ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
            ->whereNotIn('orders.status', ['dibatalkan'])
            ->groupBy('order_items.product_id')
            ->orderByDesc('total_qty')
            ->pluck('order_items.product_id')
            ->toArray();

        $recommended = collect();

        /**
         * ==========================================
         * JIKA SUDAH LOGIN:
         * ambil produk cocok PCA + populer bulan ini
         * ==========================================
         */
        if (Auth::check()) {
            $user = Auth::user()->load('pcaProfile');
            $pca  = $user->pcaProfile;

            if ($pca && $pca->skin_tone_level && $pca->undertone) {
                $profile = [
                    'skin_tone_level' => (int) $pca->skin_tone_level,
                    'undertone'       => strtolower((string) $pca->undertone),
                ];

                $recommendedShades = $engine->recommend($profile);

                $matchedProductIds = $recommendedShades
                    ->pluck('product_id')
                    ->unique()
                    ->values()
                    ->toArray();

                if (!empty($matchedProductIds)) {
                    $orderedMatchedIds = [];

                    foreach ($popularProductIds as $popularId) {
                        if (in_array($popularId, $matchedProductIds)) {
                            $orderedMatchedIds[] = $popularId;
                        }
                    }

                    foreach ($matchedProductIds as $matchedId) {
                        if (!in_array($matchedId, $orderedMatchedIds)) {
                            $orderedMatchedIds[] = $matchedId;
                        }
                    }

                    $topIds = array_slice($orderedMatchedIds, 0, 3);

                    if (!empty($topIds)) {
                        $recommended = Product::query()
                            ->where('is_active', true)
                            ->whereIn('id', $topIds)
                            ->get()
                            ->sortBy(function ($product) use ($topIds) {
                                return array_search($product->id, $topIds);
                            })
                            ->values()
                            ->map(function ($product, $index) {
                                $badgeTexts = ['Best Seller', 'Recommended', 'New'];
                                $badgeClasses = ['best', 'popular', 'new'];

                                $product->badge_text = $badgeTexts[$index] ?? 'Recommended';
                                $product->badge_class = $badgeClasses[$index] ?? 'popular';

                                return $product;
                            });
                    }
                }
            }
        }

        /**
         * ==========================================
         * FALLBACK:
         * guest / user belum punya PCA / belum ada hasil cocok
         * tampilkan populer bulan ini
         * kalau belum ada penjualan -> latest
         * ==========================================
         */
        if ($recommended->isEmpty()) {
            if (!empty($popularProductIds)) {
                $topIds = array_slice($popularProductIds, 0, 3);

                $recommended = Product::query()
                    ->where('is_active', true)
                    ->whereIn('id', $topIds)
                    ->get()
                    ->sortBy(function ($product) use ($topIds) {
                        return array_search($product->id, $topIds);
                    })
                    ->values()
                    ->map(function ($product, $index) {
                        $badgeTexts = ['Best Seller', 'Popular', 'New'];
                        $badgeClasses = ['best', 'popular', 'new'];

                        $product->badge_text = $badgeTexts[$index] ?? 'Popular';
                        $product->badge_class = $badgeClasses[$index] ?? 'popular';

                        return $product;
                    });
            } else {
                $recommended = Product::query()
                    ->where('is_active', true)
                    ->latest()
                    ->take(3)
                    ->get()
                    ->values()
                    ->map(function ($product, $index) {
                        $badgeTexts = ['New', 'Popular', 'Recommended'];
                        $badgeClasses = ['new', 'popular', 'best'];

                        $product->badge_text = $badgeTexts[$index] ?? 'Popular';
                        $product->badge_class = $badgeClasses[$index] ?? 'popular';

                        return $product;
                    });
            }
        }

        return view('produk', [
            'products'         => $products,
            'recommended'      => $recommended,
            'brands'           => $brands,
            'categories'       => $categories,
            'skinTypes'        => $skinTypes,
            'skinTypeOptions'  => $skinTypeOptions,
            'search'           => $search,
            'brandSelected'    => $brand,
            'catSelected'      => $category,
            'skinSelected'     => $skinType,
            'minSelected'      => $min,
            'maxSelected'      => $max,
        ]);
    }

    public function show($id, RecommendationEngine $engine)
    {
        $product = Product::query()
            ->where('is_active', true)
            ->with('shades')
            ->findOrFail($id);

        $recommendedShadeIds = [];

        if (Auth::check()) {
            $user = Auth::user()->load('pcaProfile');
            $pca  = $user->pcaProfile;

            if ($pca && $pca->skin_tone_level && $pca->undertone) {
                $profile = [
                    'skin_tone_level' => (int) $pca->skin_tone_level,
                    'undertone'       => strtolower((string) $pca->undertone),
                ];

                $recommendedShades = $engine->recommend($profile);

                $recommendedShadeIds = $recommendedShades
                    ->where('product_id', $product->id)
                    ->pluck('id')
                    ->values()
                    ->toArray();
            }
        }

        return view('detail_produk', [
            'product'             => $product,
            'recommendedShadeIds' => $recommendedShadeIds,
        ]);
    }
}