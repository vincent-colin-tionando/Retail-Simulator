<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     *  Hanya admin yang boleh membuat produk.
     */
    public function authorize(): bool
    {
        return auth()->user()?->role === 'admin';
    }

     /**
     *. Aturan validasi untuk form tambah produk baru
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku', 'regex:/^[A-Za-z0-9\-]+$/'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'unit' => ['required', 'string', 'max:20'],
            'stock' => ['required', 'integer', 'min:0'],
            'stock_min' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'price_consumer' => ['required', 'numeric', 'min:1'],
            'price_distributor' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($v) {
            if ($v->errors()->has('price_consumer') || $v->errors()->has('price_distributor')) {
                return;
            }
            
            $priceConsumer    = (float) $this->input('price_consumer', 0);
            $priceDistributor = (float) $this->input('price_distributor', 0);

            // Harga distributor harus lebih kecil dari harga consumer
            // (logika bisnis: distributor membeli lebih murah karena volume)
            if ($priceDistributor >= $priceConsumer) {
                $v->errors()->add(
                    'price_distributor',
                    'Harga distributor harus lebih murah dari harga consumer. ' .
                    "Saat ini: consumer = {$priceConsumer}, distributor = {$priceDistributor}."
                );
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
        ]);
    }

    public function messages(): array
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
