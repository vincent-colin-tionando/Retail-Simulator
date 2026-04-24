<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Item Keranjang Belanja
 *
 * Merepresentasikan satu produk di dalam keranjang seorang pengguna.
 * Harga TIDAK disimpan di sini — selalu dibaca real-time dari product_prices
 * supaya otomatis mengikuti jika admin mengubah harga.
 *
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property int $quantity
 */
class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];
    // RELASI
    /** Pemilik keranjang. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Produk yang di-cart. withTrashed() agar item tetap tampil di cart
     * meski produk di-softdelete, sehingga user bisa menghapusnya sendiri.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    // HELPER
    /**
     * Hitung subtotal item ini berdasarkan harga role user saat ini.
     *
     * Harga dibaca real-time dari relasi product->prices yang sudah
     * di-eager-load.
     * 
     * Jika produk tidak punya harga untuk role ini,
     * kembalikan 0 (tidak crash).
     *
     * Contoh: $cartItem->subtotal('consumer')  → 45000
     */
    public function subtotal(string $role): float
    {
        $price = $this->product?->priceFor($role) ?? 0;
        return $price * $this->quantity;
    }
}
