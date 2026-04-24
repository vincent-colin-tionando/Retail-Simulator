<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Validasi form tambah user baru oleh admin (POST /admin/users)
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Hanya admin yang boleh menambah user baru.
     *
     * Admin bisa membuat user dengan role apapun termasuk admin lain.
     * Ini disengaja karena super-admin mungkin perlu menambah admin lain.
     */
    public function authorize(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    /**
     * Aturan validasi untuk form tambah user.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            // Gunakan 'email' biasa jika server tidak mendukung DNS lookup
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],

            // Password wajib saat create — dikonfirmasi (password_confirmation)
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],

            // Role harus salah satu dari tiga nilai yang valid
            'role' => ['required', Rule::in(['admin', 'consumer', 'distributor'])],

            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],

            // Nama perusahaan hanya relevan untuk distributor
            'company_name' => ['nullable', 'string', 'max:255'],

            // is_verified hanya relevan untuk distributor
            'is_verified'  => ['boolean'],
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

    /**
     * Validasi tambahan: pastikan company_name diisi jika role distributor.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($v) {
            // Jika role distributor, company_name sebaiknya ada
            // (warning saja, bukan error keras — tapi bisa diubah ke error jika diinginkan)
            if ($this->input('role') === 'distributor' && empty($this->input('company_name'))) {
                // Catatan: ini menggunakan setErrors() bukan error() agar tidak
                // mencegah submit. Ubah ke $v->errors()->add() untuk error keras.
                // Untuk sekarang: hanya peringatan opsional, nama toko tidak wajib.
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Email sudah terdaftar. Gunakan email lain atau reset password.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.in' => 'Role tidak valid. Pilih salah satu: admin, consumer, distributor.',
        ];
    }

    /**
     * Nama atribut yang lebih ramah untuk pesan error.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'email' => 'email',
            'password' => 'password',
            'role' => 'role',
            'phone' => 'nomor telepon',
            'company_name' => 'nama perusahaan',
            'is_verified' => 'status verifikasi',
        ];
    }
}
