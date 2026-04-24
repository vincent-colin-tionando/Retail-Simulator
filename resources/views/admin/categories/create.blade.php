@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Kembali</a>
    <h4 class="mb-0">Tambah Kategori Baru</h4>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf

                    {{-- Pilih induk: jika diisi maka ini sub-kategori --}}
                    <div class="mb-3">
                        <label class="form-label">Kategori Induk</label>
                        <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">— Tidak ada (kategori utama) —</option>
                            @foreach ($parentCategories as $parent)
                                <option value="{{ $parent->id }}" @selected(old('parent_id') == $parent->id)>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Kosongkan untuk membuat kategori utama.
                            Pilih induk untuk membuat sub-kategori.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="cat-name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="Contoh: Minuman Dingin">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" id="cat-slug"
                            class="form-control @error('slug') is-invalid @enderror"
                            value="{{ old('slug') }}" placeholder="minuman-dingin">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Diisi otomatis dari nama. Hanya huruf kecil, angka, dan tanda hubung.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Urutan Tampil</label>
                            <input type="number" name="sort_order" min="0"
                                class="form-control" value="{{ old('sort_order', 0) }}">
                        </div>
                        <div class="col-md-8 d-flex align-items-end pb-1">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active"
                                    id="is_active" value="1"
                                    {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Kategori aktif
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>

let slugEdited = false;

document.getElementById('cat-slug').addEventListener('input', () => {
    slugEdited = true;
});

document.getElementById('cat-name').addEventListener('input', function () {
    if (slugEdited) return;

    const slug = this.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '')   // hapus karakter selain huruf/angka/spasi/strip
        .replace(/\s+/g, '-')            // spasi -> tanda hubung
        .replace(/-+/g, '-');            // strip berulang -> satu strip

    document.getElementById('cat-slug').value = slug;
});
</script>
@endpush