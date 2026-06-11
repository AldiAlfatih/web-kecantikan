<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function process(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Email atau password salah',
            ])->withInput();
        }

        // ✅ sukses login
        $request->session()->regenerate();

        // ✅ kalau admin -> langsung ke filament
        if (Auth::user()->role === 'admin') {
            return redirect('/admin'); // panel filament kamu id = admin
        }

        // ✅ kalau user biasa -> ke home
        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}