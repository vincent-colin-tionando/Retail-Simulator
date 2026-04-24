<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Membuat data item pesanan palsu untuk testing.
 *
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    // STATE DEFAULT
    public function definition(): array
    {
        $unitPrice = round(fake()->numberBetween(10000, 200000) / 500) * 500;
        $quantity  = fake()->numberBetween(1, 20);

        // Nama produk di-snapshot karena di model ini product_name wajib ada
        $product = Product::factory()->withPrices()->make();

        return [
            'order_id'     => Order::factory(),
            'product_id'   => Product::factory()->withPrices(),
            'product_name' => $product->name,  // snapshot nama
            'quantity'     => $quantity,
            'unit_price'   => $unitPrice,
            'subtotal'     => $quantity * $unitPrice,
        ];
    }
}
