<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Kategori: mewakili satu baris di tabel `kategori_pengaduan`.
 *
 * Nama tabel tidak mengikuti tebakan Laravel (yang akan mencari tabel "kategoris"),
 * jadi kita tulis sendiri lewat $table.
 */
class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori_pengaduan';

    // Kolom yang boleh diisi massal lewat Kategori::create([...]).
    protected $fillable = ['nama', 'deskripsi'];

    /**
     * Relasi: satu kategori punya BANYAK pengaduan (1 : N).
     * Dipakai untuk: $kategori->pengaduan (daftar) atau withCount('pengaduan') (jumlah).
     */
    public function pengaduan(): HasMany
    {
        return $this->hasMany(Pengaduan::class, 'kategori_id');
    }
}
