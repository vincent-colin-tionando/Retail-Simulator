<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Membuat data kategori palsu untuk testing.
 *
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    // Pool nama kategori produk elektronik/kelistrikan
    // agar data test terasa realistis dan sesuai konteks proyek
    protected static array $categoryNames = [
        'Lampu LED', 'Lampu Emergency', 'Downlight & Spotlight',
        'Lampu Jalan', 'Stop Kontak', 'MCB & Panel', 'Smart Home',
        'LED Bulb', 'LED Tube', 'Outdoor Lighting', 'Aksesori Listrik',
        'Kabel Roll', 'Saklar & Outlet', 'Fitting Lampu', 'Driver LED',
    ];

    // STATE DEFAULT
    public function definition(): array
    {
        $name = fake()->unique()->randomElement(static::$categoryNames)
                . ' ' . fake()->randomLetter();  // tambah huruf agar unik

        return [
            'parent_id'   => null,              // kategori utama
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => fake()->sentence(),
            'is_active'   => true,
            'sort_order'  => fake()->numberBetween(1, 20),
        ];
    }

    // STATE: SUB-KATEGORI
    // Buat sebagai sub-kategori dari parent tertentu.
    public function child(int $parentId): static
    {
        return $this->state(['parent_id' => $parentId]);
    }

    // STATE: NONAKTIF
    // Buat kategori yang tidak aktif.
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
