@extends('layouts.shop')

@section('title', 'Katalog Produk')

@push('styles')
<style>
    /* ── Sidebar filter ── */
    .filter-sidebar .category-link {
        display: block;
        padding: .4rem .75rem;
        border-radius: 6px;
        color: #495057;
        text-decoration: none;
        font-size: .9rem;
        transition: background .15s;
    }
    .filter-sidebar .category-link:hover { background: #f0f4f8; }
    .filter-sidebar .category-link.active {
        background: #1e2a3a;
        color: #fff;
        font-weight: 600;
    }

    /* ── Product card ── */
    .product-card {
        border: 1px solid #e8ecf1;
        border-radius: 12px;
        overflow: hidden;
        transition: box-shadow .2s, transform .2s;
        background: #fff;
    }
    .product-card:hover {
        box-shadow: 0 6px 24px rgba(0,0,0,.1);
        transform: translateY(-3px);
    }
    .product-card .card-img-top {
        height: 180px;
        object-fit: contain;
        background: #f8f9fa;
        padding: 1rem;
    }
    .product-card .stock-badge {
        position: absolute;
        top: 10px; right: 10px;
    }
</style>
@endpush

@section('content')

{{-- ── Breadcrumb ── --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item">
            <a href="{{ route('shop.index') }}">Toko</a>
        </li>
        @if (request('category'))
            <li class="breadcrumb-item active">
                {{ $categories->firstWhere('slug', request('category'))?->name ?? request('category') }}
            </li>
        @elseif (request('search'))
            <li class="breadcrumb-item active">
                Hasil pencarian: "{{ request('search') }}"
            </li>
        @else
            <li class="breadcrumb-item active">Semua Produk</li>
        @endif
    </ol>
</nav>

{{-- ── Banner harga untuk guest ── --}}
@guest
    <div class="alert alert-info d-flex align-items-center gap-3 mb-4">
        <i class="bi bi-info-circle-fill fs-4 flex-shrink-0"></i>
        <div>
            <a href="{{ route('login') }}" class="alert-link">Login</a> atau
            <a href="{{ route('register') }}" class="alert-link">daftar akun</a>
            untuk berbelanja.
        </div>
    </div>
@endguest

{{-- Banner khusus distributor belum verifikasi --}}
@auth
    @if (auth()->user()->role === 'distributor' && ! auth()->user()->is_verified)
        <div class="alert alert-warning d-flex align-items-center gap-3 mb-4">
            <i class="bi bi-clock-history fs-4 flex-shrink-0"></i>
            <div>
                <strong>Akun distributor Anda sedang ditinjau.</strong>
                Setelah diverifikasi oleh admin, Anda akan mendapatkan
                harga grosir dan bisa melakukan pembelian.
            </div>
        </div>
    @endif
@endauth

<div class="row g-4">

    {{-- SIDEBAR FILTER (kiri) --}}
    <div class="col-lg-3">
        <div class="filter-sidebar card border-0 shadow-sm p-3">

            <h6 class="fw-bold text-uppercase mb-3"
                style="font-size:.78rem; letter-spacing:.05em; color:#6c757d">
                <i class="bi bi-funnel me-1"></i> Filter Kategori
            </h6>

            {{-- Semua kategori --}}
            <a href="{{ route('shop.index', array_filter(['search' => request('search')])) }}"
               class="category-link {{ ! request('category') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3-gap me-2"></i>Semua Produk
                <span class="badge bg-secondary float-end">
                    {{ $products->total() }}
                </span>
            </a>

            <hr class="my-2">

            {{-- Daftar kategori --}}
            @foreach ($categories as $cat)
                <a href="{{ route('shop.index', array_filter([
                        'category' => $cat->slug,
                        'search'   => request('search'),
                    ])) }}"
                   class="category-link {{ request('category') === $cat->slug ? 'active' : '' }}">
                    @if ($cat->isChild())
                        <span class="ms-2 text-muted me-1" style="font-size:.7rem">└</span>
                    @else
                        <i class="bi bi-tag me-2"></i>
                    @endif
                    {{ $cat->name }}
                </a>
            @endforeach

            {{-- Reset filter --}}
            @if (request()->hasAny(['category', 'search']))
                <hr class="my-2">
                <a href="{{ route('shop.index') }}"
                   class="btn btn-outline-secondary btn-sm w-100 mt-1">
                    <i class="bi bi-x-circle me-1"></i> Reset Filter
                </a>
            @endif
        </div>
    </div>

    {{-- GRID PRODUK (kanan) --}}
    <div class="col-lg-9">

        {{-- Header hasil --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="mb-0 text-muted small">
                Menampilkan
                <strong>{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</strong>
                dari <strong>{{ $products->total() }}</strong> produk
                @if (request('search'))
                    untuk "<strong>{{ request('search') }}</strong>"
                @endif
            </p>
        </div>

        @if ($products->isEmpty())
            <div class="card border-dashed p-5 text-center text-muted">
                <i class="bi bi-search fs-1 mb-3 d-block opacity-25"></i>
                <h6>Produk tidak ditemukan</h6>
                <p class="small mb-3">
                    Coba kata kunci lain atau pilih kategori yang berbeda.
                </p>
                <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary btn-sm">
                    Lihat Semua Produk
                </a>
            </div>
        @else
            {{-- Grid 3 kolom --}}
            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">
                @foreach ($products as $product)
                <div class="col">
                    <div class="product-card h-100 d-flex flex-column">

                        {{-- Gambar + badge stok --}}
                        <div class="position-relative">
                            <a href="{{ route('shop.products.show', $product) }}">
                                <img src="{{ $product->image_url }}"
                                     alt="{{ $product->name }}"
                                     class="card-img-top">
                            </a>
                            @if ($product->stock <= 0)
                                <span class="badge bg-danger stock-badge">Habis</span>
                            @elseif ($product->isLowStock())
                                <span class="badge bg-warning text-dark stock-badge">
                                    Sisa {{ $product->stock }}
                                </span>
                            @endif
                        </div>

                        <div class="p-3 d-flex flex-column flex-grow-1">
                            {{-- Kategori --}}
                            @if ($product->category)
                                <span class="text-muted" style="font-size:.72rem">
                                    {{ $product->category->name }}
                                </span>
                            @endif

                            {{-- Nama produk --}}
                            <a href="{{ route('shop.products.show', $product) }}"
                               class="text-dark text-decoration-none fw-semibold mt-1 mb-3"
                               style="line-height:1.3; font-size:.95rem">
                                {{ $product->name }}
                            </a>

                            {{-- Tombol harga — dikerjakan oleh Blade component --}}
                            {{-- Komponen ini menangani semua kondisi:          --}}
                            {{-- tamu / consumer / distributor / stok habis     --}}
                            <div class="mt-auto">
                                <x-price-button :product="$product" :compact="true" />
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Paginasi --}}
            @if ($products->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

@endsection
