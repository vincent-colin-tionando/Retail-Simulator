@extends('layouts.admin')

@section('title', 'Detail Produk')
@section('breadcrumb', 'Produk / Detail')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">{{ $product->name }}</h4>
        <small class="text-muted">SKU: <code>{{ $product->sku }}</code></small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.edit', $product) }}"
           class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('admin.products.index') }}"
           class="btn btn-outline-secondary btn-sm">← Kembali</a>
    </div>
</div>

<div class="row g-4">

    {{-- Gambar & Info Dasar --}}
    <div class="col-md-3 text-center">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-fluid rounded border" style="max-height:220px; object-fit:contain">
    </div>

    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header fw-semibold">Informasi Produk</div>
            <div class="card-body p-0">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted ps-3" style="width:42%">Kategori</td>
                        <td>
                            @if ($product->category)
                                <a href="{{ route('admin.categories.show', $product->category) }}">
                                    {{ $product->category->full_name }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Satuan</td>
                        <td>{{ $product->unit }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Stok Saat Ini</td>
                        <td>
                            <span class="fw-bold {{ $product->isLowStock() ? 'text-danger' : 'text-success' }}">
                                {{ $product->stock }} {{ $product->unit }}
                            </span>
                            @if ($product->isLowStock())
                                <span class="badge bg-danger ms-1">Stok Menipis!</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Stok Minimum</td>
                        <td>{{ $product->stock_min }} {{ $product->unit }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Status</td>
                        <td>
                            <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Ditambahkan</td>
                        <td>{{ $product->created_at->format('d M Y') }}</td>
                    </tr>
                    @if ($product->description)
                    <tr>
                        <td class="text-muted ps-3 align-top">Deskripsi</td>
                        <td>{{ $product->description }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Harga --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header fw-semibold">Daftar Harga</div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div>
                        <div class="fw-semibold">Harga Consumer</div>
                        <small class="text-muted">Harga eceran</small>
                    </div>
                    <div class="fs-5 fw-bold text-success">
                        Rp {{ number_format($product->consumerPrice->price ?? 0, 0, ',', '.') }}
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center py-3">
                    <div>
                        <div class="fw-semibold">Harga Distributor</div>
                        <small class="text-muted">Harga grosir</small>
                    </div>
                    <div class="fs-5 fw-bold text-primary">
                        Rp {{ number_format($product->distributorPrice->price ?? 0, 0, ',', '.') }}
                    </div>
                </div>
                @if ($product->consumerPrice && $product->distributorPrice)
                    @php
                        $margin = $product->consumerPrice->price - $product->distributorPrice->price;
                        $pct    = $product->consumerPrice->price > 0 ? round($margin / $product->consumerPrice->price * 100, 1) : 0;
                    @endphp
                    <div class="alert alert-light border mt-2 mb-0 py-2">
                        <small class="text-muted">
                            Margin harga:
                            <strong>Rp {{ number_format($margin, 0, ',', '.') }}</strong>
                            ({{ $pct }}%)
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Riwayat Pembelian Stok (5 terakhir) --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between fw-semibold">
                <span>Riwayat Pembelian Stok</span>
                <span class="text-muted fw-normal small">5 terakhir</span>
            </div>
            <div class="card-body p-0">
                @if ($product->purchaseItems->isEmpty())
                    <div class="p-3 text-muted text-center small">Belum ada riwayat pembelian.</div>
                @else
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">HPP/Unit</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product->purchaseItems->take(5) as $pi)
                        <tr>
                            <td>
                                <a href="{{ route('admin.purchases.show', $pi->purchase) }}"
                                   class="small">{{ $pi->purchase->invoice_no }}</a>
                            </td>
                            <td class="text-center">+{{ $pi->quantity }}</td>
                            <td class="text-end small">
                                Rp {{ number_format($pi->unit_cost, 0, ',', '.') }}
                            </td>
                            <td class="small text-muted">
                                {{ $pi->purchase->purchased_at->format('d M Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

    {{-- Riwayat Penjualan (5 terakhir) --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between fw-semibold">
                <span>Riwayat Penjualan</span>
                <span class="text-muted fw-normal small">5 terakhir</span>
            </div>
            <div class="card-body p-0">
                @if ($product->orderItems->isEmpty())
                    <div class="p-3 text-muted text-center small">Belum ada riwayat penjualan.</div>
                @else
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Harga/Unit</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product->orderItems->take(5) as $oi)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $oi->order) }}"
                                   class="small">{{ $oi->order->order_code }}</a>
                            </td>
                            <td class="text-center">{{ $oi->quantity }}</td>
                            <td class="text-end small">
                                Rp {{ number_format($oi->unit_price, 0, ',', '.') }}
                            </td>
                            <td class="small text-muted">
                                {{ $oi->order->created_at->format('d M Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
