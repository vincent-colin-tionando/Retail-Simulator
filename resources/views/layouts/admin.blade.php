<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Admin') — Retail Simulator</title>

        {{-- Bootstrap 5 CSS --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        {{-- Bootstrap Icons --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

        <style>
            /* ── Layout utama ───────────────────────────────────────────── */
            body { background-color: #f5f6fa; }

            #sidebar {
                width: 240px;
                min-height: 100vh;
                background: #1e2a3a;
                flex-shrink: 0;
                position: sticky;
                top: 0;
                height: 100vh;
                overflow-y: auto;
            }

            #main-content {
                flex: 1;
                min-width: 0;          
            }

            /* ── Sidebar brand ──────────────────────────────────────────── */
            .sidebar-brand {
                padding: 1.25rem 1.5rem;
                border-bottom: 1px solid rgba(255,255,255,.08);
            }

            /* ── Sidebar nav item ───────────────────────────────────────── */
            .sidebar-nav .nav-link {
                color: rgba(255,255,255,.72);
                padding: .5rem 1.5rem;
                border-radius: 6px;
                margin: 2px 8px;
                font-size: .9rem;
                transition: background .15s, color .15s;
            }

            .sidebar-nav .nav-link:hover,
            .sidebar-nav .nav-link.active {
                background: rgba(255,255,255,.1);
                color: #fff;
            }

            .sidebar-nav .nav-link i {
                width: 20px;
                margin-right: 8px;
                text-align: center;
            }

            /* ── Sidebar section label ──────────────────────────────────── */
            .nav-section-label {
                font-size: .7rem;
                text-transform: uppercase;
                letter-spacing: .08em;
                color: rgba(255,255,255,.35);
                padding: .85rem 1.5rem .25rem;
            }

            /* ── Badge merah di sidebar (order pending) ─────────────────── */
            .sidebar-badge {
                font-size: .68rem;
                padding: 2px 6px;
            }

            /* ── Top navbar ─────────────────────────────────────────────── */
            #topnav {
                background: #fff;
                border-bottom: 1px solid #e8ecf1;
            }

            /* ── Card default lebih bersih ──────────────────────────────── */
            .card { border: 1px solid #e8ecf1; }
            .card-header { background: #fff; }
        </style>
    </head>
    <body>
        <div class="d-flex">
            {{-- SIDEBAR --}}
            <nav id="sidebar">
                {{-- Brand --}}
                <div class="sidebar-brand">
                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                        <span class="text-white fw-bold fs-5">
                            <i class="bi bi-shop-window me-2 text-warning"></i>Retail Admin
                        </span>
                    </a>
                </div>

                <ul class="sidebar-nav nav flex-column pt-2">
                    {{-- Dashboard --}}
                    <li class="nav-item">
                        <a class="nav-link @active('admin/dashboard')" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>

                    {{-- Katalog --}}
                    <li><div class="nav-section-label">Katalog</div></li>

                    <li class="nav-item">
                        <a class="nav-link @active('admin/categories*')" href="{{ route('admin.categories.index') }}">
                            <i class="bi bi-tag"></i> Kategori
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link @active('admin/products*')" href="{{ route('admin.products.index') }}">
                            <i class="bi bi-box-seam"></i> Produk
                        </a>
                    </li>

                    {{-- Pengadaan --}}
                    <li><div class="nav-section-label">Pengadaan</div></li>

                    <li class="nav-item">
                        <a class="nav-link @active('admin/suppliers*')" href="{{ route('admin.suppliers.index') }}">
                            <i class="bi bi-building"></i> Supplier
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link @active('admin/purchases*')" href="{{ route('admin.purchases.index') }}">
                            <i class="bi bi-cart-plus"></i> Pembelian Stok
                        </a>
                    </li>

                    {{-- Penjualan --}}
                    <li><div class="nav-section-label">Penjualan</div></li>

                    <li class="nav-item">
                        <a class="nav-link @active('admin/orders*')" href="{{ route('admin.orders.index') }}">
                            <i class="bi bi-bag-check"></i> Pesanan
                            
                            {{-- Badge jumlah order pending -- diambil dari view share --}}
                            @php $pendingOrders = \App\Models\Order::where('status','pending')->count(); @endphp
                            @if ($pendingOrders > 0)
                                <span class="badge bg-danger sidebar-badge ms-1">{{ $pendingOrders }}</span>
                            @endif
                        </a>
                    </li>

                    {{-- Pengguna --}}
                    <li><div class="nav-section-label">Pengguna</div></li>

                    <li class="nav-item">
                        <a class="nav-link @active('admin/users*')" href="{{ route('admin.users.index') }}">
                            <i class="bi bi-people"></i> Manajemen User
                            {{-- Badge distributor menunggu verifikasi --}}
                            @php $pendingDist = \App\Models\User::where('role','distributor')->where('is_verified',false)->count(); @endphp
                            @if ($pendingDist > 0)
                                <span class="badge bg-warning text-dark sidebar-badge ms-1">{{ $pendingDist }}</span>
                            @endif
                        </a>
                    </li>
                </ul>

                {{-- Info user yang login di bawah sidebar --}}
                <div class="mt-auto p-3 border-top border-secondary" style="border-color:rgba(255,255,255,.1)!important; margin-top:auto">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center"
                            style="width:32px;height:32px;flex-shrink:0">
                            <i class="bi bi-person-fill text-white" style="font-size:.9rem"></i>
                        </div>
                        <div style="min-width:0">
                            <div class="text-white small text-truncate fw-semibold">
                                {{ auth()->user()->name ?? '' }}
                            </div>
                            <div class="text-secondary" style="font-size:.72rem">Admin</div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary w-100 text-secondary">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </nav>

            {{-- MAIN AREA --}}
            <div id="main-content" class="d-flex flex-column">
                {{-- Top navbar --}}
                <nav id="topnav" class="navbar px-4 py-2 sticky-top">
                    <span class="text-muted small">
                        <i class="bi bi-house-door me-1"></i>
                        @yield('breadcrumb', 'Dashboard')
                    </span>
                    <div class="ms-auto d-flex align-items-center gap-3">
                        <span class="text-muted small">
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>
                    </div>
                </nav>

                {{-- Page content --}}
                <main class="p-4 flex-grow-1">
                    @yield('content')
                </main>

                {{-- Footer --}}
                <footer class="text-center py-3 text-muted" style="font-size:.78rem; border-top:1px solid #e8ecf1">
                    &copy; {{ date('Y') }} Retail Simulator — Admin Panel
                </footer>
            </div>
        </div>

        {{-- Bootstrap 5 JS --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        @stack('scripts')
    </body>
</html>
