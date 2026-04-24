<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Checkout') — Retail Simulator</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 60px;
        }
        #checkout-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            background: #fff;
            border-bottom: 2px solid #f0b429;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
            padding: .75rem 0;
            z-index: 1030;
        }
        #checkout-header .navbar-brand span { color: #f0b429; }
    </style>
    @stack('styles')
</head>
<body>

{{-- Header --}}
<header id="checkout-header">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand fw-bold fs-5 text-decoration-none" href="{{ route('shop.index') }}">
            <i class="bi bi-shop-window me-1" style="color:#f0b429"></i>
            <span>Hannochs</span><span class="text-dark fw-normal"> Store</span>
        </a>
        {{-- Progress steps --}}
        <div class="d-none d-md-flex align-items-center gap-2 small text-muted">
            <span class="text-muted"><i class="bi bi-cart3 me-1"></i>Keranjang</span>
            <i class="bi bi-chevron-right"></i>
            <span class="fw-semibold text-dark"><i class="bi bi-bag-check me-1"></i>Checkout</span>
            <i class="bi bi-chevron-right"></i>
            <span>Konfirmasi</span>
        </div>
        {{-- Tombol kembali ke cart --}}
        <a href="{{ route('shop.cart.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</header>

{{-- Flash Messages --}}
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0 border-0">
        <div class="container">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

{{-- Konten Utama --}}
<main class="py-4 flex-grow-1">
    <div class="container">
        @yield('content')
    </div>
</main>

{{-- Footer minimal --}}
<footer class="bg-dark text-white-50 py-3 text-center">
    <small>&copy; {{ date('Y') }} Retail Simulator — Transaksi aman & terenkripsi</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>