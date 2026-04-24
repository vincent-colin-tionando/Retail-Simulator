<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete()
                  ->comment('Jika produk dihapus, harganya ikut terhapus');
            $table->enum('role_type', ['consumer', 'distributor'])
                  ->comment('Harga berlaku untuk role mana');
            $table->decimal('price', 12, 2)
                  ->comment('Harga jual dalam Rupiah');

            $table->timestamps();

            $table->unique(['product_id', 'role_type'],
                           'unique_product_role_price')
                  ->comment('Satu produk hanya boleh punya satu harga per role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
