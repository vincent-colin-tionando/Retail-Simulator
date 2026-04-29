@extends('layouts.shop')

@section('title', 'Keranjang Belanja')

@push('styles')
<style>
    /* ── Input qty dengan tombol +/- ─── */
    .qty-wrapper { display: inline-flex; align-items: center; gap: 4px; }
    .qty-wrapper input[type=number] {
        width: 60px;
        text-align: center;
        -moz-appearance: textfield; /* Firefox: sembunyikan panah */
    }
    .qty-wrapper input[type=number]::-webkit-outer-spin-button,
    .qty-wrapper input[type=number]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-cart3 me-2"></i>Keranjang Belanja
    </h4>
    @if (! empty($cartItems))
        {{-- Tombol kosongkan semua --}}
        <form action="{{ route('shop.cart.clear') }}" method="POST" class="ms-auto">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Kosongkan seluruh keranjang?')">
                <i class="bi bi-trash me-1"></i>Kosongkan
            </button>
        </form>
    @endif
</div>

{{-- ── Error validasi stok dari checkout ── --}}
{{--
    Saat checkout gagal karena stok kurang, OrderController melempar
    ValidationException dengan key 'stock'. Error ditampilkan di sini
    agar user tahu item mana yang perlu disesuaikan sebelum coba lagi.
--}}
@if ($errors->has('stock'))
    <div class="alert alert-danger alert-dismissible fade show">
        <strong><i class="bi bi-exclamation-triangle me-1"></i>
            Checkout gagal — masalah stok:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->get('stock') as $err)
                {{-- $errors->get() mengembalikan array, loop lagi jika nested --}}
                @if (is_array($err))
                    @foreach ($err as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                @else
                    <li>{{ $err }}</li>
                @endif
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (empty($cartItems))
    {{-- ── Keranjang kosong ── --}}
    <div class="card border-0 shadow-sm text-center py-5">
        <i class="bi bi-cart-x opacity-25 d-block mb-3" style="font-size:4rem"></i>
        <h5 class="text-muted">Keranjang kamu masih kosong</h5>
        <p class="text-muted small mb-4">
            Tambahkan produk dari katalog untuk memulai belanja.
        </p>
        <div>
            <a href="{{ route('shop.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left me-1"></i> Lihat Produk
            </a>
        </div>
    </div>

@else
    <div class="row g-4">

        {{-- ══ KOLOM KIRI — Tabel item keranjang ══ --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width:45%">Produk</th>
                                    <th class="text-center">Harga / Unit</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                    <th style="width:40px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cartItems as $item)
                                <tr id="row-{{ $item['product_id'] }}">

                                    {{-- Gambar + nama + SKU --}}
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" style="width:60px;height:60px; object-fit:contain;
                                                background:#f8f9fa; border-radius:8px; padding:4px; flex-shrink:0">
                                            <div>
                                                <div class="fw-semibold" style="font-size:.9rem; line-height:1.3">
                                                    {{ $item['name'] }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ $item['sku'] }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Harga per unit — diambil dari session (server-side) --}}
                                    <td class="text-center">
                                        <span class="fw-semibold
                                            {{ $role === 'distributor' ? 'text-primary' : 'text-success' }}">
                                            Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                                        </span>
                                        @if ($role === 'distributor')
                                            <div class="text-primary" style="font-size:.68rem">
                                                Grosir
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Input jumlah — update otomatis saat berubah --}}
                                    <td class="text-center">
                                        <form action="{{ route('shop.cart.update', $item['product_id']) }}" method="POST" id="form-qty-{{ $item['product_id'] }}">
                                            @csrf @method('PATCH')
                                            <div class="qty-wrapper justify-content-center">
                                                {{-- Tombol kurang --}}
                                                <button type="button" class="btn btn-outline-secondary btn-sm px-2" onclick="changeQty({{ $item['product_id'] }}, -1)">
                                                    <i class="bi bi-dash"></i>
                                                </button>

                                                <input type="number" name="quantity" id="qty-{{ $item['product_id'] }}" value="{{ $item['quantity'] }}"
                                                       min="1" max="999" class="form-control form-control-sm" onchange="submitQty({{ $item['product_id'] }})">

                                                {{-- Tombol tambah --}}
                                                <button type="button" class="btn btn-outline-secondary btn-sm px-2" onclick="changeQty({{ $item['product_id'] }}, 1)">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>

                                    {{-- Subtotal = harga × qty (dihitung di server) --}}
                                    <td class="text-end pe-4 fw-bold" id="subtotal-{{ $item['product_id'] }}">
                                        Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </td>

                                    {{-- Tombol hapus item --}}
                                    <td class="text-center pe-2">
                                        <form action="{{ route('shop.cart.destroy', $item['product_id']) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus item"
                                                    onclick="return confirm('Hapus \'{{ addslashes($item['name']) }}\' dari keranjang?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('shop.index') }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Lanjutkan Belanja
                </a>
            </div>
        </div>

        {{-- ═══ KOLOM KANAN — Ringkasan & Checkout ═══ --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">
                    Ringkasan Pesanan
                </div>
                <div class="card-body">

                    {{-- Detail per item --}}
                    @foreach ($cartItems as $item)
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span class="text-truncate me-2" style="max-width:60%">
                                {{ $item['name'] }}
                                <span class="text-dark"> × {{ $item['quantity'] }}</span>
                            </span>
                            <span class="flex-shrink-0">
                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach

                    <hr class="my-3">

                    {{-- Grand Total --}}
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-1">
                        <span>Total</span>
                        <span class="text-primary" id="grand-total">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Info harga sesuai role --}}
                    <div class="text-muted mb-3" style="font-size:.78rem">
                        @if ($role === 'distributor')
                            <i class="bi bi-tag me-1 text-primary"></i>
                            Harga grosir distributor diterapkan.
                        @else
                            <i class="bi bi-info-circle me-1"></i>
                            Harga eceran consumer.
                        @endif
                    </div>

                    <hr class="my-3">

                    {{-- Tombol Checkout --}}
                    @auth
                        <a href="{{ route('shop.checkout') }}" class="btn btn-primary w-100 fw-semibold">
                            <i class="bi bi-bag-check me-1"></i>
                            Lanjutkan ke Checkout
                        </a>
                    @else
                        {{-- Tamu belum login → redirect ke login --}}
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Login untuk Checkout
                        </a>
                    @endauth

                </div>
            </div>
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script>
    /**
     * Tambah atau kurangi qty, lalu auto-submit form.
     *
     * @param {number} productId  — ID produk (juga ID form dan input)
     * @param {number} delta  — +1 atau -1
     */
    function changeQty(productId, delta) {
        const input = document.getElementById('qty-' + productId);
        if (!input) return;

        const current = parseInt(input.value, 10) || 1;
        const next    = Math.max(0, current + delta);

        if (next !== current) {
            input.value = next;
            submitQty(productId);
        }
    }

    /**
     * Submit form update qty untuk product tertentu.
     * Dipanggil saat input berubah secara manual atau via tombol.
     */
    function submitQty(productId) {
        const form = document.getElementById('form-qty-' + productId);
        if (form) form.submit();
    }
</script>
@endpush
