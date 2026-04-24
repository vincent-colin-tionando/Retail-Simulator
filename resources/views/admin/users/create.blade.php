@extends('layouts.admin')

@section('title', 'Tambah User')
@section('breadcrumb', 'User / Tambah')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Tambah Pengguna Baru</h4>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
</div>

<div class="card" style="max-width:680px">
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        required>
                    <div class="form-text">Minimal 8 karakter.</div>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation"
                        class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                    <select name="role" id="roleSelect"
                        class="form-select @error('role') is-invalid @enderror">
                        <option value="consumer"    @selected(old('role','consumer')==='consumer')>Consumer</option>
                        <option value="distributor" @selected(old('role')==='distributor')>Distributor</option>
                        <option value="admin"       @selected(old('role')==='admin')>Admin</option>
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Telepon</label>
                    <input type="text" name="phone"
                        class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone') }}" placeholder="08xx-xxxx-xxxx">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Kolom verifikasi — hanya untuk distributor --}}
                <div class="col-md-4" id="verifiedField"
                     style="{{ old('role') === 'distributor' ? '' : 'display:none' }}">
                    <label class="form-label fw-semibold">Status Verifikasi</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_verified"
                               value="1" id="is_verified" @checked(old('is_verified'))>
                        <label class="form-check-label" for="is_verified">
                            Langsung terverifikasi
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Alamat</label>
                    <textarea name="address" rows="2"
                        class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Nama perusahaan — hanya relevan untuk distributor --}}
                <div class="col-12" id="companyField"
                     style="{{ old('role') === 'distributor' ? '' : 'display:none' }}">
                    <label class="form-label fw-semibold">Nama Perusahaan / Toko</label>
                    <input type="text" name="company_name"
                        class="form-control @error('company_name') is-invalid @enderror"
                        value="{{ old('company_name') }}"
                        placeholder="PT / CV / UD ...">
                    @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Simpan User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('roleSelect').addEventListener('change', function () {
    const isDistributor = this.value === 'distributor';
    document.getElementById('companyField').style.display  = isDistributor ? '' : 'none';
    document.getElementById('verifiedField').style.display = isDistributor ? '' : 'none';
});
</script>
@endpush