@extends('layouts.shop')

@section('title', 'Pesanan Berhasil — ' . $order->order_code)

@section('content')

{{-- ── Konfirmasi Visual ──── --}}
<div class="text-center py-4 mb-4">
    <div class="mb-3">
        <span class="d-inline-flex align-items-center justify-content-center bg-success rounded-circle" style="width:80px;height:80px">
            <i class="bi bi-check-lg text-white" style="font-size:2.5rem"></i>
        </span>
    </div>
    <h3 class="fw-bold text-success mb-1">Pesanan Berhasil Dibuat!</h3>
    <p class="text-muted mb-0">
        Kode pesanan Anda:
        <strong class="text-dark fs-5">{{ $order->order_code }}</strong>
    </p>
    <p class="text-muted small mt-1">
        Admin akan segera memproses pesanan Anda.
    </p>
</div>

<div class="row justify-content-center">
<div class="col-lg-7">

    {{-- ── Ringkasan Pesanan ── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            Detail Pesanan — {{ $order->order_code }}
        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="text-muted ps-3" style="width:40%">Status</td>
                    <td>
                        <span class="badge {{ $order->status_badge_class }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="text-muted ps-3">Metode Bayar</td>
                    <td>{{ $order->payment_method }}</td>
                </tr>
                <tr>
                    <td class="text-muted ps-3 align-top">Alamat Kirim</td>
                    <td class="text-wrap">{{ $order->shipping_address }}</td>
                </tr>
                @if ($order->notes)
                    <tr>
                        <td class="text-muted ps-3 align-top">Catatan</td>
                        <td>{{ $order->notes }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="text-muted ps-3">Total Pembayaran</td>
                    <td class="fw-bold text-primary fs-5">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ── Daftar Produk yang Dipesan ── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            Produk ({{ $order->items->count() }} item)
        </div>
        <div class="card-body p-0">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Produk</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end pe-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                    <tr>
                        <td class="ps-3">
                            {{-- Tampilkan nama snapshot — tetap akurat
                                 meski nama produk berubah di kemudian hari --}}
                            <div class="fw-semibold" style="font-size:.9rem">
                                {{ $item->product_name }}
                            </div>
                            <small class="text-muted">
                                @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                            </small>
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end pe-3 fw-semibold">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="2" class="text-end fw-bold pe-3">Total:</td>
                        <td class="text-end fw-bold text-primary pe-3">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ── Tombol Aksi ── --}}
    <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="{{ route('shop.orders.show', $order) }}" class="btn btn-outline-primary">
            <i class="bi bi-receipt me-1"></i> Lihat Detail Pesanan
        </a>
        <a href="{{ route('shop.orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-bag-check me-1"></i> Semua Pesanan Saya
        </a>
        <a href="{{ route('shop.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-1"></i> Belanja Lagi
        </a>
    </div>

</div>
</div>

@endsection
