# Retail Simulator

Aplikasi web retail berbasis **Laravel 12** untuk simulasi katalog produk, keranjang belanja, checkout, pembelian stok, dan manajemen pesanan dengan akses berbasis role.

## Fitur Utama

- **Storefront publik** untuk melihat katalog produk
- **Keranjang belanja** dan **checkout** untuk user login
- **Riwayat pesanan** untuk pembeli
- **Admin panel** untuk:
  - manajemen kategori
  - manajemen produk
  - manajemen supplier
  - manajemen pembelian stok
  - manajemen user
  - verifikasi distributor
  - pengelolaan pesanan
- **Role-based access**:
  - `admin`
  - `distributor`
  - `consumer`
- **Soft delete** pada data tertentu
- **Harga produk berdasarkan role** (`consumer` / `distributor`)
- **Seeder data** untuk demo project

## Tech Stack

- PHP 8.2+
- Laravel 12
- Blade
- Tailwind CSS
- Vite
- MySQL

## Requirement

Pastikan sudah terpasang:

- PHP 8.2 atau lebih baru
- Composer
- Node.js dan npm
- MySQL
- Git

## Akun Demo

### Admin
- Email: `admin@hannochs.co.id`
- Password: `admin1234`

### Consumer
- Email: `budi.santoso@gmail.com`
- Password: `password`

### Distributor
- Email: `hendra@tokolistrikjaya.com`
- Password: `password`

## Alur Penggunaan

1. Buka halaman katalog produk.
2. Login sebagai user yang sesuai.
3. Tambahkan produk ke cart.
4. Lakukan checkout.
5. Admin dapat memproses pesanan dan mengelola stok dari panel admin.
