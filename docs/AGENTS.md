# AGENTS.MD — CozyCafe Development & Deployment Guide

Dokumen ini adalah panduan kerja utama (*system & workflow instructions*) untuk AI Coding Agent dalam membangun aplikasi web **CozyCafe** dari nol hingga siap *deploy* menggunakan **Dokploy**, berdasarkan spesifikasi pada `PRD.MD`.

---

## 1. Profil Proyek & Konteks Global (Wajib Dipatuhi di Setiap Step)

- **Nama Aplikasi:** CozyCafe — Aplikasi Pemesanan Menu dan Booking Meja Cafe.
- **Bahasa Antarmuka:** Bahasa Indonesia sepenuhnya (label, tombol, pesan error, notifikasi, status, laporan).
- **Timezone Wajib:** `Asia/Jakarta` (WIB). Semua logika tanggal, jam operasional, dan slot booking wajib menggunakan waktu WIB.
- **Tech Stack:**
  - **Backend:** PHP Native 8.2+ (arsitektur modular: `config/`, `controllers/`, `models/`, `views/`).
  - **Database:** MySQL 8.0+ / MariaDB 10.6+ dengan engine **InnoDB** dan charset `utf8mb4`.
  - **Frontend:** HTML5, CSS3, JavaScript (Vanilla/ES6), **Bootstrap 5**, Chart.js (untuk grafik dashboard).
  - **Library Composer:** `dompdf/dompdf` (Export PDF) dan `phpoffice/phpspreadsheet` (Export Excel).
- **Target Environment:**
  - Dapat berjalan di lingkungan lokal (**XAMPP / Laragon**).
  - Siap di-containerize dengan **Docker** (`php:8.2-apache`) untuk *deployment* di **Dokploy**.
  - Konfigurasi database dan aplikasi harus mendukung **Environment Variables** (`getenv()` / `$_ENV`) dengan nilai default lokal agar kompatibel di kedua environment.

---

## 2. Aturan Bisnis & Guardrails (Non-Negotiable)

1. **Keamanan Wajib:**
   - Gunakan **PDO** dengan *prepared statements* untuk seluruh query database.
   - Hash password dengan `password_hash()` dan verifikasi dengan `password_verify()`.
   - Lakukan `session_regenerate_id(true)` setelah login berhasil.
   - Terapkan proteksi **CSRF token** pada setiap form yang mengubah state/data (POST/PUT/DELETE).
   - Escape seluruh output HTML menggunakan `htmlspecialchars($str, ENT_QUOTES, 'UTF-8')`.
   - Validasi role (`customer` / `admin`) di sisi server pada setiap rute yang dilindungi.
   - Validasi upload gambar (ekstensi `jpg/jpeg/png/webp`, MIME type asli via `finfo`, ukuran maksimal, rename file acak/unik).
   - Blokir akses HTTP langsung ke folder `config/`, `database/`, `models/`, `controllers/`, `views/`, `vendor/`, dan file `.env` melalui `.htaccess` / konfigurasi Apache.
2. **Aturan Inti Transaksi & Booking:**
   - Pelanggan wajib login untuk checkout; tidak ada guest checkout dan tidak ada registrasi admin.
   - Booking meja **wajib** disertai pesanan menu (minimal 1 item di keranjang).
   - Booking hanya untuk **hari yang sama** (*same-day booking*).
   - Jam operasional tetap **09.00–22.00 WIB** (tidak dapat diubah dari dashboard).
   - Durasi setiap booking adalah **2 jam**, dengan slot dimulai setiap 1 jam (`09.00–11.00` hingga `20.00–22.00`). Slot yang waktunya sudah lewat di hari tersebut wajib disembunyikan.
   - Satu transaksi hanya menggunakan satu meja yang berstatus `Aktif` dan berkapasitas $\ge$ jumlah tamu.
   - **Anti Double-Booking:** Gunakan formula overlap `existing_start < requested_end AND existing_end > requested_start` (abaikan booking berstatus `Dibatalkan`). Pengecekan ulang wajib dilakukan di dalam **Database Transaction (`BEGIN ... COMMIT`)** sebelum menyimpan.
   - Format kode transaksi unik: `CC-YYYYMMDD-XXX` (contoh: `CC-20261123-001`).
   - Simpan snapshot nama menu dan harga pada tabel `order_items`.
   - Status awal booking dan pesanan dari checkout normal adalah `Dikonfirmasi`, sedangkan status pembayaran awal adalah `Belum Dibayar` (Bayar di kasir / *Cash on Arrival*).
