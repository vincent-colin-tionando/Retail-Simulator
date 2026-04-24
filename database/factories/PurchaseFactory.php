<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Membuat data transaksi pembelian stok palsu untuk testing.
 *
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    // STATE DEFAULT — received
    public function definition(): array
    {
        // Nomor invoice format: INV/[KODE]/[TAHUN]/[NNN]
        $code    = strtoupper(fake()->bothify('???'));
        $year    = fake()->year();
        $seq     = fake()->numerify('###');
        $invoice = "INV/{$code}/{$year}/{$seq}";

        return [
            'supplier_id'  => Supplier::factory(),
            // Admin yang mencatat — ambil dari user admin yang ada, atau buat baru
            'user_id'      => User::factory()->admin(),
            'invoice_no'   => $invoice,
            'total_cost'   => 0,   // di-update setelah item disimpan
            'status'       => 'received',
            'notes'        => fake()->optional(0.5)->sentence(),
            'purchased_at' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
        ];
    }

    // STATE: PENDING
    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    // STATE: CANCELLED
    public function cancelled(): static
    {
        return $this->state(['status' => 'cancelled']);
    }

    // STATE: DENGAN TOTAL COST REALISTIS
    /**
     * Set total_cost ke angka realistis (tanpa membuat PurchaseItem).
     * Berguna saat test hanya butuh angka total, bukan detail item.
     */
    public function withCost(): static
    {
        return $this->state([
            // Total antara Rp 500.000 – Rp 50.000.000
            'total_cost' => fake()->numberBetween(500000, 50000000),
        ]);
    }
}
