@extends('layouts.admin')

@section('title', 'Edit Supplier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Supplier</h4>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary btn-sm">
        ← Kembali ke Daftar
    </a>
</div>

<div class="card" style="max-width: 720px;">
    <div class="card-body">
        {{-- PUT method disimulasikan via hidden _method karena HTML form hanya kenal GET/POST --}}
        <form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Nama Supplier --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Nama Supplier <span class="text-danger">*</span>
                </label>
                <input type="text" name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $supplier->name) }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                {{-- Kontak Person --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Nama Kontak Person</label>
                    <input type="text" name="contact_person"
                        class="form-control @error('contact_person') is-invalid @enderror"
                        value="{{ old('contact_person', $supplier->contact_person) }}">
                    @error('contact_person')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Telepon --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Nomor Telepon</label>
                    <input type="text" name="phone"
                        class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone', $supplier->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $supplier->email) }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Alamat</label>
                <textarea name="address" rows="2"
                    class="form-control @error('address') is-invalid @enderror">{{ old('address', $supplier->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Catatan --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Catatan Tambahan</label>
                <textarea name="notes" rows="2"
                    class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $supplier->notes) }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Status --}}
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active"
                        value="1" id="is_active"
                        @checked(old('is_active', $supplier->is_active))>
                    <label class="form-check-label" for="is_active">
                        Supplier aktif
                    </label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
