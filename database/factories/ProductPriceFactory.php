<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Membuat data harga produk palsu untuk testing.
 *
 * @extends Factory<ProductPrice>
 */
class ProductPriceFactory extends Factory
{
    // STATE DEFAULT — consumer
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'role_type'  => 'consumer',
            // Harga antara Rp 5.000 – Rp 500.000, dibulatkan ke 500
            'price'      => round(fake()->numberBetween(5000, 500000) / 500) * 500,
        ];
    }

    // STATE: CONSUMER PRICE
    public function consumer(): static
    {
        return $this->state([
            'role_type' => 'consumer',
            'price'     => round(fake()->numberBetween(10000, 200000) / 500) * 500,
        ]);
    }

    // STATE: DISTRIBUTOR PRICE (lebih murah ~25-35% dari consumer)
    public function distributor(): static
    {
        return $this->state(function (array $attrs) {
            // Harga distributor = 65–75% dari harga consumer
            $ratio = fake()->randomFloat(2, 0.65, 0.75);
            return [
                'role_type' => 'distributor',
                'price'     => round(($attrs['price'] ?? 50000) * $ratio / 500) * 500,
            ];
        });
    }
}
