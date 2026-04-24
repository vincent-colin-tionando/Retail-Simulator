<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 *  Model PurchaseItem (Item Detail Pembelian)
 *
 * @property int   $id
 * @property int   $purchase_id
 * @property int   $product_id
 * @property int   $quantity    
 * @property float $unit_cost   HPP per unit
 * @property float $subtotal    quantity × unit_cost
 */
class PurchaseItem extends Model
{
    use HasFactory;

    // Nama tabel mengikuti konvensi migrasi yang sudah ada.
    protected $table = 'purchases_items'; 

    // MASS-ASSIGNMENT GUARD
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unit_cost',
        'subtotal',
    ];

    protected $casts = [
        'quantity'  => 'integer',
        'unit_cost' => 'float',
        'subtotal'  => 'float',
    ];
    // RELASI ELOQUENT
    // Item ini milik Purchase mana.
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    // Produk apa yang dibeli pada item ini.
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
