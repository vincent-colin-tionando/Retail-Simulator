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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete()->comment('Jika order dihapus, item-itemnya ikut terhapus');
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete()->comment('Produk tidak bisa dihapus jika masih ada di riwayat order');
            $table->string('product_name')->comment('Snapshot nama produk saat order — antisipasi jika nama produk berubah');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2)->comment('Harga jual per unit saat order dibuat — snapshot dari product_prices');
            $table->decimal('subtotal', 15, 2)->comment('quantity * unit_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
