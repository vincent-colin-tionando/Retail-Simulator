<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $adminId       = DB::table('users')->where('role', 'admin')->value('id');
        $consumerId1   = DB::table('users')->where('email', 'budi.santoso@gmail.com')->value('id');
        $consumerId2   = DB::table('users')->where('email', 'siti.rahayu@yahoo.com')->value('id');
        $distId1       = DB::table('users')->where('email', 'hendra@tokolistrikjaya.com')->value('id');
        $distId2       = DB::table('users')->where('email', 'dewi@cv-permataelektrik.com')->value('id');

        // Helper: ambil harga berdasarkan SKU & role
        $getPrice = function (string $sku, string $role): float {
            $productId = DB::table('products')->where('sku', $sku)->value('id');
            return (float) DB::table('product_prices')
                ->where('product_id', $productId)
                ->where('role_type', $role)
                ->value('price');
        };

        $pid = fn(string $sku) => DB::table('products')->where('sku', $sku)->value('id');
        $pname = fn(string $sku) => DB::table('products')->where('sku', $sku)->value('name');

        // ─────────────────────────────────────────────────────────────────
        // ORDER 1 – Consumer (Budi), completed, Feb 2026
        // ─────────────────────────────────────────────────────────────────
        $o1Items = [
            ['sku' => 'HNC-BLB-7W-DL',  'qty' => 4],
            ['sku' => 'HNC-BLB-15W-DL', 'qty' => 2],
            ['sku' => 'HNC-SKT-4L-NS',  'qty' => 1],
        ];
        $o1Total = 0;
        foreach ($o1Items as &$item) {
            $item['price']    = $getPrice($item['sku'], 'consumer');
            $item['subtotal'] = $item['price'] * $item['qty'];
            $o1Total         += $item['subtotal'];
        }
        unset($item);

        $o1Id = DB::table('orders')->insertGetId([
            'user_id'          => $consumerId1,
            'order_code'       => 'ORD-20260210-0001',
            'status'           => 'completed',
            'buyer_role'       => 'consumer',
            'total_price'      => $o1Total,
            'shipping_address' => 'Jl. Kebon Jeruk No. 12, Jakarta Barat',
            'payment_method'   => 'Transfer Bank BCA',
            'notes'            => 'Tolong bubblewrap lebih untuk lampu.',
            'admin_notes'      => 'Sudah dikirim via JNE REG.',
            'processed_at'     => '2026-02-10 10:00:00',
            'completed_at'     => '2026-02-12 14:30:00',
            'cancelled_at'     => null,
            'processed_by'     => $adminId,
            'created_at'       => '2026-02-10 09:45:00',
            'updated_at'       => '2026-02-12 14:30:00',
        ]);
        foreach ($o1Items as $item) {
            DB::table('order_items')->insert([
                'order_id'     => $o1Id,
                'product_id'   => $pid($item['sku']),
                'product_name' => $pname($item['sku']),
                'quantity'     => $item['qty'],
                'unit_price'   => $item['price'],
                'subtotal'     => $item['subtotal'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // ─────────────────────────────────────────────────────────────────
        // ORDER 2 – Consumer (Siti), completed, Feb 2026
        // ─────────────────────────────────────────────────────────────────
        $o2Items = [
            ['sku' => 'HNC-EMR-8W-SLM',  'qty' => 2],
            ['sku' => 'HNC-DWL-7W-WH',   'qty' => 4],
            ['sku' => 'HNC-FLM-4W-AMB',  'qty' => 3],
        ];
        $o2Total = 0;
        foreach ($o2Items as &$item) {
            $item['price']    = $getPrice($item['sku'], 'consumer');
            $item['subtotal'] = $item['price'] * $item['qty'];
            $o2Total         += $item['subtotal'];
        }
        unset($item);

        $o2Id = DB::table('orders')->insertGetId([
            'user_id'          => $consumerId2,
            'order_code'       => 'ORD-20260218-0002',
            'status'           => 'completed',
            'buyer_role'       => 'consumer',
            'total_price'      => $o2Total,
            'shipping_address' => 'Jl. Sudirman Blok B No. 5, Surabaya, Jawa Timur',
            'payment_method'   => 'QRIS',
            'notes'            => null,
            'admin_notes'      => 'Dikirim via SiCepat BEST.',
            'processed_at'     => '2026-02-18 11:00:00',
            'completed_at'     => '2026-02-21 09:00:00',
            'cancelled_at'     => null,
            'processed_by'     => $adminId,
            'created_at'       => '2026-02-18 10:30:00',
            'updated_at'       => '2026-02-21 09:00:00',
        ]);
        foreach ($o2Items as $item) {
            DB::table('order_items')->insert([
                'order_id'     => $o2Id,
                'product_id'   => $pid($item['sku']),
                'product_name' => $pname($item['sku']),
                'quantity'     => $item['qty'],
                'unit_price'   => $item['price'],
                'subtotal'     => $item['subtotal'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // ─────────────────────────────────────────────────────────────────
        // ORDER 3 – Distributor (Hendra / Toko Listrik Jaya), completed, Mar 2026
        // ─────────────────────────────────────────────────────────────────
        $o3Items = [
            ['sku' => 'HNC-BLB-7W-DL',   'qty' => 50],
            ['sku' => 'HNC-BLB-12W-DL',  'qty' => 30],
            ['sku' => 'HNC-TUB-T8-18W',  'qty' => 20],
            ['sku' => 'HNC-SKT-4L-NS',   'qty' => 30],
            ['sku' => 'HNC-SKT-6L-SW',   'qty' => 20],
            ['sku' => 'HNC-MCB-1P-16A',  'qty' => 20],
        ];
        $o3Total = 0;
        foreach ($o3Items as &$item) {
            $item['price']    = $getPrice($item['sku'], 'distributor');
            $item['subtotal'] = $item['price'] * $item['qty'];
            $o3Total         += $item['subtotal'];
        }
        unset($item);

        $o3Id = DB::table('orders')->insertGetId([
            'user_id'          => $distId1,
            'order_code'       => 'ORD-20260305-0003',
            'status'           => 'completed',
            'buyer_role'       => 'distributor',
            'total_price'      => $o3Total,
            'shipping_address' => 'Jl. Malioboro No. 88, Yogyakarta 55213',
            'payment_method'   => 'Transfer Bank Mandiri',
            'notes'            => 'Mohon sertakan invoice resmi untuk pembukuan toko.',
            'admin_notes'      => 'Sudah verifikasi distributor. Dikirim via JNE Cargo 2 koli.',
            'processed_at'     => '2026-03-05 09:00:00',
            'completed_at'     => '2026-03-08 16:00:00',
            'cancelled_at'     => null,
            'processed_by'     => $adminId,
            'created_at'       => '2026-03-05 08:30:00',
            'updated_at'       => '2026-03-08 16:00:00',
        ]);
        foreach ($o3Items as $item) {
            DB::table('order_items')->insert([
                'order_id'     => $o3Id,
                'product_id'   => $pid($item['sku']),
                'product_name' => $pname($item['sku']),
                'quantity'     => $item['qty'],
                'unit_price'   => $item['price'],
                'subtotal'     => $item['subtotal'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // ─────────────────────────────────────────────────────────────────
        // ORDER 4 – Distributor (Dewi / CV Permata Elektrik), processing, Apr 2026
        // ─────────────────────────────────────────────────────────────────
        $o4Items = [
            ['sku' => 'HNC-SMT-9W-RGB',   'qty' => 20],
            ['sku' => 'HNC-SMT-12W-TUN',  'qty' => 15],
            ['sku' => 'HNC-SMT-DWL-10W',  'qty' => 10],
            ['sku' => 'HNC-PNL-24W-30',   'qty' => 10],
            ['sku' => 'HNC-PNL-40W-60',   'qty' =>  5],
        ];
        $o4Total = 0;
        foreach ($o4Items as &$item) {
            $item['price']    = $getPrice($item['sku'], 'distributor');
            $item['subtotal'] = $item['price'] * $item['qty'];
            $o4Total         += $item['subtotal'];
        }
        unset($item);

        $o4Id = DB::table('orders')->insertGetId([
            'user_id'          => $distId2,
            'order_code'       => 'ORD-20260412-0004',
            'status'           => 'processing',
            'buyer_role'       => 'distributor',
            'total_price'      => $o4Total,
            'shipping_address' => 'Jl. Gatot Subroto No. 45, Bandung, Jawa Barat 40226',
            'payment_method'   => 'Transfer Bank BRI',
            'notes'            => 'Proyek smart office klien kami. Urgent sebelum 20 April.',
            'admin_notes'      => 'Sedang dipersiapkan. Konfirmasi stok panel 40W.',
            'processed_at'     => '2026-04-12 13:00:00',
            'completed_at'     => null,
            'cancelled_at'     => null,
            'processed_by'     => $adminId,
            'created_at'       => '2026-04-12 12:00:00',
            'updated_at'       => '2026-04-12 13:00:00',
        ]);
        foreach ($o4Items as $item) {
            DB::table('order_items')->insert([
                'order_id'     => $o4Id,
                'product_id'   => $pid($item['sku']),
                'product_name' => $pname($item['sku']),
                'quantity'     => $item['qty'],
                'unit_price'   => $item['price'],
                'subtotal'     => $item['subtotal'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // ─────────────────────────────────────────────────────────────────
        // ORDER 5 – Consumer (Budi), pending, Apr 2026
        // ─────────────────────────────────────────────────────────────────
        $o5Items = [
            ['sku' => 'HNC-STL-30W',     'qty' => 2],
            ['sku' => 'HNC-GDN-12W',     'qty' => 3],
            ['sku' => 'HNC-EMR-DL-10W',  'qty' => 1],
        ];
        $o5Total = 0;
        foreach ($o5Items as &$item) {
            $item['price']    = $getPrice($item['sku'], 'consumer');
            $item['subtotal'] = $item['price'] * $item['qty'];
            $o5Total         += $item['subtotal'];
        }
        unset($item);

        $o5Id = DB::table('orders')->insertGetId([
            'user_id'          => $consumerId1,
            'order_code'       => 'ORD-20260415-0005',
            'status'           => 'pending',
            'buyer_role'       => 'consumer',
            'total_price'      => $o5Total,
            'shipping_address' => 'Jl. Kebon Jeruk No. 12, Jakarta Barat',
            'payment_method'   => 'COD',
            'notes'            => 'Untuk renovasi halaman rumah.',
            'admin_notes'      => null,
            'processed_at'     => null,
            'completed_at'     => null,
            'cancelled_at'     => null,
            'processed_by'     => null,
            'created_at'       => '2026-04-15 20:15:00',
            'updated_at'       => '2026-04-15 20:15:00',
        ]);
        foreach ($o5Items as $item) {
            DB::table('order_items')->insert([
                'order_id'     => $o5Id,
                'product_id'   => $pid($item['sku']),
                'product_name' => $pname($item['sku']),
                'quantity'     => $item['qty'],
                'unit_price'   => $item['price'],
                'subtotal'     => $item['subtotal'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}
