<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // INDEX - Daftar semua pesanan
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with(['user', 'processedBy'])
            ->withCount('items')
            // Filter status
            ->when($request->input('status'), fn ($q, $s) =>
                $q->where('status', $s)
            )
            // Filter role pembeli
            ->when($request->input('buyer_role'), fn ($q, $r) =>
                $q->where('buyer_role', $r)
            )
            // Filter rentang tanggal
            ->when($request->input('date_from'), fn ($q, $d) =>
                $q->whereDate('created_at', '>=', $d)
            )
            ->when($request->input('date_to'), fn ($q, $d) =>
                $q->whereDate('created_at', '<=', $d)
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Badge hitungan order pending untuk sidebar
        $pendingCount = Order::where('status', 'pending')->count();
 
        return view('admin.orders.index', compact('orders', 'pendingCount'));
    }

    // SHOW - Detail satu pesanan
    public function show(Order $order): View
    {
        $order->load([
            'user', // data pembeli
            'processedBy', // admin yang memproses
            'items.product', // untuk menampilkan stok saat ini
        ]);

        return view('admin.orders.show', compact('order'));
    }

    // PROCESS — Ubah status pending → processing
    // Route: PATCH /admin/orders/{order}/process
    public function process(Order $order): RedirectResponse
    {
        if ($order->status !== 'pending'){
            return back()->with('error', 'Hanya pesanan berstatus "Menunggu" yang bisa diproses.');
        }

        $order->update([
            'status'       => 'processing',
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        return back()->with('success', "Order {$order->order_code} sedang diproses.");
    }


    // COMPLETE — Ubah status processing → completed
    // Route: PATCH /admin/orders/{order}/complete
    public function complete(Order $order): RedirectResponse
    {
        if ($order->status !== 'processing') {
            return back()->with('error', 'Hanya pesanan berstatus "Diproses" yang bisa diselesaikan.');
        }
 
        $order->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);
 
        return back()->with('success', "Order {$order->order_code} telah diselesaikan.");
    }

    // CANCEL — Batalkan pesanan + kembalikan stok (atomik)
    // Route: PATCH /admin/orders/{order}/cancel
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        if (! in_array($order->status, ['pending', 'processing'])) {
            return back()->with('error', 'Hanya pesanan pending atau processing yang bisa dibatalkan.');
        }
 
        // Gunakan transaksi agar rollback stok dan update status terjadi atomik.
        // Jika salah satu gagal, tidak ada perubahan yang tersimpan.
        DB::transaction(function () use ($request, $order) {
 
            // Kembalikan stok setiap produk yang ada di order ini
            foreach ($order->items as $item) {
                if ($item->product) {
                    // Increment aman: SQL UPDATE products SET stock = stock + ? WHERE id = ?
                    $item->product()->increment('stock', $item->quantity);
                }
            }
 
            // Tandai order sebagai cancelled
            $order->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
                'admin_notes'  => $request->input('cancel_reason') ?? $order->admin_notes,
            ]);
        });
 
        return back()->with('success', "Order {$order->order_code} dibatalkan. Stok produk telah dikembalikan.");
    }

    // UPDATE NOTE — Simpan catatan admin tanpa mengubah status
    // Route: PATCH /admin/orders/{order}/note
    public function updateNote(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);
 
        $order->update(['admin_notes' => $request->input('admin_notes')]);
 
        return back()->with('success', 'Catatan admin berhasil disimpan.');
    }
}
