<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan pemanggilan PENTING karena ada foreign-key constraint:
     *
     *  1. UserSeeder          → tabel users (dibutuhkan purchases & orders)
     *  2. CategorySeeder      → tabel categories (dibutuhkan products)
     *  3. ProductSeeder       → tabel products + product_prices
     *  4. SupplierSeeder      → tabel suppliers (dibutuhkan purchases)
     *  5. PurchaseSeeder      → tabel purchases + purchases_items
     *  6. OrderSeeder         → tabel orders + order_items
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            SupplierSeeder::class,
            PurchaseSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
