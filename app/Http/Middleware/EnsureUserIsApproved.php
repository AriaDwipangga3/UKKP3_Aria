<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware "approved": hanya akun berstatus 'approved' yang boleh masuk.
 *
 * Login sudah mengecek status, tapi middleware ini jadi lapisan kedua:
 * kalau admin menolak / mengembalikan akun ke pending SAAT user itu sedang
 * login, request berikutnya langsung ditendang keluar.
 */
class EnsureUserIsApproved
{
    /**
     * Cek status user. Kalau belum approved: logout paksa lalu arahkan ke halaman login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isApproved()) {
            $pesan = $user->isRejected()
                ? 'Pendaftaran akunmu ditolak oleh admin.'
                : 'Akunmu belum di-ACC admin. Silakan tunggu persetujuan.';

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => $pesan]);
        }

        return $next($request);
    }
}
