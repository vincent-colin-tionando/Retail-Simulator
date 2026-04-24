@extends('layouts.admin')

@section('title', 'Dashboard')

{{-- Breadcrumb --}}
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
@endsection

@section('content')

{{-- BAGIAN 1 — Summary Cards
     
    Empat kartu berderet:
       1. Total penjualan hari ini (dari order completed)
       2. Total order hari ini (semua status)
       3. Produk stok menipis (≤ stock_min)
       4. Jumlah distributor (semua + pending verifikasi)
--}}
<div class="row g-3 mb-4">

    {{-- ── Kartu 1: Total Penjualan Hari Ini ── --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 h-100 shadow-sm" style="background: linear-gradient(135deg, #6c5ce7, #a29bfe)">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        {{-- Label kartu --}}
                        <p class="small text-uppercase fw-semibold opacity-75 mb-1" style="font-size:.72rem; letter-spacing:.06em">
                            Penjualan Hari Ini
                        </p>

                        {{-- Nilai utama: format Rupiah, default "Rp 0" --}}
                        <p class="fs-4 fw-bold mb-0">
                            Rp {{ number_format($todaySales, 0, ',', '.') }}
                        </p>

                        {{-- Sub-info: konteks order selesai --}}
                        <p class="small opacity-75 mb-0">
                            {{ $todayOrderCount }} order masuk hari ini
                        </p>
                    </div>
                    <i class="bi bi-cash-stack opacity-50" style="font-size:2.4rem"></i>
                </div>
            </div>
            <div class="card-footer border-0 bg-transparent pt-0 pb-3">
                {{-- Link ke daftar order completed hari ini --}}
                <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" class="text-white text-decoration-none small">
                    Lihat order selesai ->
                </a>
            </div>
        </div>
    </div>

    {{-- ── Kartu 2: Total Order Hari Ini ── --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 h-100 shadow-sm" style="background: linear-gradient(135deg, #ff6b6b, #ee5a24)">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="small text-uppercase fw-semibold opacity-75 mb-1" style="font-size:.72rem; letter-spacing:.06em">
                            Total Order Hari Ini
                        </p>

                        {{-- Angka besar — display-5 agar mencolok --}}
                        <p class="display-5 fw-bold mb-0">{{ $todayOrderCount }}</p>

                        {{-- Breakdown status pending & processing --}}
                        <p class="small opacity-75 mb-0">
                            {{ $pendingOrders }} pending · {{ $processingOrders }} diproses
                        </p>
                    </div>
                    <i class="bi bi-bag-check opacity-50" style="font-size:2.4rem"></i>
                </div>
            </div>
            <div class="card-footer border-0 bg-transparent pt-0 pb-3">
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-white text-decoration-none small">
                    Proses order pending ->
                </a>
            </div>
        </div>
    </div>

    {{-- ── Kartu 3: Stok Menipis ── --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 h-100 shadow-sm" style="background: linear-gradient(135deg, #f9ca24, #f0932b)">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="small text-uppercase fw-semibold opacity-75 mb-1" style="font-size:.72rem; letter-spacing:.06em">
                            Stok Menipis
                        </p>
                        <p class="display-5 fw-bold mb-0">{{ $lowStockCount }}</p>
                        <p class="small opacity-75 mb-0">produk perlu restok segera</p>
                    </div>
                    <i class="bi bi-box-seam opacity-50" style="font-size:2.4rem"></i>
                </div>
            </div>
            <div class="card-footer border-0 bg-transparent pt-0 pb-3">
                <a href="{{ route('admin.purchases.create') }}" class="text-white text-decoration-none small">
                    + Catat pembelian stok ->
                </a>
            </div>
        </div>
    </div>

    {{-- ── Kartu 4: Jumlah Distributor ── --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 h-100 shadow-sm" style="background: linear-gradient(135deg, #00b894, #00cec9)">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="small text-uppercase fw-semibold opacity-75 mb-1" style="font-size:.72rem; letter-spacing:.06em">
                            Distributor
                        </p>
                        <p class="display-5 fw-bold mb-0">{{ $distributorCount }}</p>

                        {{-- Sorot berapa yang masih pending verifikasi --}}
                        <p class="small opacity-75 mb-0">
                            @if ($pendingDistributors > 0)
                                <span class="badge bg-warning text-dark me-1">
                                    {{ $pendingDistributors }} menunggu verifikasi
                                </span>
                            @else
                                Semua sudah terverifikasi ✓
                            @endif
                        </p>
                    </div>
                    <i class="bi bi-people opacity-50" style="font-size:2.4rem"></i>
                </div>
            </div>
            <div class="card-footer border-0 bg-transparent pt-0 pb-3">
                <a href="{{ route('admin.users.index', ['role' => 'distributor', 'unverified' => 1]) }}" class="text-white text-decoration-none small">
                    Verifikasi sekarang ->
                </a>
            </div>
        </div>
    </div>

</div>{{-- /row kartu --}}

{{-- BAGIAN 2 — GRAFIK PENJUALAN 7 HARI TERAKHIR (Chart.js)
     
    Data label & data dikirim sebagai JSON dari controller.
    Chart.js diinisialisasi di @push('scripts') bawah.
--}}
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    <i class="bi bi-graph-up-arrow me-2 text-primary"></i>
                    Grafik Penjualan 7 Hari Terakhir
                </span>
                <span class="text-muted small">
                    {{ now()->subDays(6)->translatedFormat('d M') }} – {{ now()->translatedFormat('d M Y') }}
                </span>
            </div>
            <div class="card-body">
                {{--
                    Canvas Chart.js.
                    height="80" → Chart.js menggunakan aspek ratio, bukan px absolut.
                    id="salesChart" → direferensi di JS bawah.
                --}}
                <canvas id="salesChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- BAGIAN 3 — DUA TABEL: Order Terbaru & Stok Menipis --}}
<div class="row g-4 mb-4">

    {{-- ── Tabel Kiri: 10 Pesanan Terbaru ── --}}
    <div class="col-xl-7">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    <i class="bi bi-receipt me-2 text-primary"></i>
                    Pesanan Terbaru
                </span>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Kode Order</th>
                                <th>Pembeli</th>
                                <th>Role</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Status</th>
                                <th class="text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{--
                                Iterasi $recentOrders (Collection dari controller).
                                @forelse -> tampilkan pesan kosong jika belum ada order.
                            --}}
                            @forelse ($recentOrders as $order)
                            <tr>
                                {{-- Kode order sebagai link ke detail --}}
                                <td class="ps-3">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="fw-semibold text-decoration-none small">
                                        {{ $order->order_code }}
                                    </a>
                                    <div class="text-muted" style="font-size:.7rem">
                                        {{ $order->created_at->diffForHumans() }}
                                    </div>
                                </td>

                                {{-- Nama pembeli (eager loaded) --}}
                                <td class="small">{{ $order->user->name ?? '—' }}</td>

                                {{-- Role saat order dibuat (snapshot buyer_role) --}}
                                <td>
                                    <span class="badge
                                        {{ $order->buyer_role === 'distributor' ? 'bg-primary' : 'bg-success' }}"
                                          style="font-size:.65rem">
                                        {{ ucfirst($order->buyer_role) }}
                                    </span>
                                </td>

                                {{-- Total harga —  number_format untuk ribuan --}}
                                <td class="text-end small fw-semibold">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </td>

                                {{-- Badge status — accessor status_badge_class & status_label dari model --}}
                                <td class="text-center">
                                    <span class="badge {{ $order->status_badge_class }}"
                                          style="font-size:.68rem">
                                        {{ $order->status_label }}
                                    </span>
                                </td>

                                {{-- Tombol detail --}}
                                <td class="text-center pe-3">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-xs btn-outline-secondary btn-sm py-0 px-2">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                    Belum ada pesanan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>{{-- /col tabel order --}}

    {{-- ── Tabel Kanan: Produk Stok Menipis ── --}}
    <div class="col-xl-5">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    <i class="bi bi-exclamation-triangle me-2 text-warning"></i>
                    Stok Menipis
                </span>
                <a href="{{ route('admin.purchases.create') }}" class="btn btn-sm btn-warning">
                    + Beli Stok
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Produk</th>
                                <th>Kategori</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center pe-3">Min</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- $lowStockProducts diurutkan dari stok terendah sehingga produk paling kritis muncul pertama. --}}
                            @forelse ($lowStockProducts as $product)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('admin.products.show', $product) }}" class="text-dark text-decoration-none small">
                                        {{ Str::limit($product->name, 28) }}
                                    </a>
                                </td>
                                <td class="small text-muted">
                                    {{ $product->category->name ?? '—' }}
                                </td>

                                {{-- Badge merah untuk stok saat ini --}}
                                <td class="text-center">
                                    <span class="badge bg-danger">{{ $product->stock }}</span>
                                </td>

                                {{-- Angka minimum stok yang ditetapkan --}}
                                <td class="text-center text-muted small pe-3">
                                    {{ $product->stock_min }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-check-circle text-success fs-4 d-block mb-1"></i>
                                    Semua stok aman! 
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>{{-- /col tabel stok --}}
</div>{{-- /row tabel --}}

{{-- BAGIAN 4 — PANEL VERIFIKASI DISTRIBUTOR
     
    Hanya tampil jika ada distributor yang menunggu verifikasi.
    Menampilkan daftar + tombol toggle verifikasi inline.
--}}
@if ($pendingDistributors > 0)
<div class="row g-4">
    <div class="col-12">
        <div class="card shadow-sm border-warning">

            {{-- Header kartu dengan badge jumlah pending --}}
            <div class="card-header bg-warning bg-opacity-10 d-flex align-items-center">
                <i class="bi bi-person-check me-2 text-warning fs-5"></i>
                <span class="fw-semibold">Verifikasi Distributor</span>
                <span class="badge bg-warning text-dark ms-2">
                    {{ $pendingDistributors }} menunggu
                </span>
                <a href="{{ route('admin.users.index', ['role' => 'distributor', 'unverified' => 1]) }}"
                   class="btn btn-sm btn-outline-warning ms-auto">
                    Kelola Semua
                </a>
            </div>

            <div class="card-body p-0">
                @php
                    $pendingList = \App\Models\User::role('distributor')
                        ->where('is_verified', false)
                        ->withCount('orders')
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp

                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Nama</th>
                                <th>Email</th>
                                <th>Perusahaan</th>
                                <th class="text-center">Total Order</th>
                                <th class="text-center">Daftar</th>
                                <th class="text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingList as $dist)
                            <tr>
                                <td class="ps-3 fw-semibold small">{{ $dist->name }}</td>
                                <td class="small text-muted">{{ $dist->email }}</td>
                                <td class="small">{{ $dist->company_name ?? '—' }}</td>
                                <td class="text-center small">{{ $dist->orders_count }}</td>
                                <td class="text-center small text-muted">
                                    {{ $dist->created_at->translatedFormat('d M Y') }}
                                </td>
                                <td class="text-center pe-3">
                                    <div class="d-flex gap-1 justify-content-center">
                                        {{-- Tombol Verifikasi: --}}
                                        <form action="{{ route('admin.users.verify', $dist) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success py-0 px-2">
                                                <i class="bi bi-check-lg"></i> Verifikasi
                                            </button>
                                        </form>

                                        {{-- Link ke halaman edit user lengkap --}}
                                        <a href="{{ route('admin.users.edit', $dist) }}"
                                           class="btn btn-sm btn-outline-secondary py-0 px-2">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>{{-- /card-body --}}
        </div>{{-- /card --}}
    </div>
</div>{{-- /row verifikasi --}}
@endif

@endsection

{{-- BAGIAN 5 — SCRIPTS: Chart.js via CDN --}}
@push('scripts')

    {{-- Chart.js 4 via CDN (tidak perlu npm/vite) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /**
            * Data dikirim dari PHP ke JS via json_encode.
            *
            * Contoh output:
            *   const labels = ["Sen, 13 Mei", "Sel, 14 Mei", ..., "Min, 19 Mei"];
            *   const data   = [0, 125000, 350000, 0, 487000, 215000, 620000];
            */
            const labels = @json($chartLabels);
            const data   = @json($chartData);

            // Ambil elemen canvas berdasarkan id
            const ctx = document.getElementById('salesChart');

            if (!ctx) return; // Guard: jika canvas tidak ditemukan, hentikan eksekusi

            new Chart(ctx, {
                type: 'bar', // Tipe grafik: bar chart (batang)

                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Penjualan (Rp)',
                        data: data,

                        // Warna batang — gradasi biru-ungu konsisten dengan kartu
                        backgroundColor: 'rgba(108, 92, 231, 0.7)',
                        borderColor:     'rgba(108, 92, 231, 1)',
                        borderWidth: 1,

                        // Batang rounded di ujung atas
                        borderRadius: 4,
                        borderSkipped: false,
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: true,

                    plugins: {
                        legend: {
                            display: false // Sembunyikan legend — label sudah jelas dari sumbu
                        },
                        tooltip: {
                            callbacks: {
                                /**
                                * Format nilai tooltip menjadi Rupiah.
                                *
                                * context.raw → nilai mentah (float).
                                * toLocaleString('id-ID') → format angka Indonesia.
                                */
                                label: function (context) {
                                    const val = context.raw;
                                    return ' Rp ' + val.toLocaleString('id-ID');
                                }
                            }
                        }
                    },

                    scales: {
                        x: {
                            grid: { display: false }, // Hilangkan grid vertikal agar lebih bersih
                            ticks: { font: { size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: { size: 11 },
                                /**
                                * Format label sumbu Y menjadi singkatan Rupiah.
                                *
                                * Contoh: 1500000 → "Rp 1,5 jt"
                                *          500000  → "Rp 500 rb"
                                */
                                callback: function (value) {
                                    if (value >= 1_000_000) {
                                        return 'Rp ' + (value / 1_000_000).toFixed(1) + ' jt';
                                    }
                                    if (value >= 1_000) {
                                        return 'Rp ' + (value / 1_000).toFixed(0) + ' rb';
                                    }
                                    return 'Rp ' + value;
                                }
                            }
                        }
                    }
                }
            });

        });
    </script>
@endpush