3. **Batasan Fitur (Out of Scope):**
   - **Jangan** menambahkan: takeaway/delivery, payment gateway/pembayaran online, integrasi WhatsApp/SMS/email, multi-admin, guest login, rating/review, promo/diskon, stok bahan baku, pengaturan jam buka di dashboard, multibahasa, lupa password otomatis, cetak struk/PDF pelanggan, atau fitur restore database.

---

## 3. Panduan Eksekusi Step-by-Step (Prompts untuk Agent)

Jalankan prompt di bawah ini secara berurutan. Pastikan setiap step diverifikasi sebelum melanjutkan ke step berikutnya.

---

### STEP 1: Inisialisasi Struktur Project, Composer, & Konfigurasi Dual-Environment

**Prompt untuk Agent:**
> Baca file `PRD.MD` dan `AGENTS.MD`. Buat struktur direktori proyek `CozyCafe` sesuai arsitektur modular pada bagian 10 `PRD.MD`:
> - `config/` (`app.php`, `database.php`)
> - `controllers/`, `models/`, `views/layouts/`, `views/auth/`, `views/customer/`, `views/admin/`
> - `admin/index.php`, `customer/index.php`, `index.php`
> - `assets/css/`, `assets/js/`, `assets/images/`
> - `database/`
> - `uploads/menu/` dan `uploads/cafe/` (tambahkan `.gitkeep` dan proteksi agar eksekusi PHP di dalam `uploads/` dinonaktifkan)
>
> Lakukan tugas berikut:
> 1. Buat `composer.json` dengan dependency `dompdf/dompdf` dan `phpoffice/phpspreadsheet`, serta autoloading atau helper loader yang rapi, lalu jalankan `composer install`.
> 2. Buat `.env.example` dan `.gitignore` (abaikan `vendor/`, `.env`, dan file upload user kecuali `.gitkeep`).
> 3. Buat `config/app.php` yang mengatur `date_default_timezone_set('Asia/Jakarta')`, konstanta jam operasional (`09:00` - `22:00`), durasi booking (`2` jam), `BASE_URL` dinamis (mendukung `APP_URL` dari env var maupun deteksi localhost XAMPP), dan konfigurasi session yang aman.
> 4. Buat `config/database.php` menggunakan PDO (error mode exception, emulate prepares false, default fetch assoc) yang membaca `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` dari environment variable (`getenv()`) dengan fallback default XAMPP (`localhost`, `3306`, `cozycafe`, `root`, `""`).
> 5. Buat `.htaccess` di root proyek untuk URL routing bersih (jika diperlukan) serta memblokir akses langsung browser ke folder `config/`, `controllers/`, `models/`, `views/`, `database/`, `vendor/`, dan file `.env` / `composer.*`. Buat juga `.htaccess` di `uploads/` untuk menonaktifkan eksekusi script PHP (`php_flag engine off` atau `RemoveHandler`).

---

### STEP 2: Perancangan Skema Database & Data Awal (`database/cozycafe.sql`)

