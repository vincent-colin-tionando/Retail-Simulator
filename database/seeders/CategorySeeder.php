<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        // ── Kategori Utama (parent_id = null) ──────────────────────────────
        $parentIds = [];

        $parents = [
            [
                'slug'        => 'lampu-led',
                'name'        => 'Lampu LED',
                'description' => 'Rangkaian lengkap lampu LED hemat energi Hannochs untuk kebutuhan rumah, kantor, dan industri.',
                'sort_order'  => 1,
            ],

            [
                'slug'        => 'lampu-emergency',
                'name'        => 'Lampu Emergency',
                'description' => 'Lampu darurat otomatis menyala saat listrik padam, cocok untuk rumah, hotel, dan gedung.',
                'sort_order'  => 2,
            ],
            
            [
                'slug'        => 'downlight-spotlight',
                'name'        => 'Downlight & Spotlight',
                'description' => 'Lampu tanam dan sorot LED untuk dekorasi interior dan pencahayaan terfokus.',
                'sort_order'  => 3,
            ],
            [
                'slug'        => 'lampu-jalan-outdoor',
                'name'        => 'Lampu Jalan & Outdoor',
                'description' => 'Solusi pencahayaan eksterior: jalan, taman, fasad, dan area parkir.',
                'sort_order'  => 4,
            ],
            [
                'slug'        => 'stop-kontak-kabel-rol',
                'name'        => 'Stop Kontak & Kabel Rol',
                'description' => 'Terminal daya, kabel ekstensi, dan aksesori kelistrikan rumah tangga.',
                'sort_order'  => 5,
            ],
            [
                'slug'        => 'mcb-panel-listrik',
                'name'        => 'MCB & Panel Listrik',
                'description' => 'Pemutus arus miniatur (MCB) dan kelengkapan panel listrik untuk instalasi rumah dan industri.',
                'sort_order'  => 6,
            ],
            [
                'slug'        => 'smart-home-lighting',
                'name'        => 'Smart Home Lighting',
                'description' => 'Lampu pintar WiFi yang dapat dikontrol via aplikasi dan asisten suara.',
                'sort_order'  => 7,
            ],
        ];

        foreach ($parents as $p) {
            $id = DB::table('categories')->insertGetId([
                'parent_id'   => null,
                'name'        => $p['name'],
                'slug'        => $p['slug'],
                'description' => $p['description'],
                'is_active'   => true,
                'sort_order'  => $p['sort_order'],
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $parentIds[$p['slug']] = $id;
        }

        // ── Sub-kategori Lampu LED ─────────────────────────────────────────
        $ledSubcategories = [
            [
                'slug'        => 'led-bulb-bohlam',
                'name'        => 'LED Bulb / Bohlam',
                'description' => 'Bohlam LED berbasis E27 dan E14 berbagai wattage, pengganti lampu pijar & CFL.',
                'sort_order'  => 1,
            ],
            [
                'slug'        => 'led-tube-pengganti-tl',
                'name'        => 'LED Tube (Pengganti TL)',
                'description' => 'Tabung LED T8 pengganti lampu neon/TL konvensional, lebih hemat dan tahan lama.',
                'sort_order'  => 2,
            ],
            [
                'slug'        => 'led-panel-slim',
                'name'        => 'LED Panel Slim',
                'description' => 'Panel LED ultra-tipis untuk plafon kantor, ruang rapat, dan area komersial.',
                'sort_order'  => 3,
            ],
            [
                'slug'        => 'led-filament',
                'name'        => 'LED Filament',
                'description' => 'Lampu LED bergaya vintage dengan filamen dekoratif, cocok untuk kafe dan restoran.',
                'sort_order'  => 4,
            ],
        ];

        foreach ($ledSubcategories as $s) {
            DB::table('categories')->insert([
                'parent_id'   => $parentIds['lampu-led'],
                'name'        => $s['name'],
                'slug'        => $s['slug'],
                'description' => $s['description'],
                'is_active'   => true,
                'sort_order'  => $s['sort_order'],
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }
}

