<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order; 
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * DashboardController — Halaman ringkasan admin.
 *
 * Semua query memakai Eloquent aggregate (sum, count, whereDate, groupBy)
 * sehingga tetap readable dan tidak menyertakan raw SQL string.
 *
 * Route: GET /admin/dashboard  →  admin.dashboard
 */
class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        /* ────────────────────────────────────────────────────────────────
         |  STATISTIK KARTU RINGKASAN
         |  Setiap variabel merepresentasikan satu kartu di view.
         * ──────────────────────────────────────────────────────────────── */

        // Total penjualan (revenue) hari ini dari order berstatus completed.
        $todaySales = Order::whereDate('created_at', today())
            ->where('status', 'completed')->sum('total_price');

        // Total order yang masuk hari ini (semua status).
        $todayOrderCount = Order::whereDate('created_at', today())
            ->count();

        // Jumlah produk dengan stok dibawah ambang batas (stock_min).
        $lowStockCount = Product::lowStock()->count();

        // Jumlah akun distributor (terverifikasi maupun belum).
        $distributorCount = User::role('distributor')->count();

        // Distributor yang belum diverifikasi - untuk badge sidebar & kartu
        $pendingDistributors = User::role('distributor')
            ->where('is_verified', false)->count();

        
        /* ────────────────────────────────────────────────────────────────
         |  TABEL DATA
         * ──────────────────────────────────────────────────────────────── */

        // 10 pesanan terbaru — eager load relasi user agar tidak N+1.
        $recentOrders = Order::with('user')->latest()->take(10)->get();

        // Produk dengan stok menipis, diurutkan dari yang paling kritis.
        $lowStockProducts = Product::lowStock()
            ->with('category')->orderBy('stock')->take(10)->get();

        
         /* ────────────────────────────────────────────────────────────────
         |  DATA GRAFIK — Penjualan 7 hari terakhir (Chart.js)
         * ──────────────────────────────────────────────────────────────── */

        // Range: 6 hari lalu s/d hari ini → 7 titik data
        $startDate = now()->subDays(6)->startOfDay();
        $endDate   = now()->endOfDay();

        // Query penjualan per hari.
        $salesByDate = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        // Bangun array label & data yang sudah sinkron (aligned).
        $chartLabels = [];  // Contoh: ['Senin', 'Selasa', ...]
        $chartData   = [];  // Contoh: [0, 150000, 320000, ...]

        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            // Format label singkat untuk sumbu X chart
            $chartLabels[] = $date->translatedFormat('D, d M');

            // Ambil penjualan hari itu; default 0 jika tidak ada order
            $chartData[] = (float) ($salesByDate[$date->toDateString()] ?? 0);
        }

        /* ────────────────────────────────────────────────────────────────
         |  TAMBAHAN: order pending & processing untuk konteks admin
         * ──────────────────────────────────────────────────────────────── */
        $pendingOrders    = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();

        // Kirim semua variabel ke view
        return view('admin.dashboard', compact(
            // Kartu statistik
            'todaySales',
            'todayOrderCount',
            'lowStockCount',
            'distributorCount',
            'pendingDistributors',
            'pendingOrders',
            'processingOrders',
            
            // Tabel
            'recentOrders',
            'lowStockProducts',

            // Chart
            'chartLabels',
            'chartData',
        ));
    }
}