**Prompt untuk Agent:**
> Berdasarkan Bagian 9 pada `PRD.MD`, buat file `database/cozycafe.sql` untuk database `cozycafe` dengan engine **InnoDB** dan charset **utf8mb4_unicode_ci**.
>
> Buat 14 tabel lengkap beserta tipe data, constraint `UNIQUE`, `FOREIGN KEY`, dan `INDEX`:
> 1. `users` (`id`, `name`, `email` UNIQUE, `phone`, `password`, `role` ENUM('customer','admin'), `created_at`, `updated_at`)
> 2. `categories` (`id`, `name` UNIQUE, `description`, `created_at`, `updated_at`)
> 3. `menus` (`id`, `category_id` FK -> categories ON DELETE RESTRICT, `name`, `description`, `price` DECIMAL(12,2), `image`, `availability_status` ENUM('Tersedia','Tidak Tersedia'), `created_at`, `updated_at`)
> 4. `cafe_tables` (`id`, `table_number` UNIQUE, `area` ENUM('Indoor','Outdoor'), `capacity` INT, `status` ENUM('Aktif','Tidak Aktif'), `created_at`, `updated_at`)
> 5. `carts` (`id`, `user_id` UNIQUE FK -> users ON DELETE CASCADE, `created_at`, `updated_at`)
> 6. `cart_items` (`id`, `cart_id` FK -> carts ON DELETE CASCADE, `menu_id` FK -> menus ON DELETE CASCADE, `quantity` INT, `note` TEXT, `created_at`, `updated_at`)
> 7. `transactions` (`id`, `transaction_code` UNIQUE, `user_id` FK -> users, `total_amount` DECIMAL(12,2), `payment_status` ENUM('Belum Dibayar','Sudah Dibayar'), `created_at`, `updated_at`)
> 8. `bookings` (`id`, `transaction_id` UNIQUE FK -> transactions ON DELETE CASCADE, `table_id` FK -> cafe_tables, `booking_date` DATE, `start_time` TIME, `end_time` TIME, `guest_count` INT, `status` ENUM('Dikonfirmasi','Dibatalkan','Selesai'), `created_at`, `updated_at`) — tambahkan composite index `(booking_date, table_id, status)` dan `(booking_date, start_time, end_time)` untuk mempercepat query overlap.
> 9. `orders` (`id`, `transaction_id` UNIQUE FK -> transactions ON DELETE CASCADE, `order_status` ENUM('Menunggu Konfirmasi','Dikonfirmasi','Sedang Disiapkan','Siap Disajikan','Selesai','Dibatalkan'), `subtotal` DECIMAL(12,2), `created_at`, `updated_at`)
> 10. `order_items` (`id`, `order_id` FK -> orders ON DELETE CASCADE, `menu_id` FK -> menus ON DELETE SET NULL, `menu_name_snapshot`, `price_snapshot` DECIMAL(12,2), `quantity` INT, `note` TEXT, `subtotal` DECIMAL(12,2), `created_at`, `updated_at`)
> 11. `notifications` (`id`, `user_id` FK -> users ON DELETE CASCADE, `transaction_id` FK -> transactions ON DELETE CASCADE, `title`, `message`, `is_read` TINYINT(1) DEFAULT 0, `created_at`)
> 12. `cancellation_histories` (`id`, `transaction_id` FK -> transactions ON DELETE CASCADE, `canceled_by` ENUM('customer','admin'), `reason` TEXT, `created_at`)
> 13. `order_change_histories` (`id`, `order_id` FK -> orders ON DELETE CASCADE, `admin_id` FK -> users, `change_description` TEXT, `old_total` DECIMAL(12,2), `new_total` DECIMAL(12,2), `created_at`)
> 14. `cafe_settings` (`id`, `cafe_name`, `logo`, `description`, `address`, `phone`, `banner`, `updated_at`)
>
> Sertakan **Seed Data** di bagian bawah `cozycafe.sql`:
> - 1 akun Admin default (misal email `admin@cozycafe.id`, password di-hash valid dengan `password_hash()` PHP untuk `Admin123!`).
> - 1 data awal `cafe_settings` untuk CozyCafe.
> - 5 kategori awal (`Coffee`, `Non-Coffee`, `Main Course`, `Snack`, `Dessert`).
> - Contoh data meja (`Indoor` dan `Outdoor` dengan variasi kapasitas 2, 4, 6) dan beberapa menu contoh agar aplikasi langsung siap diuji.

---

### STEP 3: Core Helpers, Keamanan, Routing Entry Points, & Desain UI Minimalist

