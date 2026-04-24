@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Kembali</a>
    <h4 class="mb-0">Edit Kategori: {{ $category->name }}</h4>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                {{-- Method PUT untuk update, Laravel hanya menerima POST dari HTML form --}}
                <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Kategori Induk</label>
                        <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">— Tidak ada (kategori utama) —</option>
                            @foreach ($parentCategories as $parent)
                                <option value="{{ $parent->id }}"
                                    @selected(old('parent_id', $category->parent_id) == $parent->id)>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $category->name) }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug"
                            class="form-control @error('slug') is-invalid @enderror"
                            value="{{ old('slug', $category->slug) }}">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Urutan Tampil</label>
                            <input type="number" name="sort_order" min="0"
                                class="form-control"
                                value="{{ old('sort_order', $category->sort_order) }}">
                        </div>
                        <div class="col-md-8 d-flex align-items-end pb-1">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active"
                                    id="is_active" value="1"
                                    {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Kategori aktif</label>
                            </div>
                        </div>
                    </div>

                    {{-- Info sub-kategori yang dimiliki (peringatan sebelum ubah induk) --}}
                    @if ($category->children->isNotEmpty())
                        <div class="alert alert-warning small mb-3">
                            Kategori ini memiliki {{ $category->children->count() }} sub-kategori:
                            <strong>{{ $category->children->pluck('name')->join(', ') }}</strong>.
                            Mengubah induk kategori ini tidak mempengaruhi sub-kategorinya.
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection