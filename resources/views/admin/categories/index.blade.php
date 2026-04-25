@extends('layouts.admin')

@section('title', 'Manajemen Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Manajemen Kategori</h4>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">+ Tambah Kategori</a>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Filter pencarian --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:280px"
                placeholder="Cari nama kategori..." value="{{ $search ?? '' }}">
            <button class="btn btn-sm btn-secondary">Cari</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0" style="table-layout:fixed">
            <colgroup>
                <col style="width:32%">
                <col style="width:22%">
                <col style="width:9%">
                <col style="width:7%">
                <col style="width:7%">
                <col style="width:9%">
                <col style="width:14%">
            </colgroup>
            <thead class="table-light">
                <tr>
                    <th>Nama Kategori</th>
                    <th>Slug</th>
                    <th class="text-center">Sub-kategori</th>
                    <th class="text-center">Produk</th>
                    <th class="text-center">Urutan</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($parents as $parent)
                    {{-- ── Baris Induk ── --}}
                    <tr class="table-light fw-semibold">
                        <td>
                            @if ($parent->children_count > 0)
                                <button class="btn btn-sm btn-link p-0 me-1 text-decoration-none toggle-btn"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#sub-{{ $parent->id }}"
                                    aria-expanded="true"
                                    title="Tampilkan/sembunyikan sub-kategori">
                                    <i class="bi bi-chevron-down toggle-icon" style="transition:.2s"></i>
                                </button>
                            @else
                                <span class="me-1" style="display:inline-block;width:24px"></span>
                            @endif
                            <i class="bi bi-folder2 text-warning me-1"></i>
                            {{ $parent->name }}
                        </td>
                        <td><code class="small">{{ $parent->slug }}</code></td>
                        <td class="text-center">
                            @if ($parent->children_count > 0)
                                <span class="badge bg-info-subtle text-info">{{ $parent->children_count }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark">{{ $parent->products_count + $parent->children->sum('products_count') }}</span>
                        </td>
                        <td class="text-center text-muted">{{ $parent->sort_order }}</td>
                        <td class="text-center">
                            <span class="badge {{ $parent->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $parent->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.categories.edit', $parent) }}"
                                class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $parent) }}"
                                method="POST" class="d-inline"
                                onsubmit="return confirm('Hapus kategori {{ $parent->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>

                    {{-- ── Baris Sub-kategori (collapsible) ── --}}
                    @if ($parent->children_count > 0)
                        <tr class="p-0 border-0">
                            <td colspan="7" class="p-0 border-0">
                                <div class="collapse show" id="sub-{{ $parent->id }}">
                                    <table class="table table-sm align-middle mb-0" style="table-layout:fixed">
                                        <colgroup>
                                            <col style="width:32%">
                                            <col style="width:22%">
                                            <col style="width:9%">
                                            <col style="width:7%">
                                            <col style="width:7%">
                                            <col style="width:9%">
                                            <col style="width:14%">
                                        </colgroup>
                                        <tbody>
                                            @foreach ($parent->children as $child)
                                                <tr style="background:#f8f9ff">
                                                    <td style="padding-left:2.8rem">
                                                        <span class="text-muted me-1">↳</span>
                                                        <span class="badge bg-secondary-subtle text-secondary me-1">Sub</span>
                                                        {{ $child->name }}
                                                    </td>
                                                    <td><code class="small text-muted">{{ $child->slug }}</code></td>
                                                    <td class="text-center text-muted">—</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-light text-dark">{{ $child->products_count }}</span>
                                                    </td>
                                                    <td class="text-center text-muted">{{ $child->sort_order }}</td>
                                                    <td class="text-center">
                                                        <span class="badge {{ $child->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $child->is_active ? 'Aktif' : 'Nonaktif' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('admin.categories.edit', $child) }}"
                                                            class="btn btn-sm btn-outline-secondary">Edit</a>
                                                        <form action="{{ route('admin.categories.destroy', $child) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Hapus kategori {{ $child->name }}?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @endif

                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Rotate chevron icon saat collapse toggle
document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
    const target = document.querySelector(btn.dataset.bsTarget);
    target.addEventListener('hide.bs.collapse', () => {
        btn.querySelector('.toggle-icon').style.transform = 'rotate(-90deg)';
    });
    target.addEventListener('show.bs.collapse', () => {
        btn.querySelector('.toggle-icon').style.transform = 'rotate(0deg)';
    });
});
</script>
@endpush
