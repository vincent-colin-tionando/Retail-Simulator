<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('suppliers')->insert([
            [
                'name'           => 'PT Hannochs Mitra Cahaya',
                'contact_person' => 'Andi Setiawan',
                'phone'          => '021-5556789',
                'email'          => 'sales@hannochs-mitra.co.id',
                'address'        => 'Kawasan Industri MM2100, Jl. Selayar Blok A5, Cikarang Barat, Bekasi, Jawa Barat 17520',
                'notes'          => 'Distributor resmi & agen tunggal produk Hannochs untuk wilayah Jabodetabek. Minimum order 50 pcs per SKU. Terms net 30.',
                'is_active'      => true,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name'           => 'UD Surya Elektrika Nusantara',
                'contact_person' => 'Bambang Prasetyo',
                'phone'          => '031-8881234',
                'email'          => 'order@surya-elektrika.com',
                'address'        => 'Jl. Rungkut Industri III No. 22, Surabaya, Jawa Timur 60293',
                'notes'          => 'Pemasok aksesori dan spare part kelistrikan. Minimum order Rp 2.000.000. Pengiriman via JNE Cargo.',
                'is_active'      => true,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name'           => 'CV Terang Benderang Mandiri',
                'contact_person' => 'Ridwan Halim',
                'phone'          => '022-7009988',
                'email'          => 'ridwan@terang-benderang.co.id',
                'address'        => 'Jl. Soekarno-Hatta No. 456, Bandung, Jawa Barat 40226',
                'notes'          => 'Spesialis panel listrik dan MCB. Garansi produk 2 tahun. Pembelian di atas 100 pcs diskon 5%.',
                'is_active'      => true,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
        ]);
    }
}
