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
        Schema::create('purchases_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')
                  ->constrained('purchases')
                  ->cascadeOnDelete()
                  ->comment('Jika purchase dihapus, item-itemnya ikut terhapus');
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->restrictOnDelete()
                  ->comment('Produk tidak bisa dihapus jika ada di riwayat pembelian');
            $table->unsignedInteger('quantity')
                  ->comment('Jumlah unit yang dibeli dari supplier');
            $table->decimal('unit_cost', 12, 2)
                  ->comment('Harga beli per unit dari supplier (HPP)');
            $table->decimal('subtotal', 15, 2)
                  ->comment('quantity * unit_cost — disimpan agar tidak perlu hitung ulang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases_items');
    }
};
