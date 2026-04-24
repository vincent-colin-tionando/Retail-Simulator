<?php

namespace App\Policies;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Aturan otorisasi resource Purchase (Pembelian Stok).
 *
 * Ini adalah keputusan desain yang tepat — pembelian yang sudah dicatat
 * tidak boleh diubah/dihapus untuk menjaga integritas data stok.
 */
class PurchasePolicy
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

    /** Boleh melihat daftar semua pembelian? */
    public function viewAny(User $user): bool
    {
        return false; // hanya admin
    }

    /** Boleh melihat detail satu pembelian? */
    public function view(User $user, Purchase $purchase): bool
    {
        return false;
    }

    /**
     * Boleh mencatat pembelian baru?
     *
     * RISIKO MANIPULASI STOK:
     * Jika non-admin bisa akses create, mereka bisa menaikkan stok
     * tanpa pembelian nyata. Policy ini mencegah hal tersebut.
     */
    public function create(User $user): bool
    {
        return false;
    }
}
