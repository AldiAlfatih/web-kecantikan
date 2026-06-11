<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',

            'address' => 'required|string',
            'phone' => 'required|string',

            'skin_type' => 'required',
            'skin_tone_level' => 'required|integer|min:1|max:6',
            'undertone' => 'required|in:cool,neutral,warm',
            'skin_problem' => 'required',
            'vein_color' => 'required|in:blue_purple,green_olive,mixed',
        ]);

        DB::transaction(function () use ($validated) {

            // 1️⃣ simpan user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'role' => 'user',
            ]);

            // 2️⃣ simpan user profile
            $user->profile()->create([
                'skin_type' => $validated['skin_type'],
                'tone' => $validated['skin_tone_level'],
                'undertone' => $validated['undertone'],
                'skin_problem' => $validated['skin_problem'],
                'vein_color' => $validated['vein_color'],
            ]);

            // 3️⃣ simpan PCA profile
            $user->pcaProfile()->create([
                'skin_tone_level' => $validated['skin_tone_level'],
                'undertone' => $validated['undertone'],
                'vein_color' => $validated['vein_color'],
            ]);
        });

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil, silakan login.');
    }
}