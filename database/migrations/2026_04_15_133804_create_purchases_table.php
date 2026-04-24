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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')
                  ->constrained('suppliers')
                  ->restrictOnDelete()
                  ->comment('Supplier tidak bisa dihapus jika masih ada riwayat pembelian');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete()
                  ->comment('Admin yang mencatat transaksi pembelian ini');
            $table->string('invoice_no')->unique()
                  ->comment('Nomor faktur/invoice dari supplier, contoh: INV/2024/001');
            $table->decimal('total_cost', 15, 2)
                  ->comment('Total biaya pembelian — dihitung dari sum purchase_items');
            $table->enum('status', ['pending', 'received', 'cancelled'])->default('received')
                  ->comment('received = stok sudah masuk gudang');
            $table->text('notes')->nullable();
            $table->date('purchased_at')
                  ->comment('Tanggal transaksi terjadi (bisa berbeda dari created_at)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
