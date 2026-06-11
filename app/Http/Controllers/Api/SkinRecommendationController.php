<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPcaProfile;
use App\Services\RecommendationEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkinRecommendationController extends Controller
{
    /**
     * POST /api/skin-recommend
     *
     * Menerima hasil analisis warna kulit dari client-side JS (MediaPipe),
     * meneruskan ke RecommendationEngine yang sudah ada, dan mengembalikan
     * daftar produk + shade yang cocok dalam format JSON.
     *
     * Body: { skin_tone_level: int(1-6), undertone: "cool"|"neutral"|"warm" }
     */
    public function recommend(Request $request, RecommendationEngine $engine): JsonResponse
    {
        $validated = $request->validate([
            'skin_tone_level' => 'required|integer|min:1|max:6',
            'undertone'       => 'required|string|in:cool,neutral,warm',
        ]);

        $profile = [
            'skin_tone_level' => (int) $validated['skin_tone_level'],
            'undertone'       => strtolower($validated['undertone']),
        ];

        // Gunakan RecommendationEngine yang sudah ada — tidak perlu tulis ulang logika
        $shades = $engine->recommend($profile);

        // 1 produk = 1 shade paling cocok, maksimum 6 produk
        $recommendations = $shades
            ->groupBy('product_id')
            ->map(fn ($group) => $group->first()->load('product'))
            ->values()
            ->take(6)
            ->filter(fn ($shade) => $shade->product !== null)
            ->values()
            ->map(fn ($shade) => [
                'shade_id'         => $shade->id,
                'shade_name'       => $shade->shade_name,
                'hex_color'        => $shade->hex_color,
                'tone'             => $shade->tone,
                'undertone'        => $shade->undertone,
                'product' => [
                    'id'              => $shade->product->id,
                    'name'            => $shade->product->name,
                    'brand'           => $shade->product->brand,
                    'category'        => $shade->product->category,
                    'price'           => $shade->product->price,
                    'price_formatted' => 'Rp ' . number_format($shade->product->price, 0, ',', '.'),
                    'image'           => $shade->product->image
                        ? asset('storage/' . $shade->product->image)
                        : asset('assets/image/1.png'),
                    'url_detail'      => route('produk.show', $shade->product->id),
                    'url_tryon'       => route('tryon.show', [
                        'product' => $shade->product->id,
                        'shade'   => $shade->id,
                    ]),
                ],
            ]);

        return response()->json([
            'success'          => true,
            'skin_tone_level'  => $profile['skin_tone_level'],
            'undertone'        => $profile['undertone'],
            'tone_label'       => $this->toneLabelFromLevel($profile['skin_tone_level']),
            'undertone_label'  => ucfirst($profile['undertone']),
            'count'            => $recommendations->count(),
            'recommendations'  => $recommendations,
        ]);
    }

    /**
     * POST /api/skin-save
     *
     * Menyimpan hasil analisis warna kulit otomatis dari AI ke tabel
     * user_pca_profiles. Dipanggil client-side setelah user konfirmasi
     * "Simpan hasil analisis ini ke profilku?".
     *
     * Body: { skin_tone_level: int(1-6), undertone: "cool"|"neutral"|"warm", hex_sample: "#RRGGBB" }
     */
    public function saveProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'skin_tone_level' => 'required|integer|min:1|max:6',
            'undertone'       => 'required|string|in:cool,neutral,warm',
            'hex_sample'      => 'nullable|string|max:20',
        ]);

        $user       = Auth::user();
        $existing   = $user->pcaProfile;

        // Pertahankan vein_color lama jika sudah ada — tidak bisa dideteksi dari analisis wajah
        // Jika profil belum pernah ada, gunakan 'mixed' sebagai default netral
        $veinColor  = $existing?->vein_color ?? 'mixed';

        UserPcaProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'skin_tone_level' => (int) $validated['skin_tone_level'],
                'undertone'       => strtolower($validated['undertone']),
                'vein_color'      => $veinColor,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Profil warna kulit berhasil disimpan dari hasil analisis AI.',
            'data'    => [
                'skin_tone_level' => (int) $validated['skin_tone_level'],
                'tone_label'      => $this->toneLabelFromLevel((int) $validated['skin_tone_level']),
                'undertone'       => strtolower($validated['undertone']),
                'undertone_label' => ucfirst($validated['undertone']),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function toneLabelFromLevel(int $level): string
    {
        return match ($level) {
            1 => 'Sangat terang (Fair)',
            2 => 'Terang (Light)',
            3 => 'Sedang / Kuning langsat (Medium)',
            4 => 'Sawo matang terang (Tan)',
            5 => 'Sawo matang gelap (Deep)',
            6 => 'Gelap (Dark)',
            default => 'Tidak diketahui',
        };
    }
}
