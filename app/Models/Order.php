<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
 
/**
 * Model Order (Pesanan Penjualan)
 *
 * Merepresentasikan satu transaksi penjualan dari consumer atau distributor.
 * Setiap Order berisi satu atau lebih OrderItem (detail produk yang dipesan).
 *
 * @property int         $id
 * @property int         $user_id         Pembeli
 * @property string      $order_code      Kode unik, contoh: ORD-20240101-0001
 * @property string      $status          pending|processing|completed|cancelled
 * @property string      $buyer_role      Snapshot role saat order dibuat
 * @property float       $total_price     Sum subtotal order_items
 * @property string|null $shipping_address
 * @property string|null $payment_method
 * @property string|null $notes           Catatan dari pembeli
 * @property string|null $admin_notes     Catatan internal admin
 * @property int|null    $processed_by    Admin yang memproses
 * @property \Carbon\Carbon|null $processed_at
 * @property \Carbon\Carbon|null $completed_at
 * @property \Carbon\Carbon|null $cancelled_at
 */

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_code',
        'status',
        'buyer_role',
        'total_price',
        'shipping_address',
        'payment_method',
        'notes',
        'admin_notes',
        'processed_at',
        'completed_at',
        'cancelled_at',
        'processed_by',
    ];
 
    protected $casts = [
        'total_price'  => 'float',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // RELASI ELOQUENT

    /** Pembeli (consumer / distributor). */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
 
    /** Admin yang memproses order ini. */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
 
    /** Daftar produk dalam order ini. */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
 
    // QUERY SCOPE

    /**
     * scopeStatus — Filter berdasarkan status pesanan.
     *
     * Jika $status null/kosong, scope tidak melakukan apa-apa (passthrough).
     */
    public function scopeStatus($query, ?string $status)
    {
        if (! $status) return $query;
        return $query->where('status', $status);
    }

     /**
     * scopeForUser — Filter pesanan berdasarkan pemilik secara otomatis.
     *
     * Logika:
     * ────────
     * - Jika user adalah admin -> tidak ada filter, semua order dikembalikan.
     *   Admin perlu melihat semua pesanan untuk mengelolanya.
     *
     * - Jika user bukan admin (consumer / distributor) -> filter by user_id.
     *   Pembeli hanya boleh melihat pesanannya sendiri.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        // Admin melihat semua pesanan — tidak perlu filter user_id
        if ($user->role === 'admin') {
            return $query;
        }

        // Consumer & distributor hanya melihat pesanan miliknya sendiri
        return $query->where('user_id', $user->id);
    }
 
    // HELPER / ACCESSOR
     public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'completed'  => 'bg-success',
            'processing' => 'bg-primary',
            'pending'    => 'bg-warning text-dark',
            'cancelled'  => 'bg-danger',
            default      => 'bg-secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed'  => 'Selesai',
            'processing' => 'Diproses',
            'pending'    => 'Menunggu',
            'cancelled'  => 'Dibatalkan',
            default      => ucfirst($this->status),
        };
    }
}
