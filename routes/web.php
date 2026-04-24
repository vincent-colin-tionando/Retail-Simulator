<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Shop\CatalogController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\OrderController as ShopOrderController;
use Illuminate\Support\Facades\Route;

// REDIRECT ROOT berdasarkan role
Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'distributor' => redirect()->route('distributor.dashboard'),
            'consumer' => redirect()->route('consumer.dashboard'),
            default => redirect()->route('shop.index'),
        };
    }
    return redirect()->route('shop.index');
});

// STOREFRONT — PUBLIK (tidak perlu login)
// Guest bisa mengakses katalog dan detail produk 
// tetapi tidak bisa ke cart/checkout sehingga memerlukan login.
Route::name('shop.')->group(function () {

    // Katalog: GET /shop  dan  GET /shop/{product}
    Route::get('/shop', [CatalogController::class, 'index'])->name('index');
    Route::get('/shop/{product}', [CatalogController::class, 'show'])->name('products.show');

    // CART dan CHECKOUT (wajib login)
    // Middleware 'auth' di sini menyebabkan Laravel otomatis redirect ke halaman login jika tamu mencoba mengakses URL cart.
    Route::middleware('auth')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
        Route::patch ('/cart/{productId}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{productId}', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

        // CHECKOUT
        // GET  /checkout - form isian (alamat, metode bayar, catatan)
        // POST /checkout - proses: buat order + kurangi stok
        Route::get('/checkout', [ShopOrderController::class, 'create'])->name('checkout');
        Route::post('/checkout', [ShopOrderController::class, 'store'])->name('checkout.store');

        // Riwayat pesanan pembeli
        Route::get('/orders', [ShopOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [ShopOrderController::class, 'show'])->name('orders.show');

        // Halaman konfirmasi sukses (muncul sekali setelah checkout berhasil)
        Route::get('/orders/{order}/success', [ShopOrderController::class, 'success'])->name('orders.success');
    });
});


// GROUP ROUTE ADMIN
// Semua route di sini dilindungi middleware:
//   - 'auth'       : wajib login
//   - 'role:admin' : wajib role admin (via CheckRole middleware)
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard admin
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Manajemen Kategori 
        Route::resource('categories', CategoryController::class);

        // Manajemen Produk 
        Route::resource('products', ProductController::class);

        // Toggle aktif/nonaktif produk tanpa perlu buka halaman edit
        Route::patch('/products/{product}/toggle', function (\App\Models\Product $product) {
            $product->update(['is_active' => ! $product->is_active]);
            return response()->json([
                'is_active' => $product->is_active,
                'message'   => $product->is_active ? 'Produk diaktifkan' : 'Produk dinonaktifkan',
            ]);
        })->name('products.toggle');

        // Manajemen Supplier
        Route::resource('suppliers', SupplierController::class)->except(['show']);

        // Manajemen Pembelian Stok      
        Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);

        // Manajemen Pengguna
        Route::resource('users', UserController::class);

        // Verifikasi / cabut verifikasi distributor
        Route::patch('/users/{user}/verify', [UserController::class, 'toggleVerify'])->name('users.verify');

        // Penjualan: Pesanan 
        // Tidak pakai Route::resource karena method-nya custom (bukan CRUD standar)
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');

        // Transisi status order (masing-masing idempoten & dilindungi cek di controller)
        Route::patch('/orders/{order}/process', [AdminOrderController::class, 'process'])->name('orders.process');
        Route::patch('/orders/{order}/complete', [AdminOrderController::class, 'complete'])->name('orders.complete');
        Route::patch('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');

        // Simpan catatan admin tanpa mengubah status
        Route::patch('/orders/{order}/note', [AdminOrderController::class, 'updateNote'])->name('orders.note');
    });

// GRUP DISTRIBUTOR
Route::middleware(['auth', 'role:distributor'])
    ->prefix('distributor')
    ->name('distributor.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('distributor.dashboard');
        })->name('dashboard');
    });

// GRUP CONSUMER
Route::middleware(['auth', 'role:consumer'])
    ->prefix('consumer')
    ->name('consumer.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('consumer.dashboard');
        })->name('dashboard');
    });

// PROFIL USER (dari Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
