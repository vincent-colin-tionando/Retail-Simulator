@extends('layouts.shop')

@section('title', 'Detail Pesanan — ' . $order->order_code)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Detail Pesanan</h4>
        <small class="text-muted">{{ $order->order_code }}</small>
    </div>
    <a href="{{ route('shop.orders.index') }}" class="btn btn-outline-secondary btn-sm">
        ← Pesanan Saya
    </a>
</div>

<div class="row g-4">

    {{-- ── Status Pesanan (progres visual) --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center position-relative">

                    {{-- Garis penghubung di belakang --}}
                    <div class="position-absolute w-100 top-50 translate-middle-y"
                         style="height:2px; background:#dee2e6; z-index:0; left:0"></div>

                    @php
                        // Urutan tahap: pending → processing → completed
                        $stages = ['pending', 'processing', 'completed'];
                        $currentIdx = array_search($order->status, $stages);
                        // Jika cancelled, tunjukkan di pending
                        if ($order->status === 'cancelled') $currentIdx = -1;
                    @endphp

                    @foreach (['pending' => ['Menunggu', 'bi-clock'], 'processing' => ['Diproses', 'bi-gear'], 'completed' => ['Selesai', 'bi-check-circle']] as $stage => [$label, $icon])
                    @php
                        $stageIdx = array_search($stage, $stages);
                        $isDone   = $currentIdx !== -1 && $stageIdx <= $currentIdx;
                        $isActive = $order->status === $stage;
                    @endphp
                    <div class="text-center position-relative" style="z-index:1; flex:1">
                        <div class="rounded-circle d-inline-flex align-items-center
                                    justify-content-center mb-2"
                             style="width:44px; height:44px;
                                    background: {{ $isDone ? '#1e2a3a' : '#e9ecef' }};
                                    color: {{ $isDone ? '#fff' : '#adb5bd' }};">
                            <i class="bi {{ $icon }}"></i>
                        </div>
                        <div class="small fw-{{ $isActive ? 'bold' : 'normal' }}
                                    text-{{ $isDone ? 'dark' : 'muted' }}">
                            {{ $label }}
                        </div>
                    </div>
                    @endforeach

                    {{-- Jika cancelled, tampilkan di akhir --}}
                    @if ($order->status === 'cancelled')
                        <div class="text-center position-relative" style="z-index:1; flex:1">
                            <div class="rounded-circle d-inline-flex align-items-center
                                        justify-content-center mb-2"
                                 style="width:44px;height:44px;background:#dc3545;color:#fff">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <div class="small fw-bold text-danger">Dibatalkan</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Info Pesanan ── --}}
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold bg-white">Informasi Pesanan</div>
            <div class="card-body p-0">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted ps-3" style="width:42%">Kode Pesanan</td>
                        <td class="fw-semibold">{{ $order->order_code }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Status</td>
                        <td>
                            <span class="badge {{ $order->status_badge_class }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Tanggal Pesan</td>
                        <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Metode Bayar</td>
                        <td>{{ $order->payment_method ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3 align-top">Alamat Kirim</td>
                        <td>{{ $order->shipping_address ?? '—' }}</td>
                    </tr>
                    @if ($order->notes)
                    <tr>
                        <td class="text-muted ps-3 align-top">Catatan Anda</td>
                        <td class="fst-italic text-muted">{{ $order->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- ── Ringkasan Total ─── --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 border-top border-primary border-3">
            <div class="card-body d-flex flex-column justify-content-center text-center">
                <div class="text-muted small mb-1">Total Pembayaran</div>
                <div class="fs-2 fw-bold text-primary">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </div>
                <div class="text-muted small mt-2">
                    {{ $order->items->count() }} jenis ·
                    {{ number_format($order->items->sum('quantity')) }} unit
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tabel Produk yang Dipesan ─── --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold bg-white">Produk yang Dipesan</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width:45%">Produk</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga Saat Itu</th>
                                <th class="text-end pe-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        @if ($item->product)
                                            <img src="{{ $item->product->image_url }}"
                                                 style="width:48px;height:48px;object-fit:contain;
                                                        background:#f8f9fa;border-radius:6px;padding:4px">
                                        @endif
                                        <div>
                                            {{-- Tampilkan nama snapshot — bukan nama produk saat ini --}}
                                            <div class="fw-semibold" style="font-size:.9rem">
                                                {{ $item->product_name }}
                                            </div>
                                            @if ($item->product)
                                                <small class="text-muted">
                                                    {{ $item->product->sku }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end text-muted small">
                                    Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="text-end pe-4 fw-semibold">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold pe-3">Total:</td>
                                <td class="text-end fw-bold text-primary pe-4 fs-5">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
