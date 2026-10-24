<?php

namespace Database\Factories;

use App\Models\Kategori;
use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * PengaduanFactory: pembuat pengaduan palsu untuk testing.
 * Contoh: Pengaduan::factory()->create(['user_id' => $customer->id]);
 *
 * @extends Factory<Pengaduan>
 */
class PengaduanFactory extends Factory
{
    protected $model = Pengaduan::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),     // kalau tidak ditentukan, buat customer baru
            'kategori_id' => Kategori::factory(), // kalau tidak ditentukan, buat kategori baru
            'jenis'       => fake()->randomElement(Pengaduan::DAFTAR_JENIS),
            'judul'       => fake()->sentence(4),
            'isi'         => fake()->paragraph(),
            'foto'        => null,
            'status'      => Pengaduan::STATUS_BARU,
            'tanggapan'   => null,
        ];
    }
}
