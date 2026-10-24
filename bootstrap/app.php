<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureUserIsApproved;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

/**
 * Titik awal konfigurasi aplikasi Laravel: routing, middleware, dan penanganan error.
 */
return Application::configure(basePath: dirname(__DIR__))
    // Mendaftarkan file route yang dipakai aplikasi.
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // Memberi "nama panggilan" ke middleware buatan sendiri agar bisa dipakai di routes/web.php:
    //   'role'     -> cek role user (admin / customer)
    //   'approved' -> cek akun sudah di-ACC admin
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'approved' => EnsureUserIsApproved::class,
        ]);
    })
    // Kalau request meminta JSON (mis. API), error juga dikembalikan dalam bentuk JSON.
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
