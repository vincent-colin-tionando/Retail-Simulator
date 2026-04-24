<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel cart_items menyimpan isi keranjang belanja per pengguna.
 *
 * Desain:
 * 1. Satu baris = satu produk milik satu user.
 * 2. Unique constraint (user_id, product_id) memastikan tidak ada duplikat:
 *   jika produk yang sama ditambahkan lagi, controller cukup increment quantity.
 * 3. CascadeOnDelete di user_id: keranjang otomatis bersih saat akun dihapus.
 * 4. RestrictOnDelete di product_id: produk tidak bisa dihapus selama ada di keranjang seseorang (admin perlu kosongkan dulu).
 * 5. Tidak ada harga di sini — harga selalu dibaca real-time dari product_prices
 *   agar tidak stale jika admin mengubah harga setelah barang di-cart.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()
                ->comment('Pemilik keranjang — hapus user → cart otomatis bersih');

            $table->foreignId('product_id')->constrained('products')->restrictOnDelete()
                ->comment('Produk tidak bisa dihapus jika masih ada di keranjang');

            $table->unsignedInteger('quantity')
                ->default(1)->comment('Jumlah unit yang ingin dibeli');

            // Satu user hanya boleh punya satu baris per produk.
            // Jika produk ditambah lagi, lakukan UPDATE quantity, bukan INSERT baru.
            $table->unique(['user_id', 'product_id'], 'unique_user_product_cart');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
