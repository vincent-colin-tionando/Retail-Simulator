@extends('layouts.admin')

@section('title', 'Detail Kategori')
@section('breadcrumb', 'Kategori / Detail')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">{{ $category->name }}</h4>
        <small class="text-muted">
            @if ($category->isChild())
                Sub-kategori dari <strong>{{ $category->parent->name }}</strong>
            @else
                Kategori Utama
            @endif
        </small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.categories.edit', $category) }}"
           class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('admin.categories.index') }}"
           class="btn btn-outline-secondary btn-sm">← Kembali</a>
    </div>
</div>

<div class="row g-4">

    {{-- Informasi Kategori --}}
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header fw-semibold">Informasi Kategori</div>
            <div class="card-body p-0">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted ps-3" style="width:40%">Nama</td>
                        <td class="fw-semibold">{{ $category->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Slug</td>
                        <td><code>{{ $category->slug }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Induk</td>
                        <td>
                            @if ($category->parent)
                                <a href="{{ route('admin.categories.show', $category->parent) }}">
                                    {{ $category->parent->name }}
                                </a>
                            @else
                                <span class="text-muted fst-italic">— (Kategori utama)</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Urutan</td>
                        <td>{{ $category->sort_order }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Status</td>
                        <td>
                            @if ($category->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    @if ($category->description)
                    <tr>
                        <td class="text-muted ps-3 align-top">Deskripsi</td>
                        <td class="text-wrap">{{ $category->description }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted ps-3">Dibuat</td>
                        <td>{{ $category->created_at->format('d M Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="col-md-3">
        <div class="card text-center mb-3">
            <div class="card-body py-4">
                <div class="display-5 fw-bold text-primary">
                    {{ $category->products->count() }}
                </div>
                <div class="text-muted mt-1">Total Produk</div>
            </div>
        </div>
        <div class="card text-center">
            <div class="card-body py-4">
                <div class="display-5 fw-bold text-info">
                    {{ $category->children->count() }}
                </div>
                <div class="text-muted mt-1">Sub-kategori</div>
            </div>
        </div>
    </div>

    {{-- Sub-kategori --}}
    @if ($category->children->isNotEmpty())
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-semibold">
                Sub-kategori ({{ $category->children->count() }})
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th class="text-center">Produk</th>
                            <th class="text-center">Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($category->children as $child)
                        <tr>
                            <td>{{ $child->name }}</td>
                            <td><code class="small">{{ $child->slug }}</code></td>
                            <td class="text-center">{{ $child->products->count() }}</td>
                            <td class="text-center">
                                <span class="badge {{ $child->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $child->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.categories.show', $child) }}"
                                   class="btn btn-sm btn-outline-info">Detail</a>
                                <a href="{{ route('admin.categories.edit', $child) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Daftar Produk --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-semibold">
                Produk dalam Kategori Ini ({{ $category->products->count() }})
            </div>
            <div class="card-body p-0">
                @if ($category->products->isEmpty())
                    <div class="p-4 text-center text-muted">
                        Belum ada produk di kategori ini.
                        <a href="{{ route('admin.products.create') }}">Tambah produk</a>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Produk</th>
                                <th>SKU</th>
                                <th class="text-center">Stok</th>
                                <th class="text-end">Harga Consumer</th>
                                <th class="text-center">Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($category->products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td><code class="small">{{ $product->sku }}</code></td>
                                <td class="text-center">
                                    <span class="{{ $product->isLowStock() ? 'text-danger fw-bold' : '' }}">
                                        {{ $product->stock }}
                                    </span>
                                    @if ($product->isLowStock())
                                        <span class="badge bg-danger ms-1" style="font-size:.65rem">Low</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    Rp {{ number_format($product->consumerPrice->price ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.show', $product) }}"
                                       class="btn btn-sm btn-outline-info">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
