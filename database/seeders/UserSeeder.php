<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            // ── Admin ──────────────────────────────────────────────────────
            [
                'name'         => 'Administrator',
                'email'        => 'admin@hannochs.co.id',
                'password'     => Hash::make('admin1234'),
                'role'         => 'admin',
                'is_verified'  => true,
                'phone'        => '021-5550001',
                'address'      => 'Jl. Industri Raya No. 1, Tangerang, Banten',
                'company_name' => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],

            // ── Consumers ──────────────────────────────────────────────────
            [
                'name'         => 'Budi Santoso',
                'email'        => 'budi.santoso@gmail.com',
                'password'     => Hash::make('password'),
                'role'         => 'consumer',
                'is_verified'  => false,
                'phone'        => '081234567890',
                'address'      => 'Jl. Kebon Jeruk No. 12, Jakarta Barat',
                'company_name' => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Siti Rahayu',
                'email'        => 'siti.rahayu@yahoo.com',
                'password'     => Hash::make('password'),
                'role'         => 'consumer',
                'is_verified'  => false,
                'phone'        => '082198765432',
                'address'      => 'Jl. Sudirman Blok B No. 5, Surabaya, Jawa Timur',
                'company_name' => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],

            // ── Distributors ───────────────────────────────────────────────
            [
                'name'         => 'Hendra Wijaya',
                'email'        => 'hendra@tokolistrikjaya.com',
                'password'     => Hash::make('password'),
                'role'         => 'distributor',
                'is_verified'  => true,
                'phone'        => '0274-556677',
                'address'      => 'Jl. Malioboro No. 88, Yogyakarta',
                'company_name' => 'Toko Listrik Jaya',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Dewi Permata',
                'email'        => 'dewi@cv-permataelektrik.com',
                'password'     => Hash::make('password'),
                'role'         => 'distributor',
                'is_verified'  => true,
                'phone'        => '022-7334455',
                'address'      => 'Jl. Gatot Subroto No. 45, Bandung, Jawa Barat',
                'company_name' => 'CV Permata Elektrik',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}
