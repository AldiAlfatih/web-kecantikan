<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Cek apakah user sudah melengkapi profil (minimal: nomor telepon).
     * Jika belum, redirect ke halaman profil.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip jika belum login (biar middleware 'auth' yang handle)
        if (!$user) {
            return $next($request);
        }

        // Skip jika admin
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Cek apakah phone sudah diisi
        if (empty($user->phone)) {
            return redirect()->route('profile')
                ->with('warning', 'Silakan lengkapi profil (nomor telepon & alamat) sebelum melanjutkan checkout.');
        }

        return $next($request);
    }
}
