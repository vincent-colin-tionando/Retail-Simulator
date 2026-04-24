<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Toko') — Retail Simulator</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── Topbar ─── */
        #topbar {
            background: #1e2a3a;
            font-size: .8rem;
            padding: .35rem 0;
        }

        /* ── Navbar ─── */
        #shopnav { border-bottom: 2px solid #f0b429; box-shadow: 0 2px 8px rgba(0,0,0,.06); }

        #shopnav .navbar-brand span { color: #f0b429; }

        /* Ikon keranjang — badge merah menampilkan jumlah item */
        .cart-icon { position: relative; }
        .cart-icon .badge {
            position: absolute;
            top: -6px; right: -8px;
            font-size: .6rem;
        }

        /* ── Role badge di navbar ─── */
        .role-pill {
            font-size: .72rem;
            padding: 3px 10px;
            border-radius: 20px;
        }

        /* ── Flash message container ─── */
        .flash-container { min-height: 0; }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── Topbar: info singkat & admin link ── --}}
<div id="topbar" class="text-white-50">
    <div class="container d-flex justify-content-between align-items-center">
        <small>
            <i class="bi bi-truck me-1"></i> Pengiriman ke seluruh Indonesia
        </small>
        <div class="d-flex align-items-center gap-3">
            @auth
                {{-- Jika admin, tampilkan link ke panel admin --}}
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}"
                       class="text-warning text-decoration-none small">
                        <i class="bi bi-speedometer2 me-1"></i>Panel Admin
                    </a>
                @endif
            @endauth
        </div>
    </div>
</div>

{{-- ── Navbar Utama ── --}}
<nav id="shopnav" class="navbar navbar-expand-lg navbar-white bg-white sticky-top">
    <div class="container">

        {{-- Brand --}}
        <a class="navbar-brand fw-bold fs-5" href="{{ route('shop.index') }}">
            <i class="bi bi-shop-window me-1" style="color:#f0b429"></i>
            <span>Hannochs</span> <span class="text-dark fw-normal">Store</span>
        </a>

        {{-- Search bar (tengah) --}}
        <form class="d-none d-lg-flex mx-4 flex-grow-1"
              action="{{ route('shop.index') }}" method="GET">
            {{-- Pertahankan filter kategori saat search --}}
            @if (request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="input-group">
                <input type="text" name="search" class="form-control"
                    placeholder="Cari produk, mis: LED Bulb 9W..."
                    value="{{ request('search') }}">
                <button class="btn btn-warning fw-semibold" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navCollapse">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">

                {{-- Keranjang/Cart — untuk SEMUA user termasuk guest --}}
                @php
                    $cartSessionCount = array_sum(
                        array_column(session('cart', []), 'quantity')
                    );
                @endphp
                <li class="nav-item">
                    <a class="nav-link cart-icon" href="{{ route('shop.cart.index') }}">
                        <i class="bi bi-cart3 fs-5"></i>
                        @if ($cartSessionCount > 0)
                            <span class="badge bg-danger rounded-pill">{{ $cartSessionCount }}</span>
                        @endif
                    </a>
                </li>
               
                {{-- User dropdown --}}
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                           href="#" data-bs-toggle="dropdown">
                            {{-- Badge role --}}
                            @php $u = auth()->user(); @endphp
                            <span class="role-pill {{ $u->role_badge_class }}">
                                {{ $u->role_label }}
                            </span>
                            <span class="d-none d-lg-inline">{{ $u->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <span class="dropdown-item-text text-muted small">
                                    {{ $u->email }}
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>

                            @if ($u->role !== 'admin')
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-cart3 me-2"></i>Panel Admin
                                    </a>
                                </li>
                            @elseif($u->role !== 'distributor')
                                <li>
                                    <a class="dropdown-item" href="{{ route('distributor.dashboard') }}">
                                        <i class="bi bi-bag-check me-2"></i>Dashboard
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a class="dropdown-item" href="{{ route('consumer.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i>Profil Saya
                                </a>
                            </li>

                            @if ($u->role !== 'admin')
                                <li>
                                    <a class="dropdown-item" href="{{ route('shop.cart.index') }}">
                                        <i class="bi bi-cart3 me-2"></i>Keranjang
                                        @if ($cartSessionCount > 0)
                                            <span class="badge bg-danger ms-1">
                                                {{ $cartSessionCount }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('shop.orders.index') }}">
                                        <i class="bi bi-bag-check me-2"></i>Pesanan Saya
                                    </a>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>

                @else
                    {{-- Tamu: tampilkan tombol login & register --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-warning btn-sm fw-semibold"
                           href="{{ route('register') }}">Daftar</a>
                    </li>
                @endauth

            </ul>

            {{-- Search bar untuk mobile --}}
            <form class="d-lg-none mt-2" action="{{ route('shop.index') }}" method="GET">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control"
                        placeholder="Cari produk..."
                        value="{{ request('search') }}">
                    <button class="btn btn-warning" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</nav>

{{-- ── Flash Messages ── --}}
<div class="flash-container">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0 rounded-0 border-0">
            <div class="container">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0 border-0">
            <div class="container">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
</div>

{{-- ── Main Content ── --}}
<main class="py-4 flex-grow-1">
    <div class="container">
        @yield('content')
    </div>
</main>

{{-- ── Footer ── --}}
<footer class="bg-dark text-white-50 py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
                <h6 class="text-white fw-bold mb-2">
                    <i class="bi bi-shop-window me-1" style="color:#f0b429"></i>
                    Hannochs Store
                </h6>
                <small>Produk pencahayaan & kelistrikan terpercaya.</small>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <h6 class="text-white fw-bold mb-2">Akun</h6>
                <ul class="list-unstyled small mb-0">
                    @auth
                        <li>
                            <a href="{{ route('shop.orders.index') }}"
                               class="text-white-50 text-decoration-none">
                                Pesanan Saya
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.edit') }}"
                               class="text-white-50 text-decoration-none">
                                Profil Saya
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('login') }}"
                               class="text-white-50 text-decoration-none">Login</a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}"
                               class="text-white-50 text-decoration-none">Daftar Akun</a>
                        </li>
                    @endauth
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-white fw-bold mb-2">Katalog</h6>
                <ul class="list-unstyled small mb-0">
                    <li>
                        <a href="{{ route('shop.index') }}"
                           class="text-white-50 text-decoration-none">Semua Produk</a>
                    </li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary my-3">
        <div class="text-center">
            <small>&copy; {{ date('Y') }} Retail Simulator</small>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
