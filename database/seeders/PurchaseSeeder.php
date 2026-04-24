<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminId    = DB::table('users')->where('role', 'admin')->value('id');
        $supplier1  = DB::table('suppliers')->where('name', 'PT Hannochs Mitra Cahaya')->value('id');
        $supplier2  = DB::table('suppliers')->where('name', 'CV Terang Benderang Mandiri')->value('id');

        // Helper: ambil product_id berdasarkan SKU
        $pid = fn(string $sku) => DB::table('products')->where('sku', $sku)->value('id');

        // ── Purchase #1: Restock LED Bulb & Tube (Jan 2026) ──────────────
        $p1Id = DB::table('purchases')->insertGetId([
            'supplier_id'  => $supplier1,
            'user_id'      => $adminId,
            'invoice_no'   => 'INV/HMC/2026/001',
            'total_cost'   => 0,        // akan diupdate setelah item dimasukkan
            'status'       => 'received',
            'notes'        => 'Restock awal tahun. Semua barang sudah diterima dan dicek kualitas.',
            'purchased_at' => '2026-01-10',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $p1Items = [
            ['sku' => 'HNC-BLB-7W-DL',  'qty' => 200, 'cost' =>  8000],
            ['sku' => 'HNC-BLB-12W-DL', 'qty' => 150, 'cost' => 12000],
            ['sku' => 'HNC-BLB-15W-DL', 'qty' => 100, 'cost' => 16000],
            ['sku' => 'HNC-TUB-T8-18W', 'qty' => 100, 'cost' => 33000],
            ['sku' => 'HNC-TUB-T8-24W', 'qty' =>  80, 'cost' => 43000],
        ];

        $p1Total = 0;
        foreach ($p1Items as $item) {
            $subtotal = $item['qty'] * $item['cost'];
            $p1Total += $subtotal;
            DB::table('purchases_items')->insert([
                'purchase_id' => $p1Id,
                'product_id'  => $pid($item['sku']),
                'quantity'    => $item['qty'],
                'unit_cost'   => $item['cost'],
                'subtotal'    => $subtotal,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
        DB::table('purchases')->where('id', $p1Id)->update(['total_cost' => $p1Total]);

        // ── Purchase #2: Restock Panel & MCB (Feb 2026) ───────────────────
        $p2Id = DB::table('purchases')->insertGetId([
            'supplier_id'  => $supplier2,
            'user_id'      => $adminId,
            'invoice_no'   => 'INV/TBM/2026/014',
            'total_cost'   => 0,
            'status'       => 'received',
            'notes'        => 'Pembelian MCB dan box panel untuk proyek perumahan klien.',
            'purchased_at' => '2026-02-15',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $p2Items = [
            ['sku' => 'HNC-MCB-1P-6A',   'qty' => 100, 'cost' => 22000],
            ['sku' => 'HNC-MCB-1P-16A',  'qty' => 100, 'cost' => 26000],
            ['sku' => 'HNC-MCB-2P-20A',  'qty' =>  60, 'cost' => 56000],
            ['sku' => 'HNC-BOX-MCB-6G',  'qty' =>  30, 'cost' => 95000],
        ];

        $p2Total = 0;
        foreach ($p2Items as $item) {
            $subtotal = $item['qty'] * $item['cost'];
            $p2Total += $subtotal;
            DB::table('purchases_items')->insert([
                'purchase_id' => $p2Id,
                'product_id'  => $pid($item['sku']),
                'quantity'    => $item['qty'],
                'unit_cost'   => $item['cost'],
                'subtotal'    => $subtotal,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
        DB::table('purchases')->where('id', $p2Id)->update(['total_cost' => $p2Total]);

        // ── Purchase #3: Restock Smart & Emergency (Mar 2026) ─────────────
        $p3Id = DB::table('purchases')->insertGetId([
            'supplier_id'  => $supplier1,
            'user_id'      => $adminId,
            'invoice_no'   => 'INV/HMC/2026/038',
            'total_cost'   => 0,
            'status'       => 'received',
            'notes'        => 'Permintaan smart bulb dan emergency light meningkat menjelang akhir Q1.',
            'purchased_at' => '2026-03-05',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $p3Items = [
            ['sku' => 'HNC-SMT-9W-RGB',   'qty' =>  60, 'cost' =>  72000],
            ['sku' => 'HNC-SMT-12W-TUN',  'qty' =>  50, 'cost' =>  86000],
            ['sku' => 'HNC-EMR-8W-SLM',   'qty' =>  50, 'cost' =>  72000],
            ['sku' => 'HNC-EMR-BLB-9W',   'qty' =>  60, 'cost' =>  55000],
            ['sku' => 'HNC-SKT-6L-SW',    'qty' => 100, 'cost' =>  31000],
            ['sku' => 'HNC-KRL-4L-USB-5M','qty' =>  60, 'cost' =>  55000],
        ];

        $p3Total = 0;
        foreach ($p3Items as $item) {
            $subtotal = $item['qty'] * $item['cost'];
            $p3Total += $subtotal;
            DB::table('purchases_items')->insert([
                'purchase_id' => $p3Id,
                'product_id'  => $pid($item['sku']),
                'quantity'    => $item['qty'],
                'unit_cost'   => $item['cost'],
                'subtotal'    => $subtotal,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
        DB::table('purchases')->where('id', $p3Id)->update(['total_cost' => $p3Total]);
    }
}
