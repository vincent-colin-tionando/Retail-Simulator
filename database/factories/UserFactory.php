<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Membuat data pengguna palsu untuk testing dan development.
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Password di-cache agar tidak di-hash ulang setiap record.
     * Semua user test memakai password "password".
     */
    protected static ?string $password;

    // STATE DEFAULT — consumer biasa
    public function definition(): array
    {
        // Nama Indonesia sederhana
        $firstNames = ['Budi', 'Siti', 'Andi', 'Dewi', 'Rudi', 'Rina', 'Hendra', 'Wati',
                       'Agus', 'Fitri', 'Dedi', 'Yuni', 'Joko', 'Lina', 'Tono', 'Maya'];
        $lastNames  = ['Santoso', 'Rahayu', 'Wijaya', 'Pratama', 'Susanto', 'Kurniawan',
                       'Permata', 'Saputra', 'Nugroho', 'Hidayat', 'Wibowo', 'Setiawan'];

        $name  = fake()->randomElement($firstNames) . ' ' . fake()->randomElement($lastNames);
        $phone = '08' . fake()->numerify('##########');

        return [
            'name'         => $name,
            'email'        => fake()->unique()->safeEmail(),
            'password'     => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role'         => 'consumer',
            'is_verified'  => false,
            'phone'        => $phone,
            'address'      => fake()->streetAddress() . ', ' . fake()->city(),
            'company_name' => null,
        ];
    }

    // STATE: ADMIN
    // Buat user dengan role admin.
    public function admin(): static
    {
        return $this->state([
            'role'        => 'admin',
            'is_verified' => true,
            'company_name'=> null,
        ]);
    }

    // STATE: CONSUMER
    // Buat user dengan role consumer (state default, ditulis eksplisit agar bisa dipakai di test secara deskriptif). 
    public function consumer(): static
    {
        return $this->state([
            'role'         => 'consumer',
            'is_verified'  => false,
            'company_name' => null,
        ]);
    }

    // STATE: DISTRIBUTOR (sudah diverifikasi)
    // Buat distributor yang sudah terverifikasi admin.
    public function distributor(): static
    {
        $companies = [
            'PT Cahaya Nusantara', 'CV Maju Bersama', 'UD Sumber Rejeki',
            'Toko Listrik Jaya', 'CV Terang Abadi', 'PT Kilat Elektrik',
        ];

        return $this->state([
            'role'         => 'distributor',
            'is_verified'  => true,
            'company_name' => fake()->randomElement($companies),
        ]);
    }

    // STATE: DISTRIBUTOR BELUM TERVERIFIKASI
    // Distributor yang belum mendapat persetujuan admin.
    public function unverifiedDistributor(): static
    {
        return $this->distributor()->state([
            'is_verified' => false,
        ]);
    }

    // STATE: UNVERIFIED (alias, cocok untuk email-verification flow)
    // User yang email-nya belum diverifikasi. Dipakai saat testing alur email verification.
    public function unverified(): static
    {
        return $this->state([
            'is_verified' => false,
        ]);
    }
}
