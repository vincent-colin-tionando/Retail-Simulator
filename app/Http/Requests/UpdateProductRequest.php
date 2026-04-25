<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Ambil ID produk dari route parameter (Route Model Binding)
        $productId = $this->route('product')?->id;

        return [
            // Nama unik KECUALI produk yang sedang diedit
            'name'         => ['required', 'string', 'max:255', "unique:products,name,{$productId}"],

            // SKU unik KECUALI produk yang sedang diedit
            'sku'          => ['required', 'string', 'max:50', "unique:products,sku,{$productId}", 'regex:/^[A-Za-z0-9\-]+$/'],

            'category_id'  => ['nullable', 'exists:categories,id'],
            'unit'         => ['required', 'string', 'max:20'],
            'stock'        => ['required', 'integer', 'min:0'],
            'stock_min'    => ['required', 'integer', 'min:0'],

            // Gambar opsional saat edit — jika tidak diisi, pertahankan yang lama
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'description'  => ['nullable', 'string'],
            'is_active'    => ['boolean'],

            // Nama field konsisten dengan StoreProductRequest
            'price_consumer'    => ['required', 'numeric', 'min:1'],
            'price_distributor' => ['required', 'numeric', 'min:1'],
        ];
    }

    /**
     * Validasi lintas-field: harga distributor < consumer.
     *
     * Sama persis dengan StoreProductRequest::withValidator().
     * Dipisahkan di sini agar UpdateProductRequest bisa di-test secara mandiri.
     * 
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($v) {
            if ($v->errors()->has('price_consumer') || $v->errors()->has('price_distributor')) {
                return;
            }
            
            $priceConsumer    = (float) $this->input('price_consumer', 0);
            $priceDistributor = (float) $this->input('price_distributor', 0);

            if ($priceDistributor >= $priceConsumer) {
                $v->errors()->add(
                    'price_distributor',
                    'Harga distributor harus lebih murah dari harga consumer.'
                );
            }
        });
    }

    // Normalisasi boolean sebelum validasi.
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
        ]);
    }

    public function messages():array
    {
        return [
            'sku.unique' => 'SKU sudah digunakan oleh produk lain.',
            'sku.regex' => 'SKU hanya boleh berisi huruf, angka, dan tanda hubung (-).',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'image.mimes' => 'Format gambar tidak valid. Gunakan JPG, PNG, atau WebP.',
            'price_consumer.min' => 'Harga consumer minimal Rp 1.',
            'price_distributor.min' => 'Harga distributor minimal Rp 1.',
        ];
    }
}
