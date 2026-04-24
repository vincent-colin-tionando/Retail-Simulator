<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Menampilkan halaman toko publik: katalog produk dan detail produk.
 *
 * Route ini TIDAK dilindungi auth — tamu bisa melihat produk.
 * Logika perbedaan harga dan tombol beli ditangani oleh komponen
 * <x-price-button> berdasarkan status login saat view dirender.
 */

class CatalogController extends Controller
{
    // INDEX — Halaman katalog produk (grid + filter)
    public function index(Request $request): View
    {
        // Query produk 
        $products = Product::query()->active() // hanya produk yang is_active = true

            // Eager load harga KEDUA role sekaligus.
            // Komponen <x-price-button> akan memilih yang sesuai saat render.
            // Ini satu query JOIN, jauh lebih efisien daripada N+1 query per produk.
            ->with(['category', 'prices'])

            // Filter pencarian nama atau SKU
            ->search($request->input('search'))

            // Filter berdasarkan kategori yang dipilih
            ->when($request->input('category'), fn ($q, $slug) =>
                $q->whereHas('category', fn ($c) => $c->where('slug', $slug))
            )

            // Urutkan: terbaru ditambahkan (default), atau populer (bisa dikembangkan)
            ->latest()
            ->paginate(12)
            ->withQueryString(); // pertahankan filter saat ganti halaman

        // Daftar kategori untuk sidebar filter 
        // Hanya kategori yang memiliki produk aktif — agar filter tidak kosong
        $categories = Category::active()
            ->ordered()
            ->whereHas('products', fn ($q) => $q->active())
            ->get();

        return view('shop.catalog.index', compact('products', 'categories'));
    }

    // SHOW — Halaman detail satu produk
    public function show(Product $product): View
    {
        // Tolak akses ke produk nonaktif untuk semua pengguna (termasuk guest)
        // Admin tetap bisa akses via panel admin mereka
        abort_if(! $product->is_active, 404);

        // Load semua relasi yang dibutuhkan di halaman detail
        $product->load([
            'category',
            'prices', // dibutuhkan oleh <x-price-button>
        ]);

        // Produk serupa (kategori sama, bukan produk ini sendiri) — maksimal 4
        $related = Product::active()
            ->with(['category', 'prices'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('shop.catalog.show', compact('product', 'related'));
    }
}
