{{--
    Partial: Form Hapus Akun
    Di-include oleh profile/edit.blade.php

    Ditampilkan hanya untuk consumer dan distributor.
    (Admin tidak bisa hapus akun sendiri — lihat profile/edit.blade.php)

    Menggunakan named error bag 'userDeletion' agar error tidak campur
    dengan form lain di halaman yang sama.

    Route: DELETE /profile → ProfileController@destroy (dari Breeze)
    ProfileController@destroy akan logout otomatis setelah akun dihapus.
--}}

<p class="text-muted small mb-3">
    Setelah akun dihapus, semua data Anda termasuk riwayat pesanan
    akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
</p>

{{-- Tombol yang membuka modal konfirmasi --}}
<button type="button" class="btn btn-danger btn-sm"
        data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
    <i class="bi bi-trash me-1"></i> Hapus Akun Saya
</button>

{{-- ── Modal Konfirmasi ── --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1"
     aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger" id="deleteAccountModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Hapus Akun Secara Permanen?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')

                <div class="modal-body">
                    <p class="text-muted small">
                        Semua data akun Anda akan dihapus secara permanen.
                        Masukkan password Anda untuk mengkonfirmasi.
                    </p>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">
                            Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" name="password"
                            class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                            placeholder="Masukkan password Anda" required>
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Ya, Hapus Akun Saya
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Buka modal otomatis jika ada error dari named error bag --}}
@if ($errors->userDeletion->isNotEmpty())
    @push('scripts')
    <script>
        // Jika validasi gagal (password salah), buka modal kembali
        // agar user tidak bingung kenapa tombol tidak bereaksi
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('deleteAccountModal')).show();
        });
    </script>
    @endpush
@endif
