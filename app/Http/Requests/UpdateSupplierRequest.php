<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateSupplierRequest — Validasi form edit supplier (PUT /admin/suppliers/{supplier})
 *
 */
class UpdateSupplierRequest extends FormRequest
{
    // Hanya admin yang boleh mengubah data supplier.
    public function authorize(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    // Aturan validasi untuk form edit supplier.
    public function rules(): array
    {
        // Ambil ID supplier dari route parameter (Route Model Binding)
        // $this->route('supplier') mengembalikan instance model Supplier
        $supplierId = $this->route('supplier')?->id;

        return [
            // unique tapi kecualikan ID supplier yang sedang diedit
            'name'           => ['required', 'string', 'max:255', "unique:suppliers,name,{$supplierId}"],

            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:30', 'regex:/^[0-9\s\+\-\(\)]+$/'],
            'email'          => ['nullable', 'email:rfc,dns', 'max:255'],
            'address'        => ['nullable', 'string', 'max:1000'],
            'notes'          => ['nullable', 'string', 'max:2000'],
            'is_active'      => ['boolean'],
        ];
    }

    /**
     * Normalisasi checkbox is_active menjadi boolean sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'Nama supplier sudah digunakan oleh supplier lain.',
            'phone.regex' => 'Format nomor telepon tidak valid.',
            'email.email' => 'Format email tidak valid.',
        ];
    }
}
