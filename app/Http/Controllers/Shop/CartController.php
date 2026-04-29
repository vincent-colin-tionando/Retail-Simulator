<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CartController — Keranjang Belanja Berbasis Session
 *
 * Mengapa Session, bukan tabel cart_items di database?
 * ─────────────────────────────────────────────────────
 * Session lebih cepat (tidak ada query INSERT/UPDATE per aksi),
 * tidak butuh migrasi, dan cocok untuk guest sekalipun.
 *
 * Struktur data session (key: 'cart'):
 * ─────────────────────────────────────
 *   session('cart') = [
 *     "5" => [                      ← key = product_id (string)
 *       'product_id' => 5,
 *       'name' => 'LED Bulb 7W', // snapshot nama
 *       'sku'  => 'HNC-001',     // snapshot SKU
 *       'unit' => 'pcs',
 *       'image_url'  => 'http://...',
 *       'unit_price' => 15000.0,    ← diambil dari DB server-side
 *       'quantity'   => 3,
 *     ],
 *     "12" => [ ... ],
 *   ]
 *
 * Prinsip keamanan harga:
 * ────────────────────────
 * Harga TIDAK pernah diambil dari request/form.
 * Setiap kali produk masuk cart, controller mengambil harga
 * langsung dari tabel product_prices di database.
 * Ini mencegah manipulasi harga dari sisi client.
 * 
 */

class CartController extends Controller
{
    // ─── Nama key di session ───
    private const SESSION_KEY = 'cart';
    
    // INDEX — Halaman isi keranjang
    public function index(): View
    {
        $cart = $this->getCart();
        $role = auth()->check() ? auth()->user()->effectivePriceRole() : 'consumer';
        $cartItems = $this->buildCartItems($cart, $role);

        // Grand total dihitung dari harga di session (bukan re-query ke DB)
        // Harga yang ditampilkan = harga yang akan dipesan
        $grandTotal = array_sum(array_column($cartItems, 'subtotal'));

        return view('shop.cart.index', compact('cartItems', 'grandTotal', 'role'));
    }

    // STORE — Tambah produk ke keranjang
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        // Ambil produk BESERTA harganya dari DB
        $product = Product::with('prices')->active()->findOrFail($request->product_id);

        // Cek apakah produk masih tersedia
        if ($product->stock <= 0) {
            return back()->with('error', "Produk \"{$product->name}\" sedang habis stok.");
        }

        // ── Ambil harga dari DB server-side (BUKAN dari request) ────
        // effectivePriceRole() ada di model User — distributor terverif
        // mendapat harga grosir, selainnya harga consumer.
        $role = auth()->check() ? auth()->user()->effectivePriceRole() : 'consumer';
        $price = $product->priceFor($role);

        if ($price === null) {
            return back()->with('error', "Harga produk \"{$product->name}\" belum tersedia.");
        }

        // ── Tambah atau update qty di session ───────────────────────
        $cart = $this->getCart();
        $key = (string) $product->id;
        $currentQty = $cart[$key]['quantity'] ?? 0;
        $newQty = $currentQty + $request->quantity;

        // Cek apakah total quantity tidak melebihi stok
        if ($newQty > $product->stock) {
            return back()->with('error',
                "Total \"{$product->name}\" di keranjang ({$newQty}) melebihi stok tersedia ({$product->stock}).");
        }

        // Simpan ke session — harga diambil dari DB, bukan dari form
        $cart[$key] = [
            'product_id' => $product->id,
            'name' => $product->name, // snapshot nama
            'sku' => $product->sku,
            'unit' => $product->unit,
            'image_url' => $product->image_url,
            'unit_price' => $price, // didapat dari DB, server-side
            'quantity' => $newQty,
        ];

        $this->saveCart($cart);

        return back()->with('success', "\"{$product->name}\" berhasil ditambahkan ke keranjang.");
    }

    // UPDATE — Ubah quantity satu item
    public function update(Request $request, int $productId): RedirectResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $cart = $this->getCart();
        $key  = (string) $productId;

        // Pastikan item memang ada di cart
        if (!isset($cart[$key])) {
            return back()->with('error', 'Item tidak ditemukan di keranjang.');
        }

        // Jika quantity = 0 -> hapus item
        if ($request->quantity == 0) {
            $name = $cart[$key]['name'];

            unset($cart[$key]);
            $this->saveCart($cart);

            return back->with('success', "\"{$name}"\"dihapus dari keranjang.");
        }
        
        // Cek stok terbaru dari DB agar tidak over-order
        $product = Product::find($productId);
        if ($product && $request->quantity > $product->stock) {
            return back()->with('error',
                "Stok \"{$product->name}\" hanya tersisa {$product->stock} unit.");
        }

        // Update quantity — harga TIDAK diubah (tetap dari session)
        $cart[$key]['quantity'] = $request->quantity;
        $this->saveCart($cart);

        return back()->with('success', 'Jumlah item diperbarui.');
    }

    // DESTROY — Hapus satu item dari keranjang
    public function destroy(int $productId): RedirectResponse
    {
        $cart = $this->getCart();
        $key  = (string) $productId;
        $name = $cart[$key]['name'] ?? 'Item';

        unset($cart[$key]);
        $this->saveCart($cart);

        return back()->with('success', "\"{$name}\" dihapus dari keranjang.");
    }

    // CLEAR - Kosongkan seluruh keranjang
    public function clear(): RedirectResponse
    {
        session()->forget(self::SESSION_KEY);
        return back()->with('success', 'Keranjang berhasil dikosongkan.');
    }

    // PRIVATE HELPER
    /**
     * Ambil isi keranjang dari session.
     * Kembalikan array kosong jika belum ada.
     *
     * @return array<string, array{product_id:int, name:string, sku:string,
     *                             unit:string, image_url:string,
     *                             unit_price:float, quantity:int}>
     */
    private function getCart(): array
    {
        return session(self::SESSION_KEY, []);
    }

    /** Simpan array cart ke session. */
    private function saveCart(array $cart): void
    {
        session([self::SESSION_KEY => $cart]);
    }

    /**
     * Bangun array item yang sudah dihitung subtotal-nya.
     * Dipisah ke method tersendiri agar bisa dipanggil ulang
     * di view maupun di OrderController.
     *
     * @param  array  $cart Data mentah dari session
     * @param  string $role 'consumer' | 'distributor'
     * @return array  Array item + subtotal field
     */
    private function buildCartItems(array $cart, string $role): array
    {
        return array_map(function (array $item) {
            return array_merge($item, [
                'subtotal' => $item['unit_price'] * $item['quantity'],
            ]);
        }, $cart);
    }

    /**
     * Helper statis: jumlah total item di keranjang.
     * Dipakai di layout/navbar untuk badge cart.
     *
     * Contoh: CartController::count()
     */
    public static function count(): int
    {
        $cart = session(self::SESSION_KEY, []);
        return array_sum(array_column($cart, 'quantity'));
    }
}
