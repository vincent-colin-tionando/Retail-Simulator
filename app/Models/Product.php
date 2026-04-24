<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'unit',
        'stock',
        'stock_min',
        'image',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stock'     => 'integer',
        'stock_min' => 'integer',
    ];

    // RELASI ELOQUENT
    /**
     * Produk ini masuk ke satu kategori.
     *
     * Dipakai untuk menampilkan nama kategori di tabel produk.
     * Contoh: $product->category->name
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Satu produk punya banyak baris harga (satu per role).
     *
     * Dipakai saat mengambil semua harga sekaligus, contoh di form edit.
     * Contoh: $product->prices  -> koleksi [consumer => ..., distributor => ...]
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    // Shortcut: harga khusus untuk consumer.
    public function consumerPrice(): HasOne
    {
        return $this->hasOne(ProductPrice::class)
                    ->where('role_type', 'consumer');
    }

    // Shortcut: harga khusus untuk distributor.
    public function distributorPrice(): HasOne
    {
        return $this->hasOne(ProductPrice::class)
                    ->where('role_type', 'distributor');
    }

    /**
     * Produk ini pernah menjadi item di purchase mana saja.
     *
     * Dipakai untuk mencegah hapus produk yang masih ada di riwayat pembelian.
     */
    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Produk ini pernah menjadi item di order mana saja.
     *
     * Dipakai untuk mencegah hapus produk yang masih ada di riwayat order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Item keranjang yang sedang menyimpan produk ini.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // QUERY SCOPE
    /** Hanya produk aktif. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Produk yang stoknya sudah menyentuh atau di bawah stock_min.
     * Dipakai di dashboard untuk peringatan stok menipis.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'stock_min');
    }

    /**
     * Cari produk berdasarkan nama atau SKU.
     * Contoh: Product::search('aqua')->get()
     */
    public function scopeSearch($query, ?string $keyword)
    {
        if (! $keyword) return $query;

        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('sku',  'like', "%{$keyword}%");
        });
    }

    // ACCESSOR / HELPER
    /**
     * Apakah stok produk ini sudah menipis?
     * Dipakai di view untuk menampilkan badge merah.
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->stock_min;
    }

    /**
     * Ambil harga yang sesuai dengan role pengguna yang sedang login.
     * Dipakai di storefront agar tidak perlu if-else di Blade.
     *
     * Contoh: $product->priceFor('consumer')  -> 5000
     *         $product->priceFor('distributor') -> 3800
     */
    public function priceFor(string $role): ?float
    {
        return $this->prices
            ->firstWhere('role_type', $role)
            ?->price;
    }

    /**
     * URL gambar produk. Jika tidak ada gambar, kembalikan placeholder.
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/product-placeholder.png');
    }
}