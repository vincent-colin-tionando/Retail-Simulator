<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Manajemen pengguna oleh admin:
 *   - Lihat daftar semua user (admin / consumer / distributor)
 *   - Detail user + riwayat order
 *   - Tambah user baru (termasuk admin lain)
 *   - Edit data user
 *   - Verifikasi / cabut verifikasi distributor
 *   - Soft-delete user
 *
 */

class UserController extends Controller
{
    // INDEX - Daftar Semua User    
    public function index(Request $request):View
    {
        $users = User::query()
            //Hitung jumlah order per user untuk tampilan tabel
            ->withCount('orders')
            ->search($request->input('search'))
            // Filter berdasarkan role
            ->when($request->input('role'), fn ($q, $role) =>
                $q->where('role', $role)
            )
            // Filter distributor yang belum diverifikasi
            ->when($request->input('unverified'), fn ($q) =>
                $q->where('role', 'distributor')->where('is_verified', false)
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Hitung badge notifikasi distributor pending verifikasi
        $pendingDistributors = User::where('role', 'distributor')
            ->where('is_verified', false)
            ->count();
 
        return view('admin.users.index', compact('users', 'pendingDistributors'));
    }

    // SHOW — Detail satu user
    public function show(User $user): View
    {
        // Load riwayat order user, diurutkan dari terbaru
        $orders = $user->orders()
            ->with('items')
            ->latest()
            ->take(10)
            ->get();
 
        return view('admin.users.show', compact('user', 'orders'));
    }
 
    // CREATE — Form tambah user baru
    public function create(): View
    {
        // Gate check: hanya admin yang bisa akses form ini
        $this->authorize('create', User::class);

        return view('admin.users.create');
    }
 
    // STORE — Simpan user baru
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
 
        User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
            'is_verified' => $request->boolean('is_verified'),
        ]);
 
        return redirect()
            ->route('admin.users.index')
            ->with('success', "User \"{$validated['name']}\" berhasil ditambahkan.");
    }
 
    // EDIT — Form edit user
    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', compact('user'));
    }
 
    // UPDATE — Simpan perubahan user
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validated();
 
        // Bangun data yang akan di-update
        // Exclude password dari array utama dulu
        $data = collect($validated)->except('password')->toArray();

        // Pastikan is_verified tersimpan sebagai boolean bukan string
        $data['is_verified'] = $request->boolean('is_verified');

        // Hanya update password jika admin mengisi field password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }
 
        $user->update($data);
 
        return redirect()
            ->route('admin.users.index')
            ->with('success', "Data user \"{$user->name}\" berhasil diperbarui.");
    }
 
    // TOGGLE VERIFY — Verifikasi / cabut verifikasi distributor
    // Route: PATCH /admin/users/{user}/verify
    public function toggleVerify(User $user): RedirectResponse
    {
        // Policy cek: hanya admin, dan hanya untuk target distributor
        $this->authorize('toggleVerify', $user);
 
        $user->update(['is_verified' => ! $user->is_verified]);
 
        $action = $user->is_verified ? 'diverifikasi' : 'dicabut verifikasinya';
 
        return back()->with('success', "Distributor \"{$user->name}\" berhasil {$action}.");
    }
 
    // DESTROY — Soft-delete user
    public function destroy(User $user): RedirectResponse
    {
        // Policy mencegah admin hapus dirinya sendiri
        $this->authorize('delete', $user);
 
        // Cegah hapus jika user masih punya order aktif
        if ($user->orders()->whereIn('status', ['pending', 'processing'])->exists()) {
            return back()->with('error', "User \"{$user->name}\" tidak bisa dihapus karena masih memiliki order yang aktif.");
        }
 
        $user->delete();
 
        return back()->with('success', "User \"{$user->name}\" berhasil dihapus.");
    }
}
