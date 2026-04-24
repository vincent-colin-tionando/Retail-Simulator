@extends('layouts.shop')

@section('title', $product->name)

@section('content')

{{-- ── Breadcrumb ─── --}}
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item">
            <a href="{{ route('shop.index') }}">Toko</a>
        </li>
        @if ($product->category)
            <li class="breadcrumb-item">
                <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}">
                    {{ $product->category->name }}
                </a>
            </li>
        @endif
        <li class="breadcrumb-item active">{{ $product->name }}</li>
    </ol>
</nav>

<div class="row g-5">

    {{-- ── Gambar Produk --}}
    <div class="col-md-5">
        <div class="card border-0 shadow-sm p-3" style="border-radius:16px">
            <img src="{{ $product->image_url }}"
                 alt="{{ $product->name }}"
                 class="img-fluid"
                 style="max-height:360px; object-fit:contain">
        </div>
    </div>

    {{-- ── Detail & Aksi --}}
    <div class="col-md-7">

        {{-- Kategori --}}
        @if ($product->category)
            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}"
               class="badge bg-light text-primary border border-primary text-decoration-none mb-2">
                {{ $product->category->full_name }}
            </a>
        @endif

        {{-- Nama Produk --}}
        <h2 class="fw-bold mb-1">{{ $product->name }}</h2>

        {{-- SKU --}}
        <p class="text-muted small mb-3">
            SKU: <code>{{ $product->sku }}</code>
        </p>

        {{-- ── Blok Harga & Tombol --}}
        {{--
            <x-price-button> digunakan dengan :compact="false" (default)
            sehingga ditampilkan dalam format detail:
            - Harga besar
            - Input quantity
            - Tombol "Tambah ke Keranjang"

            Semua kondisi (tamu / consumer / distributor / stok habis)
            ditangani di dalam komponen — view ini tidak perlu tahu.
        --}}
        <div class="card border-0 bg-light p-4 mb-4" style="border-radius:12px">
            <x-price-button :product="$product" :compact="false" />
        </div>

        {{-- ── Informasi Stok ────── --}}
        <div class="d-flex gap-4 mb-4 text-sm">
            <div>
                <span class="text-muted small d-block">Ketersediaan</span>
                @if ($product->stock > 0)
                    <span class="text-success fw-semibold">
                        <i class="bi bi-check-circle me-1"></i>
                        Tersedia ({{ $product->stock }} {{ $product->unit }})
                    </span>
                @else
                    <span class="text-danger fw-semibold">
                        <i class="bi bi-x-circle me-1"></i>Stok Habis
                    </span>
                @endif
            </div>
            <div>
                <span class="text-muted small d-block">Satuan</span>
                <span class="fw-semibold">{{ $product->unit }}</span>
            </div>
        </div>

        {{-- ── Deskripsi Produk ───── --}}
        @if ($product->description)
            <div>
                <h6 class="fw-bold mb-2">Deskripsi Produk</h6>
                <p class="text-muted" style="line-height:1.8">
                    {{ $product->description }}
                </p>
            </div>
        @endif
    </div>
</div>

{{-- ── Produk Terkait ─── --}}
@if ($related->isNotEmpty())
    <hr class="my-5">
    <h5 class="fw-bold mb-4">Produk Serupa</h5>
    <div class="row row-cols-2 row-cols-md-4 g-3">
        @foreach ($related as $rel)
        <div class="col">
            <div class="card h-100 border shadow-none"
                 style="border-color:#e8ecf1!important; border-radius:10px; overflow:hidden">
                <a href="{{ route('shop.products.show', $rel) }}">
                    <img src="{{ $rel->image_url }}"
                         alt="{{ $rel->name }}"
                         class="card-img-top p-2"
                         style="height:140px; object-fit:contain; background:#f8f9fa">
                </a>
                <div class="card-body p-2">
                    <a href="{{ route('shop.products.show', $rel) }}"
                       class="text-dark text-decoration-none small fw-semibold d-block mb-2"
                       style="line-height:1.3">
                        {{ $rel->name }}
                    </a>
                    {{-- Komponen harga dalam mode compact --}}
                    <x-price-button :product="$rel" :compact="true" />
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection
