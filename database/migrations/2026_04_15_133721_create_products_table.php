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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('sku')->unique()->comment('Stock Keeping Unit — kode unik produk, contoh: MNM-001');
            $table->string('unit')->default('pcs')->comment('Satuan jual: pcs, kg, liter, lusin, karton, dll.');
            $table->unsignedInteger('stock')->default(0)->comment('Stok saat ini — diupdate otomatis saat purchase/order');
            $table->unsignedInteger('stock_min')->default(5)->comment('Batas minimum stok untuk peringatan stok menipis');
            $table->string('image')->nullable()->comment('Path file gambar produk');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->comment('False = produk disembunyikan dari storefront');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
