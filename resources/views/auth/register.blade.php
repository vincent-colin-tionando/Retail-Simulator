@extends('layouts.guest')

@section('title', 'Daftar Akun')
@section('subtitle', 'Buat akun baru')

@section('content')

<h5 class="fw-bold mb-4 text-center">Buat Akun Baru</h5>

<form method="POST" action="{{ route('register') }}" id="registerForm">
    @csrf

    {{-- Nama Lengkap --}}
    <div class="mb-3">
        <label class="form-label fw-semibold">Nama Lengkap</label>
        <input type="text" name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name') }}"
            placeholder="Masukkan nama lengkap" autofocus required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Email --}}
    <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" name="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email') }}"
            placeholder="nama@email.com" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Nomor HP --}}
    <div class="mb-3">
        <label class="form-label fw-semibold">Nomor HP / WhatsApp</label>
        <input type="text" name="phone"
            class="form-control @error('phone') is-invalid @enderror"
            value="{{ old('phone') }}"
            placeholder="081234567890" required>
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Alamat --}}
    <div class="mb-3">
        <label class="form-label fw-semibold">Alamat</label>
        <textarea name="address" rows="2"
            class="form-control @error('address') is-invalid @enderror"
            placeholder="Jl. Contoh No. 1, Kota..." required>{{ old('address') }}</textarea>
        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Pilihan Role --}}
    <div class="mb-3">
        <label class="form-label fw-semibold">Daftar Sebagai</label>
        <div class="row g-2">
            {{-- Consumer --}}
            <div class="col-6">
                <input type="radio" class="btn-check" name="role" id="role_consumer"
                    value="consumer" @checked(old('role', 'consumer') === 'consumer')>
                <label class="btn btn-outline-success w-100 text-start p-3"
                       for="role_consumer" style="line-height:1.3">
                    <i class="bi bi-person-fill d-block fs-5 mb-1"></i>
                    <strong>Consumer</strong><br>
                    <small class="fw-normal text-muted">Pembelian eceran</small>
                </label>
            </div>
            {{-- Distributor --}}
            <div class="col-6">
                <input type="radio" class="btn-check" name="role" id="role_distributor"
                    value="distributor" @checked(old('role') === 'distributor')>
                <label class="btn btn-outline-primary w-100 text-start p-3"
                       for="role_distributor" style="line-height:1.3">
                    <i class="bi bi-building d-block fs-5 mb-1"></i>
                    <strong>Distributor</strong><br>
                    <small class="fw-normal text-muted">Harga grosir</small>
                </label>
            </div>
        </div>
        @error('role') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Nama Perusahaan — muncul hanya saat pilih distributor --}}
    <div class="mb-3" id="companyNameField"
         style="{{ old('role') === 'distributor' ? '' : 'display:none' }}">
        <label class="form-label fw-semibold">
            Nama Perusahaan / Toko <span class="text-danger">*</span>
        </label>
        <input type="text" name="company_name"
            class="form-control @error('company_name') is-invalid @enderror"
            value="{{ old('company_name') }}"
            placeholder="PT / CV / UD / Toko ...">
        <div class="form-text text-muted">
            <i class="bi bi-info-circle me-1"></i>
            Akun distributor memerlukan verifikasi admin sebelum dapat harga grosir.
        </div>
        @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Password --}}
    <div class="mb-3">
        <label class="form-label fw-semibold">Password</label>
        <div class="input-group">
            <input type="password" name="password" id="pwd"
                class="form-control @error('password') is-invalid @enderror"
                autocomplete="new-password" required>
            <button type="button" class="btn btn-outline-secondary"
                    onclick="togglePwd('pwd', this)">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        <div class="form-text">Minimal 8 karakter.</div>
        @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Konfirmasi Password --}}
    <div class="mb-4">
        <label class="form-label fw-semibold">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" id="pwd_confirm"
            class="form-control" autocomplete="new-password" required>
    </div>

    <button type="submit" class="btn btn-primary w-100 fw-semibold">
        <i class="bi bi-person-plus me-1"></i> Daftar Sekarang
    </button>
</form>

@endsection

@section('footer_link')
    <span class="text-white-50 small">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-warning fw-semibold">Login di sini</a>
    </span>
@endsection

@push('scripts')
<script>
// Tampilkan/sembunyikan field nama perusahaan berdasarkan pilihan role
document.querySelectorAll('input[name="role"]').forEach(function(radio) {
    radio.addEventListener('change', function () {
        var field = document.getElementById('companyNameField');
        field.style.display = this.value === 'distributor' ? '' : 'none';
        var input = field.querySelector('input');
        input.required = this.value === 'distributor';
    });
});

// Toggle show/hide password
function togglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endpush
