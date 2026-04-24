<?php

namespace App\View\Components;

use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

/**
 * Menampilkan harga dan tombol aksi yang berbeda tergantung kondisi
 */
class PriceButton extends Component
{
    /** Harga yang relevan untuk user saat ini (sudah dihitung di constructor). */
    public ?float $price;

    /** Role efektif: role yang dipakai untuk menentukan harga. */
    public string $effectiveRole;

    /** Apakah user bisa langsung checkout (bukan distributor yang belum verif). */
    public bool $canCheckout;

    /** Apakah user sudah login. */
    public bool $isGuest;

    public function __construct(public Product $product, public bool $compact = false, ) 
    {
        $user = auth()->user();

        // ── Tentukan role efektif ──
        // Guest -> tidak perlu role
        // Admin -> pakai harga consumer sebagai referensi
        // Distributor terverifikasi -> harga distributor
        // Distributor belum verif -> harga consumer (belum dapat grosir)
        // Consumer -> harga consumer

        if (! $user) {
            $this->isGuest = true;
            $this->effectiveRole = 'consumer';
            $this->canCheckout = false;
            // Tampilkan harga consumer untuk guest (bisa tambah ke cart, checkout butuh login)
            $this->price = $product->priceFor('consumer');
            return;
        }

        $this->isGuest = false;

        $this->effectiveRole = match (true) {
            $user->role === 'distributor' && $user->is_verified => 'distributor',
            default => 'consumer',
        };

        // canCheckout = false jika distributor belum diverifikasi
        $this->canCheckout = ! ($user->role === 'distributor' && ! $user->is_verified);

        // Ambil harga dari relasi yang sudah di-eager-load
        // Tidak melakukan query baru di sini — harga harus sudah ada di $product->prices
        $this->price = $product->priceFor($this->effectiveRole);
    }

    public function render(): View
    {
        return view('components.price-button');
    }
}
