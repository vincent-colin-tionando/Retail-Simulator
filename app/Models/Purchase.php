<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int         $id
 * @property int         $supplier_id
 * @property int         $user_id        
 * @property string      $invoice_no     
 * @property float       $total_cost    
 * @property string      $status         
 * @property string|null $notes
 * @property \Carbon\Carbon $purchased_at 
 */
class Purchase extends Model
{
    use HasFactory;

    // MASS-ASSIGNMENT GUARD
    protected $fillable = [
        'supplier_id',
        'user_id',
        'invoice_no',
        'total_cost',
        'status',
        'notes',
        'purchased_at',
    ];

    protected $casts = [
        'total_cost'   => 'float',
        'purchased_at' => 'date',   
    ];

    // RELASI ELOQUENT
    // Purchase ini berasal dari supplier mana.
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // Admin (user) yang mencatat transaksi pembelian ini.     
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Daftar item/produk yang ada di dalam faktur ini.    
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    // QUERY SCOPE
    // Filter berdasarkan status.
    public function scopeStatus($query, ?string $status)
    {
        if (! $status) return $query;
        return $query->where('status', $status);
    }

    // HELPER / ACCESSOR
    // Kembalikan label badge Bootstrap berdasarkan status.
    // Dipakai di Blade tanpa logic kondisional yang bertele-tele.
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'received'  => 'bg-success',
            'pending'   => 'bg-warning text-dark',
            'cancelled' => 'bg-danger',
            default     => 'bg-secondary',
        };
    }

    // Label status dalam Bahasa Indonesia - tampilan UI.
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'received'  => 'Diterima',
            'pending'   => 'Menunggu',
            'cancelled' => 'Dibatalkan',
            default     => ucfirst($this->status),
        };
    }
}

