{{--
    Partial: Form Ubah Password
    Di-include oleh profile/edit.blade.php

    Menggunakan named error bag 'updatePassword' agar error dari
    form ini tidak tercampur dengan error dari form informasi profil
    yang ada di halaman yang sama.

    Route: PUT /password → PasswordController@update (dari Breeze)
--}}

<form method="POST" action="{{ route('password.update') }}" id="updatePasswordForm">
    @csrf
    @method('PUT')

    {{-- Status sukses (dikirim sebagai session flash) --}}
    @if (session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible fade show py-2 mb-3">
            <i class="bi bi-check-circle me-1"></i> Password berhasil diperbarui.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">

        {{-- Password Saat Ini --}}
        <div class="col-md-6">
            <label class="form-label fw-semibold">
                Password Saat Ini <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <input type="password" name="current_password" id="currentPwd"
                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                    autocomplete="current-password" required>
                <button type="button" class="btn btn-outline-secondary"
                        onclick="togglePwd('currentPwd', this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('current_password', 'updatePassword')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Spacer --}}
        <div class="col-md-6 d-none d-md-block"></div>

        {{-- Password Baru --}}
        <div class="col-md-6">
            <label class="form-label fw-semibold">
                Password Baru <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <input type="password" name="password" id="newPwd"
                    class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                    autocomplete="new-password" required>
                <button type="button" class="btn btn-outline-secondary"
                        onclick="togglePwd('newPwd', this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <div class="form-text">Minimal 8 karakter.</div>
            @error('password', 'updatePassword')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Konfirmasi Password Baru --}}
        <div class="col-md-6">
            <label class="form-label fw-semibold">
                Konfirmasi Password Baru <span class="text-danger">*</span>
            </label>
            <input type="password" name="password_confirmation" id="confirmPwd"
                class="form-control" autocomplete="new-password" required>
        </div>

    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-warning fw-semibold">
            <i class="bi bi-shield-lock me-1"></i> Perbarui Password
        </button>
    </div>
</form>

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
