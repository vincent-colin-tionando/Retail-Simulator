<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Aturan otorisasi manajemen User oleh admin.
 *
 * Aturan ini lebih granular dari sekedar "cek role = admin" dan
 * merupakan salah satu contoh mengapa Policy lebih baik dari middleware saja.
 */
class UserManagementPolicy
{
    use HandlesAuthorization;

    /**
     * Admin bypass — tapi dengan pengecualian khusus (lihat method di bawah).
     */

    /** Boleh melihat daftar semua user? Hanya admin. */
    public function viewAny(User $actor): bool
    {
        return $actor->role === 'admin';
    }

    /**
     * Boleh melihat profil user lain?
     *
     * Admin bisa melihat profil siapapun.
     * User biasa tidak bisa akses panel admin.
     */
    public function view(User $actor, User $target): bool
    {
        return $actor->role === 'admin';
    }

    /** Boleh membuat user baru? Hanya admin. */
    public function create(User $actor): bool
    {
        return $actor->role === 'admin';
    }

    /**
     * Boleh mengubah data user lain?
     *
     * Admin boleh edit semua user.
     * TAPI admin tidak boleh mengubah role-nya sendiri ke non-admin
     * (dilakukan di controller sebagai business logic check).
     */
    public function update(User $actor, User $target): bool
    {
        return $actor->role === 'admin';
    }

    /**
     * Boleh menghapus user?
     *
     * ATURAN PENTING: Admin tidak boleh menghapus dirinya sendiri.
     * Ini mencegah skenario tidak ada admin tersisa di sistem.
     *
     * @param User $actor  Admin yang melakukan aksi
     * @param User $target User yang ingin dihapus
     */
    public function delete(User $actor, User $target): bool
    {
        // Bukan admin → tidak boleh
        if ($actor->role !== 'admin') {
            return false;
        }

        // Admin tidak boleh hapus dirinya sendiri
        if ($actor->id === $target->id) {
            return false;
        }

        return true;
    }

    /**
     * Boleh toggle verifikasi distributor?
     *
     * Hanya admin, dan hanya untuk user yang role-nya distributor.
     * Non-distributor tidak ada artinya di-verify/unverify.
     */
    public function toggleVerify(User $actor, User $target): bool
    {
        if ($actor->role !== 'admin') {
            return false;
        }

        // Tidak masuk akal memverifikasi non-distributor
        return $target->role === 'distributor';
    }
}
