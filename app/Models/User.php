<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User: mewakili satu baris di tabel `users` (admin maupun customer).
 *
 * Selain data login (nama, email, password), tiap user punya:
 *  - role   -> siapa dia (admin / customer)
 *  - status -> sudah di-ACC atau belum (pending / approved / rejected)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Daftar nilai role. Dipakai konstanta supaya tidak salah ketik di banyak tempat.
    public const ROLE_ADMIN = 'admin';
    public const ROLE_CUSTOMER = 'customer';

    // Daftar nilai status akun.
    public const STATUS_PENDING = 'pending';   // baru daftar, menunggu ACC admin
    public const STATUS_APPROVED = 'approved'; // sudah di-ACC, boleh login
    public const STATUS_REJECTED = 'rejected'; // ditolak admin, tidak boleh login

    /**
     * Kolom yang boleh diisi massal lewat User::create([...]) / $user->update([...]).
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'approved_at',
    ];

    /**
     * Kolom yang disembunyikan saat model diubah jadi array/JSON (keamanan).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Aturan konversi tipe data kolom.
     * 'hashed' = password otomatis di-hash saat disimpan, jadi tidak perlu Hash::make() manual.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** True kalau user ini admin. */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /** True kalau user ini customer. */
    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    /** True kalau akun sudah di-ACC admin (boleh login). */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /** True kalau akun masih menunggu ACC admin. */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /** True kalau akun ditolak admin. */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Mengubah status akun sekaligus mengatur kolom approved_at.
     * - Kalau jadi 'approved'  -> approved_at diisi waktu sekarang (kalau belum ada).
     * - Kalau jadi status lain -> approved_at dikosongkan.
     * Method ini hanya mengubah di memori; panggil save() setelahnya.
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;
        $this->approved_at = $status === self::STATUS_APPROVED
            ? ($this->approved_at ?? now())
            : null;

        return $this;
    }

    /**
     * Query scope: User::pending()->count() = hitung akun yang menunggu ACC.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Relasi: satu user (customer) punya BANYAK pengaduan.
     * Dipakai di sisi customer: auth()->user()->pengaduan() = hanya pengaduan miliknya sendiri.
     */
    public function pengaduan(): HasMany
    {
        return $this->hasMany(Pengaduan::class);
    }
}
