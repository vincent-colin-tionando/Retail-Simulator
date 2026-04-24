<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Membuat data produk palsu untuk testing.
 *
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected static array $productNames = [
        'Hannochs LED Bulb 5W', 'Hannochs LED Bulb 7W', 'Hannochs LED Bulb 9W',
        'Hannochs LED Tube T8 18W', 'Hannochs LED Tube T8 24W',
        'Hannochs Downlight 7W', 'Hannochs Downlight 12W',
        'Hannochs Emergency 10W', 'Hannochs Emergency 20W',
        'Hannochs Stop Kontak 4 Lubang', 'Hannochs Kabel Roll 5m',
        'Hannochs MCB 1P 10A', 'Hannochs MCB 1P 16A', 'Hannochs MCB 2P 20A',
        'Hannochs Street Light 30W', 'Hannochs Garden Light 12W',
        'Hannochs Smart Bulb WiFi 9W', 'Hannochs Panel LED 40W',
    ];

    protected static array $units = ['pcs', 'pcs', 'pcs', 'unit', 'set'];

    // STATE DEFAULT
    public function definition(): array
    {
        $name      = fake()->unique()->randomElement(static::$productNames);
        $watt      = fake()->randomElement(['5W', '7W', '9W', '12W', '15W', '18W', '24W']);
        $skuSuffix = strtoupper(fake()->bothify('###-??'));

        return [
            'category_id' => Category::factory(), // auto-create jika tidak ada
            'name'        => $name . ' ' . $watt,
            'sku'         => 'HNC-' . $skuSuffix,
            'unit'        => fake()->randomElement(static::$units),
            'stock'       => fake()->numberBetween(20, 500),
            'stock_min'   => 10,
            'image'       => null,
            'description' => fake()->sentence(12),
            'is_active'   => true,
        ];
    }

    // STATE: NONAKTIF
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    // STATE: STOK MENIPIS (di bawah stock_min)
    /**
     * Produk dengan stok di bawah atau sama dengan batas minimum.
     * Berguna untuk test tampilan badge "Stok Menipis".
     */
    public function lowStock(): static
    {
        return $this->state([
            'stock'     => fake()->numberBetween(1, 10),
            'stock_min' => 15,
        ]);
    }

    // STATE: STOK HABIS
    public function outOfStock(): static
    {
        return $this->state(['stock' => 0]);
    }

    // STATE: DENGAN HARGA (consumer + distributor)
    /**
     * Buat produk sekaligus dengan dua baris harga.
     * Berguna agar tidak perlu attach ProductPriceFactory secara terpisah.
     *
     */
    public function withPrices(): static
    {
        return $this->afterCreating(function (Product $product) {
            $consumerPrice     = fake()->numberBetween(10000, 150000);
            $distributorPrice  = (int) ($consumerPrice * fake()->randomFloat(2, 0.65, 0.80));

            $product->prices()->createMany([
                ['role_type' => 'consumer',    'price' => $consumerPrice],
                ['role_type' => 'distributor', 'price' => $distributorPrice],
            ]);
        });
    }
}
