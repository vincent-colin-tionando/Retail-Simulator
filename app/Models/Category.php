<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    // Cast tipe data otomatis
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    
    // RELASI ELOQUENT
    /**
     * Relasi ke kategori induk (self-join BelongsTo).
     *
     * Contoh: "Minuman Dingin" -> induknya "Minuman"
     * Dipakai untuk menampilkan nama induk di tabel dan form.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Relasi ke sub-kategori (self-join balik).
     * Satu kategori induk bisa punya banyak sub-kategori.
    */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /**
    * Relasi ke produk-produk yang masuk kategori ini.
    */
    public function products()
    {
       return $this->hasMany(Product::class);
    }

    
    // QUERY SCOPE — filter siap pakai yang bisa dirantai ke query builder
    /** Hanya kategori aktif. Contoh: Category::active()->get() */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Hanya kategori utama (tanpa induk). Contoh: Category::parentOnly()->get() */
    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_id');
    }

    /** Urutkan berdasarkan sort_order lalu nama. */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    
    // ACCESSOR / HELPER
    /**
     * Cek apakah kategori ini adalah sub-kategori.
     * Dipakai di view untuk menampilkan badge "Sub-kategori".
     */
    public function isChild(): bool
    {
        return ! is_null($this->parent_id);
    }

    /**
     * Nama lengkap dengan induk. Dipakai di dropdown pilih kategori produk.
     * Contoh: "Minuman > Minuman Dingin"
     */
    public function getFullNameAttribute(): string
    {
        return $this->parent
            ? $this->parent->name . ' › ' . $this->name
            : $this->name;
    }
}