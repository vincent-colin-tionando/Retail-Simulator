<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Menangani login dan logout pengguna.
 *
 * Perbedaan dari versi default Laravel Breeze:
 * - Setelah login berhasil, arahkan ke halaman yang berbeda
 *   berdasarkan role pengguna:
 *
 *   admin        → /admin/dashboard (panel admin)
 *   consumer     → consumer.dashboard
 *   distributor  → distributor.dashboard
 *
 * - Jika ada "intended URL" (user coba akses halaman tertentu sebelum
 *   login), Laravel akan redirect ke sana terlebih dahulu.
 */

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Arahkan berdasarkan role
        // intended() → kembali ke URL yang dicoba sebelum diarahkan ke login
        // Jika tidak ada intended URL, gunakan default sesuai role.
        $user = Auth::user();

        $default = match ($user->role) {
            'admin' => route('admin.dashboard'),
            'consumer' => route('consumer.dashboard'),
            'distributor' => route('distributor.dashboard'),
            default  => route('shop.index'),  
        };

        return redirect()->intended($default);
    }

    /**
     * Destroy an authenticated session and logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Setelah logout, arahkan ke halaman toko (bukan halaman login)
        // agar pengguna tetap bisa melihat produk sebagai tamu
        return redirect()->route('shop.index');
    }
}
