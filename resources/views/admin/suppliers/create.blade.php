@extends('layouts.admin')

@section('title', 'Tambah Supplier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Tambah Supplier Baru</h4>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary btn-sm">
        ← Kembali ke Daftar
    </a>
</div>

<div class="card" style="max-width: 720px;">
    <div class="card-body">
        <form action="{{ route('admin.suppliers.store') }}" method="POST">
            @csrf

            {{-- Nama Supplier --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Nama Supplier <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" placeholder="Contoh: PT Sumber Makmur">
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
                        value="{{ old('contact_person') }}"
                        placeholder="Nama sales / PIC yang bisa dihubungi">
                    @error('contact_person')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Telepon --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Nomor Telepon</label>
                    <input type="text" name="phone"
                        class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone') }}"
                        placeholder="0811-xxxx-xxxx">
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
                    value="{{ old('email') }}"
                    placeholder="supplier@example.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Alamat</label>
                <textarea name="address" rows="2"
                    class="form-control @error('address') is-invalid @enderror"
                    placeholder="Jl. Contoh No. 1, Kota ...">{{ old('address') }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Catatan --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Catatan Tambahan</label>
                <textarea name="notes" rows="2"
                    class="form-control @error('notes') is-invalid @enderror"
                    placeholder="Contoh: Minimal order 1 karton, pembayaran tempo 30 hari">{{ old('notes') }}</textarea>
                <div class="form-text">Catatan internal, tidak terlihat oleh supplier.</div>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Status --}}
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active"
                        value="1" id="is_active"
                        @checked(old('is_active', true))>
                    <label class="form-check-label" for="is_active">
                        Supplier aktif (dapat dipilih saat catat pembelian)
                    </label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Supplier</button>
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
