<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi form tambah supplier baru (POST /admin/suppliers)
 *
 * Memindahkan validasi dari SupplierController::store() ke sini
 * agar controller lebih bersih dan aturan validasi mudah di-test.
 */
class StoreSupplierRequest extends FormRequest
{
    /**
     * Hanya admin yang boleh menambah supplier baru.
     */
    public function authorize(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    /**
     * Aturan validasi untuk form tambah supplier.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Nama supplier wajib, unik di tabel suppliers
            'name' => ['required', 'string', 'max:255', 'unique:suppliers,name'],

            // Kontak person opsional
            'contact_person' => ['nullable', 'string', 'max:255'],

            // Telepon opsional, dibatasi format agar tidak ada inject aneh
            // regex memastikan hanya angka, spasi, +, -, dan tanda kurung
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9\s\+\-\(\)]+$/'],

            // Email opsional, harus format valid
            'email' => ['nullable', 'email:rfc,dns', 'max:255'],

            // Alamat dan catatan opsional, text bebas
            'address' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],

            // Boolean: aktif/nonaktif
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Konversi checkbox is_active menjadi boolean sebelum validasi.
     *
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * Pesan error custom.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'Nama supplier sudah terdaftar. Gunakan nama lain.',
            'phone.regex' => 'Format nomor telepon tidak valid. Hanya angka, +, -, spasi, dan tanda kurung.',
            'email.email' => 'Format email tidak valid.',
        ];
    }
}
