<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

// Mengelola halaman profil pengguna.
// Dipakai oleh SEMUA role (admin, consumer, distributor) via route /profile.

class ProfileController extends Controller
{
    /**
     * EDIT - Tampilkan form profil
     */
    public function edit(Request $request): View
    {
        // Teruskan $user ke view agar form bisa diisi dengan data yang ada.
        // View profile/edit.blade.php memilih layout (admin/shop) berdasarkan role.
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * UPDATE - Simpan perubahan profil
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // fill() mengisi kolom yang ada di $fillable
        $request->user()->fill($request->validated());

        // Update field tambahan (tidak ada di ProfileUpdateRequest default Breeze)
        $request->user()->phone = $request->input('phone');
        $request->user()->address = $request->input('address');

        // company_name hanya relevan untuk distributor
        $request->user()->company_name = $request->user()->role === 'distributor'
            ? $request->input('company_name')
            : null;
        
        // Jika email diubah, reset verifikasi email (standar Laravel)
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * DESTROY - Hapus akun pengguna
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validasi password sebelum menghapus — error masuk ke named bag 'userDeletion'
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout(); // Logout sebelum delete agar sesi tidak menggantung
        $user->delete(); // SoftDeletes jika model menggunakannya

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Setelah hapus akun, arahkan ke toko (bukan login)
        return Redirect::route('shop.index');
    }
}
