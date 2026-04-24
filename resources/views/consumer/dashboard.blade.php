@extends('layouts.shop')

@section('title', 'Dashboard Consumer')

@section('content')

@php
    $user       = auth()->user();
    $cartCount  = \App\Models\CartItem::where('user_id', $user->id)->count();
    $orders     = \App\Models\Order::where('user_id', $user->id)
                    ->latest()->take(5)->get();
    $totalSpent = \App\Models\Order::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->sum('total_price');
@endphp

{{-- ── Salam ── --}}
<div class="mb-4">
    <h4 class="fw-bold mb-0">
        Halo, {{ $user->name }}! 
    </h4>
    <p class="text-muted mb-0">Selamat datang di Hannochs Store.</p>
</div>

{{-- ── Kartu Ringkasan ── --}}
<div class="row g-3 mb-4">

    <div class="col-sm-4">
        <a href="{{ route('shop.cart.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 text-center py-4">
                <div class="display-5 fw-bold text-warning mb-1">{{ $cartCount }}</div>
                <div class="text-muted small">
                    <i class="bi bi-cart3 me-1"></i>Item di Keranjang
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-4">
        <a href="{{ route('shop.orders.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 text-center py-4">
                <div class="display-5 fw-bold text-primary mb-1">{{ $orders->count() }}</div>
                <div class="text-muted small">
                    <i class="bi bi-bag-check me-1"></i>Pesanan Saya
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100 text-center py-4">
            <div class="fs-4 fw-bold text-success mb-1">
                Rp {{ number_format($totalSpent, 0, ',', '.') }}
            </div>
            <div class="text-muted small">
                <i class="bi bi-cash-stack me-1"></i>Total Belanja
            </div>
        </div>
    </div>
</div>

{{-- ── Dua Kolom ── --}}
<div class="row g-4">

    {{-- Riwayat Pesanan Terakhir --}}
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                <span>Pesanan Terbaru</span>
                <a href="{{ route('shop.orders.index') }}"
                   class="btn btn-sm btn-outline-secondary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                @if ($orders->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-bag-x opacity-25 d-block fs-2 mb-2"></i>
                        Belum ada pesanan.
                        <a href="{{ route('shop.index') }}">Mulai belanja</a>
                    </div>
                @else
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Kode</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('shop.orders.show', $order) }}"
                                       class="text-decoration-none fw-semibold small">
                                        {{ $order->order_code }}
                                    </a>
                                    <div class="text-muted" style="font-size:.72rem">
                                        {{ $order->created_at->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="text-end small">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $order->status_badge_class }}"
                                          style="font-size:.68rem">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    {{-- Aksi Cepat --}}
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Aksi Cepat</div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('shop.index') }}"
                   class="btn btn-primary w-100 text-start">
                    <i class="bi bi-search me-2"></i>Jelajahi Produk
                </a>
                <a href="{{ route('shop.cart.index') }}"
                   class="btn btn-outline-warning w-100 text-start">
                    <i class="bi bi-cart3 me-2"></i>Lihat Keranjang
                    @if ($cartCount > 0)
                        <span class="badge bg-danger ms-1">{{ $cartCount }}</span>
                    @endif
                </a>
                <a href="{{ route('shop.orders.index') }}"
                   class="btn btn-outline-primary w-100 text-start">
                    <i class="bi bi-bag-check me-2"></i>Riwayat Pesanan
                </a>
                <a href="{{ route('profile.edit') }}"
                   class="btn btn-outline-secondary w-100 text-start">
                    <i class="bi bi-person me-2"></i>Edit Profil
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
