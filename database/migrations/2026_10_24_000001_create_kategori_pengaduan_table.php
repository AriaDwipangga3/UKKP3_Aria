<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: membuat tabel `kategori_pengaduan`.
 *
 * Isinya daftar kategori yang bisa dipilih customer saat membuat pengaduan
 * (contoh: Pelayanan, Kebersihan, Menu & Rasa, Fasilitas).
 * Kategori dikelola admin lewat halaman CRUD Kategori.
 *
 * File ini HARUS berjalan sebelum migration `pengaduan`, karena tabel pengaduan
 * punya kolom kategori_id yang menunjuk ke tabel ini (itu sebabnya nama file-nya
 * bernomor lebih kecil: ...000001 dulu, baru ...000002).
 */
return new class extends Migration
{
    /** up() = dijalankan saat `php artisan migrate`. */
    public function up(): void
    {
        Schema::create('kategori_pengaduan', function (Blueprint $table) {
            $table->id();                           // kolom id (primary key, otomatis naik)
            $table->string('nama')->unique();       // nama kategori, tidak boleh kembar
            $table->text('deskripsi')->nullable();  // penjelasan singkat (boleh kosong)
            $table->timestamps();                   // created_at & updated_at
        });
    }

    /** down() = dijalankan saat `php artisan migrate:rollback`. */
    public function down(): void
    {
        Schema::dropIfExists('kategori_pengaduan');
    }
};
