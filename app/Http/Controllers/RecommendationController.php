<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\RecommendationEngine;

class RecommendationController extends Controller
{
    public function index(RecommendationEngine $engine)
    {
        $user = Auth::user()->load('pcaProfile');
        $pca  = $user->pcaProfile;

        $recommendations = collect([]);
        $toneLabel = '';
        $undertoneLabel = '';

        if ($pca && $pca->skin_tone_level && $pca->undertone) {

            $profile = [
                'skin_tone_level' => (int) $pca->skin_tone_level,
                'undertone'       => strtolower($pca->undertone),
            ];

            $shades = $engine->recommend($profile);

            // tampilkan 1 produk saja per product_id
            $recommendations = $shades
                ->groupBy('product_id')
                ->map(fn ($group) => $group->first())
                ->values();

            $toneLabel = $this->toneLabelFromLevel((int) $pca->skin_tone_level);
            $undertoneLabel = ucfirst(strtolower($pca->undertone));
        }

        return view('rekomendasi', [
            'recommendations' => $recommendations,
            'tone'            => $toneLabel,
            'undertone'       => $undertoneLabel,
            'pca'             => $pca,
        ]);
    }

    private function toneLabelFromLevel(int $level): string
    {
        return match ($level) {
            1 => 'Sangat terang (Fair)',
            2 => 'Terang (Light)',
            3 => 'Sedang / Kuning langsat (Medium)',
            4 => 'Sawo matang terang (Tan)',
            5 => 'Sawo matang gelap (Deep)',
            6 => 'Gelap (Dark)',
            default => '',
        };
    }
}