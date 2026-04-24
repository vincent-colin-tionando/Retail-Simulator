@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')
@section('breadcrumb', 'Pesanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        Manajemen Pesanan
        @if ($pendingCount > 0)
            <span class="badge bg-warning text-dark ms-2" style="font-size:.75rem">
                {{ $pendingCount }} menunggu
            </span>
        @endif
    </h4>
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

{{-- Filter --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="pending"    @selected(request('status')==='pending')>Menunggu</option>
                    <option value="processing" @selected(request('status')==='processing')>Diproses</option>
                    <option value="completed"  @selected(request('status')==='completed')>Selesai</option>
                    <option value="cancelled"  @selected(request('status')==='cancelled')>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="buyer_role" class="form-select form-select-sm">
                    <option value="">Semua Role</option>
                    <option value="consumer"    @selected(request('buyer_role')==='consumer')>Consumer</option>
                    <option value="distributor" @selected(request('buyer_role')==='distributor')>Distributor</option>
                </select>
            </div>
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
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode Order</th>
                        <th>Pembeli</th>
                        <th class="text-center">Role</th>
                        <th class="text-center">Jml Item</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Status</th>
                        <th>Tanggal</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                    <tr class="{{ $order->status === 'pending' ? 'table-warning' : '' }}">
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="fw-semibold text-decoration-none">
                                {{ $order->order_code }}
                            </a>
                        </td>
                        <td>{{ $order->user->name ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $order->buyer_role === 'distributor' ? 'bg-primary' : 'bg-success' }}">
                                {{ ucfirst($order->buyer_role) }}
                            </span>
                        </td>
                        <td class="text-center">{{ $order->items_count }}</td>
                        <td class="text-end">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $order->status_badge_class }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="text-end pe-3">
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="btn btn-sm btn-outline-info">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Tidak ada data pesanan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if ($orders->hasPages())
    <div class="mt-3">{{ $orders->links() }}</div>
@endif
@endsection