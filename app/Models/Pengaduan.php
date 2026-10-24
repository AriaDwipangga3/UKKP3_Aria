<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Pengaduan: mewakili satu baris di tabel `pengaduan`
 * (satu keluhan / saran dari customer).
 *
 * Alur status: baru -> diproses -> selesai
 *  - baru     : baru dikirim customer
 *  - diproses : admin sudah membuka detailnya (otomatis) / sedang ditangani
 *  - selesai  : admin sudah menanggapi dan menutup pengaduan
 */
class Pengaduan extends Model
{
    use HasFactory;

    protected $table = 'pengaduan';

    // Konstanta supaya tidak salah ketik 'diproses' / 'diproses ' di banyak tempat.
    public const JENIS_KELUHAN = 'keluhan';
    public const JENIS_SARAN = 'saran';

    public const STATUS_BARU = 'baru';
    public const STATUS_DIPROSES = 'diproses';
    public const STATUS_SELESAI = 'selesai';

    // Daftar lengkap, dipakai untuk validasi (Rule::in) dan tab filter.
    public const DAFTAR_JENIS = [self::JENIS_KELUHAN, self::JENIS_SARAN];
    public const DAFTAR_STATUS = [self::STATUS_BARU, self::STATUS_DIPROSES, self::STATUS_SELESAI];

    protected $fillable = [
        'user_id',
        'kategori_id',
        'jenis',
        'judul',
        'isi',
        'foto',
        'status',
        'tanggapan',
    ];

    /** Relasi: pengaduan ini DIBUAT OLEH satu user (customer). */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Relasi: pengaduan ini MEMILIKI satu kategori. */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Alamat (URL) foto agar bisa ditampilkan di <img>.
     * Foto disimpan di storage/app/public/pengaduan/..., dan dibuka lewat /storage/...
     * (butuh `php artisan storage:link` sekali saja). Kalau tidak ada foto -> null.
     */
    public function fotoUrl(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    /** Tulisan status untuk ditampilkan: 'baru' -> 'Baru'. */
    public function statusLabel(): string
    {
        return ucfirst($this->status);
    }

    /** Nama class CSS untuk warna label status: pill-baru / pill-diproses / pill-selesai (lihat dashboard.css). */
    public function statusCss(): string
    {
        return 'pill-' . $this->status;
    }
}
