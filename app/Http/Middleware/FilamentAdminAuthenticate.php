<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate;

class FilamentAdminAuthenticate extends Authenticate
{
    /**
     * Kalau user belum login dan coba akses /admin,
     * arahkan ke /login (halaman login utama) — BUKAN /admin/login.
     */
    protected function redirectTo($request): ?string
    {
        return route('login');
    }
}
