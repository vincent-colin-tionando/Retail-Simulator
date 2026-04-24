{{--
    Komponen: <x-price-button :product="$product" />
    Opsional: <x-price-button :product="$product" :compact="true" />

    $isGuest      — bool: user belum login
    $price        — ?float: harga untuk role saat ini (null jika guest)
    $effectiveRole — string: 'consumer' atau 'distributor'
    $canCheckout  — bool: false jika distributor belum terverifikasi
    $product      — App\Models\Product
    $compact      — bool: tampilan ringkas untuk card grid
--}}

@php
    $outOfStock  = $product->stock <= 0;
    $user        = auth()->user();
    $isDistributor = $user && $user->role === 'distributor';
    $needsVerify   = $isDistributor && ! $user->is_verified;
@endphp

{{-- KONDISI 1: TAMU (belum login) — tampilkan harga & tombol tambah, checkout butuh login --}}
@if ($isGuest)
    <div class="price-block">
        <div class="{{ $compact ? 'fs-6' : 'fs-4' }} fw-bold text-success mb-2">
            @if ($price !== null)
                Rp {{ number_format($price, 0, ',', '.') }}
                @if (! $compact)
                    <small class="text-muted fw-normal fs-6">/ {{ $product->unit }}</small>
                @endif
            @else
                <span class="text-muted">Harga belum tersedia</span>
            @endif
        </div>

        @if ($price !== null && $product->stock > 0)
            <form action="{{ route('shop.cart.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-primary {{ $compact ? 'btn-sm w-100' : 'w-100' }}">
                    <i class="bi bi-cart-plus me-1"></i> Tambah
                </button>
            </form>
        @elseif ($product->stock <= 0)
            <button class="btn btn-secondary {{ $compact ? 'btn-sm w-100' : 'w-100' }}" disabled>
                <i class="bi bi-x-circle me-1"></i> Stok Habis
            </button>
        @endif
    </div>

{{-- KONDISI 2: LOGIN, tapi tidak punya harga --}}
@elseif ($price === null)
    <div class="text-muted small fst-italic">Harga belum tersedia.</div>

{{-- KONDISI 3: DISTRIBUTOR BELUM TERVERIFIKASI --}}
@elseif ($needsVerify)

    <div class="price-block">
        {{-- Tampilkan harga consumer saja (belum dapat grosir) --}}
        <div class="{{ $compact ? 'fs-6' : 'fs-5' }} fw-bold text-dark mb-1">
            Rp {{ number_format($price, 0, ',', '.') }}
        </div>
        <div class="alert alert-warning py-1 px-2 mb-2" style="font-size:.78rem; border-radius:6px">
            <i class="bi bi-clock me-1"></i>
            Akun distributor Anda sedang menunggu verifikasi admin.
            Setelah terverifikasi, Anda mendapat harga grosir.
        </div>
        <button type="button"
                class="btn btn-secondary {{ $compact ? 'btn-sm w-100' : 'w-100' }}" disabled>
            <i class="bi bi-cart-x me-1"></i> Menunggu Verifikasi
        </button>
    </div>

{{-- KONDISI 4: STOK HABIS --}}
@elseif ($outOfStock)
    <div class="price-block">
        <div class="{{ $compact ? 'fs-6' : 'fs-5' }} fw-bold {{ $effectiveRole === 'distributor' ? 'text-primary' : 'text-dark' }} mb-2">
            Rp {{ number_format($price, 0, ',', '.') }}
            @if (! $compact)
                <small class="text-muted fw-normal fs-6">/ {{ $product->unit }}</small>
            @endif
        </div>

        <button type="button" class="btn btn-secondary {{ $compact ? 'btn-sm w-100' : 'w-100' }}" disabled>
                <i class="bi bi-x-circle me-1"></i> Stok Habis
        </button>
    </div>

{{-- KONDISI 5: NORMAL — bisa beli --}}
@else

    <div class="price-block">
        {{-- Badge harga grosir untuk distributor --}}
        @if ($effectiveRole === 'distributor' && ! $compact)
            <div class="mb-1">
                <span class="badge bg-primary" style="font-size:.72rem">
                    <i class="bi bi-tag me-1"></i>Harga Distributor
                </span>
            </div>
        @endif

        <div class="{{ $compact ? 'fs-6' : 'fs-4' }} fw-bold {{ $effectiveRole === 'distributor' ? 'text-primary' : 'text-success' }} mb-2">
            Rp {{ number_format($price, 0, ',', '.') }}
            @if (! $compact)
                <small class="text-muted fw-normal fs-6">/ {{ $product->unit }}</small>
            @endif
        </div>

        {{-- Form tambah ke keranjang --}}
        <form action="{{ route('shop.cart.store') }}" method="POST" class="{{ $compact ? '' : 'row g-2' }}">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            @if (! $compact)
                {{-- Tampilan detail: input qty + tombol --}}
                <div class="col-4">
                    <input type="number" name="quantity" class="form-control text-center" min="1" max="{{ $product->stock }}" value="1">
                </div>

                <div class="col-8">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-cart-plus me-1"></i> Tambah ke Keranjang
                    </button>
                </div>
            @else
                {{-- Tampilan card grid: tombol saja, qty default 1 --}}
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-cart-plus me-1"></i> Tambah
                </button>
            @endif
        </form>
    </div>

@endif
