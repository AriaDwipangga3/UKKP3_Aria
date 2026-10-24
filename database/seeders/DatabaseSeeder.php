<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder: mengisi data awal ke database (`php artisan db:seed`).
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Membuat 1 akun admin awal (admin tidak bisa daftar lewat form register).
     *
     * Email & password diambil dari file .env (ADMIN_EMAIL, ADMIN_PASSWORD).
     * Kalau tidak diisi, dipakai nilai bawaan di bawah -- GANTI setelah login pertama!
     * firstOrNew() = aman dijalankan berkali-kali (admin dengan email yang sama tidak dibuat dobel).
     */
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => env('ADMIN_EMAIL', 'admin@example.com')]);

        $admin->fill([
            'name'     => 'Administrator',
            'password' => env('ADMIN_PASSWORD', 'admin12345'),
            'role'     => User::ROLE_ADMIN,
        ])->setStatus(User::STATUS_APPROVED)->save();

        // Kategori pengaduan bawaan supaya form customer tidak kosong saat pertama dicoba.
        // firstOrCreate = buat kalau belum ada, lewati kalau sudah ada (aman dijalankan berulang).
        // Admin bebas menambah / mengubah / menghapusnya lewat menu "Kategori Pengaduan".
        foreach (['Pelayanan', 'Kebersihan', 'Menu & Rasa', 'Fasilitas', 'Lainnya'] as $nama) {
            Kategori::firstOrCreate(['nama' => $nama]);
        }
    }
}
