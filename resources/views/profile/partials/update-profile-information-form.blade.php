{{--
    Partial: Form Update Informasi Profil
    Di-include oleh profile/edit.blade.php

    Perbedaan dari versi Breeze default:
    - Memakai Bootstrap 5 (bukan Tailwind)
    - Menampilkan field tambahan yang ada di tabel users:
        phone, address, company_name (untuk distributor)
    - Tidak ada x-components (x-input-label, x-text-input, dll.)
      karena kita tidak menggunakan komponen Tailwind/Breeze

    Variabel yang tersedia (dikirim oleh ProfileController@edit):
    - $user → App\Models\User (user yang sedang login)
--}}

<form method="POST" action="{{ route('profile.update') }}" id="profileInfoForm">
    @csrf
    @method('PATCH')

    {{-- Status sukses --}}
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show py-2 mb-3">
            <i class="bi bi-check-circle me-1"></i> Profil berhasil diperbarui.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">

        {{-- Nama --}}
        <div class="col-md-6">
            <label class="form-label fw-semibold">
                Nama Lengkap <span class="text-danger">*</span>
            </label>
            <input type="text" name="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $user->name) }}" required autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="col-md-6">
            <label class="form-label fw-semibold">
                Email <span class="text-danger">*</span>
            </label>
            <input type="email" name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $user->email) }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Telepon --}}
        <div class="col-md-6">
            <label class="form-label fw-semibold">Nomor Telepon / WhatsApp</label>
            <input type="text" name="phone"
                class="form-control @error('phone') is-invalid @enderror"
                value="{{ old('phone', $user->phone) }}"
                placeholder="081234567890">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Role (hanya tampil, tidak bisa diubah) --}}
        <div class="col-md-6">
            <label class="form-label fw-semibold">Role Akun</label>
            <div class="form-control bg-light text-muted" style="cursor:default">
                <span class="badge {{ $user->role_badge_class }} me-2">
                    {{ $user->role_label }}
                </span>
                {{-- Tampilkan status verifikasi untuk distributor --}}
                @if ($user->role === 'distributor')
                    @if ($user->is_verified)
                        <span class="badge bg-success">
                            <i class="bi bi-check-circle me-1"></i>Terverifikasi
                        </span>
                    @else
                        <span class="badge bg-warning text-dark">
                            <i class="bi bi-clock me-1"></i>Menunggu Verifikasi
                        </span>
                    @endif
                @endif
            </div>
            <div class="form-text">Role tidak dapat diubah sendiri.</div>
        </div>

        {{-- Alamat --}}
        <div class="col-12">
            <label class="form-label fw-semibold">Alamat</label>
            <textarea name="address" rows="2"
                class="form-control @error('address') is-invalid @enderror"
                placeholder="Jl. Contoh No. 1, Kota...">{{ old('address', $user->address) }}</textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Nama Perusahaan — hanya untuk distributor --}}
        @if ($user->role === 'distributor')
            <div class="col-12">
                <label class="form-label fw-semibold">Nama Perusahaan / Toko</label>
                <input type="text" name="company_name"
                    class="form-control @error('company_name') is-invalid @enderror"
                    value="{{ old('company_name', $user->company_name) }}"
                    placeholder="PT / CV / UD / Toko ...">
                @error('company_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        @endif

    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Simpan Perubahan
        </button>
    </div>
</form>
