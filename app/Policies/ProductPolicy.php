<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Aturan otorisasi akses ke resource Product.
 */
class ProductPolicy
{
    use HandlesAuthorization;

     
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'admin') {
            return true; // admin selalu boleh — tidak perlu cek method lain
        }

        return null; // non-admin → lanjut ke method spesifik di bawah
    }

    /**
     * Boleh melihat daftar semua produk di panel admin?
     * Hanya admin (sudah bypass di before()).
     */
    public function viewAny(User $user): bool
    {
        // Non-admin tidak boleh akses panel admin product list
        return false;
    }

    /**
     * Boleh melihat detail satu produk di panel admin?
     */
    public function view(User $user, Product $product): bool
    {
        return false; // non-admin tidak ada akses panel admin
    }

    /**
     * Boleh membuat produk baru?
     */
    public function create(User $user): bool
    {
        return false; // hanya admin (sudah bypass di before())
    }

    /**
     * Boleh mengubah data produk?
     */
    public function update(User $user, Product $product): bool
    {
        return false; // hanya admin
    }

    /**
     * Boleh menghapus produk?
     *
     * Cek tambahan (ada di riwayat order/purchase) tetap dilakukan
     * di controller karena itu business logic, bukan aturan otorisasi.
     */
    public function delete(User $user, Product $product): bool
    {
        return false; // hanya admin
    }

    /**
     * Boleh toggle aktif/nonaktif produk?
     * Route: PATCH /admin/products/{product}/toggle
     */
    public function toggle(User $user, Product $product): bool
    {
        return false; // hanya admin
    }
}
