<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        // ── Ambil ID kategori berdasarkan slug ─────────────────────────────
        $cat = DB::table('categories')->pluck('id', 'slug');

        // ─────────────────────────────────────────────────────────────────
        // Format setiap produk:
        // [ category_slug, name, sku, unit, stock, stock_min,
        //   description, harga_consumer, harga_distributor ]
        // ─────────────────────────────────────────────────────────────────
        $products = [

            // ── LED Bulb / Bohlam ─────────────────────────────────────────
            [
                'category'    => 'led-bulb-bohlam',
                'name'        => 'Hannochs LED Bulb 5W Kuning E27',
                'sku'         => 'HNC-BLB-5W-YL',
                'unit'        => 'pcs',
                'stock'       => 250,
                'stock_min'   => 20,
                'description' => 'Lampu bohlam LED 5W setara 40W, cahaya kuning hangat (3000K), fitting E27. Hemat energi 88%, umur 25.000 jam.',
                'price_consumer'     => 12500,
                'price_distributor'  => 9000,
            ],
            [
                'category'    => 'led-bulb-bohlam',
                'name'        => 'Hannochs LED Bulb 7W Putih E27',
                'sku'         => 'HNC-BLB-7W-DL',
                'unit'        => 'pcs',
                'stock'       => 300,
                'stock_min'   => 25,
                'description' => 'Bohlam LED 7W setara 60W, cahaya putih siang (6500K), fitting E27. Cocok untuk ruang tamu dan kamar tidur.',
                'price_consumer'     => 15000,
                'price_distributor'  => 11000,
            ],
            [
                'category'    => 'led-bulb-bohlam',
                'name'        => 'Hannochs LED Bulb 9W Kuning E27',
                'sku'         => 'HNC-BLB-9W-YL',
                'unit'        => 'pcs',
                'stock'       => 200,
                'stock_min'   => 20,
                'description' => 'Bohlam LED 9W cahaya hangat 3000K, E27. Ideal untuk ruang keluarga dan lorong.',
                'price_consumer'     => 18000,
                'price_distributor'  => 13500,
            ],
            [
                'category'    => 'led-bulb-bohlam',
                'name'        => 'Hannochs LED Bulb 12W Putih E27',
                'sku'         => 'HNC-BLB-12W-DL',
                'unit'        => 'pcs',
                'stock'       => 180,
                'stock_min'   => 15,
                'description' => 'Bohlam LED 12W setara 100W, cahaya putih terang 6500K, E27. Ideal untuk garasi dan dapur.',
                'price_consumer'     => 22000,
                'price_distributor'  => 16500,
            ],
            [
                'category'    => 'led-bulb-bohlam',
                'name'        => 'Hannochs LED Bulb 15W Putih E27',
                'sku'         => 'HNC-BLB-15W-DL',
                'unit'        => 'pcs',
                'stock'       => 150,
                'stock_min'   => 15,
                'description' => 'Bohlam LED 15W daya tinggi, cahaya putih 6500K, lumen 1400 lm. Cocok untuk area luas.',
                'price_consumer'     => 28000,
                'price_distributor'  => 21000,
            ],
            [
                'category'    => 'led-bulb-bohlam',
                'name'        => 'Hannochs LED Bulb 18W Putih E27',
                'sku'         => 'HNC-BLB-18W-DL',
                'unit'        => 'pcs',
                'stock'       => 120,
                'stock_min'   => 12,
                'description' => 'Bohlam LED 18W ultra terang 1700 lm, 6500K. Pengganti TL 36W di fitting E27.',
                'price_consumer'     => 35000,
                'price_distributor'  => 26500,
            ],
            [
                'category'    => 'led-bulb-bohlam',
                'name'        => 'Hannochs LED Bulb 23W Putih E27',
                'sku'         => 'HNC-BLB-23W-DL',
                'unit'        => 'pcs',
                'stock'       => 100,
                'stock_min'   => 10,
                'description' => 'Bohlam LED 23W performa industri, 2200 lm, 6500K. Sangat terang untuk gudang dan bengkel.',
                'price_consumer'     => 45000,
                'price_distributor'  => 34000,
            ],

            // ── LED Tube / Pengganti TL ───────────────────────────────────
            [
                'category'    => 'led-tube-pengganti-tl',
                'name'        => 'Hannochs LED Tube T8 10W 60cm Putih',
                'sku'         => 'HNC-TUB-T8-10W',
                'unit'        => 'pcs',
                'stock'       => 200,
                'stock_min'   => 15,
                'description' => 'Tabung LED T8 panjang 60 cm, 10W setara TL 18W, 6500K. Hemat 50% energi, tanpa flicker.',
                'price_consumer'     => 38000,
                'price_distributor'  => 28500,
            ],
            [
                'category'    => 'led-tube-pengganti-tl',
                'name'        => 'Hannochs LED Tube T8 18W 120cm Putih',
                'sku'         => 'HNC-TUB-T8-18W',
                'unit'        => 'pcs',
                'stock'       => 180,
                'stock_min'   => 15,
                'description' => 'Tabung LED T8 panjang 120 cm, 18W setara TL 36W, 1800 lm. Kompatibel ballast & tanpa ballast.',
                'price_consumer'     => 58000,
                'price_distributor'  => 44000,
            ],
            [
                'category'    => 'led-tube-pengganti-tl',
                'name'        => 'Hannochs LED Tube T8 24W 150cm Putih',
                'sku'         => 'HNC-TUB-T8-24W',
                'unit'        => 'pcs',
                'stock'       => 120,
                'stock_min'   => 10,
                'description' => 'Tabung LED T8 panjang 150 cm, 24W setara TL 58W, 2400 lm. Ideal untuk supermarket dan pabrik.',
                'price_consumer'     => 75000,
                'price_distributor'  => 57000,
            ],

            // ── LED Panel Slim ────────────────────────────────────────────
            [
                'category'    => 'led-panel-slim',
                'name'        => 'Hannochs LED Panel Slim 12W 15x15cm',
                'sku'         => 'HNC-PNL-12W-15',
                'unit'        => 'pcs',
                'stock'       => 80,
                'stock_min'   => 8,
                'description' => 'Panel LED slim 12W, ukuran 15×15 cm, cahaya merata 6500K. Tebal hanya 10mm, mudah dipasang.',
                'price_consumer'     => 85000,
                'price_distributor'  => 64000,
            ],
            [
                'category'    => 'led-panel-slim',
                'name'        => 'Hannochs LED Panel Slim 24W 30x30cm',
                'sku'         => 'HNC-PNL-24W-30',
                'unit'        => 'pcs',
                'stock'       => 60,
                'stock_min'   => 6,
                'description' => 'Panel LED slim 24W, ukuran 30×30 cm, 2200 lm. Cocok untuk plafon kantor dan ruang meeting.',
                'price_consumer'     => 145000,
                'price_distributor'  => 110000,
            ],
            [
                'category'    => 'led-panel-slim',
                'name'        => 'Hannochs LED Panel Slim 40W 60x60cm',
                'sku'         => 'HNC-PNL-40W-60',
                'unit'        => 'pcs',
                'stock'       => 40,
                'stock_min'   => 5,
                'description' => 'Panel LED slim 40W, ukuran 60×60 cm, 4000 lm, 6500K. Standar plafon Armstrong untuk perkantoran.',
                'price_consumer'     => 240000,
                'price_distributor'  => 182000,
            ],

            // ── LED Filament ──────────────────────────────────────────────
            [
                'category'    => 'led-filament',
                'name'        => 'Hannochs LED Filament 4W E27 Amber',
                'sku'         => 'HNC-FLM-4W-AMB',
                'unit'        => 'pcs',
                'stock'       => 100,
                'stock_min'   => 10,
                'description' => 'Lampu filament LED dekoratif 4W, warna amber hangat 2200K. Tampilan retro vintage, fitting E27.',
                'price_consumer'     => 32000,
                'price_distributor'  => 24000,
            ],
            [
                'category'    => 'led-filament',
                'name'        => 'Hannochs LED Filament 6W E27 Amber Globe',
                'sku'         => 'HNC-FLM-6W-GLB',
                'unit'        => 'pcs',
                'stock'       => 80,
                'stock_min'   => 8,
                'description' => 'Bohlam filament LED globe 6W, cahaya amber 2200K 360°. Dekoratif untuk kafe, bar, dan restoran.',
                'price_consumer'     => 42000,
                'price_distributor'  => 32000,
            ],

            // ── Lampu Emergency ───────────────────────────────────────────
            [
                'category'    => 'lampu-emergency',
                'name'        => 'Hannochs LED Emergency Light 8W Slim',
                'sku'         => 'HNC-EMR-8W-SLM',
                'unit'        => 'pcs',
                'stock'       => 70,
                'stock_min'   => 8,
                'description' => 'Lampu darurat 8W dengan baterai Li-Ion built-in. Menyala otomatis saat listrik padam, daya tahan 3 jam.',
                'price_consumer'     => 125000,
                'price_distributor'  => 95000,
            ],
            [
                'category'    => 'lampu-emergency',
                'name'        => 'Hannochs LED Emergency Dual Light 2x5W',
                'sku'         => 'HNC-EMR-DL-10W',
                'unit'        => 'pcs',
                'stock'       => 50,
                'stock_min'   => 5,
                'description' => 'Lampu darurat dua arah 2×5W, baterai Li-Ion 4 jam. Kepala lampu dapat diputar 180°.',
                'price_consumer'     => 185000,
                'price_distributor'  => 140000,
            ],
            [
                'category'    => 'lampu-emergency',
                'name'        => 'Hannochs LED Emergency Bulb 9W E27',
                'sku'         => 'HNC-EMR-BLB-9W',
                'unit'        => 'pcs',
                'stock'       => 90,
                'stock_min'   => 10,
                'description' => 'Bohlam LED emergency 9W fitting E27. Beroperasi normal saat listrik hidup, otomatis menyala saat padam. 4 jam backup.',
                'price_consumer'     => 95000,
                'price_distributor'  => 72000,
            ],

            // ── Downlight & Spotlight ─────────────────────────────────────
            [
                'category'    => 'downlight-spotlight',
                'name'        => 'Hannochs LED Downlight COB 7W Putih',
                'sku'         => 'HNC-DWL-7W-WH',
                'unit'        => 'pcs',
                'stock'       => 120,
                'stock_min'   => 10,
                'description' => 'Downlight tanam LED COB 7W, diameter 9 cm, 6500K. Ring putih, sudut sinar 120°. IP20.',
                'price_consumer'     => 48000,
                'price_distributor'  => 36000,
            ],
            [
                'category'    => 'downlight-spotlight',
                'name'        => 'Hannochs LED Downlight COB 12W Kuning',
                'sku'         => 'HNC-DWL-12W-YL',
                'unit'        => 'pcs',
                'stock'       => 100,
                'stock_min'   => 10,
                'description' => 'Downlight tanam LED COB 12W, diameter 12 cm, cahaya kuning 3000K. Ring putih, 1100 lm.',
                'price_consumer'     => 68000,
                'price_distributor'  => 51000,
            ],
            [
                'category'    => 'downlight-spotlight',
                'name'        => 'Hannochs LED Spotlight GU10 7W Putih',
                'sku'         => 'HNC-SPT-GU10-7W',
                'unit'        => 'pcs',
                'stock'       => 80,
                'stock_min'   => 8,
                'description' => 'Spotlight LED GU10 7W sudut 38°, 6500K. Ideal untuk rak display, galeri seni, dan toko retail.',
                'price_consumer'     => 52000,
                'price_distributor'  => 39000,
            ],

            // ── Lampu Jalan & Outdoor ─────────────────────────────────────
            [
                'category'    => 'lampu-jalan-outdoor',
                'name'        => 'Hannochs LED Street Light 30W',
                'sku'         => 'HNC-STL-30W',
                'unit'        => 'pcs',
                'stock'       => 30,
                'stock_min'   => 5,
                'description' => 'Lampu jalan LED 30W, 3300 lm, IP65 tahan hujan. Bobot ringan 2 kg, pemasangan mudah di tiang PJU.',
                'price_consumer'     => 385000,
                'price_distributor'  => 292000,
            ],
            [
                'category'    => 'lampu-jalan-outdoor',
                'name'        => 'Hannochs LED Street Light 60W',
                'sku'         => 'HNC-STL-60W',
                'unit'        => 'pcs',
                'stock'       => 20,
                'stock_min'   => 3,
                'description' => 'Lampu jalan LED 60W, 6600 lm, IP65. Cocok untuk jalan perumahan dan parkir besar.',
                'price_consumer'     => 720000,
                'price_distributor'  => 545000,
            ],
            [
                'category'    => 'lampu-jalan-outdoor',
                'name'        => 'Hannochs LED Garden Light 12W Taman',
                'sku'         => 'HNC-GDN-12W',
                'unit'        => 'pcs',
                'stock'       => 45,
                'stock_min'   => 5,
                'description' => 'Lampu taman LED 12W dengan tiang setinggi 60 cm. IP44, cahaya putih, cocok untuk halaman dan area outdoor.',
                'price_consumer'     => 195000,
                'price_distributor'  => 148000,
            ],

            // ── Stop Kontak & Kabel Rol ───────────────────────────────────
            [
                'category'    => 'stop-kontak-kabel-rol',
                'name'        => 'Hannochs Stop Kontak 4 Lubang Tanpa Saklar',
                'sku'         => 'HNC-SKT-4L-NS',
                'unit'        => 'pcs',
                'stock'       => 200,
                'stock_min'   => 20,
                'description' => '4 lubang universal tanpa saklar, kabel 1,5 m. Kapasitas 2200W / 10A. Tersertifikasi SNI.',
                'price_consumer'     => 32000,
                'price_distributor'  => 24000,
            ],
            [
                'category'    => 'stop-kontak-kabel-rol',
                'name'        => 'Hannochs Stop Kontak 6 Lubang + Saklar Induk',
                'sku'         => 'HNC-SKT-6L-SW',
                'unit'        => 'pcs',
                'stock'       => 160,
                'stock_min'   => 15,
                'description' => '6 lubang dengan saklar utama, kabel 3 m. Dilengkapi surge protector. Kapasitas 2200W / 10A. Bersertifikat SNI.',
                'price_consumer'     => 55000,
                'price_distributor'  => 41500,
            ],
            [
                'category'    => 'stop-kontak-kabel-rol',
                'name'        => 'Hannochs Kabel Rol 4 Lubang + USB 5m',
                'sku'         => 'HNC-KRL-4L-USB-5M',
                'unit'        => 'pcs',
                'stock'       => 100,
                'stock_min'   => 10,
                'description' => 'Kabel rol 5 m, 4 stopkontak universal + 2 port USB 2.4A. Proteksi anak, tahan panas 75°C.',
                'price_consumer'     => 95000,
                'price_distributor'  => 72000,
            ],
            [
                'category'    => 'stop-kontak-kabel-rol',
                'name'        => 'Hannochs Extension Cord 3 Lubang 10m',
                'sku'         => 'HNC-EXT-3L-10M',
                'unit'        => 'pcs',
                'stock'       => 75,
                'stock_min'   => 8,
                'description' => 'Kabel perpanjangan 10 m, 3 stopkontak. Kabel tembaga murni 2×1,5 mm², tahan beban 1650W. Cocok untuk bengkel.',
                'price_consumer'     => 135000,
                'price_distributor'  => 102000,
            ],

            // ── MCB & Panel Listrik ───────────────────────────────────────
            [
                'category'    => 'mcb-panel-listrik',
                'name'        => 'Hannochs MCB 1 Pole 6A C-Curve',
                'sku'         => 'HNC-MCB-1P-6A',
                'unit'        => 'pcs',
                'stock'       => 150,
                'stock_min'   => 15,
                'description' => 'MCB 1 kutub 6A kurva C, tegangan 230V AC. Kapasitas pemutusan 6kA. Sesuai standar IEC 60898.',
                'price_consumer'     => 38000,
                'price_distributor'  => 28500,
            ],
            [
                'category'    => 'mcb-panel-listrik',
                'name'        => 'Hannochs MCB 1 Pole 16A C-Curve',
                'sku'         => 'HNC-MCB-1P-16A',
                'unit'        => 'pcs',
                'stock'       => 120,
                'stock_min'   => 12,
                'description' => 'MCB 1 kutub 16A kurva C, 230V AC. Cocok untuk sirkuit lampu dan stopkontak rumah.',
                'price_consumer'     => 45000,
                'price_distributor'  => 34000,
            ],
            [
                'category'    => 'mcb-panel-listrik',
                'name'        => 'Hannochs MCB 2 Pole 20A C-Curve',
                'sku'         => 'HNC-MCB-2P-20A',
                'unit'        => 'pcs',
                'stock'       => 80,
                'stock_min'   => 8,
                'description' => 'MCB 2 kutub 20A kurva C, 230/400V AC. Untuk proteksi sirkuit AC, pompa air, dan water heater.',
                'price_consumer'     => 98000,
                'price_distributor'  => 74000,
            ],
            [
                'category'    => 'mcb-panel-listrik',
                'name'        => 'Hannochs Box Panel MCB 6 Group Surface',
                'sku'         => 'HNC-BOX-MCB-6G',
                'unit'        => 'pcs',
                'stock'       => 40,
                'stock_min'   => 5,
                'description' => 'Box panel MCB 6 grup, mounting tempel dinding. Material ABS tahan api UL94-V0. Lengkap dengan busbar dan terminal.',
                'price_consumer'     => 165000,
                'price_distributor'  => 125000,
            ],

            // ── Smart Home Lighting ───────────────────────────────────────
            [
                'category'    => 'smart-home-lighting',
                'name'        => 'Hannochs Smart LED Bulb 9W WiFi RGB E27',
                'sku'         => 'HNC-SMT-9W-RGB',
                'unit'        => 'pcs',
                'stock'       => 90,
                'stock_min'   => 8,
                'description' => 'Bohlam LED pintar 9W RGB+W, WiFi 2.4GHz, kompatibel Google Home & Amazon Alexa. 16 juta warna via aplikasi.',
                'price_consumer'     => 125000,
                'price_distributor'  => 94000,
            ],
            [
                'category'    => 'smart-home-lighting',
                'name'        => 'Hannochs Smart LED Bulb 12W WiFi Tunable E27',
                'sku'         => 'HNC-SMT-12W-TUN',
                'unit'        => 'pcs',
                'stock'       => 70,
                'stock_min'   => 6,
                'description' => 'Bohlam LED pintar 12W Tunable White (2700K–6500K), WiFi 2.4GHz. Atur suhu warna dan kecerahan via smartphone.',
                'price_consumer'     => 148000,
                'price_distributor'  => 112000,
            ],
            [
                'category'    => 'smart-home-lighting',
                'name'        => 'Hannochs Smart Downlight 10W RGB WiFi',
                'sku'         => 'HNC-SMT-DWL-10W',
                'unit'        => 'pcs',
                'stock'       => 50,
                'stock_min'   => 5,
                'description' => 'Downlight LED pintar 10W RGB+W, diameter 10 cm. Jadwal otomatis, sinkronisasi musik, Google & Alexa compatible.',
                'price_consumer'     => 195000,
                'price_distributor'  => 148000,
            ],
        ];

        foreach ($products as $p) {
            $categoryId = $cat[$p['category']] ?? null;

            $productId = DB::table('products')->insertGetId([
                'category_id' => $categoryId,
                'name'        => $p['name'],
                'sku'         => $p['sku'],
                'unit'        => $p['unit'],
                'stock'       => $p['stock'],
                'stock_min'   => $p['stock_min'],
                'description' => $p['description'],
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            // Harga consumer
            DB::table('product_prices')->insert([
                'product_id'  => $productId,
                'role_type'   => 'consumer',
                'price'       => $p['price_consumer'],
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            // Harga distributor (lebih murah ±25%)
            DB::table('product_prices')->insert([
                'product_id'  => $productId,
                'role_type'   => 'distributor',
                'price'       => $p['price_distributor'],
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }
}
