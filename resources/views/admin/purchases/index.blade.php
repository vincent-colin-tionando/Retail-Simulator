@extends('layouts.admin')

@section('title', 'Riwayat Pembelian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Riwayat Pembelian Stok</h4>
    <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary">
        + Catat Pembelian Baru
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

{{-- Filter --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('admin.purchases.index') }}" class="row g-2 align-items-end">
            {{-- Filter supplier --}}
            <div class="col-md-3">
                <select name="supplier_id" class="form-select form-select-sm">
                    <option value="">Semua Supplier</option>
                    @foreach ($suppliers as $sup)
                        <option value="{{ $sup->id }}" @selected(request('supplier_id') == $sup->id)>
                            {{ $sup->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            {{-- Filter status --}}
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="received"  @selected(request('status') === 'received')>Diterima</option>
                    <option value="pending"   @selected(request('status') === 'pending')>Menunggu</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan</option>
                </select>
            </div>
            {{-- Filter rentang tanggal --}}
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm"
                    value="{{ request('date_from') }}" title="Dari tanggal">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm"
                    value="{{ request('date_to') }}" title="Sampai tanggal">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
                <a href="{{ route('admin.purchases.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Riwayat Pembelian --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Invoice</th>
                        <th>Supplier</th>
                        <th>Tgl Transaksi</th>
                        <th class="text-center">Jml Produk</th>
                        <th class="text-end">Total Biaya</th>
                        <th class="text-center">Status</th>
                        <th>Dicatat Oleh</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                    <tr>
                        <td>
                            <a href="{{ route('admin.purchases.show', $purchase) }}"
                               class="fw-semibold text-decoration-none">
                                {{ $purchase->invoice_no }}
                            </a>
                        </td>
                        <td>{{ $purchase->supplier->name ?? '—' }}</td>
                        <td>{{ $purchase->purchased_at->format('d M Y') }}</td>
                        {{-- items_count diisi oleh withCount('items') di controller --}}
                        <td class="text-center">{{ $purchase->items_count }}</td>
                        <td class="text-end">
                            Rp {{ number_format($purchase->total_cost, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $purchase->status_badge_class }}">
                                {{ $purchase->status_label }}
                            </span>
                        </td>
                        <td>{{ $purchase->user->name ?? '—' }}</td>
                        <td class="text-end pe-3">
                            <a href="{{ route('admin.purchases.show', $purchase) }}"
                               class="btn btn-sm btn-outline-info">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Belum ada riwayat pembelian.
                            <a href="{{ route('admin.purchases.create') }}">Catat sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Paginasi --}}
@if ($purchases->hasPages())
    <div class="mt-3">
        {{ $purchases->links() }}
    </div>
@endif
@endsection
