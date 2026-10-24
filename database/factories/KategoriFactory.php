<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * KategoriFactory: pembuat kategori palsu untuk testing.
 *
 * @extends Factory<Kategori>
 */
class KategoriFactory extends Factory
{
    protected $model = Kategori::class;

    public function definition(): array
    {
        return [
            'nama'      => fake()->unique()->words(2, true),
            'deskripsi' => fake()->sentence(),
        ];
    }
}
