<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'role_type',   // 'consumer' | 'distributor'
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];
    // RELASI ELOQUENT
    // Relasi ke produk pemilik harga ini.
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    // SCOPE
    /**
     * Filter harga untuk role tertentu.
     *
     * Penggunaan:
     *   ProductPrice::forRole('consumer')->get()
     *   ProductPrice::forRole(auth()->user()->role)->first()
     */
    public function scopeForRole($query, string $role)
    {
        return $query->where('role_type', $role);
    }
    // HELPER
    /**
     * Format harga ke Rupiah.
     * Penggunaan di Blade: {{ $price->formatted }}
     */
    public function getFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
