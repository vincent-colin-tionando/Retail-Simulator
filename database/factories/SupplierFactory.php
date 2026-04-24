<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Membuat data supplier palsu untuk testing.
 *
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    // Pool nama perusahaan supplier elektronikal Indonesia
    protected static array $companyPrefixes = ['PT', 'CV', 'UD', 'PD'];
    protected static array $companyNames = [
        'Cahaya Elektrik Nusantara', 'Sumber Makmur Jaya', 'Terang Benderang Mandiri',
        'Surya Elektrika', 'Kilat Gemilang', 'Prima Cahaya Abadi',
        'Mitra Listrik Sejati', 'Bintang Elektro Persada', 'Anugerah Teknik',
        'Karya Mandiri Elektrik', 'Global Lighting Solutions', 'Indah Cahaya',
    ];

    // STATE DEFAULT
    public function definition(): array
    {
        $prefix = fake()->randomElement(static::$companyPrefixes);
        $name   = $prefix . ' ' . fake()->randomElement(static::$companyNames);
        $city   = fake()->randomElement([
            'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang',
            'Yogyakarta', 'Bekasi', 'Tangerang', 'Depok', 'Makassar',
        ]);

        return [
            'name'           => $name,
            'contact_person' => fake()->name(),
            'phone'          => fake()->randomElement(['021', '031', '022', '024']). '-' . fake()->numerify('######'),
            'email'          => strtolower(str_replace([' ', '.'], '-', $name)) . '@example.co.id',
            'address'        => 'Jl. ' . fake()->streetName(). ' No. ' . fake()->buildingNumber(). ', ' . $city,
            'notes'          => fake()->optional(0.6)->sentence(),
            'is_active'      => true,
        ];
    }

    // STATE: NONAKTIF
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
