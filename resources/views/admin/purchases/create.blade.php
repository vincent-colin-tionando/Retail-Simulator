@extends('layouts.admin')

@section('title', 'Catat Pembelian Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Catat Pembelian Stok Baru</h4>
    <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary btn-sm">
        ← Kembali ke Riwayat
    </a>
</div>

{{-- Tampilkan error validasi di atas form jika ada --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <strong>Terdapat kesalahan input:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('admin.purchases.store') }}" method="POST" id="purchaseForm">
@csrf

{{-- BAGIAN 1 — HEADER FAKTUR --}}
<div class="card mb-4">
    <div class="card-header fw-semibold bg-light">🧾 Informasi Faktur Pembelian</div>
    <div class="card-body">
        <div class="row g-3">

            {{-- Supplier --}}
            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    Supplier <span class="text-danger">*</span>
                </label>
                <select name="supplier_id"
                    class="form-select @error('supplier_id') is-invalid @enderror"
                    required>
                    <option value="">— Pilih Supplier —</option>
                    @foreach ($suppliers as $sup)
                        <option value="{{ $sup->id }}"
                            @selected(old('supplier_id') == $sup->id)>
                            {{ $sup->name }}
                        </option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                {{-- Link cepat ke form tambah supplier --}}
                <div class="form-text">
                    Supplier belum terdaftar?
                    <a href="{{ route('admin.suppliers.create') }}" target="_blank">
                        Tambah supplier baru ↗
                    </a>
                </div>
            </div>

            {{-- Nomor Invoice --}}
            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    No. Invoice / Faktur <span class="text-danger">*</span>
                </label>
                <input type="text" name="invoice_no"
                    class="form-control @error('invoice_no') is-invalid @enderror"
                    value="{{ old('invoice_no') }}"
                    placeholder="Contoh: INV/2024/001"
                    required>
                @error('invoice_no')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Nomor dari faktur fisik supplier.</div>
            </div>

            {{-- Tanggal Transaksi --}}
            <div class="col-md-2">
                <label class="form-label fw-semibold">
                    Tanggal Transaksi <span class="text-danger">*</span>
                </label>
                <input type="date" name="purchased_at"
                    class="form-control @error('purchased_at') is-invalid @enderror"
                    value="{{ old('purchased_at', now()->toDateString()) }}"
                    required>
                @error('purchased_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Status --}}
            <div class="col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select name="status"
                    class="form-select @error('status') is-invalid @enderror">
                    {{-- received = stok langsung masuk gudang (paling umum) --}}
                    <option value="received"
                        @selected(old('status', 'received') === 'received')>
                        Diterima
                    </option>
                    {{-- pending = barang belum sampai, stok belum bertambah --}}
                    <option value="pending"
                        @selected(old('status') === 'pending')>
                        Menunggu
                    </option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    "Diterima" → stok langsung bertambah.
                </div>
            </div>

            {{-- Catatan --}}
            <div class="col-12">
                <label class="form-label fw-semibold">Catatan (opsional)</label>
                <textarea name="notes" rows="2" class="form-control"
                    placeholder="Contoh: Pembayaran akan dilakukan via transfer 7 hari setelah barang diterima.">{{ old('notes') }}</textarea>
            </div>

        </div>
    </div>
</div>

{{-- BAGIAN 2 — TABEL ITEM PRODUK
     Baris ditambah/hapus secara dinamis menggunakan JavaScript.
     Name attribute menggunakan format array: items[0][product_id], dst. --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center bg-light">
        <span class="fw-semibold">Detail Produk yang Dibeli</span>
        <button type="button" class="btn btn-sm btn-success" id="addRowBtn">
            + Tambah Baris Produk
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0" id="itemsTable">
                <thead class="table-secondary">
                    <tr>
                        <th style="width:34%">Produk</th>
                        <th style="width:12%" class="text-center">Stok Sekarang</th>
                        <th style="width:14%">Jumlah Beli</th>
                        <th style="width:18%">Harga Beli / Unit (Rp)</th>
                        <th style="width:16%" class="text-end">Subtotal</th>
                        <th style="width:6%" class="text-center">—</th>
                    </tr>
                </thead>

                <tbody id="itemsBody">
                    {{--
                        Satu baris template awal.
                        JavaScript akan menduplikasi baris ini saat tombol
                        "+ Tambah Baris" diklik, lalu mengubah index arraynya.
                    --}}
                    <tr class="item-row">
                        {{-- Dropdown produk --}}
                        <td>
                            <select name="items[0][product_id]"
                                class="form-select form-select-sm product-select"
                                required>
                                <option value="">— Pilih Produk —</option>
                                @foreach ($products as $p)
                                    {{-- Simpan stok di data-attribute agar JS bisa baca --}}
                                    <option value="{{ $p->id }}"
                                            data-stock="{{ $p->stock }}"
                                            data-unit="{{ $p->unit }}">
                                        {{ $p->name }}
                                        ({{ $p->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        {{-- Stok saat ini — diisi JS saat produk dipilih --}}
                        <td class="text-center">
                            <span class="stock-display text-muted">—</span>
                        </td>

                        {{-- Jumlah yang dibeli --}}
                        <td>
                            <input type="number" name="items[0][quantity]"
                                class="form-control form-control-sm qty-input"
                                min="1" value="{{ old('items.0.quantity', 1) }}"
                                required>
                        </td>

                        {{-- Harga beli per unit (HPP) --}}
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="items[0][unit_cost]"
                                    class="form-control cost-input"
                                    min="0" step="100"
                                    value="{{ old('items.0.unit_cost') }}"
                                    placeholder="0"
                                    required>
                            </div>
                        </td>

                        {{-- Subtotal (diisi & diupdate oleh JS) --}}
                        <td class="text-end fw-semibold subtotal-cell text-muted">
                            Rp 0
                        </td>

                        {{-- Tombol hapus baris --}}
                        <td class="text-center">
                            <button type="button"
                                class="btn btn-sm btn-outline-danger remove-row"
                                title="Hapus baris ini">✕</button>
                        </td>
                    </tr>
                </tbody>

                {{-- Baris grand total — selalu terlihat di bawah --}}
                <tfoot class="table-light">
                    <tr>
                        <td colspan="4" class="text-end fw-bold py-2 pe-3">
                            Total Keseluruhan:
                        </td>
                        <td class="text-end fw-bold text-primary" id="grandTotal">
                            Rp 0
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{--     TOMBOL SUBMIT --}}
<div class="d-flex gap-2 mb-5">
    <button type="submit" class="btn btn-primary px-5">
        Simpan & Perbarui Stok
    </button>
    <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary">
        Batal
    </a>
</div>

</form>

{{--     JAVASCRIPT — Dynamic rows + kalkulasi real-time --}}
<script>
/**
 * Semua data produk dikirim dari controller sebagai JSON.
 * Di-key-by ID agar lookup O(1): products[123].stock
 */
const products = @json($products->keyBy('id'));

/** Counter index untuk name attribute baris baru (items[N][...]) */
let rowIndex = 1;

// Utilitas
/** Format angka ke "Rp 1.000.000" */
function formatRupiah(num) {
    return 'Rp ' + Math.round(num || 0).toLocaleString('id-ID');
}

/** Hitung subtotal satu baris dan perbaharui grand total */
function recalcRow(row) {
    const qty  = parseFloat(row.querySelector('.qty-input').value)  || 0;
    const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
    const sub  = qty * cost;

    const cell = row.querySelector('.subtotal-cell');
    cell.textContent = formatRupiah(sub);
    // Beri warna hanya jika ada nilai agar tidak membingungkan
    cell.classList.toggle('text-muted', sub === 0);
    cell.classList.toggle('text-dark',  sub > 0);

    recalcGrandTotal();
}

/** Jumlahkan semua subtotal dan tampilkan di tfoot */
function recalcGrandTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal-cell').forEach(cell => {
        // Bersihkan format "Rp 1.000" → ambil angka saja
        const num = cell.textContent.replace(/[^\d]/g, '');
        total += parseInt(num, 10) || 0;
    });
    document.getElementById('grandTotal').textContent = formatRupiah(total);
}

/** Tampilkan stok saat ini ketika produk dipilih di dropdown */
function onProductChange(selectEl) {
    const row     = selectEl.closest('.item-row');
    const pid     = selectEl.value;
    const product = pid ? products[pid] : null;
    const display = row.querySelector('.stock-display');

    if (product) {
        const isLow = product.stock <= 5; // ambang batas stok rendah sederhana
        display.textContent = `${product.stock} ${product.unit || 'pcs'}`;
        display.className   = `stock-display fw-semibold ${isLow ? 'text-danger' : 'text-success'}`;
    } else {
        display.textContent = '—';
        display.className   = 'stock-display text-muted';
    }
}

// Event: Tambah baris baru
document.getElementById('addRowBtn').addEventListener('click', function () {
    // Clone baris pertama sebagai template
    const template = document.querySelector('.item-row');
    const clone    = template.cloneNode(true);

    // Ganti semua name attribute dengan index baru
    const i = rowIndex++;
    clone.querySelector('.product-select').name = `items[${i}][product_id]`;
    clone.querySelector('.qty-input').name       = `items[${i}][quantity]`;
    clone.querySelector('.cost-input').name      = `items[${i}][unit_cost]`;

    // Reset isi baris agar kosong
    clone.querySelector('.product-select').value = '';
    clone.querySelector('.qty-input').value       = 1;
    clone.querySelector('.cost-input').value      = '';
    clone.querySelector('.stock-display').textContent = '—';
    clone.querySelector('.stock-display').className   = 'stock-display text-muted';
    clone.querySelector('.subtotal-cell').textContent = 'Rp 0';

    // Pasang event listener pada baris baru
    attachRowEvents(clone);

    document.getElementById('itemsBody').appendChild(clone);

    // Fokus ke dropdown produk baris baru agar UX lebih cepat
    clone.querySelector('.product-select').focus();
});

// Event: Hapus baris
document.addEventListener('click', function (e) {
    if (!e.target.classList.contains('remove-row')) return;

    const allRows = document.querySelectorAll('.item-row');
    if (allRows.length <= 1) {
        alert('Minimal satu produk harus diisi.');
        return;
    }
    e.target.closest('.item-row').remove();
    recalcGrandTotal();
});

// Pasang event listener pada sebuah baris
function attachRowEvents(row) {
    row.querySelector('.product-select')
       .addEventListener('change', function () { onProductChange(this); });

    row.querySelector('.qty-input')
       .addEventListener('input', () => recalcRow(row));

    row.querySelector('.cost-input')
       .addEventListener('input', () => recalcRow(row));
}

// Pasang event ke baris awal yang sudah ada saat halaman dimuat
document.querySelectorAll('.item-row').forEach(attachRowEvents);

// Konfirmasi sebelum submit — mencegah klik tidak sengaja
document.getElementById('purchaseForm').addEventListener('submit', function (e) {
    const total = document.getElementById('grandTotal').textContent;
    if (!confirm(`Simpan pembelian ini?\nTotal: ${total}\n\nStok produk akan diperbarui secara otomatis.`)) {
        e.preventDefault();
    }
});
</script>
@endsection
