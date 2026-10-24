<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware "role": membatasi halaman berdasarkan role user.
 *
 * Dipakai di route seperti: ->middleware('role:admin')
 * Kalau role user tidak cocok, tampilkan error 403 (dilarang).
 */
class EnsureUserHasRole
{
    /**
     * Dijalankan setiap request yang melewati middleware ini.
     * $roles berisi daftar role yang diizinkan (boleh lebih dari satu, dipisah koma).
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'Kamu tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}
