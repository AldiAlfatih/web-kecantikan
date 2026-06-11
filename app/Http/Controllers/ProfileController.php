<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Models\UserPcaProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load(['profile', 'pcaProfile']);

        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'phone'           => 'required|string|max:30',
            'address'         => 'required|string|max:500',
            'skin_type'       => 'required|in:normal,berminyak,kering,kombinasi,sensitif',
            'skin_problem'    => 'nullable|array',
            'skin_tone_level' => 'required|integer|min:1|max:6',
            'vein_color'      => 'nullable|in:blue_purple,green_olive,mixed',
        ]);

        $user->update([
            'name'    => $validated['name'],
            'phone'   => $validated['phone'],
            'address' => $validated['address'],
        ]);

        $skinProblemString = isset($validated['skin_problem'])
            ? strtolower(implode(', ', $validated['skin_problem']))
            : null;

        $veinColor = $validated['vein_color'] ?? null;

        $undertone = match ($veinColor) {
            'blue_purple' => 'cool',
            'green_olive' => 'warm',
            'mixed'       => 'neutral',
            default       => null,
        };

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'skin_type'    => strtolower(trim($validated['skin_type'])),
                'skin_problem' => $skinProblemString,

                // sementara tetap diisi agar kolom wajib tidak error
                'tone'         => 'fair',
                'undertone'    => $undertone ?? 'neutral',
                'vein_color'   => $veinColor,
            ]
        );

        UserPcaProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'skin_tone_level' => (int) $validated['skin_tone_level'],
                'vein_color'      => $veinColor,
                'undertone'       => $undertone,
                'hue'             => $undertone === 'neutral' ? 'neutral' : $undertone,
                'value'           => null,
                'chroma'          => null,
                'season'          => null,
                'confidence'      => null,
            ]
        );

        return back()->with('success', 'Profil berhasil disimpan.');
    }
}