**Prompt untuk Agent:**
> Buat fondasi helper, middleware, routing, dan layout UI dengan konsep **Modern Minimalist** (dominan putih `#FFFFFF`, cream `#FAF6F0`, aksen cokelat kopi `#6F4E37` dan `#4A3222`, Bootstrap 5, tipografi modern Google Fonts seperti Plus Jakarta Sans / Inter).
>
> Lakukan tugas berikut:
> 1. Buat helper umum (misal di `config/helpers.php` atau `core/`) berisi:
>    - `e($string)` untuk XSS escaping.
>    - `csrf_token()`, `csrf_field()`, `verify_csrf()` untuk proteksi CSRF.
>    - `set_flash($type, $message)` dan `get_flash()` untuk notifikasi alert session.
>    - `format_rupiah($angka)` dan helper format tanggal/waktu Indonesia (WIB).
>    - `redirect($path)` dan helper auth (`is_logged_in()`, `current_user()`, `require_customer()`, `require_admin()`).
>    - `upload_image($file, $target_dir, $max_size_mb = 2)` dengan validasi ekstensi, `finfo_file` MIME type, ukuran, dan penamaan file aman.
> 2. Buat `assets/css/style.css` dan `assets/js/app.js` untuk tema Modern Minimalist CozyCafe yang responsif di desktop, tablet, dan smartphone.
> 3. Buat layout di `views/layouts/`:
>    - `header.php` & `footer.php` untuk publik/pelanggan dengan navigasi sesuai Bagian 6.2 `PRD.MD`: Home, Menu Makanan, Menu Minuman, Keranjang (dengan badge jumlah item), Booking Meja, Riwayat Transaksi, Notifikasi (ikon lonceng + badge jumlah belum dibaca), Profil, Login/Logout.
>    - `admin_header.php`, `admin_sidebar.php`, & `admin_footer.php` dengan 12 item navigasi admin sesuai Bagian 6.3 `PRD.MD`: Dashboard, Kelola Kategori Menu, Kelola Menu, Kelola Meja, Kelola Booking, Kelola Pesanan, Kelola Pembayaran, Kelola Pelanggan, Laporan Transaksi, Pengaturan Profil (termasuk Info Cafe), Backup Database, Logout.
> 4. Siapkan struktur front-controller/router pada `index.php`, `customer/index.php`, dan `admin/index.php` sehingga akses terorganisir rapi dan terlindungi sesuai role.

---

### STEP 4: Modul Autentikasi & Profil Pelanggan

**Prompt untuk Agent:**
> Implementasikan fitur Autentikasi dan Profil sesuai Bagian 5.1 pada `PRD.MD`:
> 1. Buat Model `models/User.php` dengan method pencarian by email/id, create customer, update profile/password, dan list customers untuk admin.
> 2. Buat Controller `controllers/AuthController.php` dan Views di `views/auth/`:
>    - **Registrasi Pelanggan (`register.php`):** Field Nama Lengkap, Email (wajib unik), Nomor Telepon, Password, Konfirmasi Password. Password di-hash dengan `password_hash(..., PASSWORD_DEFAULT)`. Role otomatis `customer`. Tidak ada registrasi admin.
>    - **Login (`login.php`):** Untuk pelanggan dan admin. Validasi email & `password_verify()`. Panggil `session_regenerate_id(true)`. Arahkan `admin` ke dashboard admin dan `customer` ke homepage/halaman sebelumnya.
>    - **Info Lupa Password:** Tampilkan catatan informatif bahwa jika pelanggan lupa password, silakan menghubungi admin cafe secara langsung (tanpa fitur reset otomatis).
>    - **Logout:** Bersihkan session secara aman.
> 3. Buat halaman **Profil Pelanggan** (`views/customer/profile.php`): Pelanggan dapat memperbarui Nama Lengkap, Email (tetap validasi unik), Nomor Telepon, dan mengganti Password (opsional jika diisi, verifikasi password lama). Tidak ada foto profil atau alamat.
> 4. Pastikan semua form menggunakan CSRF token dan validasi server-side berbahasa Indonesia.

---

### STEP 5: Homepage Publik, Katalog Menu, Pencarian & Filter

**Prompt untuk Agent:**
> Implementasikan Homepage dan Katalog Menu sesuai Bagian 5.2 dan 5.3 pada `PRD.MD`:
> 1. Buat Model `models/Category.php`, `models/Menu.php`, dan model untuk `cafe_settings`.
> 2. Buat Controller `controllers/MenuController.php`.
> 3. Implementasikan **Homepage (`views/customer/home.php` atau `index.php`)** yang menampilkan secara dinamis dari `cafe_settings`:
>    - Logo & nama cafe, Banner cafe, Deskripsi cafe.
>    - Tombol CTA **Lihat Menu** dan **Booking Meja**.
>    - Informasi jam operasional tetap (**09.00–22.00 WIB setiap hari**).
>    - Informasi area **Indoor** dan **Outdoor**.
>    - Alamat cafe, nomor kontak, dan footer.
>    - **Seluruh menu** makanan dan minuman (bukan hanya unggulan), menampilkan foto, nama, kategori, harga, dan badge status ketersediaan (`Tersedia` / `Tidak Tersedia`). Menu `Tidak Tersedia` tetap tampil, namun tombol "Tambah ke Keranjang" dinonaktifkan (`disabled`).
> 4. Implementasikan fitur **Pencarian dan Filter Menu** (berfungsi di halaman utama / halaman menu makanan & minuman):
>    - Cari berdasarkan nama menu.
>    - Filter berdasarkan kategori (termasuk link cepat Menu Makanan & Menu Minuman dari navbar).
>    - Filter rentang harga (min - max).
>    - Urutkan harga: Termurah (`ASC`) dan Termahal (`DESC`).
>    - Checkbox/toggle "Tampilkan menu yang tersedia saja".

