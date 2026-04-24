@extends('layouts.checkout')

@section('title', 'Checkout')

@section('content')

{{-- ── Breadcrumb ── --}}
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item">
            <a href="{{ route('shop.index') }}">Toko</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('shop.cart.index') }}">Keranjang</a>
        </li>
        <li class="breadcrumb-item active">Checkout</li>
    </ol>
</nav>

<h4 class="fw-bold mb-4">
    <i class="bi bi-bag-check me-2"></i>Checkout
</h4>

{{-- ── Error stok (jika ada dari submit sebelumnya) ── --}}
@if ($errors->has('stock'))
    <div class="alert alert-danger alert-dismissible fade show mb-4">
        <strong><i class="bi bi-exclamation-triangle me-1"></i>
            Tidak dapat memproses pesanan:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->get('stock') as $err)
                @if (is_array($err))
                    @foreach ($err as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                @else
                    <li>{{ $err }}</li>
                @endif
            @endforeach
        </ul>
        <p class="mb-0 mt-2 small">
            Silakan <a href="{{ route('shop.cart.index') }}" class="alert-link">kembali ke keranjang</a> dan sesuaikan jumlah pesanan.
        </p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('shop.checkout.store') }}" method="POST" id="checkoutForm">
    @csrf
    <div class="row g-4">

        {{-- ═══ KOLOM KIRI — Form Detail Pengiriman & Pembayaran ═══ --}}
        <div class="col-lg-7">

            {{-- ── Info Pembeli ── --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-person me-1"></i> Informasi Pembeli
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label small text-muted">Nama</label>
                            <div class="fw-semibold">{{ $user->name }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small text-muted">Email</label>
                            <div>{{ $user->email }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small text-muted">Telepon</label>
                            <div>{{ $user->phone ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small text-muted">Role</label>
                            <div>
                                <span class="badge {{ $user->role_badge_class }}">
                                    {{ $user->role_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            Bukan kamu?
                            <a href="{{ route('logout') }}"onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                        </small>
                    </div>
                </div>
            </div>

            {{-- ── Alamat Pengiriman ── --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-geo-alt me-1"></i> Alamat Pengiriman
                    <span class="text-danger ms-1">*</span>
                </div>
                <div class="card-body">
                    <textarea name="shipping_address" rows="3"
                        class="form-control @error('shipping_address') is-invalid @enderror"
                        placeholder="Masukkan alamat lengkap pengiriman..."
                        required>{{ old('shipping_address', $user->address) }}</textarea>
                    {{--
                        old() akan mengisi ulang field jika validasi gagal,
                        $user->address adalah default dari profil user.
                    --}}
                    @error('shipping_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Pastikan alamat lengkap termasuk nama jalan, nomor,
                        kelurahan, kota, dan kode pos.
                    </div>
                </div>
            </div>

            {{-- ── Metode Pembayaran ── --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-credit-card me-1"></i> Metode Pembayaran
                    <span class="text-danger ms-1">*</span>
                </div>
                <div class="card-body">

                    @php
                        $methods = [
                            'Transfer Bank BCA' => 'bi-bank',
                            'Transfer Bank Mandiri' => 'bi-bank',
                            'Transfer Bank BRI' => 'bi-bank',
                            'QRIS' => 'bi-qr-code',
                            'COD (Bayar di Tempat)' => 'bi-cash-coin',
                            'Tunai' => 'bi-wallet2',
                        ];
                    @endphp

                    <div class="row g-2">
                        @foreach ($methods as $method => $icon)
                            @php $methodId = 'pay_' . Str::slug($method); @endphp
                            <div class="col-sm-6">
                                <div class="form-check border rounded p-2 ps-4 @error('payment_method') border-danger @enderror"
                                    style="cursor:pointer" onclick="document.getElementById('{{ $methodId }}').click()">
                                    <input class="form-check-input"
                                        type="radio" name="payment_method" id="{{ $methodId }}"
                                        value="{{ $method }}" @checked(old('payment_method') === $method) required>
                                    <label class="form-check-label w-100" for="{{ $methodId }}"
                                        style="cursor:pointer">
                                        <i class="bi {{ $icon }} me-2 text-muted"></i>
                                        {{ $method }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @error('payment_method')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- ── Catatan (opsional) ── --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-chat-text me-1"></i> Catatan untuk Admin
                    <span class="text-muted fw-normal small ms-1">(opsional)</span>
                </div>
                <div class="card-body">
                    <textarea name="notes" rows="2" class="form-control"
                        placeholder="Contoh: Tolong dikemas dengan bubble wrap.">{{ old('notes') }}</textarea>
                </div>
            </div>

        </div>

        {{-- ══ KOLOM KANAN — Ringkasan Pesanan ══ --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm sticky-top" style="top:80px">
                <div class="card-header bg-white fw-bold">
                    Ringkasan Pesanan
                    <span class="badge bg-secondary ms-2">{{ count($cartItems) }} item</span>
                </div>
                <div class="card-body p-0">

                    {{-- Daftar item --}}
                    <div class="p-3">
                        @foreach ($cartItems as $item)
                            <div class="d-flex gap-3 mb-3">
                                <img src="{{ $item['image_url'] }}"
                                    style="width:48px;height:48px;object-fit:contain;
                                            background:#f8f9fa;border-radius:6px;
                                            padding:3px;flex-shrink:0">
                                <div class="flex-grow-1 min-width-0">
                                    <div class="small fw-semibold text-truncate">
                                        {{ $item['name'] }}
                                    </div>
                                    <div class="text-muted" style="font-size:.78rem">
                                        {{ $item['quantity'] }} × Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="small fw-semibold flex-shrink-0 text-end">
                                    Rp {{ number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-top px-3 py-3">
                        {{-- Total --}}
                        <div class="d-flex justify-content-between fw-bold fs-5 mb-2">
                            <span>Total Pembayaran</span>
                            <span class="text-primary">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </span>
                        </div>
                        
                        <div class="text-muted mb-3" style="font-size:.78rem">
                            Belum termasuk ongkos kirim.
                        </div>

                        {{-- Submit checkout --}}
                        <button type="submit" class="btn btn-primary w-100 fw-semibold py-2"
                                id="submitBtn">
                            <i class="bi bi-lock-fill me-1"></i>
                            Buat Pesanan Sekarang
                        </button>

                        <p class="text-center text-muted mt-2 mb-0" style="font-size:.75rem">
                            <i class="bi bi-shield-check me-1 text-success"></i>
                            Harga sudah dikonfirmasi dari server.
                            Tidak bisa dimanipulasi.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>