@extends('layouts.admin')

@section('title', 'Edit User')
@section('breadcrumb', 'User / Edit')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Pengguna — {{ $user->name }}</h4>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
</div>

<div class="card" style="max-width:680px">
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password Baru</label>
                    <input type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror">
                    <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                    <select name="role" id="roleSelect"
                        class="form-select @error('role') is-invalid @enderror">
                        <option value="consumer"    @selected(old('role',$user->role)==='consumer')>Consumer</option>
                        <option value="distributor" @selected(old('role',$user->role)==='distributor')>Distributor</option>
                        <option value="admin"       @selected(old('role',$user->role)==='admin')>Admin</option>
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Telepon</label>
                    <input type="text" name="phone"
                        class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone', $user->phone) }}">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4" id="verifiedField"
                     style="{{ old('role', $user->role) === 'distributor' ? '' : 'display:none' }}">
                    <label class="form-label fw-semibold">Status Verifikasi</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_verified"
                               value="1" id="is_verified"
                               @checked(old('is_verified', $user->is_verified))>
                        <label class="form-check-label" for="is_verified">Terverifikasi</label>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Alamat</label>
                    <textarea name="address" rows="2"
                        class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->address) }}</textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12" id="companyField"
                     style="{{ old('role', $user->role) === 'distributor' ? '' : 'display:none' }}">
                    <label class="form-label fw-semibold">Nama Perusahaan / Toko</label>
                    <input type="text" name="company_name"
                        class="form-control @error('company_name') is-invalid @enderror"
                        value="{{ old('company_name', $user->company_name) }}">
                    @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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