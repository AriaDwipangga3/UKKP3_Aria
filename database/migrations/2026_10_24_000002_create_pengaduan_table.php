<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: membuat tabel `pengaduan` (sesuai ERD).
 *
 * Relasi:
 *  - user_id     -> users.id              (siapa yang mengadu)
 *                   cascadeOnDelete : kalau user dihapus, pengaduannya ikut terhapus
 *  - kategori_id -> kategori_pengaduan.id (kategori yang dipilih)
 *                   restrictOnDelete: kategori yang MASIH dipakai pengaduan tidak bisa dihapus
 *                   (database sendiri yang menolak -> data tidak mungkin "yatim")
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();

            // Foreign key: menghubungkan baris ini ke tabel lain.
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori_pengaduan')->restrictOnDelete();

            $table->enum('jenis', ['keluhan', 'saran']);                         // jenis pengaduan
            $table->string('judul');                                              // judul singkat
            $table->text('isi');                                                  // isi lengkap
            $table->string('foto')->nullable();                                   // path foto (opsional)
            $table->enum('status', ['baru', 'diproses', 'selesai'])->default('baru'); // awalnya selalu 'baru'
            $table->text('tanggapan')->nullable();                                // balasan admin (kosong dulu)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
