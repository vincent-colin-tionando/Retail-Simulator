<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * UpdateUserRequest — Validasi form edit user oleh admin (PUT /admin/users/{user})
 */
class UpdateUserRequest extends FormRequest
{
    /**
     * Hanya admin yang boleh mengubah data user.
     */
    public function authorize(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    // Aturan validasi untuk update user
    public function rules(): array
    {
        // Ambil user dari route parameter
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],

            // Email unik KECUALI milik user yang sedang diedit
            // Rule::unique()->ignore() adalah cara aman vs string 'unique:users,email,ID'
            'email' => ['required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            // Password OPSIONAL saat edit, tapi kalau diisi harus sesuai aturan dan dikonfirmasi
            'password' => ['nullable', 'confirmed',
                Password::min(8)->letters()->numbers(),
            ],

            'role' => ['required', Rule::in(['admin', 'consumer', 'distributor'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'is_verified' => ['boolean'],
        ];
    }

    /**
     * Normalisasi boolean sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_verified' => $this->boolean('is_verified'),
        ]);
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'role.in' => 'Role tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'email' => 'email',
            'password' => 'password',
            'role' => 'role',
            'phone' => 'nomor telepon',
            'company_name' => 'nama perusahaan',
        ];
    }
}
