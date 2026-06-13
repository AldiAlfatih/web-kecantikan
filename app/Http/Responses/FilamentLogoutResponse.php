<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;

class FilamentLogoutResponse implements Responsable
{
    /**
     * Setelah admin sign out dari panel Filament,
     * arahkan ke halaman home (/) — BUKAN /admin/login.
     */
    public function toResponse($request): RedirectResponse
    {
        return redirect('/');
    }
}
