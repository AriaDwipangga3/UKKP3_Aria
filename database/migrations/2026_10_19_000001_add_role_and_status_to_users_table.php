<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: menambah 3 kolom ke tabel `users`.
 *
 *  - role        : 'admin' atau 'customer' (hanya 2 role di aplikasi ini)
 *  - status      : 'pending' (menunggu ACC), 'approved' (sudah di-ACC), 'rejected' (ditolak)
 *  - approved_at : waktu akun di-ACC admin (kosong kalau belum disetujui)
 */
return new class extends Migration
{
    /**
     * up() dijalankan saat `php artisan migrate`: menambahkan kolom.
     *
     * Default role = customer dan default status = pending, jadi siapa pun yang
     * mendaftar lewat form register otomatis jadi customer yang HARUS di-ACC admin.
     * Akun admin tidak dibuat lewat register, tapi lewat seeder (lihat DatabaseSeeder).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('email');
            $table->string('status')->default('pending')->after('role');
            $table->timestamp('approved_at')->nullable()->after('status');
        });
    }

    /**
     * down() dijalankan saat `php artisan migrate:rollback`: menghapus kolom tadi.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'approved_at']);
        });
    }
};
