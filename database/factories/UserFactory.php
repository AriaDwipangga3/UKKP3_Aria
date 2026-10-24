<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * UserFactory: pembuat data user palsu (dipakai untuk testing).
 * Contoh: User::factory()->admin()->create();  User::factory()->pending()->create();
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Password yang dipakai bersama (di-hash sekali saja agar test cepat).
     */
    protected static ?string $password;

    /**
     * Data bawaan: customer yang sudah di-ACC, password-nya "password".
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_APPROVED,
            'approved_at' => now(),
        ];
    }

    /** State: user berrole admin. */
    public function admin(): static
    {
        return $this->state(fn () => ['role' => User::ROLE_ADMIN]);
    }

    /** State: customer yang masih menunggu ACC. */
    public function pending(): static
    {
        return $this->state(fn () => ['status' => User::STATUS_PENDING, 'approved_at' => null]);
    }

    /** State: customer yang ditolak. */
    public function rejected(): static
    {
        return $this->state(fn () => ['status' => User::STATUS_REJECTED, 'approved_at' => null]);
    }
}
