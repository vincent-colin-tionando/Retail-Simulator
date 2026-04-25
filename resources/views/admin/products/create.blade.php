@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Kembali</a>
    <h4 class="mb-0">Tambah Produk Baru</h4>
</div>

{{-- Form action mengarah ke route products.store (POST /admin/products) --}}
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        {{-- ── Kolom Kiri: Info Utama ──────────────────────────────────── --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Informasi Produk</div>
                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        {{-- old() mengisi ulang nilai jika form gagal validasi --}}
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="Contoh: Aqua Air Mineral 600ml">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                value="{{ old('sku') }}" placeholder="Contoh: MNM-001"
                                style="text-transform:uppercase">
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Kode unik produk. Hanya huruf, angka, dan tanda hubung.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">— Pilih Kategori —</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>
                                        {{ $cat->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Satuan <span class="text-danger">*</span></label>
                            <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror"
                                value="{{ old('unit', 'pcs') }}" placeholder="pcs / kg / karton / lusin">
                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"
                            placeholder="Deskripsi singkat produk (opsional)">{{ old('description') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- Harga --}}
            <div class="card mt-4">
                <div class="card-header">Harga Jual</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Harga Consumer <span class="text-danger">*</span></label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="price_consumer" min="1" step="any"
                                    class="form-control @error('price_consumer') is-invalid @enderror"
                                    value="{{ old('price_consumer') }}" placeholder="5000">
                                @error('price_consumer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Harga untuk pembeli eceran.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga Distributor <span class="text-danger">*</span></label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="price_distributor" min="1" step="any"
                                    class="form-control @error('price_distributor') is-invalid @enderror"
                                    value="{{ old('price_distributor') }}" placeholder="3800">
                                @error('price_distributor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Harus lebih murah dari harga consumer.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Stok, Gambar, Status --}}
        <div class="col-lg-4">

            <div class="card">
                <div class="card-header">Stok</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Stok Awal <span class="text-danger">*</span></label>
                        <input type="number" name="stock" min="0"
                            class="form-control @error('stock') is-invalid @enderror"
                            value="{{ old('stock', 0) }}">
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">Batas Minimum Stok <span class="text-danger">*</span></label>
                        <input type="number" name="stock_min" min="0"
                            class="form-control @error('stock_min') is-invalid @enderror"
                            value="{{ old('stock_min', 5) }}">
                        @error('stock_min')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Peringatan stok menipis muncul di bawah nilai ini.</div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">Gambar Produk</div>
                <div class="card-body">
                    {{-- Preview gambar sebelum upload --}}
                    <img id="img-preview" src="{{ asset('images/product-placeholder.png') }}"
                        class="img-fluid rounded mb-2" style="max-height:160px;width:100%;object-fit:cover;">
                    <input type="file" name="image" id="img-input" accept="image/*"
                        class="form-control @error('image') is-invalid @enderror">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">JPG/PNG/WEBP, maks. 2MB.</div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">Status</div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active"
                            id="is_active" value="1"
                            {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Produk aktif (tampil di storefront)
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary">Simpan Produk</button>
            </div>

        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Preview gambar sebelum di-upload
document.getElementById('img-input').addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('img-preview').src = e.target.result;
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
