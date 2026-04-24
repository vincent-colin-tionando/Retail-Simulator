<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseRequest;
use App\Models\Product; 
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  
use Illuminate\View\View;

class PurchaseController extends Controller
{
    // INDEX — Riwayat semua transaksi pembelian
    // Route: GET /admin/purchases
    public function index(Request $request): View
    {
        $purchases = Purchase::query()
            ->with(['supplier', 'user'])
            ->withCount('items')
            ->when($request->input('supplier_id'), fn ($q, $id) =>
                $q->where('supplier_id', $id)
            )
            ->when($request->input('status'), fn ($q, $s) =>
                $q->where('status', $s)
            )
            ->when($request->input('date_from'), fn ($q, $d) =>
                $q->where('purchased_at', '>=', $d)
            )
            ->when($request->input('date_to'), fn ($q, $d) =>
                $q->where('purchased_at', '<=', $d)
            )
            ->latest('purchased_at') 
            ->paginate(20)
            ->withQueryString();

        // Untuk dropdown filter supplier
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('admin.purchases.index', compact('purchases', 'suppliers'));
    }

    // CREATE — Form catat pembelian baru
    // Route: GET /admin/purchases/create
    public function create(): View
    {
        // Hanya supplier aktif yang boleh dipilih
        $suppliers = Supplier::active()->orderBy('name')->get();

        // Produk aktif beserta stok saat ini untuk referensi admin
        // Urutkan berdasarkan nama agar mudah dicari di form
        $products = Product::active()
            ->with('category')
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'unit', 'stock', 'category_id']);

        return view('admin.purchases.create', compact('suppliers', 'products'));
    }

    // STORE — Simpan pembelian baru + update stok (atomik via transaksi)
    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        // Semua validasi sudah beres. Langsung transaksi.
        DB::transaction(function () use ($request) {

            // a. Buat record Purchase (header faktur) 
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'user_id' => auth()->id(), // admin yang sedang login
                'invoice_no' => $request->invoice_no,
                'total_cost' => 0, // akan diperbarui setelah semua item disimpan
                'status' => $request->status,
                'notes' => $request->notes,
                'purchased_at' => $request->purchased_at,
            ]);

            // b. Simpan setiap item & kumpulkan subtotal 
            // PurchaseItemObserver::created() akan auto-increment stok produk
            $totalCost = 0;

            foreach ($request->items as $row) {
                $subtotal = $row['quantity'] * $row['unit_cost'];
                $totalCost += $subtotal;

                // PurchaseItem::create() di sini akan memicu
                // PurchaseItemObserver::created(), yang secara otomatis
                // menjalankan: products.stock += quantity
                //
                // Karena masih di dalam transaksi yang sama,
                // jika baris ini gagal -> increment stok ikut di-rollback.
                $purchase->items()->create([
                    'product_id' => $row['product_id'],
                    'quantity' => $row['quantity'],
                    'unit_cost' => $row['unit_cost'],
                    'subtotal' => $subtotal,
                ]);
            }

            // c. Update total cost setelah semua item tersimpan
            // Dihitung server-side untuk mencegah manipulasi dari JS/frontend
            $purchase->update(['total_cost' => $totalCost]);
        });

        // Jika kode sampai sini, semua berhasil → transaksi di-commit.
        // Jika ada exception di dalam closure → otomatis rollback.
        return redirect()
            ->route('admin.purchases.index')
            ->with('success', "Pembelian dengan invoice \"{$request->invoice_no}\" berhasil dicatat. Stok produk telah diperbarui.");
    }

    // SHOW — Detail satu transaksi pembelian
    // Route: GET /admin/purchases/{purchase}
    public function show(Purchase $purchase): View
    {
        // Load semua relasi yang dibutuhkan di halaman detail
        $purchase->load([
            'supplier',
            'user',
            'items.product.category', // item -> produk -> kategori produk
        ]);

        return view('admin.purchases.show', compact('purchase'));
    }
}
