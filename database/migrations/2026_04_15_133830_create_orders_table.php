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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete()
                  ->comment('Consumer atau distributor yang membuat pesanan');
            $table->string('order_code')->unique()
                  ->comment('Kode unik pesanan untuk ditampilkan ke pembeli, contoh: ORD-20240101-0001');
            $table->enum('status', [
                      'pending',      // Baru masuk, belum diproses admin
                      'processing',   // Admin sedang menyiapkan
                      'completed',    // Selesai, barang sudah diterima
                      'cancelled',    // Dibatalkan (stok dikembalikan)
                  ])->default('pending');
            $table->enum('buyer_role', ['consumer', 'distributor'])
                  ->comment('Snapshot role pembeli saat order dibuat — untuk keperluan laporan');
            $table->decimal('total_price', 15, 2)
                  ->comment('Total harga pesanan — dihitung dari sum order_items');
            $table->text('shipping_address')->nullable()
                  ->comment('Alamat pengiriman, jika berbeda dari alamat akun');
            $table->string('payment_method')->nullable()
                  ->comment('Transfer, tunai, COD, QRIS, dll.');
            $table->text('notes')->nullable()
                  ->comment('Catatan dari pembeli');
            $table->text('admin_notes')->nullable()
                  ->comment('Catatan internal admin');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('processed_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
