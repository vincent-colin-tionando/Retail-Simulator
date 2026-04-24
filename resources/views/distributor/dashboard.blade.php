@extends('layouts.shop')

@section('title', 'Dashboard Distributor')

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
    <p class="text-muted mb-0">
        {{ $user->company_name ?? 'Distributor' }} — Hannochs Store
    </p>
</div>

{{-- ═══
    BANNER STATUS VERIFIKASI
    Ditampilkan paling atas karena ini informasi paling penting
    bagi distributor baru yang belum bisa berbelanja. 
    ═══ --}}
@if (! $user->is_verified)
    <div class="alert alert-warning d-flex gap-3 mb-4">
        <i class="bi bi-clock-history fs-3 flex-shrink-0 mt-1"></i>
        <div>
            <h6 class="fw-bold mb-1">Akun Anda Sedang Dalam Proses Verifikasi</h6>
            <p class="mb-2 small">
                Tim admin kami sedang meninjau akun distributor Anda.
                Setelah diverifikasi, Anda akan mendapatkan:
            </p>
            <ul class="small mb-2">
                <li>Harga grosir distributor (lebih murah dari harga eceran)</li>
                <li>Akses untuk melakukan pembelian dalam jumlah besar</li>
            </ul>
            <small class="text-muted">
                Proses verifikasi biasanya memakan waktu 1×24 jam.
                Jika ada pertanyaan, hubungi admin kami.
            </small>
        </div>
    </div>
@else
    <div class="alert alert-success d-flex gap-3 mb-4 py-2">
        <i class="bi bi-check-circle-fill flex-shrink-0 mt-1"></i>
        <div>
            <strong>Akun Terverifikasi.</strong>
            Anda mendapatkan harga grosir distributor pada semua produk.
        </div>
    </div>
@endif

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
                <div class="display-5 fw-bold text-primary mb-1">
                    {{ $orders->count() }}
                </div>
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
                <i class="bi bi-cash-stack me-1"></i>Total Pembelian
            </div>
        </div>
    </div>
</div>

{{-- ── Dua Kolom ── --}}
<div class="row g-4">

    {{-- Riwayat Pesanan --}}
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
                        @if ($user->is_verified)
                            <a href="{{ route('shop.index') }}">Mulai belanja</a>
                        @else
                            Selesaikan verifikasi terlebih dahulu.
                        @endif
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

    {{-- Aksi Cepat + Info Akun --}}
    <div class="col-md-5">

        {{-- Info Akun Distributor --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Info Akun</div>
            <div class="card-body p-0">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted ps-3" style="width:45%">Nama</td>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Perusahaan</td>
                        <td>{{ $user->company_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Harga</td>
                        <td>
                            @if ($user->is_verified)
                                <span class="badge bg-primary">Grosir Distributor</span>
                            @else
                                <span class="badge bg-secondary">Eceran (belum verif)</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Aksi Cepat --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Aksi Cepat</div>
            <div class="card-body d-flex flex-column gap-2">
                @if ($user->is_verified)
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
                @else
                    <a href="{{ route('shop.index') }}"
                       class="btn btn-outline-secondary w-100 text-start">
                        <i class="bi bi-eye me-2"></i>Lihat Katalog (Harga Tersembunyi)
                    </a>
                @endif
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
