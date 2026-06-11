<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class SkinAnalysisController extends Controller
{
    /**
     * Halaman utama Skin Analysis & AR Try-On.
     * Hanya bisa diakses setelah login (middleware 'auth' di route).
     */
    public function index()
    {
        $user = Auth::user()->load('pcaProfile');

        // Ambil semua produk aktif beserta shade-nya untuk AR overlay picker
        // Hanya produk yang punya minimal 1 shade stok > 0
        $products = Product::query()
            ->where('is_active', true)
            ->whereHas('shades', fn ($q) => $q->where('stock', '>', 0))
            ->with(['shades' => fn ($q) => $q->where('stock', '>', 0)])
            ->latest()
            ->get();

        // Mapping skin_tone_level -> label (konsisten dengan RecommendationController)
        $toneLevels = [
            1 => 'Sangat terang (Fair)',
            2 => 'Terang (Light)',
            3 => 'Sedang / Kuning langsat (Medium)',
            4 => 'Sawo matang terang (Tan)',
            5 => 'Sawo matang gelap (Deep)',
            6 => 'Gelap (Dark)',
        ];

        return view('skin_analysis', [
            'user'       => $user,
            'pca'        => $user->pcaProfile,
            'products'   => $products,
            'toneLevels' => $toneLevels,
        ]);
    }
}
