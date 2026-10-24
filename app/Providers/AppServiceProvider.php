<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

/**
 * AppServiceProvider: tempat mengatur layanan bawaan aplikasi saat start.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * register(): mendaftarkan layanan ke container (tidak dipakai di aplikasi ini).
     */
    public function register(): void
    {
        //
    }

    /**
     * boot(): dijalankan sekali saat aplikasi mulai.
     * Di sini kita memakai tampilan pagination buatan sendiri (resources/views/partials/pagination.blade.php)
     * supaya serasi dengan CSS dashboard, bukan tampilan bawaan Laravel yang butuh Tailwind.
     */
    public function boot(): void
    {
        Paginator::defaultView('partials.pagination');
        Paginator::defaultSimpleView('partials.pagination');
    }
}
