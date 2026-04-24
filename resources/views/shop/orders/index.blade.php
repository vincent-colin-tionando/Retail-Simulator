@extends('layouts.shop')

@section('title', 'Pesanan Saya')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-bag-check me-2"></i>Pesanan Saya
    </h4>
    <a href="{{ route('shop.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-cart-plus me-1"></i> Belanja Lagi
    </a>
</div>

@if ($orders->isEmpty())
    <div class="card border-0 shadow-sm text-center p-5">
        <i class="bi bi-bag-x opacity-25 d-block mb-3" style="font-size:4rem"></i>
        <h5 class="text-muted">Belum ada pesanan</h5>
        <p class="text-muted small mb-4">
            Mulai belanja dan pesanan Anda akan muncul di sini.
        </p>
        <div>
            <a href="{{ route('shop.index') }}" class="btn btn-primary">
                Lihat Produk
            </a>
        </div>
    </div>

@else
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Kode Pesanan</th>
                            <th class="text-center">Jml Produk</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Status</th>
                            <th>Tanggal</th>
                            <th class="pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $order->order_code }}</td>
                            <td class="text-center text-muted">{{ $order->items_count }}</td>
                            <td class="text-end fw-semibold">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $order->status_badge_class }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $order->created_at->format('d M Y') }}
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('shop.orders.show', $order) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($orders->hasPages())
        <div class="mt-3">{{ $orders->links() }}</div>
    @endif
@endif

@endsection