---

### STEP 6: Modul Keranjang Belanja Persisten, Booking Meja, & Checkout Atomik

**Prompt untuk Agent:**
> Implementasikan fitur paling kritikal CozyCafe sesuai Bagian 5.6, 5.7, 5.8, dan Business Rules `BR-01`–`BR-20` pada `PRD.MD`.
>
> 1. **Keranjang Belanja (`models/Cart.php`, `controllers/CartController.php`, `views/customer/cart.php`):**
>    - Keranjang disimpan permanen di database (`carts` & `cart_items`) agar tidak hilang saat logout dan tidak memiliki batas waktu.
>    - Pelanggan wajib login untuk menambahkan/mengelola keranjang.
>    - Tolak penambahan menu jika status menu `Tidak Tersedia`.
>    - Pelanggan dapat menambah item, mengubah kuantitas, menghapus item, dan menyimpan catatan khusus per item (misal: "Less sugar").
>    - Tampilkan subtotal per item dan total keranjang.
>
> 2. **Booking Meja (`models/CafeTable.php`, `models/Booking.php`, `controllers/BookingController.php`, `views/customer/booking.php`):**
>    - Jika keranjang kosong, cegah pelanggan melanjutkan booking meja dan tampilkan pesan untuk memilih menu terlebih dahulu (`BR-02`).
>    - Booking hanya untuk **hari ini** (`date('Y-m-d')` dalam `Asia/Jakarta`). Tampilkan tanggal hari ini secara jelas (read-only).
>    - Generate daftar slot waktu 2 jam per jam: `09:00–11:00` hingga `20:00–22:00`. **Sembunyikan** slot yang jam mulainya sudah terlewat dari waktu WIB saat ini.
>    - Pelanggan memilih **Jumlah Tamu** dan **Slot Waktu**.
>    - Sediakan endpoint/AJAX atau reload form yang memeriksa ketersediaan meja secara real-time: hanya ambil meja dengan `status = 'Aktif'` dan `capacity >= guest_count`, lalu filter meja yang tidak memiliki booking overlap dengan rumus:
>      `existing_start < requested_end AND existing_end > requested_start` (kecualikan booking dengan status `Dibatalkan`).
>    - Tampilkan pilihan meja dalam bentuk **Card / List sederhana** (tanpa visual floorplan), lengkap dengan Nomor Meja, Area (`Indoor`/`Outdoor`), dan Kapasitas.
>
> 3. **Halaman Ringkasan & Checkout Atomik (`models/Transaction.php`, `models/Order.php`, `views/customer/checkout_summary.php`):**
>    - Tampilkan ringkasan lengkap sebelum konfirmasi: Tanggal booking, Jam booking, Nomor & Area meja, Jumlah tamu, Daftar menu + qty + catatan + harga + subtotal, Total pembayaran, dan Metode pembayaran ("Bayar langsung di kasir cafe").
>    - Saat pelanggan menekan **Konfirmasi Pesanan**, jalankan **Database Transaction (`$pdo->beginTransaction()`)**:
>      1. Validasi ulang keranjang tidak kosong dan seluruh menu di keranjang masih berstatus `Tersedia`. Hitung ulang total harga dari data menu di database (bukan dari input form!).
>      2. Lakukan query cek ulang overlap meja dengan `FOR UPDATE` (atau locking row) untuk mencegah *race condition* / *double booking* oleh dua user bersamaan. Jika bentrok, `rollBack()` dan beri pesan error ramah agar memilih meja lain.
>      3. Generate `transaction_code` unik format `CC-YYYYMMDD-XXX` (urut harian, aman dari duplikasi).
>      4. Insert `transactions` (`payment_status = 'Belum Dibayar'`).
>      5. Insert `bookings` (`status = 'Dikonfirmasi'`).
>      6. Insert `orders` (`order_status = 'Dikonfirmasi'`).
>      7. Insert `order_items` dengan `menu_name_snapshot` dan `price_snapshot`.
>      8. Insert `notifications` ("Booking Berhasil & Pesanan Dikonfirmasi").
>      9. Kosongkan `cart_items` milik user tersebut.
>      10. `commit()`. Jika ada error apa pun, `rollBack()`.
>    - Tampilkan halaman konfirmasi keberhasilan beserta Kode Transaksi.

