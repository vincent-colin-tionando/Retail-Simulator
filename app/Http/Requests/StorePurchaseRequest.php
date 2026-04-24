<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi form catat pembelian stok (POST /admin/purchases)
 *
 */
class StorePurchaseRequest extends FormRequest
{
    /**
     * Hanya admin yang boleh mencatat pembelian stok.
     */
    public function authorize(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    /**
     * Aturan validasi pembelian.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // ── Header Purchase ───

            // Supplier harus ada di tabel suppliers
            'supplier_id'  => ['required', 'exists:suppliers,id'],

            // Nomor invoice harus unik (tidak boleh catat faktur yang sama dua kali)
            'invoice_no'   => ['required', 'string', 'max:100', 'unique:purchases,invoice_no'],

            // Tanggal pembelian wajib ada dan harus berformat tanggal valid
            // 'before_or_equal:today' mencegah tanggal di masa depan
            'purchased_at' => ['required', 'date', 'before_or_equal:today'],

            // Status enum ketat — hanya 3 nilai yang diizinkan
            'status'       => ['required', 'in:pending,received,cancelled'],

            // Catatan opsional
            'notes'        => ['nullable', 'string', 'max:2000'],

            // ── Items Array ───
            // Array items wajib ada dan minimal berisi 1 item
            // 'array' memastikan data yang masuk benar-benar array
            'items'                => ['required', 'array', 'min:1'],

            // Setiap baris item harus punya product_id yang valid di DB
            'items.*.product_id'   => ['required', 'integer', 'exists:products,id'],

            // Kuantitas harus bilangan bulat positif
            'items.*.quantity'     => ['required', 'integer', 'min:1', 'max:99999'],

            // Harga beli boleh 0 (gratis/sample) tapi tidak negatif
            'items.*.unit_cost'    => ['required', 'numeric', 'min:0', 'max:99999999'],
        ];
    }

    /**
     * Pesan error dengan penomoran baris yang jelas.
     *
     */
    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Pilih supplier untuk pembelian ini.',
            'supplier_id.exists' => 'Supplier yang dipilih tidak valid.',
            'invoice_no.unique' => 'Nomor invoice sudah pernah digunakan. Periksa kembali.',
            'purchased_at.before_or_equal' => 'Tanggal pembelian tidak boleh di masa depan.',
            'status.in' => 'Status pembelian tidak valid.',
            'items.required' => 'Minimal masukkan satu produk.',
            'items.min' => 'Minimal masukkan satu produk.',
            'items.*.product_id.required' => 'Baris :position: Pilih produk.',
            'items.*.product_id.exists' => 'Baris :position: Produk tidak ditemukan.',
            'items.*.quantity.required' => 'Baris :position: Jumlah wajib diisi.',
            'items.*.quantity.min' => 'Baris :position: Jumlah minimal 1.',
            'items.*.quantity.max' => 'Baris :position: Jumlah terlalu besar.',
            'items.*.unit_cost.required' => 'Baris :position: Harga beli wajib diisi.',
            'items.*.unit_cost.min' => 'Baris :position: Harga beli tidak boleh negatif.',
        ];
    }

    /**
     * Validasi tambahan setelah aturan dasar lolos.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($v) {
            $items = $this->input('items', []);

            // Kumpulkan semua product_id
            $productIds = array_filter(array_column($items, 'product_id'));

            // Cek duplikat: jika ada product_id yang muncul lebih dari sekali
            $duplicates = array_filter(
                array_count_values($productIds),
                fn ($count) => $count > 1
            );

            if (! empty($duplicates)) {
                $v->errors()->add('items', 'Produk yang sama tidak boleh muncul lebih dari satu kali. Gabungkan ke satu baris dan sesuaikan jumlahnya.');
            }
        });
    }
}
