<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Aturan otorisasi resource Supplier.
 *
 */
class SupplierPolicy
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

    /** Boleh melihat daftar supplier? */
    public function viewAny(User $user): bool
    {
        return false; // hanya admin via before()
    }

    /** Boleh melihat detail satu supplier? */
    public function view(User $user, Supplier $supplier): bool
    {
        return false;
    }

    /** Boleh membuat supplier baru? */
    public function create(User $user): bool
    {
        return false;
    }

    /** Boleh mengubah data supplier? */
    public function update(User $user, Supplier $supplier): bool
    {
        return false;
    }

    /**
     * Boleh menghapus supplier?
     *
     * Cek "masih punya purchase" tetap dilakukan di controller.
     * Di sini hanya cek hak akses (siapa), bukan kelayakan bisnis (apakah).
     */
    public function delete(User $user, Supplier $supplier): bool
    {
        return false;
    }
}
