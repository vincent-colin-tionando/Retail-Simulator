<?php

namespace App\Observers;

use App\Models\PurchaseItem;

// Observer untuk PurchaseItem

class PurchaseItemObserver
{
    public function created(PurchaseItem $item): void
    {
        // Tambah stok produk sebesar jumlah yang baru dibeli.
        $item->product()->increment('stock', $item->quantity);
    }

    // Untuk hapus PurchaseItem
    public function deleted(PurchaseItem $item): void
    {
        $item->product()->decrement('stock', $item->quantity);
    }
}
