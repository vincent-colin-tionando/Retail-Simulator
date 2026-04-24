<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi form checkout (POST /checkout)
 *
 */
class StoreOrderRequest extends FormRequest
{
    /**
     * Otorisasi: siapa yang boleh submit form checkout?
     *
     * Syarat: sudah login DAN bisa berbelanja.
     * canShop() di model User mengembalikan:
     *   - true  untuk consumer & admin
     *   - true  untuk distributor yang sudah diverifikasi admin
     *   - false untuk distributor yang BELUM diverifikasi
     *
     * Jika authorize() return false → Laravel lempar 403 AuthorizationException.
     */
    public function authorize(): bool
    {
        $user = auth()->user();

        // Harus sudah login (middleware 'auth' di route sudah menjamin ini,
        // tapi double-check di sini sebagai defense in depth)
        if (! $user) {
            return false;
        }

        // Distributor yang belum diverifikasi admin tidak boleh checkout
        return $user->canShop();
    }

    /**
     * Aturan validasi form checkout.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Alamat pengiriman wajib ada, string, max 500 karakter
            'shipping_address' => ['required', 'string', 'max:500'],

            // Metode pembayaran wajib, hanya boleh dari daftar yang valid
            'payment_method'   => [
                'required',
                'string',
                'in:Transfer Bank BCA,Transfer Bank Mandiri,Transfer Bank BRI,QRIS,COD (Bayar di Tempat),Tunai',
            ],

            // Catatan opsional — dibatasi 1000 karakter
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Pesan error custom yang lebih ramah pengguna.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'shipping_address.required' => 'Alamat pengiriman wajib diisi.',
            'shipping_address.max' => 'Alamat pengiriman maksimal 500 karakter.',
            'payment_method.required' => 'Pilih metode pembayaran terlebih dahulu.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }

    /**
     * Pesan fallback jika authorize() gagal (distributor belum terverifikasi).
     */
    protected function failedAuthorization(): never
    {
        if (auth()->check() && auth()->user()->role === 'distributor' && ! auth()->user()->is_verified) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'Akun distributor Anda belum diverifikasi oleh admin. ' .
                'Silakan hubungi admin untuk mendapatkan akses berbelanja.'
            );
        }

        parent::failedAuthorization();
    }
}