---

### STEP 7: Riwayat Transaksi, Detail, Pembatalan oleh Pelanggan, & Notifikasi In-App

**Prompt untuk Agent:**
> Implementasikan Bagian 5.13 (sisi pelanggan), 5.14, dan 5.15 pada `PRD.MD`:
> 1. **Riwayat Transaksi & Detail (`views/customer/transactions.php`, `views/customer/transaction_detail.php`):**
>    - Gabungkan data booking dan pesanan dalam satu halaman **Riwayat Transaksi** milik user yang login (gunakan pagination).
>    - Tampilkan: Kode transaksi, Tanggal transaksi, Tanggal & Jam booking, Nomor & Area meja, Jumlah tamu, Daftar menu, Total pembayaran, Status booking, Status pesanan, dan Status pembayaran.
>    - Di halaman **Detail Transaksi**, tampilkan juga riwayat perubahan pesanan (`order_change_histories`) jika admin pernah mengubah pesanan, serta alasan pembatalan (`cancellation_histories`) jika dibatalkan. Tidak ada tombol download PDF/cetak struk untuk pelanggan.
> 2. **Pembatalan oleh Pelanggan:**
>    - Pelanggan hanya dapat membatalkan transaksi miliknya yang masih berstatus aktif (`Dikonfirmasi`), dengan syarat **minimal 2 jam sebelum `start_time`** booking (gunakan perhitungan `DateTime` `Asia/Jakarta`).
>    - Jika kurang dari 2 jam, tombol batal disembunyikan/dinonaktifkan dan tampilkan informasi untuk menghubungi admin.
>    - Proses pembatalan dijalankan dalam DB transaction: ubah `bookings.status = 'Dibatalkan'`, `orders.order_status = 'Dibatalkan'`, simpan ke `cancellation_histories` (`canceled_by = 'customer'`), dan buat notifikasi pembatalan.
> 3. **Notifikasi Dalam Aplikasi (`models/Notification.php`, `controllers/NotificationController.php`, `views/customer/notifications.php`):**
>    - Tampilkan jumlah notifikasi `is_read = 0` di ikon lonceng navbar pelanggan.
>    - Pelanggan dapat melihat seluruh riwayat notifikasi, menandai satu notifikasi sebagai sudah dibaca, atau menandai semua sudah dibaca, serta langsung menuju detail transaksi terkait.

---

### STEP 8: Dashboard Admin & Seluruh Fitur Operasional Cafe

