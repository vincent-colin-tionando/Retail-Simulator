@extends('layouts.admin')

@section('title', 'Manajemen Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Manajemen Produk</h4>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        + Tambah Produk
    </a>
</div>

{{-- Flash Message --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Filter & Pencarian --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Cari nama produk atau SKU..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>
                            {{ $cat->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="is_active" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="1" @selected(request('is_active') === '1')>Aktif</option>
                    <option value="0" @selected(request('is_active') === '0')>Nonaktif</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Produk --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px">Gambar</th>
                        <th>Nama Produk</th>
                        <th>SKU</th>
                        <th>Kategori</th>
                        <th class="text-end">Harga Consumer</th>
                        <th class="text-end">Harga Distributor</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            {{-- Gambar thumbnail --}}
                            <td>
                                <img src="{{ $product->image_url }}"
                                    alt="{{ $product->name }}"
                                    class="rounded"
                                    style="width:44px;height:44px;object-fit:cover;">
                            </td>

                            {{-- Nama & unit --}}
                            <td>
                                <div class="fw-500">{{ $product->name }}</div>
                                <small class="text-muted">per {{ $product->unit }}</small>
                            </td>

                            <td><code class="small">{{ $product->sku }}</code></td>

                            {{-- Kategori --}}
                            <td>
                                {{ $product->category?->name ?? '-' }}
                            </td>

                            {{-- Harga consumer --}}
                            <td class="text-end">
                                {{ $product->consumerPrice
                                    ? 'Rp ' . number_format($product->consumerPrice->price, 0, ',', '.')
                                    : '-' }}
                            </td>

                            {{-- Harga distributor --}}
                            <td class="text-end">
                                {{ $product->distributorPrice
                                    ? 'Rp ' . number_format($product->distributorPrice->price, 0, ',', '.')
                                    : '-' }}
                            </td>

                            {{-- Stok: merah jika menipis --}}
                            <td class="text-center">
                                <span class="badge {{ $product->isLowStock() ? 'bg-danger' : 'bg-success' }}">
                                    {{ $product->stock }}
                                </span>
                                @if ($product->isLowStock())
                                    <div><small class="text-danger">Menipis!</small></div>
                                @endif
                            </td>

                            {{-- Toggle aktif/nonaktif --}}
                            <td class="text-center">
                                <div class="form-check form-switch d-flex justify-content-center mb-0"
                                    title="{{ $product->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}">
                                    <input class="form-check-input toggle-active"
                                        type="checkbox"
                                        data-id="{{ $product->id }}"
                                        data-url="{{ route('admin.products.toggle', $product) }}"
                                        {{ $product->is_active ? 'checked' : '' }}>
                                </div>
                            </td>

                            {{-- Tombol aksi --}}
                            <td class="text-center">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                    class="btn btn-sm btn-outline-secondary" title="Edit">
                                    Edit
                                </a>

                                {{-- Tombol hapus dengan konfirmasi --}}
                                <form action="{{ route('admin.products.destroy', $product) }}"
                                    method="POST" class="d-inline"
                                    onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                Tidak ada produk ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginasi --}}
    @if ($products->hasPages())
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Menampilkan {{ $products->firstItem() }}–{{ $products->lastItem() }}
                dari {{ $products->total() }} produk
            </small>
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Toggle aktif/nonaktif produk via AJAX tanpa reload halaman
document.querySelectorAll('.toggle-active').forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        const url = this.dataset.url;

        fetch(url, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(res => res.json())
        .then(data => {
            // Tampilkan pesan singkat di bawah halaman
            const toast = document.getElementById('toast-msg');
            if (toast) {
                toast.textContent = data.message;
                toast.classList.remove('d-none');
                setTimeout(() => toast.classList.add('d-none'), 2500);
            }
        })
        .catch(() => {
            // Jika gagal, kembalikan checkbox ke posisi sebelumnya
            this.checked = ! this.checked;
            alert('Gagal mengubah status. Silakan coba lagi.');
        });
    });
});
</script>
@endpush