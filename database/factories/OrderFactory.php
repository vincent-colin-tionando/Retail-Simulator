<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Membuat data pesanan palsu untuk testing.
 *
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    // STATE DEFAULT — consumer, pending
    public function definition(): array
    {
        // Format kode: ORD-YYYYMMDD-NNNN
        $date    = now()->format('Ymd');
        $seq     = fake()->numerify('####');
        $code    = "ORD-{$date}-{$seq}";

        $methods = ['Transfer Bank BCA', 'Transfer Bank Mandiri', 'COD', 'QRIS', 'Tunai'];

        return [
            'user_id'          => User::factory()->consumer(),
            'order_code'       => $code,
            'status'           => 'pending',
            'buyer_role'       => 'consumer',
            'total_price'      => 0,  // dihitung setelah item disimpan
            'shipping_address' => fake()->streetAddress() . ', ' . fake()->city(),
            'payment_method'   => fake()->randomElement($methods),
            'notes'            => fake()->optional(0.4)->sentence(),
            'admin_notes'      => null,
            'processed_at'     => null,
            'completed_at'     => null,
            'cancelled_at'     => null,
            'processed_by'     => null,
        ];
    }

    // STATE: PENDING (state default, ditulis eksplisit)
    public function pending(): static
    {
        return $this->state([
            'status'       => 'pending',
            'processed_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
        ]);
    }

    // STATE: PROCESSING
    public function processing(): static
    {
        return $this->state([
            'status'       => 'processing',
            'processed_at' => now()->subHours(2),
            'processed_by' => User::factory()->admin(),
        ]);
    }

    // STATE: COMPLETED
    public function completed(): static
    {
        return $this->state([
            'status'       => 'completed',
            'processed_at' => now()->subDays(3),
            'completed_at' => now()->subDays(1),
            'processed_by' => User::factory()->admin(),
        ]);
    }

    // STATE: CANCELLED
    public function cancelled(): static
    {
        return $this->state([
            'status'       => 'cancelled',
            'cancelled_at' => now()->subDays(1),
            'admin_notes'  => 'Dibatalkan karena stok tidak tersedia.',
        ]);
    }

    // STATE: ORDER DARI DISTRIBUTOR
    public function forDistributor(): static
    {
        return $this->state([
            'user_id'    => User::factory()->distributor(),
            'buyer_role' => 'distributor',
        ]);
    }

    // STATE: DENGAN TOTAL REALISTIS
    public function withTotal(): static
    {
        return $this->state([
            'total_price' => fake()->numberBetween(50000, 5000000),
        ]);
    }
}