**Prompt untuk Agent:**
> Implementasikan seluruh fitur Admin sesuai Bagian 5.3, 5.4, 5.5, 5.10, 5.11, 5.12, 5.13 (sisi admin), 5.16, dan 5.18 pada `PRD.MD` melalui `controllers/AdminController.php`, `controllers/PaymentController.php`, `controllers/OrderController.php`, dan views di `views/admin/`:
>
> 1. **Dashboard Admin (`views/admin/dashboard.php`):**
>    - Kartu Statistik: Total Pelanggan, Total Menu, Total Booking, Total Pesanan, dan **Total Pendapatan** (hanya hitung transaksi dengan `payment_status = 'Sudah Dibayar'`).
>    - Grafik menggunakan **Chart.js**: Grafik Transaksi Harian (misal 7/14 hari terakhir) dan Grafik Transaksi Bulanan (12 bulan tahun berjalan).
>    - Tabel ringkas: Booking Terbaru dan Pesanan Terbaru.
> 2. **Kelola Kategori Menu:** CRUD lengkap. Validasi nama kategori unik. **Cegah penghapusan** kategori jika masih memiliki menu terkait (tampilkan pesan error yang jelas).
> 3. **Kelola Menu:** CRUD lengkap dengan upload foto (disimpan di `uploads/menu/`), harga harus positif, status `Tersedia` / `Tidak Tersedia`, pagination & pencarian admin. Hapus foto lama dengan aman saat foto diganti/menu dihapus.
> 4. **Kelola Meja:** CRUD lengkap (`table_number` unik, `area` Indoor/Outdoor, `capacity` $\ge 1$, `status` Aktif/Tidak Aktif).
> 5. **Kelola Booking & Pembatalan Admin:**
>    - Lihat seluruh daftar booking (filter tanggal/status, pagination).
>    - Admin dapat mengubah status booking menjadi `Selesai` atau membatalkan booking **kapan saja** dengan **alasan pembatalan wajib diisi**.
>    - Saat admin membatalkan: ubah booking & order menjadi `Dibatalkan`, catat ke `cancellation_histories` (`canceled_by = 'admin'`), kirim notifikasi ke pelanggan.
> 6. **Kelola Pesanan & Perubahan Pesanan (`OrderController`):**
>    - Admin dapat memperbarui `order_status` (`Menunggu Konfirmasi`, `Dikonfirmasi`, `Sedang Disiapkan`, `Siap Disajikan`, `Selesai`, `Dibatalkan`). Setiap perubahan status relevan (`Sedang Disiapkan`, `Siap Disajikan`, `Selesai`) otomatis mengirim **notifikasi in-app** ke pelanggan.
>    - **Ubah Detail Pesanan oleh Admin:** Pada detail pesanan yang belum selesai/dibatalkan, admin dapat menambah menu baru, menghapus item, mengubah kuantitas, dan mengedit catatan. Sistem otomatis menghitung ulang `orders.subtotal` dan `transactions.total_amount` dalam DB Transaction, serta mencatat deskripsi perubahan, `old_total`, `new_total`, dan `admin_id` ke tabel `order_change_histories`.
> 7. **Kelola Pembayaran (`PaymentController`):**
>    - Tampilkan daftar transaksi dengan status pembayaran (`Belum Dibayar` / `Sudah Dibayar`).
>    - Admin dapat mengonfirmasi pembayaran kasir menjadi `Sudah Dibayar`. Status pembayaran tetap terpisah dari status pesanan.
> 8. **Kelola Pelanggan:** Lihat daftar pelanggan (search & pagination) dan halaman detail informasi akun pelanggan beserta ringkasan transaksinya.
> 9. **Pengaturan Informasi Cafe & Profil Admin:** Form untuk mengubah `cafe_name`, `description`, `address`, `phone`, upload `logo`, dan upload `banner` (disimpan di `uploads/cafe/`), plus ubah password/profil akun admin sendiri. Jam operasional tidak boleh ada di form ini.

---

### STEP 9: Modul Laporan Transaksi, Backup Database SQL, & Pengujian

**Prompt untuk Agent:**
> Implementasikan fitur Prioritas 3 sesuai Bagian 5.17, 5.19, dan Acceptance Criteria pada `PRD.MD`:
>
> 1. **Laporan Transaksi (`views/admin/reports.php`):**
>    - Filter berdasarkan `tanggal_mulai` dan `tanggal_akhir` (default bulan berjalan).
>    - Tampilkan ringkasan Total Transaksi dan **Total Pendapatan** (dihitung dari transaksi yang berstatus `Sudah Dibayar`).
>    - Tabel detail transaksi lengkap (Kode Transaksi, Tanggal, Pelanggan, Meja, Total, Status Pembayaran, Status Booking, Status Pesanan).
> 2. **Backup Database Manual (`views/admin/backup.php`):**
>    - Buat halaman Backup Database dengan tombol **Download Backup Database (.sql)**.
>    - **PENTING:** Implementasikan generator dump SQL menggunakan **Pure PHP PDO** (membaca `SHOW TABLES`, `SHOW CREATE TABLE`, dan `SELECT *` untuk menghasilkan `INSERT INTO` statements yang valid dalam satu transaction/foreign_key_checks=0). Jangan hanya mengandalkan `exec('mysqldump')` agar fitur ini **100% berfungsi** baik di Windows XAMPP maupun di dalam container Docker/Dokploy yang tidak memiliki binary `mysqldump`.
>    - Hasilkan file `.sql` yang di-stream langsung ke browser untuk diunduh (`cozycafe_backup_YYYYMMDD_His.sql`).
> 3. **Checklist Pengujian Akhir (Verifikasi Otomatis/Manual):**
>    - Periksa seluruh 23 Business Rules (`BR-01` s/d `BR-23`) dan Acceptance Criteria di Bagian 11 `PRD.MD`.
>    - Pastikan tidak ada SQL injection, XSS, error PHP Notice/Warning, atau celah akses admin oleh role `customer`.

---

### STEP 10: Containerisasi Docker & Deployment ke Dokploy

