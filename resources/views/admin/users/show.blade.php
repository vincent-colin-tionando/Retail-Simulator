@extends('layouts.admin')

@section('title', 'Detail User')
@section('breadcrumb', 'User / Detail')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">{{ $user->name }}</h4>
        <small class="text-muted">
            <span class="badge {{ $user->role_badge_class }}">{{ $user->role_label }}</span>
            &nbsp;Bergabung {{ $user->created_at->format('d M Y') }}
        </small>
    </div>
    <div class="d-flex gap-2">
        @if ($user->role === 'distributor')
            <form action="{{ route('admin.users.verify', $user) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit"
                    class="btn btn-sm {{ $user->is_verified ? 'btn-outline-warning' : 'btn-success' }}">
                    @if ($user->is_verified)
                        <i class="bi bi-x-circle me-1"></i> Cabut Verifikasi
                    @else
                        <i class="bi bi-check-circle me-1"></i> Verifikasi Sekarang
                    @endif
                </button>
            </form>
        @endif
        <a href="{{ route('admin.users.edit', $user) }}"
           class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="btn btn-outline-secondary btn-sm">← Kembali</a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">

    {{-- Info Profil --}}
    <div class="col-md-5">
        <div class="card">
            <div class="card-header fw-semibold">Profil Pengguna</div>
            <div class="card-body p-0">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted ps-3" style="width:40%">Nama</td>
                        <td class="fw-semibold">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Email</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Telepon</td>
                        <td>{{ $user->phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3 align-top">Alamat</td>
                        <td>{{ $user->address ?? '—' }}</td>
                    </tr>
                    @if ($user->company_name)
                    <tr>
                        <td class="text-muted ps-3">Perusahaan</td>
                        <td>{{ $user->company_name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted ps-3">Role</td>
                        <td>
                            <span class="badge {{ $user->role_badge_class }}">
                                {{ $user->role_label }}
                            </span>
                        </td>
                    </tr>
                    @if ($user->role === 'distributor')
                    <tr>
                        <td class="text-muted ps-3">Status Verifikasi</td>
                        <td>
                            @if ($user->is_verified)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Terverifikasi
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock me-1"></i>Belum Diverifikasi
                                </span>
                                <div class="text-muted small mt-1">
                                    Belum bisa mendapatkan harga grosir.
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="col-md-3">
        <div class="card text-center mb-3">
            <div class="card-body py-4">
                <div class="display-5 fw-bold text-primary">{{ $orders->count() }}</div>
                <div class="text-muted mt-1 small">Total Order (10 terakhir)</div>
            </div>
        </div>
        <div class="card text-center">
            <div class="card-body py-4">
                <div class="fs-4 fw-bold text-success">
                    Rp {{ number_format($orders->sum('total_price'), 0, ',', '.') }}
                </div>
                <div class="text-muted mt-1 small">Total Belanja</div>
            </div>
        </div>
    </div>

    {{-- 10 Order Terakhir --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-semibold">10 Order Terakhir</div>
            <div class="card-body p-0">
                @if ($orders->isEmpty())
                    <div class="p-4 text-center text-muted">Belum ada riwayat order.</div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode Order</th>
                                <th class="text-center">Jml Item</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Status</th>
                                <th>Tanggal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                            <tr>
                                <td class="fw-semibold">{{ $order->order_code }}</td>
                                <td class="text-center">{{ $order->items->count() }}</td>
                                <td class="text-end">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $order->status_badge_class }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    {{ $order->created_at->format('d M Y') }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}"
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