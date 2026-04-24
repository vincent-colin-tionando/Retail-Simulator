<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * RegisteredUserController
 *
 * Menangani registrasi pengguna baru dari halaman publik.
 *
 * Dua pilihan role saat registrasi:
 *   - consumer    : langsung aktif, bisa langsung belanja
 *   - distributor : perlu verifikasi admin sebelum mendapat harga grosir
 *
 * Admin tidak bisa dibuat lewat form publik ini —
 * hanya bisa ditambahkan melalui panel admin (/admin/users/create).
 */

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    /** Proses form registrasi dan login otomatis setelah berhasil. */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'phone'        => ['required', 'string', 'max:30'],
            'address'      => ['required', 'string', 'max:500'],
            // company_name wajib diisi hanya jika memilih role distributor
            'company_name' => ['nullable', 'string', 'max:255',
                               'required_if:role,distributor'],
            'role'         => ['required', 'in:consumer,distributor'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'company_name.required_if' => 'Nama perusahaan wajib diisi untuk akun distributor.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'role'         => $request->role,// Distributor belum diverifikasi, consumer langsung aktif
            'is_verified' => false, 
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Arahkan ke dashboard sesuai role
        return match ($user->role) {
            'distributor' => redirect()->route('distributor.dashboard'),
            default       => redirect()->route('consumer.dashboard'),
        };
    }
}
