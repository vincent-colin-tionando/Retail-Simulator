@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Kembali</a>
    <h4 class="mb-0">Edit Produk: {{ $product->name }}</h4>
</div>

{{-- Method spoofing: form HTML hanya mendukung GET & POST.
     Tambahkan @method('PUT') agar Laravel tahu ini adalah request PUT.
     Route products.update mendengarkan PUT /admin/products/{product} --}}
<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Informasi Produk</div>
                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        {{-- old() diisi terlebih dahulu; jika tidak ada, gunakan nilai dari database --}}
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $product->name) }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" name="sku"
                                class="form-control @error('sku') is-invalid @enderror"
                                value="{{ old('sku', $product->sku) }}"
                                style="text-transform:uppercase">
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select">
                                <option value="">— Tanpa Kategori —</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        @selected(old('category_id', $product->category_id) == $cat->id)>
                                        {{ $cat->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Satuan <span class="text-danger">*</span></label>
                            <input type="text" name="unit"
                                class="form-control @error('unit') is-invalid @enderror"
                                value="{{ old('unit', $product->unit) }}">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                    </div>

                </div>
            </div>

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
                                    value="{{ old('price_consumer', $product->consumerPrice?->price) }}">
                                @error('price_consumer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga Distributor <span class="text-danger">*</span></label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="price_distributor" min="1" step="any"
                                    class="form-control @error('price_distributor') is-invalid @enderror"
                                    value="{{ old('price_distributor', $product->distributorPrice?->price) }}">
                                @error('price_distributor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">

            <div class="card">
                <div class="card-header">Stok</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Stok Saat Ini</label>
                        <input type="number" name="stock" min="0"
                            class="form-control @error('stock') is-invalid @enderror"
                            value="{{ old('stock', $product->stock) }}">
                        <div class="form-text text-warning">
                            Mengubah stok di sini tidak mencatat riwayat pembelian.
                            Gunakan menu Pembelian untuk tambah stok yang tercatat.
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Batas Minimum Stok</label>
                        <input type="number" name="stock_min" min="0"
                            class="form-control"
                            value="{{ old('stock_min', $product->stock_min) }}">
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">Gambar Produk</div>
                <div class="card-body">
                    {{-- Tampilkan gambar lama --}}
                    <img id="img-preview" src="{{ $product->image_url }}"
                        class="img-fluid rounded mb-2"
                        style="max-height:160px;width:100%;object-fit:cover;">
                    <input type="file" name="image" id="img-input" accept="image/*"
                        class="form-control @error('image') is-invalid @enderror">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Kosongkan jika tidak ingin mengubah gambar.</div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">Status</div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active"
                            id="is_active" value="1"
                            {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Produk aktif
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-grid mt-3 gap-2">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>

        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
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
