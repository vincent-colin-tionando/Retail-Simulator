<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * SupplierController
 *
 * Mengelola data pemasok/distributor produk.
 * Semua route dilindungi middleware 'auth' + 'role:admin'.
 *
 * Routes yang dipakai (dari web.php):
 *   GET    /admin/suppliers           → index
 *   GET    /admin/suppliers/create    → create
 *   POST   /admin/suppliers           → store
 *   GET    /admin/suppliers/{id}/edit → edit
 *   PUT    /admin/suppliers/{id}      → update
 *   DELETE /admin/suppliers/{id}      → destroy
 */

class SupplierController extends Controller
{
    // INDEX — Daftar semua supplier
    // Route: GET /admin/suppliers

    public function index(Request $request): View
    {
        $suppliers = Supplier::query()
            ->withCount('purchases')
            ->search($request->input('search'))
            ->when($request->has('is_active'), fn ($q) =>
                $q->where('is_active', $request->input('is_active'))
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.suppliers.index', compact('suppliers'));
    }

    // CREATE — Form tambah supplier baru
    // Route: GET /admin/suppliers/create
    public function create(): View
    {
        return view('admin.suppliers.create');
    }

    // STORE — Simpan supplier baru ke database
    // Route: POST /admin/suppliers
    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        Supplier::create($request->validated());

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', "Supplier \"{$request->name}\" berhasil ditambahkan.");
    }

    // EDIT — Form edit data supplier
    // Route: GET /admin/suppliers/{supplier}/edit
    public function edit(Supplier $supplier): View
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    // UPDATE — Simpan perubahan supplier ke database
    // Route: PUT /admin/suppliers/{supplier}
    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', "Data supplier \"{$supplier->name}\" berhasil diperbarui.");
    }
    
    // DESTROY — Hapus supplier (soft delete)
    // Route: DELETE /admin/suppliers/{supplier}
    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchases()->exists()) {
            return back()->with(
                'error',
                "Supplier \"{$supplier->name}\" tidak bisa dihapus karena masih memiliki riwayat pembelian."
            );
        }

        // SoftDeletes: kolom deleted_at diisi, data tidak hilang dari DB.
        $supplier->delete();

        return back()->with('success', "Supplier \"{$supplier->name}\" berhasil dihapus.");
    }
    
}
