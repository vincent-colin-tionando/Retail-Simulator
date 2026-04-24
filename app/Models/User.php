<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Satu model untuk tiga jenis pengguna:
 *   - admin       : mengelola produk, supplier, purchase, dan order
 *   - consumer    : pembeli eceran, mendapat harga consumer
 *   - distributor : pembeli grosir, mendapat harga distributor
 *                   (harus diverifikasi admin sebelum bisa berbelanja)
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string      $password
 * @property string      $role          admin | consumer | distributor
 * @property bool        $is_verified   khusus distributor
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $company_name  nama toko/usaha (distributor)
 */

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    // MASS-ASSIGNMENT GUARD
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_verified',
        'phone',
        'address',
        'company_name',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password'    => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    // RELASI ELOQUENT
    
    /**
     * Order yang dibuat oleh user ini (consumer / distributor).
     * Admin menggunakan relasi processedOrders untuk pesanan yang ia tangani.
     */
    public function orders():HasMany
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Purchase (pembelian stok) yang dicatat oleh admin ini.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Order yang diproses oleh admin ini (field processed_by).
     */
    public function processedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'processed_by');
    }

    // QUERY SCOPE
 
    /** Hanya user dengan role tertentu. Contoh: User::role('admin')->get() */
    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }
 
    /** Distributor yang sudah diverifikasi. */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
 
    /** Cari berdasarkan nama atau email. */
    public function scopeSearch($query, ?string $keyword)
    {
        if (! $keyword) return $query;
        return $query->where(function ($q) use ($keyword) {
            $q->where('name',  'like', "%{$keyword}%")
              ->orWhere('email', 'like', "%{$keyword}%")
              ->orWhere('phone', 'like', "%{$keyword}%");
        });
    }
 
    // HELPER / ACCESSOR
 
    /** Apakah user ini seorang admin? */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
 
    /** Apakah user ini seorang distributor? */
    public function isDistributor(): bool
    {
        return $this->role === 'distributor';
    }
 
    /** Apakah user ini seorang consumer? */
    public function isConsumer(): bool
    {
        return $this->role === 'consumer';
    }
 
    /**
     * Apakah distributor ini sudah bisa berbelanja dengan harga grosir?
     * Consumer → ya. Admin → ya (sebagai referensi).
     * Distributor → hanya jika sudah diverifikasi.
     */
    public function canShop(): bool
    {
        if ($this->role !== 'distributor') return true;
        return $this->is_verified;
    }

    /**
     * Role harga efektif untuk storefront.
     * Distributor yang belum verifikasi mendapat harga consumer.
     */
    public function effectivePriceRole(): string
    {
        if ($this->role === 'distributor' && $this->is_verified) {
            return 'distributor';
        }
        return 'consumer';
    }
    
    /**
     * Label badge Bootstrap untuk role.
     * Dipakai di Blade: <span class="badge {{ $user->roleBadgeClass }}">...</span>
     */
    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            'admin'       => 'bg-danger',
            'distributor' => 'bg-primary',
            'consumer'    => 'bg-success',
            default       => 'bg-secondary',
        };
    }
 
    /** Label role dalam Bahasa Indonesia. */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin'       => 'Admin',
            'distributor' => 'Distributor',
            'consumer'    => 'Consumer',
            default       => ucfirst($this->role),
        };
    }
}
