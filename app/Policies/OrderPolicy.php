<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Aturan otorisasi akses ke resource Order.
 *
 * Cara kerja Laravel Policy:
 *   1. Policy ini didaftarkan di AppServiceProvider via Gate::policy()
 *   2. Di controller, panggil: $this->authorize('view', $order)
 *   3. Laravel otomatis memanggil method yang sesuai (view(), update(), dll.)
 *   4. Jika method return false → Laravel lempar 403 Forbidden
 *
 * Kenapa Policy lebih baik dari abort_if() langsung di controller?
 *   - Satu tempat aturan otorisasi (Single Responsibility)
 *   - Reusable di controller, Blade (@can), dan test
 *   - Lebih mudah di-audit: "siapa yang boleh akses apa" jelas di satu file
 *   - Mudah diextend: cukup tambah method baru di Policy ini
 *
 * Hierarki akses:
 *   Admin bisa lihat SEMUA order, ubah status, beri catatan
 *   Consumer hanya bisa lihat order MILIKNYA sendiri
 *   Distributor hanya bisa lihat order MILIKNYA sendiri
 */

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * before() dipanggil sebelum semua method policy lain.
     * Admin selalu diizinkan — tidak perlu cek lebih lanjut.
     * Non-admin → lanjut ke method spesifik (return null).
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        return null;
    }

    /**
     * Boleh melihat daftar order?
     * Semua user boleh; filtering dilakukan oleh scope/where di controller.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    // Apakah user boleh melihat detail satu pesanan?
    /**
    * @param  User   $user   User yang sedang login
    * @param  Order  $order  Order yang ingin dilihat
    */
    public function view(User $user, Order $order): bool
    {
        // Consumer / distributor hanya boleh lihat order miliknya sendiri
        // Admin sudah di-bypass oleh method before()
        return $user->id === $order->user_id;
    }    
    
    // Apakah user boleh mengubah status pesanan?
    /**
    * Hanya admin yang boleh (sudah di-bypass oleh before()).
    * Consumer / distributor tidak bisa mengubah status.
    *
    * @param  User   $user
    * @param  Order  $order
    */
    public function updateStatus(User $user, Order $order): bool
    {
        // Consumer/distributor tidak boleh ubah status
        // Admin sudah di-bypass oleh before() tidak sampai sini
        return false;
    }

    // Apakah user boleh melihat halaman sukses checkout?
    public function viewSuccess(User $user, Order $order): bool
    {
        // Hanya pemilik order yang boleh akses.
        return $order->user_id === $user->id;
    }

    // Boleh membatalkan order?
    public function cancel(User $user, Order $order): bool
    {
        // Hanya admin (sudah di-handle before()).
        return false;
    }
}