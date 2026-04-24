@extends('layouts.admin')

@section('title', 'Detail Pembelian — {{ $purchase->invoice_no }}')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Detail Pembelian</h4>
        <small class="text-muted">Invoice: <strong>{{ $purchase->invoice_no }}</strong></small>
    </div>
    <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary btn-sm">
        ← Kembali ke Riwayat
    </a>
</div>

<div class="row g-4 mb-4">

    {{-- Kartu Info Faktur --}}
    <div class="col-md-6 col-lg-5">
        <div class="card h-100">
            <div class="card-header fw-semibold bg-light">Informasi Faktur</div>
            <div class="card-body p-0">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted ps-3" style="width:42%">No. Invoice</td>
                            <td class="fw-semibold">{{ $purchase->invoice_no }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Status</td>
                            <td>
                                <span class="badge {{ $purchase->status_badge_class }}">
                                    {{ $purchase->status_label }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Tanggal Transaksi</td>
                            <td>{{ $purchase->purchased_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Dicatat Pada</td>
                            <td>{{ $purchase->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Dicatat Oleh</td>
                            <td>{{ $purchase->user->name ?? '—' }}</td>
                        </tr>
                        @if ($purchase->notes)
                        <tr>
                            <td class="text-muted ps-3">Catatan</td>
                            <td class="text-wrap">{{ $purchase->notes }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Kartu Info Supplier --}}
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-semibold bg-light">🏭 Supplier</div>
            <div class="card-body p-0">
                @php $sup = $purchase->supplier; @endphp
                @if ($sup)
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted ps-3" style="width:42%">Nama</td>
                            <td class="fw-semibold">{{ $sup->name }}</td>
                        </tr>
                        @if ($sup->contact_person)
                        <tr>
                            <td class="text-muted ps-3">Kontak Person</td>
                            <td>{{ $sup->contact_person }}</td>
                        </tr>
                        @endif
                        @if ($sup->phone)
                        <tr>
                            <td class="text-muted ps-3">Telepon</td>
                            <td>{{ $sup->phone }}</td>
                        </tr>
                        @endif
                        @if ($sup->email)
                        <tr>
                            <td class="text-muted ps-3">Email</td>
                            <td>{{ $sup->email }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                @else
                    <div class="p-3 text-muted fst-italic">Data supplier tidak tersedia.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Kartu Ringkasan Biaya --}}
    <div class="col-md-12 col-lg-3">
        <div class="card h-100 border-primary">
            <div class="card-header fw-semibold bg-primary text-white">Ringkasan Biaya</div>
            <div class="card-body d-flex flex-column justify-content-center text-center">
                <div class="text-muted mb-1 small">Total Biaya Pembelian</div>
                <div class="fs-2 fw-bold text-primary">
                    Rp {{ number_format($purchase->total_cost, 0, ',', '.') }}
                </div>
                <hr class="my-2">
                <div class="small text-muted">
                    {{-- items sudah di-load dengan eager loading di controller --}}
                    <div>{{ $purchase->items->count() }} jenis produk</div>
                    <div>{{ number_format($purchase->items->sum('quantity')) }} unit total</div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- TABEL DETAIL ITEM PRODUK --}}
<div class="card">
    <div class="card-header fw-semibold bg-light">Daftar Produk yang Dibeli</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-secondary">
                    <tr>
                        <th style="width:40px" class="text-center">#</th>
                        <th>Nama Produk</th>
                        <th>SKU</th>
                        <th>Kategori</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Harga Beli / Unit</th>
                        <th class="text-end pe-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->items as $i => $item)
                    <tr>
                        <td class="text-center text-muted">{{ $i + 1 }}</td>

                        {{-- Nama produk — bisa null jika produk di-softdelete --}}
                        <td>
                            @if ($item->product)
                                <span class="fw-semibold">{{ $item->product->name }}</span>
                                @if (! $item->product->is_active)
                                    <span class="badge bg-secondary ms-1">Nonaktif</span>
                                @endif
                            @else
                                <span class="text-muted fst-italic">[Produk telah dihapus]</span>
                            @endif
                        </td>

                        <td>
                            <code class="small">{{ $item->product->sku ?? '—' }}</code>
                        </td>

                        <td>
                            {{ $item->product->category->name ?? '—' }}
                        </td>

                        <td class="text-center">
                            <strong>{{ number_format($item->quantity) }}</strong>
                            <span class="text-muted small">
                                {{ $item->product->unit ?? 'pcs' }}
                            </span>
                        </td>

                        <td class="text-end">
                            Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
                        </td>

                        <td class="text-end pe-3 fw-semibold">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>

                {{-- Footer total --}}
                <tfoot class="table-light">
                    <tr>
                        <td colspan="6" class="text-end fw-bold py-2 pe-3">
                            Total Biaya:
                        </td>
                        <td class="text-end fw-bold text-primary pe-3 fs-5">
                            Rp {{ number_format($purchase->total_cost, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- Tombol aksi di bawah --}}
<div class="mt-4 mb-5">
    <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary">
        ← Kembali ke Riwayat
    </a>
</div>
@endsection
