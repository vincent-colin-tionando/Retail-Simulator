<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Aturan otorisasi resource Category.
 *
 * Kategori di-manage admin. Consumer/distributor bisa melihat kategori
 * di storefront tapi tidak bisa mengubahnya.
 *
 */
class CategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Admin bypass semua pemeriksaan.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        return null;
    }

    /**
     * Siapapun boleh melihat daftar kategori di storefront (non-admin), tapi tidak di panel admin.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Boleh melihat detail satu kategori?
     * Untuk panel admin: hanya admin.
     */
    public function view(User $user, Category $category): bool
    {
        return false; // non-admin tidak perlu akses panel admin
    }

    /** Boleh membuat kategori baru? Hanya admin. */
    public function create(User $user): bool
    {
        return false;
    }

    /** Boleh mengubah kategori? Hanya admin. */
    public function update(User $user, Category $category): bool
    {
        return false;
    }

    /**
     * Boleh menghapus kategori?
     *
     * Catatan: cek "ada produk di kategori ini" tetap di controller.
     */
    public function delete(User $user, Category $category): bool
    {
        return false;
    }
}
