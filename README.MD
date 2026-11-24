# CozyCafe

Aplikasi web pemesanan menu dan booking meja cafe yang dibangun dengan PHP Native, MySQL, JavaScript, dan Bootstrap 5.

## Teknologi

- PHP 8.2+
- MySQL 8.0+ atau MariaDB 10.6+
- PDO dengan prepared statements
- Apache melalui XAMPP
- HTML5 dan CSS3
- JavaScript ES6
- Bootstrap 5
- Chart.js
- DomPDF
- PhpSpreadsheet

## Persyaratan

- XAMPP atau Laragon
- PHP 8.2 atau lebih baru
- MySQL atau MariaDB
- Composer
- Ekstensi PHP aktif:
  - `pdo_mysql`
  - `gd`
  - `intl`
  - `mbstring`
  - `zip`

## Instalasi Lokal dengan XAMPP

1. Pastikan project berada di:

   ```text
   D:\xampp\htdocs\cozycafe
   ```

2. Jalankan Apache dan MySQL dari XAMPP Control Panel.

3. Buat konfigurasi environment:

   ```powershell
   Copy-Item .env.example .env
   ```

4. Sesuaikan `.env` jika perlu:

   ```env
   APP_URL=http://localhost/cozycafe
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=cozycafe
   DB_USER=root
   DB_PASS=
   ```

5. Instal dependency Composer:

   ```powershell
   composer install
   ```

6. Impor database:

   - Buka phpMyAdmin: `http://localhost/phpmyadmin`
   - Pilih menu **Import**
   - Pilih file `database/cozycafe.sql`
   - Jalankan import

   Database dan data awal akan dibuat secara otomatis.

7. Buka aplikasi:

   ```text
   http://localhost/cozycafe/
   ```

## Akun Admin Default

```text
Email: admin@cozycafe.id
Password: Admin123!
```

Wajib ubah password admin setelah instalasi pertama.

## Fitur Pelanggan

- Registrasi dan login pelanggan
- Profil pelanggan dan pergantian password
- Homepage dan katalog menu
- Pencarian serta filter kategori dan harga
- Keranjang belanja tersimpan permanen
- Booking meja untuk hari yang sama
- Slot booking dua jam dengan mulai setiap satu jam
- Pemeriksaan konflik meja
- Ringkasan dan checkout atomik
- Riwayat transaksi
- Pembatalan transaksi dalam batas aturan
- Notifikasi in-app

## Fitur Administrator

- Dashboard statistik dan grafik
- Kelola kategori menu
- Kelola menu beserta foto
- Kelola meja
- Kelola booking
- Kelola status pesanan
- Ubah detail pesanan
- Kelola pembayaran kasir
- Kelola pelanggan
- Pengaturan profil admin
- Pengaturan informasi cafe
- Laporan transaksi berbasis rentang tanggal
- Backup database manual dalam format SQL

## Aturan Utama

- Zona waktu aplikasi adalah `Asia/Jakarta`.
- Jam operasional tetap `09.00-22.00 WIB`.
- Durasi booking adalah 2 jam.
- Slot booking dimulai setiap satu jam.
- Booking hanya berlaku untuk hari yang sama.
- Booking harus disertai minimal satu item keranjang.
- Pembayaran dilakukan langsung di kasir.
- Transaksi awal berstatus `Belum Dibayar`.
- Booking dan pesanan awal berstatus `Dikonfirmasi`.
- Pelanggan dapat membatalkan transaksi minimal 2 jam sebelum jam booking.
- Password disimpan menggunakan `password_hash()`.
- Seluruh perubahan data menggunakan proteksi CSRF.
- Query database menggunakan PDO prepared statements.

## Struktur Project

```text
cozycafe/
├── admin/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── config/
├── controllers/
├── database/
├── models/
├── uploads/
│   ├── cafe/
│   └── menu/
├── views/
│   ├── admin/
│   ├── auth/
│   ├── customer/
│   └── layouts/
├── vendor/
├── .env.example
├── .htaccess
├── composer.json
├── composer.lock
├── index.php
└── README.MD
```

## Rute Utama

```text
/                       Beranda dan katalog
/menu/makanan           Menu makanan
/menu/minuman           Menu minuman
/auth/login             Login
/auth/register          Registrasi pelanggan
/customer/profile       Profil pelanggan
/cart                   Keranjang
/booking                Booking meja
/transactions           Riwayat transaksi
/notifications          Notifikasi
/admin/dashboard        Dashboard admin
/admin/categories       Kelola kategori
/admin/menus            Kelola menu
/admin/tables           Kelola meja
/admin/bookings         Kelola booking
/admin/orders           Kelola pesanan
/admin/payments         Kelola pembayaran
/admin/customers        Kelola pelanggan
/admin/reports          Laporan transaksi
/admin/settings         Pengaturan cafe dan admin
/admin/backup           Backup database
```

## Backup Database

1. Login sebagai admin.
2. Buka `/admin/backup`.
3. Klik **Download Backup Database (.sql)**.
4. File SQL dibuat oleh PHP melalui PDO tanpa mengandalkan executable `mysqldump`.

## Verifikasi Singkat

1. Buka homepage.
2. Registrasi atau login sebagai pelanggan.
3. Tambahkan beberapa menu ke keranjang.
4. Pilih booking meja dan slot waktu.
5. Konfirmasi checkout.
6. Login sebagai admin.
7. Periksa dashboard, pesanan, pembayaran, laporan, dan backup.

## Keamanan

- Pastikan `.env` tidak dapat diakses melalui browser.
- Folder `config`, `controllers`, `models`, `views`, `database`, dan `vendor` diblokir melalui Apache.
- Folder `uploads` menonaktifkan eksekusi PHP.
- Jangan menggunakan akun admin default setelah aplikasi dipasang untuk penggunaan nyata.
