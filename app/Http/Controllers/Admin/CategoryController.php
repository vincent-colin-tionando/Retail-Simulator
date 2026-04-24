<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    // INDEX — Daftar semua kategori
    // Route: GET /admin/categories/index
    public function index(): View
    {
        $search = request('search');

        // Ambil semua parent kategori beserta children-nya (accordion UI)
        $parents = Category::query()
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->withCount('products')->ordered(),])
            ->withCount('products')
            ->withCount('children')
            ->when($search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
            ->orWhereHas('children', fn ($c) => $c->where('name', 'like', "%{$s}%")))
            ->ordered()
            ->get();

        return view('admin.categories.index', compact('parents', 'search'));
    }

    // CREATE — Form tambah kategori
    // Route: GET /admin/categories/create
    public function create(): View
    {
        // Hanya tampilkan kategori utama (parentOnly) sebagai pilihan induk.
        // Kita tidak mengizinkan sub-kategori dari sub-kategori (max 2 level).
        $parentCategories = Category::parentOnly()->active()->ordered()->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    // STORE — Simpan kategori baru
    // Route: POST /admin/categories/index
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create([
            'parent_id' => $request->parent_id,  // null = kategori utama
            'name' => $request->name,
            // Buat slug dari nama jika user tidak mengisi slug manual
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    // SHOW — Detail satu kategori beserta sub-kategorinya
    // Route: GET /admin/categories/{category}
    public function show(Category $category): View
    {
        $category->load([
            'parent',
            'children',               
            'price' => fn ($q) => $q->with('consumerPrice', 'distributorPrice')->latest(),
        ]);

        return view('admin.categories.show', compact('category'));
    }

    // EDIT — Form edit kategori
    // Route: GET /admin/categories/{category}/edit
    public function edit(Category $category): View
    {
        // Exclude kategori yang sedang diedit dari pilihan induk
        // agar tidak bisa memilih dirinya sendiri sebagai induk.
        // Juga exclude semua children-nya untuk mencegah circular reference.
        $excludedIds = $category->children->pluck('id')->push($category->id);

        $parentCategories = Category::parentOnly()
            ->active()->whereNotIn('id', $excludedIds)
            ->ordered()->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    // UPDATE — Simpan perubahan kategori
    // Route: PUT/PATCH /admin/categories/{category}
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    // DESTROY — Hapus kategori
    // Route: DELETE /admin/categories/{category}
    public function destroy(Category $category): RedirectResponse
    {
        // Cegah hapus jika masih punya sub-kategori aktif
        if ($category->children()->exists()) {
            return back()->with('error',
                "Kategori \"{$category->name}\" tidak bisa dihapus karena masih memiliki sub-kategori. Hapus sub-kategorinya terlebih dahulu.");
        }

        // Cegah hapus jika masih ada produk aktif di kategori ini
        if ($category->products()->exists()) {
            return back()->with('error',
                "Kategori \"{$category->name}\" tidak bisa dihapus karena masih memiliki produk. Pindahkan atau hapus produknya terlebih dahulu.");
        }

        $category->delete();

        return back()->with('success', "Kategori \"{$category->name}\" berhasil dihapus.");
    }
}
