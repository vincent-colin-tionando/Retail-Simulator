@extends('layouts.admin')

@section('title', 'Manajemen Supplier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Manajemen Supplier</h4>
    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
        + Tambah Supplier
    </a>
</div>

{{-- Flash Messages --}}
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
        <form method="GET" action="{{ route('admin.suppliers.index') }}" class="row g-2 align-items-end">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Cari nama, email, atau telepon..."
                    value="{{ request('search') }}">
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
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Supplier --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Nama Supplier</th>
                        <th>Kontak Person</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th class="text-center">Total Pembelian</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                    <tr>
                        {{-- Nomor urut tetap konsisten antar halaman --}}
                        <td class="text-muted">{{ $suppliers->firstItem() + $loop->index }}</td>
                        <td>
                            <strong>{{ $supplier->name }}</strong>
                            @if ($supplier->notes)
                                {{-- Tooltip catatan supplier, tanpa memenuhi kolom --}}
                                <span class="ms-1 text-muted" style="cursor:help"
                                      title="{{ $supplier->notes }}">
                                    <small>ⓘ</small>
                                </span>
                            @endif
                        </td>
                        <td>{{ $supplier->contact_person ?? '—' }}</td>
                        <td>{{ $supplier->phone ?? '—' }}</td>
                        <td>{{ $supplier->email ?? '—' }}</td>
                        <td class="text-center">
                            {{-- purchases_count diisi oleh withCount('purchases') di controller --}}
                            <span class="badge bg-info text-dark">
                                {{ $supplier->purchases_count }} invoice
                            </span>
                        </td>
                        <td class="text-center">
                            @if ($supplier->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('admin.suppliers.edit', $supplier) }}"
                               class="btn btn-sm btn-outline-primary">Edit</a>

                            {{-- Tombol hapus hanya muncul jika belum punya riwayat pembelian --}}
                            @if ($supplier->purchases_count === 0)
                                <form action="{{ route('admin.suppliers.destroy', $supplier) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus supplier {{ addslashes($supplier->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            @else
                                {{-- Tombol nonaktifkan sebagai alternatif hapus --}}
                                @if ($supplier->is_active)
                                    <form action="{{ route('admin.suppliers.update', $supplier) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        {{-- Kirim semua field yang diperlukan (hidden) --}}
                                        <input type="hidden" name="name" value="{{ $supplier->name }}">
                                        <input type="hidden" name="contact_person" value="{{ $supplier->contact_person }}">
                                        <input type="hidden" name="phone" value="{{ $supplier->phone }}">
                                        <input type="hidden" name="email" value="{{ $supplier->email }}">
                                        <input type="hidden" name="address" value="{{ $supplier->address }}">
                                        <input type="hidden" name="notes" value="{{ $supplier->notes }}">
                                        <input type="hidden" name="is_active" value="0">
                                        <button type="submit" class="btn btn-sm btn-outline-warning"
                                                onclick="return confirm('Nonaktifkan supplier ini?')">
                                            Nonaktifkan
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Tidak ada data supplier.
                            <a href="{{ route('admin.suppliers.create') }}">Tambah sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Paginasi --}}
@if ($suppliers->hasPages())
    <div class="mt-3">
        {{ $suppliers->links() }}
    </div>
@endif
@endsection
