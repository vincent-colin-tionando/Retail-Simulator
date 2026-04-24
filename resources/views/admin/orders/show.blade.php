@extends('layouts.admin')

@section('title', 'Detail Order')
@section('breadcrumb', 'Pesanan / Detail')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">{{ $order->order_code }}</h4>
        <small class="text-muted">
            Masuk {{ $order->created_at->format('d M Y, H:i') }}
        </small>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── Tombol Aksi Status ────────────────────────────────────────── --}}
<div class="card mb-4 border-0 bg-light">
    <div class="card-body py-3 d-flex align-items-center gap-3 flex-wrap">
        <span class="fw-semibold">Ubah Status:</span>

        @if ($order->status === 'pending')
            {{-- pending → processing --}}
            <form action="{{ route('admin.orders.process', $order) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-play-circle me-1"></i> Proses Sekarang
                </button>
            </form>

            {{-- pending → cancelled --}}
            <button type="button" class="btn btn-outline-danger btn-sm"
                    data-bs-toggle="modal" data-bs-target="#cancelModal">
                <i class="bi bi-x-circle me-1"></i> Batalkan
            </button>

        @elseif ($order->status === 'processing')
            {{-- processing → completed --}}
            <form action="{{ route('admin.orders.complete', $order) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-success btn-sm"
                    onclick="return confirm('Tandai order ini sebagai selesai?')">
                    <i class="bi bi-check-circle me-1"></i> Tandai Selesai
                </button>
            </form>

            {{-- processing → cancelled --}}
            <button type="button" class="btn btn-outline-danger btn-sm"
                    data-bs-toggle="modal" data-bs-target="#cancelModal">
                <i class="bi bi-x-circle me-1"></i> Batalkan & Kembalikan Stok
            </button>

        @else
            {{-- completed / cancelled — tidak ada aksi lagi --}}
            <span class="badge {{ $order->status_badge_class }} fs-6 px-3 py-2">
                {{ $order->status_label }}
            </span>
            @if ($order->status === 'completed')
                <small class="text-muted">
                    Selesai {{ $order->completed_at?->format('d M Y, H:i') }}
                </small>
            @endif
        @endif
    </div>
</div>

<div class="row g-4">

    {{-- Info Order --}}
    <div class="col-md-5">
        <div class="card">
            <div class="card-header fw-semibold">Detail Pesanan</div>
            <div class="card-body p-0">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted ps-3" style="width:42%">Kode Order</td>
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
                        <td class="text-muted ps-3">Metode Bayar</td>
                        <td>{{ $order->payment_method ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3 align-top">Alamat Kirim</td>
                        <td>{{ $order->shipping_address ?? '—' }}</td>
                    </tr>
                    @if ($order->notes)
                    <tr>
                        <td class="text-muted ps-3 align-top">Catatan Pembeli</td>
                        <td class="fst-italic">{{ $order->notes }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted ps-3">Diproses Oleh</td>
                        <td>{{ $order->processedBy->name ?? '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Info Pembeli --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header fw-semibold">Pembeli</div>
            <div class="card-body p-0">
                @php $buyer = $order->user; @endphp
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted ps-3" style="width:42%">Nama</td>
                        <td>
                            @if ($buyer)
                                <a href="{{ route('admin.users.show', $buyer) }}">
                                    {{ $buyer->name }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Role</td>
                        <td>
                            <span class="badge {{ $order->buyer_role === 'distributor' ? 'bg-primary' : 'bg-success' }}">
                                {{ ucfirst($order->buyer_role) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Email</td>
                        <td class="small">{{ $buyer->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Telepon</td>
                        <td>{{ $buyer->phone ?? '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Ringkasan Total --}}
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-header fw-semibold bg-primary text-white">Total Pesanan</div>
            <div class="card-body text-center py-4">
                <div class="fs-2 fw-bold text-primary">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </div>
                <div class="text-muted small mt-2">
                    {{ $order->items->count() }} produk ·
                    {{ number_format($order->items->sum('quantity')) }} unit
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Item Produk --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-semibold">Daftar Produk Dipesan</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Produk</th>
                                <th>SKU</th>
                                <th class="text-center">Stok Saat Ini</th>
                                <th class="text-center">Qty Dipesan</th>
                                <th class="text-end">Harga/Unit</th>
                                <th class="text-end pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    {{-- Tampilkan nama snapshot, bukan dari relasi --}}
                                    <span class="fw-semibold">{{ $item->product_name }}</span>
                                    @if (! $item->product)
                                        <span class="badge bg-secondary ms-1"
                                              title="Produk telah dihapus">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <code class="small">{{ $item->product->sku ?? '—' }}</code>
                                </td>
                                {{-- Stok saat ini (real-time dari produk) --}}
                                <td class="text-center">
                                    @if ($item->product)
                                        <span class="{{ $item->product->isLowStock() ? 'text-danger fw-bold' : '' }}">
                                            {{ $item->product->stock }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center fw-semibold">{{ $item->quantity }}</td>
                                <td class="text-end">
                                    Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-semibold pe-3">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="6" class="text-end fw-bold pe-3">Total:</td>
                                <td class="text-end fw-bold text-primary pe-3 fs-5">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Catatan Admin --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header fw-semibold">Catatan Admin</div>
            <div class="card-body">
                <form action="{{ route('admin.orders.note', $order) }}" method="POST">
                    @csrf @method('PATCH')
                    <textarea name="admin_notes" rows="3" class="form-control mb-2"
                        placeholder="Tambahkan catatan internal di sini...">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-save me-1"></i> Simpan Catatan
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>{{-- end row --}}

{{-- ── Modal Konfirmasi Pembatalan ────────────────────────────── --}}
@if (in_array($order->status, ['pending', 'processing']))
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Batalkan Pesanan?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.orders.cancel', $order) }}" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <p>Order <strong>{{ $order->order_code }}</strong> akan dibatalkan dan
                    stok semua produk di dalamnya akan dikembalikan secara otomatis.</p>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Alasan Pembatalan</label>
                        <textarea name="cancel_reason" rows="2" class="form-control"
                            placeholder="Opsional — akan disimpan sebagai catatan admin"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">
                        Ya, Batalkan & Kembalikan Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection