<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\PurchasePolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UserManagementPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/**
 * Mendaftarkan semua Policy dan Gate di Laravel.
 *
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Map: Model class -> Policy class yang mengelolanya.
     */
    protected $policies = [
        Category::class => CategoryPolicy::class,
        Order::class => OrderPolicy::class,
        Product::class => ProductPolicy::class,
        Purchase::class => PurchasePolicy::class,
        Supplier::class => SupplierPolicy::class,
        User::class => UserManagementPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Gate: akses panel admin secara umum.
        Gate::define('access-admin-panel', function (User $user) {
            return $user->role === 'admin';
        });

        // Gate: melihat laporan dan statistik bisnis.
        Gate::define('view-reports', function (User $user): bool {
            return $user->role === 'admin';
        });

        // Gate: mengelola user (akses menu manajemen user).
        Gate::define('manage-users', function (User $user): bool {
            return $user->role === 'admin';
        });

        // Gate: mengelola stok (akses menu purchase & supplier).
        Gate::define('manage-inventory', function (User $user): bool {
            return $user->role === 'admin';
        });

        // Gate: berbelanja di storefront.
        Gate::define('shop', function (User $user): bool {
            return $user->canShop();
        });
        
        // Gate: mengakses fitur premium (contoh fitur berbayar).
        Gate::define('access-premium-features', function (User $user): bool {
             return $user->hasPremiumSubscription();
        });

        // Gate: verifikasi distributor.
        Gate::define('verify-distributor', function (User $user): bool {
            return $user->role === 'admin';
        });

        // Gate: melihat dashboard admin.
        Gate::define('view-admin-dashboard', function (User $user): bool {
            return $user->role === 'admin';
        });

        // Gate: mengubah status order (process/complete/cancel).
        Gate::define('change-order-status', function (User $user): bool {
            return $user->role === 'admin';
        });
    }
}
