<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Membuat data item pembelian palsu untuk testing.
 *
 * @extends Factory<PurchaseItem>
 */
class PurchaseItemFactory extends Factory
{
    // STATE DEFAULT
    public function definition(): array
    {
        // Harga beli HPP antara Rp 3.000 – Rp 100.000
        $unitCost = round(fake()->numberBetween(3000, 100000) / 500) * 500;
        $quantity = fake()->numberBetween(10, 200);

        return [
            'purchase_id' => Purchase::factory(),
            'product_id'  => Product::factory()->withPrices(),
            'quantity'    => $quantity,
            'unit_cost'   => $unitCost,
            'subtotal'    => $quantity * $unitCost,  // pre-computed, bukan virtual
        ];
    }

    // STATE: KUANTITAS KECIL (pengujian stok menipis)
    public function smallQty(): static
    {
        return $this->state(function (array $attrs) {
            $qty = fake()->numberBetween(1, 5);
            return [
                'quantity' => $qty,
                'subtotal' => $qty * $attrs['unit_cost'],
            ];
        });
    }
}