**Prompt untuk Agent:**
> Siapkan seluruh konfigurasi Docker untuk men-deploy aplikasi CozyCafe ke **Dokploy**, serta buatkan panduan langkah demi langkah di dalam `DEPLOY_DOKPLOY.md`:
>
> 1. **Buat `Dockerfile` (berbasis `php:8.2-apache`):**
>    - Install system dependencies yang dibutuhkan PHP extensions & DomPDF/PhpSpreadsheet (`libpng-dev`, `libjpeg-dev`, `libfreetype6-dev`, `libzip-dev`, `libicu-dev`, `libxml2-dev`, `unzip`, `git`).
>    - Konfigurasi & install ekstensi PHP: `pdo_mysql`, `mysqli`, `gd` (dengan freetype & jpeg untuk DomPDF/gambar), `zip`, `intl`, `mbstring`, `opcache`.
>    - Aktifkan modul Apache `rewrite` dan `headers`.
>    - Install Composer di dalam image dan jalankan `composer install --no-dev --optimize-autoloader`.
>    - Atur `DocumentRoot` ke `/var/www/html` dan izinkan `AllowOverride All` agar `.htaccess` berfungsi.
>    - Atur PHP `date.timezone = Asia/Jakarta` dan `upload_max_filesize = 10M` melalui `php.ini` custom.
>    - Pastikan kepemilikan dan permission folder `/var/www/html/uploads` dimiliki oleh `www-data:www-data` (`chmod -R 775`).
>    - Buat `docker-entrypoint.sh` kecil yang memastikan subdirektori `uploads/menu` dan `uploads/cafe` tersedia dan writable saat volume kosong di-mount pertama kali, lalu jalankan `apache2-foreground`.
> 2. **Buat `docker-compose.yml` (Siap untuk uji lokal Docker maupun Dokploy Docker Compose):**
>    - Service `app`: build dari `.`, expose port `80`, environment variables (`DB_HOST=db`, `DB_PORT=3306`, `DB_NAME`, `DB_USER`, `DB_PASS`, `APP_URL`, `TZ=Asia/Jakarta`), mount persistent volume `cozycafe_uploads:/var/www/html/uploads`, `depends_on` service `db` (dengan healthcheck).
>    - Service `db`: image `mariadb:10.11` (atau `mysql:8.0`), timezone `Asia/Jakarta`, mount volume `cozycafe_db_data:/var/lib/mysql`, dan mount `./database/cozycafe.sql:/docker-entrypoint-initdb.d/init.sql:ro` agar database & seed data otomatis terbuat saat container pertama kali berjalan!
> 3. **Buat file `.dockerignore`:** Abaikan `.git`, `.env`, `vendor/` (di-build ulang di container), dan file sementara lokal.
> 4. **Buat `DEPLOY_DOKPLOY.md` berisi panduan lengkap deployment ke Dokploy:**
>    - **Opsi A (Direkomendasikan): Deploy via Dokploy Compose / Docker Compose** dari Git Repository (GitHub/GitLab).
>    - **Opsi B: Deploy App (Dockerfile) + Managed MariaDB/MySQL Service di Dokploy** (termasuk cara import `database/cozycafe.sql` pertama kali via terminal/Adminer/client).
>    - Konfigurasi **Environment Variables** di dashboard Dokploy (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `APP_URL`, `TZ=Asia/Jakarta`).
>    - Konfigurasi **Persistent Volumes / Mounts** di Dokploy untuk `/var/www/html/uploads` agar foto menu dan logo/banner tidak hilang saat redeploy.
>    - Konfigurasi **Domain & HTTPS (Let's Encrypt / Traefik)** di tab Domains Dokploy (arahkan ke port container `80`).
>    - **Checklist Verifikasi Pasca-Deploy:** Uji akses homepage, login admin default, ubah password admin default segera, uji upload foto menu, uji booking & checkout WIB, uji export PDF/Excel, dan uji download backup `.sql`.

---

## 4. Konvensi Kerja Agent Saat Eksekusi

- Selalu tuliskan kode yang bersih, terstruktur, dan mudah dibaca (PSR-12 style untuk PHP).
- Jangan pernah meninggalkan *placeholder* atau `// TODO` pada logika inti; setiap step harus selesai dan siap dijalankan.
- Jika menemukan ambiguitas kecil, prioritaskan kesesuaian dengan tabel **Business Rules (`BR-01` s/d `BR-23`)** pada `PRD.MD`.
- Setelah menyelesaikan setiap step, lakukan pemeriksaan syntax PHP (`php -l`) pada file yang baru dibuat/diubah sebelum melaporkan selesai.