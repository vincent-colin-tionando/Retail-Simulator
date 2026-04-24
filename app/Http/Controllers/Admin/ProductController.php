<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    // INDEX — Daftar semua produk
    // Route: GET /admin/products/index
    public function index(): View
    {
        $products = Product::query()
            
        // Eager load relasi agar tidak terjadi N+1 query:
            // Tanpa withCount, setiap baris akan query lagi ke DB untuk hitung stok dsb.
            ->with([
                'category',          // tampilkan nama kategori
                'consumerPrice',     // tampilkan harga consumer
                'distributorPrice',  // tampilkan harga distributor
            ])

            // Cari berdasarkan nama atau SKU jika ada keyword di URL (?search=...)
            ->search(request('search'))

            // Filter berdasarkan kategori jika ada (?category_id=...)
            // Jika kategori yang dipilih punya sub-kategori (children), ikutkan juga produk dari children-nya
            ->when(request('category_id'), function ($q, $id) {
                $childIds = \App\Models\Category::where('parent_id', $id)->pluck('id');
                if ($childIds->isNotEmpty()) {

                    // Induk kategori: cari produk di induk + semua children
                    $q->whereIn('category_id', $childIds->push($id));

                } else {

                    // Kategori biasa / leaf: cari langsung
                    $q->where('category_id', $id);
                }
            })

            // Filter status aktif/nonaktif jika ada (?is_active=1 atau =0)
            // Gunakan request('is_active') !== null && !== '' agar 'Semua Status' (string kosong) tidak ikut difilter
            ->when(request('is_active') !== null && request('is_active') !== '', fn ($q) => $q->where('is_active', request('is_active')))
            ->latest()           // urut dari yang terbaru
            ->paginate(15)       // 15 produk per halaman
            ->withQueryString(); // pertahankan parameter filter saat pindah halaman

        // Ambil semua kategori untuk dropdown filter di halaman index
        $categories = Category::active()->ordered()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    // CREATE — Tampilkan form tambah produk
    // Route: GET /admin/products/create
    public function create(): View
    {
        // Ambil semua kategori aktif untuk dropdown
        // with('parent') agar bisa menampilkan nama lengkap "Induk › Sub"
        $categories = Category::with('parent')->active()->ordered()->get();

        return view('admin.products.create', compact('categories'));
    }

    // STORE — Simpan produk baru ke database
    // Route: POST /admin/products/index

    public function store(StoreProductRequest $request): RedirectResponse
    {
        // Semua validasi sudah dijalankan di StoreProductRequest sebelum sampai sini.
        // Jika ada yang gagal, Laravel otomatis redirect balik dengan error.

        // Gunakan DB::transaction agar jika salah satu langkah gagal,
        // seluruh operasi dibatalkan (tidak ada produk tanpa harga, atau sebaliknya).
        DB::transaction(function () use ($request) {
            // 1. Simpan gambar jika ada 

            $imagePath = null;

            if ($request->hasFile('image')) {
                // Simpan ke storage/app/public/products/
                // Kemudian symlink: php artisan storage:link
                $imagePath = $request->file('image')->store('products', 'public');
            }

            // 2. Buat record produk 
            $product = Product::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'sku' => strtoupper($request->sku), // SKU selalu huruf besar
                'unit' => $request->unit,
                'stock' => $request->stock,
                'stock_min' => $request->stock_min,
                'image' => $imagePath,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // 3. Simpan dua baris harga sekaligus
            // updateOrCreate digunakan agar method ini aman dipanggil ulang
            // tanpa membuat duplikat jika sudah ada data harga sebelumnya.
            $product->prices()->createMany([
                ['role_type' => 'consumer', 'price' => $request->price_consumer],
                ['role_type' => 'distributor', 'price' => $request->price_distributor],
            ]);
        });

        return redirect()
            ->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    // SHOW — Detail satu produk (jarang dipakai di admin, tapi wajib ada)
    // Route: GET /admin/products/{product}

    public function show(Product $product): View
    {
        // Load relasi yang dibutuhkan di halaman detail
        $product->load([
            'category', 
            'consumerPrices', 'distributorPrice',
            'orderItems.order', 'purchaseItems.purchase'
        ]);

        return view('admin.products.show', compact('product'));
    }

    // EDIT — Tampilkan form edit produk
    // Route: GET /admin/products/{product}/edit
    public function edit(Product $product): View
    {
        // Load harga agar bisa diisi ulang di form
        $product->load(['consumerPrice', 'distributorPrice']);

        $categories = Category::with('parent')->active()->ordered()->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    // UPDATE — Simpan perubahan produk ke database
    // Route: PUT/PATCH /admin/products/{product}
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        DB::transaction(function () use ($request, $product) {

        // 1. Tangani gambar 
            $imagePath = $product->image; // pertahankan gambar lama secara default

            if ($request->hasFile('image')) {
                // Hapus gambar lama dari storage agar tidak menumpuk
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $imagePath = $request->file('image')->store('products', 'public');
            }

            // 2. Update data produk 
            $product->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'sku' => strtoupper($request->sku),
                'unit' => $request->unit,
                'stock' => $request->stock,
                'stock_min' => $request->stock_min,
                'image' => $imagePath,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // 3. Update harga (updateOrCreate agar tidak duplikat)
            // updateOrCreate(kondisi cari, data yang di-update/dibuat)
            $product->prices()->updateOrCreate(
                ['role_type' => 'consumer'],
                ['price' => $request->price_consumer]
            );
            $product->prices()->updateOrCreate(
                ['role_type' => 'distributor'],
                ['price' => $request->price_distributor]
            );
        });

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    // DESTROY — Hapus produk (soft delete)
    // Route: DELETE /admin/products/{product}
    public function destroy(Product $product): RedirectResponse
    {
        // Cegah hapus hanya jika ada order yang MASIH AKTIF (pending/processing).
        // Order selesai (completed/cancelled) tidak menghalangi penghapusan.
        $hasActiveOrder = $product->orderItems()->whereHas('order', function ($q) {
            $q->whereIn('status', ['pending', 'processing']);
        })->exists();

        if ($hasActiveOrder) {
            return back()->with('error', "Produk \"{$product->name}\" tidak bisa dihapus karena masih ada pesanan aktif (pending/diproses).");
        }
        // SoftDeletes: produk tidak benar-benar terhapus dari DB,
        // kolom deleted_at akan diisi timestamp sekarang.
        // Data tetap aman untuk keperluan audit.
        $product->delete();
        return back()->with('success', "Produk \"{$product->name}\" berhasil dihapus.");
    }
}
