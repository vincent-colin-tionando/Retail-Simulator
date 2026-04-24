@extends('layouts.guest')

@section('title', 'Login')
@section('subtitle', 'Masuk ke akun Anda')

@section('content')

<h5 class="fw-bold mb-4 text-center">Login</h5>

{{-- Status (misalnya setelah reset password) --}}
@if (session('status'))
    <div class="alert alert-success mb-3">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    {{-- Email --}}
    <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                placeholder="nama@email.com"
                autofocus autocomplete="username" required>
        </div>
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Password --}}
    <div class="mb-3">
        <div class="d-flex justify-content-between">
            <label class="form-label fw-semibold">Password</label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small text-muted">
                    Lupa password?
                </a>
            @endif
        </div>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror"
                autocomplete="current-password" required>
            {{-- Toggle show/hide password --}}
            <button type="button" class="btn btn-outline-secondary"
                    onclick="togglePwd('password', this)">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Remember me --}}
    <div class="mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label text-muted small" for="remember">
                Ingat saya di perangkat ini
            </label>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 fw-semibold">
        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
    </button>
</form>

@endsection

@section('footer_link')
    <span class="text-white-50 small">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-warning fw-semibold">Daftar sekarang</a>
    </span>
@endsection

@push('scripts')
<script>
    function togglePwd(id, btn) {
        const input = document.getElementById(id);
        const icon  = btn.querySelector('i');
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
