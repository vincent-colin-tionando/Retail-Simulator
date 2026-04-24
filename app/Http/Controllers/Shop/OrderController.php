<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Memungkinkan consumer dan distributor melihat riwayat pesanan
 * mereka sendiri di storefront — bukan panel admin.
 *
 * Ini berbeda dari Admin\OrderController yang menampilkan SEMUA pesanan.
 * Di sini setiap user hanya bisa melihat pesanannya sendiri.
 * 
 * Mengapa DB::transaction + lockForUpdate?
 * ─────────────────────────────────────────
 * Tanpa lock: dua request bisa membaca stok = 1 secara bersamaan,
 * keduanya lolos validasi, lalu keduanya decrement → stok jadi -1.
 * Dengan lockForUpdate: query SELECT ... FOR UPDATE mengunci baris
 * di database hingga transaksi selesai, mencegah race condition.
 */

class OrderController extends Controller
{
    // CREATE - Tampilkan form checkout
    public function create(): View|RedirectResponse
    {
        $cart = session('cart', []);

        // Jika cart kosong, redirect balik ke halaman cart
        if (empty($cart)) {
            return redirect()->route('shop.cart.index')
                ->with('error', 'Keranjang Anda kosong. Tambahkan produk sebelum checkout.');
        }

        // Hitung ringkasan untuk ditampilkan di halaman checkout
        $cartItems  = array_values($cart);
        $grandTotal = array_sum(array_map(
            fn ($i) => $i['unit_price'] * $i['quantity'],
            $cartItems
        ));

        $user = auth()->user();

        return view('shop.checkout.index', compact('cartItems', 'grandTotal', 'user'));
    }

    // STORE - Proses checkout: buat order + kurangi stok (atomik)
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        // ── 1. Ambil cart dari session ──
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.cart.index')
                ->with('error', 'Keranjang Anda kosong.');
        }

        // ── 2. Validasi form checkout ──
        $validated = $request->validated(); 
        $user = auth()->user();
        $productIds = array_keys($cart);

        // ── 3. Jalankan semua dalam satu transaksi ──
        //
        // DB::transaction menjamin ATOMISITAS:
        //   - Jika satu produk stoknya kurang → semua dibatalkan
        //   - Jika DB error saat insert OrderItem → order header ikut rollback
        //   - Tidak ada kondisi: order terbuat tapi stok belum berkurang
        //
        $order = DB::transaction(function () use ($cart, $validated, $user, $productIds) {

            // ── a. Kunci baris produk untuk mencegah race condition stok ──
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate() // kunci baris di DB
                ->get()
                ->keyBy('id'); // index by ID untuk lookup O(1)

            // ── b. Validasi stok semua item SEBELUM membuat order ──
            $stockErrors = [];

            foreach ($cart as $productId => $item) {
                $product = $products->get($productId);

                // Produk tidak ditemukan (mungkin sudah dihapus)
                if (! $product) {
                    $stockErrors[] = "Produk \"{$item['name']}\" tidak lagi tersedia.";
                    continue;
                }

                // Produk dinonaktifkan setelah masuk cart
                if (! $product->is_active) {
                    $stockErrors[] = "Produk \"{$product->name}\" sudah tidak dijual.";
                    continue;
                }

                // Stok tidak mencukupi
                if ($product->stock < $item['quantity']) {
                    $stockErrors[] = "Stok \"{$product->name}\" hanya tersisa "
                        . "{$product->stock} {$product->unit}, "
                        . "Anda memesan {$item['quantity']} {$product->unit}.";
                }
            }

            // Jika ada error stok → lempar exception → transaction rollback
            if (! empty($stockErrors)) {
                // Gunakan ValidationException agar bisa ditangkap Laravel
                // dan pesan errornya dikirim ke view secara elegan
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'stock' => $stockErrors,
                ]);
            }

            // ── c. Buat record Order (header faktur) ──
            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => $this->generateOrderCode(),
                'status' => 'pending',
                'buyer_role' => $user->role, // snapshot role saat order
                'total_price' => 0, // akan diupdate setelah semua item
                'shipping_address' => $validated['shipping_address'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // ── d. Buat OrderItem + kurangi stok per produk ────────
            $totalPrice = 0;

            foreach ($cart as $productId => $item) {
                $product  = $products->get($productId);
                $subtotal = $item['unit_price'] * $item['quantity'];
                $totalPrice += $subtotal;

                // Buat baris order item
                // Harga diambil dari SESSION (yang sudah diambil dari DB saat
                // produk masuk cart) — BUKAN dari database ulang.
                // Ini menjamin konsistensi: harga yang dilihat user = harga yang dibayar.
                $order->items()->create([
                    'product_id' => $productId,
                    'product_name' => $item['name'],    // snapshot nama produk
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'], // snapshot harga dari session
                    'subtotal' => $subtotal,
                ]);

                // Kurangi stok — decrement() aman secara concurrent:
                // SQL: UPDATE products SET stock = stock - ? WHERE id = ?
                // Dikombinasikan dengan lockForUpdate() di atas → atomic & safe
                $product->decrement('stock', $item['quantity']);
            }

            // ── e. Update total_price di Order ─
            // Dihitung di server setelah semua item selesai — tidak bergantung
            // pada nilai yang dikirim dari form (mencegah manipulasi total).
            $order->update(['total_price' => $totalPrice]);

            return $order;
        });

        // ── 4. Hapus cart dari session setelah order berhasil ─────
        session()->forget('cart');

        // ── 5. Redirect ke halaman sukses ──
        return redirect()->route('shop.orders.success', $order)
            ->with('success', "Pesanan {$order->order_code} berhasil dibuat!");
    }

    // SUCCESS - Halaman konfirmasi order berhasil
    public function success(Order $order): View
    {
        // Policy OrderPolicy::view() memastikan $order->user_id === auth()->id()
        // (atau admin). Jika tidak, Laravel otomatis lempar 403.
        $this->authorize('view', $order);
        $order->load('items');
        return view('shop.orders.success', compact('order'));
    }

    // INDEX — Riwayat pesanan milik user yang sedang login
    public function index(): View
    {
        $user = auth()->user();

        $orders = Order::forUser($user)
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('shop.orders.index', compact('orders'));
    }

    // SHOW — Detail satu pesanan
    public function show(Order $order): View
    {
        // Pastikan pesanan ini milik user yang sedang login (via OrderPolicy)
        $this->authorize('view', $order);

        $order->load([
            'items.product',  // detail produk untuk setiap item
        ]);

        return view('shop.orders.show', compact('order'));
    }

    // PRIVATE HELPER
    /**
     * Generate kode order unik.
     * Format: ORD-YYYYMMDD-XXXX (contoh: ORD-20260419-0042)
     *
     * Angka urut dihitung dari jumlah order hari ini + 1,
     * sehingga kode selalu unik tanpa perlu UUID.
     */
    private function generateOrderCode(): string
    {
        $today = now()->format('Ymd');
        $countToday = Order::whereDate('created_at', today())->count();
        $req = str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

        return "ORD-{$today}-{$req}";
    }
}
