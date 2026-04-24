<?php
 
namespace App\Providers;
 
use App\Models\Order;
use App\Models\PurchaseItem;
use App\Policies\OrderPolicy;
use App\Observers\PurchaseItemObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
 
/**
 * AppServiceProvider — Titik pusat registrasi service, binding, dan gate/policy.
 * 
 * Tempat mendaftarkan:
 *   1. Observer  — PurchaseItemObserver (auto-increment stok)
 *   2. Blade directive @active — untuk menandai item sidebar aktif 
 *   3. Paginator::useBootstrapFive() - FIX panah pagination yang terlalu besar
 *   4. Gate::policy(---) - Mengatur siapa boleh melakukan apa terhadap sebuah Order.
 */

class AppServiceProvider extends ServiceProvider
{
    /**
     * Melakukan register pada application services apapun.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Observer stok pembelian 
        // Setiap PurchaseItem::create() → stock produk naik otomatis.
        PurchaseItem::observe(PurchaseItemObserver::class);
 
        // 2. Blade directive @active('pola/url')
        //
        // Dipakai di layouts/admin.blade.php untuk menandai nav-link aktif.
        //
        // Cara kerja: @active('admin/products*')
        // - Jika URL saat ini cocok dengan pola, cetak string 'active'
        // - Jika tidak, cetak string kosong
        //
        // Contoh pemakaian di Blade: <a class="nav-link @active('admin/products*')" href="...">
        //
        // Pola mendukung wildcard (*) yang dikonversi ke regex .*
        //
        Blade::directive('active', function (string $expression): string {
            // $expression berisi string termasuk tanda kutip, contoh: 'admin/products*'
            // fnmatch() membandingkan URL saat ini dengan pola wildcard
            return "<?php echo (fnmatch({$expression}, request()->path())) ? 'active' : ''; ?>";
        });

        Paginator::useBootstrapFive();

        /**
         * Daftarkan OrderPolicy untuk model Order.
         * Setelah ini, $this->authorize('view', $order) di controller
         * akan otomatis memanggil OrderPolicy::view($user, $order).
         */
        Gate::policy(Order::class, OrderPolicy::class);
    }
}
