<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model Supplier (Pemasok / Distributor)
 * @property int         $id
 * @property string      $name             
 * @property string|null $contact_person   
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property string|null $notes         
 * @property bool        $is_active
 */

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    // MASS-ASSIGNMENT GUARD
    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // RELASI ELOQUENT
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    // QUERY SCOPE
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Cari supplier berdasarkan nama atau email.
    public function scopeSearch($query, ?string $keyword)
    {
        if (! $keyword) return $query;

        return $query->where(function ($q) use ($keyword) {
            $q->where('name',  'like', "%{$keyword}%")
              ->orWhere('email', 'like', "%{$keyword}%")
              ->orWhere('phone', 'like', "%{$keyword}%");
        });
    }
}
