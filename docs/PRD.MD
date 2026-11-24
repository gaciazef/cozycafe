# PRODUCT REQUIREMENTS DOCUMENT (PRD)
# CozyCafe — Aplikasi Pemesanan Menu dan Booking Meja Cafe

**Versi:** 1.0  
**Status:** Draft Final  
**Platform:** Web Application  
**Bahasa:** Bahasa Indonesia  
**Target Implementasi:** Aplikasi lokal menggunakan XAMPP atau Laragon  
**Teknologi Utama:** PHP Native 8.2+, MySQL, Bootstrap 5, HTML, CSS, JavaScript

---

# 1. Ringkasan Produk

## 1.1 Deskripsi Produk

CozyCafe adalah aplikasi web untuk mempermudah pelanggan melakukan pemesanan makanan dan minuman sekaligus booking meja di cafe dalam satu transaksi.

Pelanggan dapat membuat akun, melihat seluruh menu, memilih makanan dan minuman, memasukkan produk ke keranjang, memilih tanggal dan waktu booking, menentukan jumlah tamu, memilih meja yang tersedia, kemudian mengonfirmasi transaksi.

Sistem akan memeriksa ketersediaan meja dan menyimpan booking serta pesanan secara bersamaan. Pelanggan melakukan pembayaran langsung di kasir cafe ketika datang.

Admin memiliki dashboard untuk mengelola menu, kategori, meja, booking, pesanan, pembayaran, pelanggan, laporan transaksi, dan informasi cafe.

## 1.2 Latar Belakang Masalah

Proses pemesanan dan booking cafe yang dilakukan secara manual memiliki beberapa kendala:

1. Pelanggan harus datang langsung untuk mengetahui menu dan melakukan pemesanan.
2. Pelanggan kesulitan mengetahui ketersediaan meja.
3. Booking meja dilakukan melalui WhatsApp atau telepon sehingga berpotensi terjadi bentrok jadwal.
4. Informasi menu dan harga tidak tersedia secara terpusat.
5. Admin harus mencatat pesanan dan booking secara manual.
6. Riwayat transaksi sulit dilacak apabila pencatatan masih dilakukan secara terpisah.

CozyCafe dibuat untuk mengintegrasikan proses booking meja dan pemesanan menu ke dalam satu sistem.

## 1.3 Tujuan Produk

- Mempermudah pelanggan melakukan booking meja.
- Memungkinkan pelanggan memesan makanan dan minuman sebelum datang.
- Mengurangi risiko bentrok jadwal booking.
- Mempermudah admin mengelola menu, meja, booking, dan pesanan.
- Menyediakan riwayat transaksi yang terintegrasi.
- Membantu admin memantau pendapatan dan aktivitas transaksi.

## 1.4 Target Pengguna

Aplikasi memiliki dua role:

| Role | Deskripsi |
|---|---|
| Pelanggan | Pengguna yang melakukan booking meja dan pemesanan menu. |
| Admin | Pengelola cafe yang mengatur seluruh data dan transaksi. |

Tidak ada role kasir terpisah atau superadmin.

---

# 2. Ruang Lingkup Produk

## 2.1 Fitur yang Termasuk

- Registrasi dan login pelanggan.
- Login admin.
- Pengelolaan profil pelanggan.
- Tampilan seluruh menu di halaman utama.
- Pencarian dan filter menu.
- Keranjang belanja tersimpan.
- Booking meja dan pemesanan dalam satu transaksi.
- Pemeriksaan ketersediaan meja otomatis.
- Konfirmasi transaksi otomatis oleh sistem.
- Pembayaran langsung di kasir.
- Pengelolaan status pesanan.
- Pembatalan booking dan pesanan.
- Notifikasi dalam aplikasi.
- Riwayat transaksi gabungan.
- Dashboard admin.
- Pengelolaan kategori, menu, meja, dan pelanggan.
- Pengelolaan booking dan pesanan.
- Pengelolaan pembayaran.
- Laporan transaksi.
- Export laporan PDF dan Excel.
- Pengaturan informasi cafe.
- Backup database manual melalui dashboard.

## 2.2 Fitur yang Tidak Termasuk

Fitur berikut tidak menjadi bagian versi awal:

- Pemesanan takeaway atau delivery.
- Pembayaran online.
- Integrasi payment gateway.
- Integrasi WhatsApp.
- Integrasi SMS.
- Registrasi admin.
- Multi-admin.
- Login pelanggan sebagai guest.
- Rating dan review.
- Promo, voucher, dan diskon.
- Sistem stok bahan baku.
- Pengaturan jam operasional melalui dashboard.
- Sistem multibahasa.
- Fitur lupa password otomatis.
- Bukti transaksi PDF khusus pelanggan.
- Cetak struk pelanggan.
- Backup dan restore otomatis.
- Aplikasi Android atau iOS.

---

# 3. User Persona

## 3.1 Pelanggan

**Karakteristik:**
- Ingin mengetahui menu dan harga sebelum datang.
- Ingin memastikan meja tersedia.
- Ingin memesan makanan dan minuman sebelum tiba.
- Membutuhkan informasi booking dan pesanan yang mudah diakses.

**Kebutuhan:**
- Melihat menu.
- Menambahkan produk ke keranjang.
- Memilih jumlah tamu.
- Memilih meja dan waktu booking.
- Melakukan checkout.
- Melihat status pesanan dan pembayaran.
- Melihat riwayat transaksi.
- Menerima notifikasi.

## 3.2 Admin

**Karakteristik:**
- Bertanggung jawab terhadap operasional cafe.
- Mengelola menu, meja, dan transaksi.
- Memantau pembayaran dan laporan.

**Kebutuhan:**
- Mengelola kategori dan menu.
- Mengelola meja.
- Memantau booking.
- Mengelola pesanan.
- Memperbarui status pesanan.
- Mencatat pembayaran.
- Melihat laporan.
- Mengunduh backup database.

---

# 4. Alur Penggunaan Utama

## 4.1 Alur Pelanggan

1. Pelanggan membuka website CozyCafe.
2. Pelanggan melihat informasi cafe dan seluruh menu.
3. Pelanggan melakukan registrasi atau login.
4. Pelanggan memilih makanan dan minuman.
5. Pelanggan memasukkan produk ke keranjang.
6. Pelanggan mengatur jumlah dan catatan setiap item.
7. Pelanggan membuka proses booking meja.
8. Pelanggan memilih jumlah tamu.
9. Sistem menampilkan meja yang memiliki kapasitas cukup.
10. Pelanggan memilih tanggal dan jam booking.
11. Sistem memeriksa ketersediaan meja.
12. Pelanggan memilih meja.
13. Pelanggan membuka halaman ringkasan transaksi.
14. Pelanggan memeriksa booking dan daftar pesanan.
15. Pelanggan menekan tombol Konfirmasi Pesanan.
16. Sistem memeriksa ulang ketersediaan meja.
17. Sistem menyimpan booking dan pesanan secara bersamaan.
18. Sistem menghasilkan kode transaksi unik.
19. Sistem menampilkan konfirmasi keberhasilan.
20. Pelanggan dapat memantau transaksi melalui halaman Riwayat Transaksi.
21. Pelanggan datang ke cafe dan melakukan pembayaran di kasir.

## 4.2 Alur Admin

1. Admin login.
2. Admin masuk ke dashboard.
3. Admin melihat ringkasan transaksi dan aktivitas cafe.
4. Admin mengelola kategori dan menu.
5. Admin mengelola meja.
6. Admin memantau booking.
7. Admin mengelola pesanan dan memperbarui statusnya.
8. Admin memeriksa pembayaran.
9. Setelah pelanggan membayar di kasir, admin mengubah status pembayaran menjadi Sudah Dibayar.
10. Admin dapat melihat laporan dan melakukan backup database.

---

# 5. Functional Requirements

## 5.1 Autentikasi dan Manajemen Pengguna

### Registrasi Pelanggan

Pelanggan wajib membuat akun sebelum melakukan transaksi.

Data registrasi:
- Nama lengkap.
- Email.
- Nomor telepon.
- Password.

Ketentuan:
- Email harus unik.
- Password disimpan menggunakan `password_hash()`.
- Login menggunakan `password_verify()`.
- Pelanggan tidak dapat melakukan checkout tanpa login.
- Tidak tersedia registrasi admin.

### Login

Sistem menyediakan halaman login untuk pelanggan dan admin.

Ketentuan:
- Validasi email dan password.
- Menggunakan PHP Session.
- Session harus diperbarui setelah login.
- Pengguna hanya dapat mengakses halaman sesuai role.

Tidak ada fitur penguncian akun setelah beberapa kali percobaan login gagal.

### Lupa Password

Tidak tersedia fitur reset password otomatis.

Pelanggan yang lupa password harus menghubungi admin untuk mendapatkan bantuan.

### Profil Pelanggan

Pelanggan dapat melihat dan memperbarui:
- Nama lengkap.
- Email.
- Nomor telepon.
- Password.

Tidak memerlukan foto profil atau alamat.

Admin dapat melihat daftar pelanggan dan detail informasi akun.

## 5.2 Homepage

Halaman utama menampilkan:

1. Logo dan nama CozyCafe.
2. Banner cafe.
3. Deskripsi cafe.
4. Tombol Lihat Menu.
5. Tombol Booking Meja.
6. Seluruh menu makanan dan minuman.
7. Foto menu.
8. Nama menu.
9. Kategori menu.
10. Harga menu.
11. Status ketersediaan menu.
12. Informasi jam operasional.
13. Informasi area Indoor dan Outdoor.
14. Alamat cafe.
15. Nomor kontak.
16. Footer.

Seluruh menu ditampilkan pada halaman utama, bukan hanya menu unggulan.

Menu tidak tersedia tetap dapat dilihat, tetapi tombol tambah ke keranjang dinonaktifkan.

Informasi cafe dikelola admin melalui dashboard, kecuali jam operasional yang bersifat tetap.

Data informasi cafe:
- Nama cafe.
- Logo.
- Deskripsi.
- Alamat.
- Nomor telepon.
- Banner.

Data tersebut disimpan di database dan ditampilkan secara dinamis.

## 5.3 Manajemen Menu

Admin dapat melakukan CRUD menu.

Data menu:
- Nama menu.
- Kategori.
- Deskripsi.
- Harga.
- Foto.
- Status ketersediaan.

Status:
- Tersedia.
- Tidak Tersedia.

Ketentuan:
- Harga harus berupa angka positif.
- Setiap menu harus memiliki kategori.
- Foto menu dapat diunggah admin.
- Menu tidak tersedia tidak dapat ditambahkan ke keranjang.
- Tidak menggunakan perhitungan stok bahan baku.

### Pencarian dan Filter Menu

Pelanggan dapat:
- Mencari berdasarkan nama.
- Memfilter kategori.
- Memfilter rentang harga.
- Mengurutkan harga termurah.
- Mengurutkan harga termahal.
- Menampilkan menu yang tersedia saja.

## 5.4 Manajemen Kategori

Admin dapat melakukan CRUD kategori.

Contoh kategori:
- Coffee.
- Non-Coffee.
- Main Course.
- Snack.
- Dessert.

Ketentuan:
- Nama kategori harus unik.
- Menu harus terhubung dengan kategori.
- Kategori yang masih digunakan menu tidak boleh dihapus sebelum menu dipindahkan atau dihapus.

## 5.5 Manajemen Meja

Admin dapat menambahkan, mengubah, dan menonaktifkan meja.

Data meja:
- Nomor meja.
- Area.
- Kapasitas.
- Status.

Area:
- Indoor.
- Outdoor.

Status:
- Aktif.
- Tidak Aktif.

Ketentuan:
- Nomor meja harus unik.
- Satu booking hanya menggunakan satu meja.
- Meja tidak aktif tidak ditampilkan kepada pelanggan.
- Ketersediaan meja dihitung berdasarkan jadwal booking, bukan status statis.
- Pelanggan memilih meja melalui tampilan card atau list sederhana.
- Tidak menggunakan visual floorplan.

## 5.6 Booking Meja

Booking meja harus dilakukan bersamaan dengan pemesanan makanan atau minuman.

Tidak tersedia booking meja tanpa pesanan menu.

### Data Booking

- Kode transaksi.
- Pelanggan.
- Tanggal booking.
- Jam mulai.
- Jam selesai.
- Jumlah tamu.
- Meja.
- Area meja.
- Status booking.

### Ketentuan Booking

- Booking hanya dapat dilakukan untuk hari yang sama.
- Jam operasional tetap 09.00–22.00 WIB.
- Durasi setiap booking adalah 2 jam.
- Slot booking tersedia setiap 1 jam.
- Pelanggan wajib memilih jumlah tamu.
- Sistem hanya menampilkan meja dengan kapasitas mencukupi.
- Satu booking menggunakan satu meja.
- Sistem mengonfirmasi booking otomatis apabila berhasil disimpan.
- Admin dapat mengelola booking apabila diperlukan.

### Jadwal Booking

| Jam Mulai | Jam Selesai |
|---|---|
| 09.00 | 11.00 |
| 10.00 | 12.00 |
| 11.00 | 13.00 |
| 12.00 | 14.00 |
| 13.00 | 15.00 |
| 14.00 | 16.00 |
| 15.00 | 17.00 |
| 16.00 | 18.00 |
| 17.00 | 19.00 |
| 18.00 | 20.00 |
| 19.00 | 21.00 |
| 20.00 | 22.00 |

Slot yang sudah lewat otomatis disembunyikan.

### Validasi Ketersediaan

Sistem harus mencegah dua booking aktif menggunakan meja yang sama pada waktu yang bertabrakan.

Contoh:

- Meja 1 sudah dibooking pukul 09.00–11.00.
- Pelanggan lain tidak boleh membooking Meja 1 pukul 10.00–12.00.
- Pelanggan masih dapat memilih meja lain yang tersedia pada waktu tersebut.

Rumus pemeriksaan overlap:

`existing_start < requested_end AND existing_end > requested_start`

Booking berstatus dibatalkan tidak dihitung sebagai konflik.

Pemeriksaan harus dilakukan kembali ketika pelanggan menekan tombol Konfirmasi Pesanan agar tidak terjadi double booking akibat transaksi bersamaan.

## 5.7 Keranjang Belanja

Pelanggan memiliki keranjang yang tersimpan di database.

Ketentuan:
- Keranjang tidak hilang ketika pelanggan logout.
- Keranjang tidak memiliki batas waktu penyimpanan.
- Keranjang tetap tersedia ketika pelanggan login kembali.
- Pelanggan dapat menambahkan produk.
- Pelanggan dapat mengubah kuantitas.
- Pelanggan dapat menghapus produk.
- Pelanggan dapat menambahkan catatan untuk setiap item.

Informasi keranjang:
- Nama menu.
- Foto menu.
- Harga.
- Kuantitas.
- Catatan.
- Subtotal item.
- Total keranjang.

Menu yang tidak tersedia tidak dapat ditambahkan.

## 5.8 Checkout dan Konfirmasi Transaksi

Checkout menggabungkan booking meja dan pesanan menu dalam satu transaksi.

Sebelum transaksi disimpan, sistem menampilkan halaman ringkasan.

Isi ringkasan:
- Tanggal booking.
- Jam booking.
- Nomor meja.
- Area meja.
- Jumlah tamu.
- Daftar menu.
- Kuantitas.
- Catatan setiap menu.
- Harga setiap item.
- Subtotal.
- Total pembayaran.
- Metode pembayaran.

Pelanggan harus menekan tombol Konfirmasi Pesanan untuk menyelesaikan transaksi.

### Ketentuan Penyimpanan

- Booking dan pesanan harus tersimpan dalam satu proses transaksi database.
- Jika penyimpanan salah satu bagian gagal, seluruh proses dibatalkan.
- Sistem harus memeriksa kembali ketersediaan meja.
- Sistem menghasilkan kode transaksi unik.
- Sistem mengosongkan keranjang setelah transaksi berhasil.

Contoh format kode transaksi:

`CC-20261123-001`

Kode transaksi digunakan bersama untuk booking dan pesanan.

## 5.9 Status Booking

Status booking:
- Dikonfirmasi.
- Dibatalkan.
- Selesai.

Booking langsung berstatus Dikonfirmasi setelah transaksi berhasil.

Tidak diperlukan persetujuan manual admin untuk booking normal.

## 5.10 Status Pesanan

Status pesanan:

1. Menunggu Konfirmasi.
2. Dikonfirmasi.
3. Sedang Disiapkan.
4. Siap Disajikan.
5. Selesai.
6. Dibatalkan.

Dalam alur normal CozyCafe, pesanan langsung dimulai dengan status Dikonfirmasi karena booking dan pesanan dikonfirmasi otomatis oleh sistem.

Status Menunggu Konfirmasi tetap tersedia sebagai opsi sistem, tetapi tidak digunakan dalam alur checkout normal.

Admin bertanggung jawab memperbarui status pesanan selanjutnya.

## 5.11 Pembayaran

Metode pembayaran hanya satu:

**Bayar langsung di kasir cafe (Cash on Arrival).**

Status pembayaran:
- Belum Dibayar.
- Sudah Dibayar.

Ketentuan:
- Transaksi baru berstatus Belum Dibayar.
- Pelanggan membayar ketika datang ke cafe.
- Admin memeriksa pembayaran.
- Admin mengubah status menjadi Sudah Dibayar setelah menerima pembayaran.
- Status pembayaran terpisah dari status pesanan.
- Tidak ada pembayaran online atau payment gateway.

## 5.12 Perubahan Pesanan oleh Admin

Setelah transaksi dikonfirmasi, pelanggan tidak dapat mengubah pesanan secara langsung.

Jika pelanggan ingin mengubah pesanan, pelanggan harus menghubungi admin.

Admin dapat:
- Menambahkan menu.
- Menghapus menu.
- Mengubah kuantitas.
- Memperbarui catatan menu.
- Menghitung ulang total transaksi.

Setiap perubahan harus menyimpan waktu perubahan dan dapat dilihat pelanggan melalui detail transaksi.

## 5.13 Pembatalan Booking dan Pesanan

Booking dan pesanan merupakan satu transaksi terintegrasi.

Pembatalan transaksi akan membatalkan booking dan pesanan terkait.

### Pembatalan oleh Pelanggan

Pelanggan dapat membatalkan transaksi miliknya sendiri dengan ketentuan:
- Pembatalan hanya dapat dilakukan minimal 2 jam sebelum waktu mulai booking.
- Jika waktu booking kurang dari 2 jam lagi, pelanggan tidak dapat membatalkan melalui aplikasi.
- Pelanggan harus menghubungi admin.

### Pembatalan oleh Admin

Admin dapat membatalkan transaksi kapan saja apabila diperlukan.

Admin wajib mengisi alasan pembatalan.

### Dampak Pembatalan

- Status booking berubah menjadi Dibatalkan.
- Status pesanan berubah menjadi Dibatalkan.
- Meja kembali tersedia.
- Riwayat pembatalan tetap tersimpan.
- Pelanggan menerima notifikasi pembatalan.

## 5.14 Riwayat Transaksi

Riwayat booking dan pesanan digabung dalam satu halaman bernama **Riwayat Transaksi**.

Setiap transaksi menggunakan satu kode transaksi yang menghubungkan booking dan pesanan.

Informasi yang ditampilkan:
- Kode transaksi.
- Tanggal transaksi.
- Tanggal booking.
- Jam booking.
- Nomor meja.
- Area meja.
- Jumlah tamu.
- Daftar menu.
- Total pembayaran.
- Status booking.
- Status pesanan.
- Status pembayaran.

Pelanggan dapat membuka detail setiap transaksi.

Tidak tersedia fitur download PDF atau cetak bukti transaksi khusus pelanggan.

## 5.15 Notifikasi

Sistem menyediakan notifikasi dalam aplikasi.

Notifikasi ditampilkan melalui:
- Ikon notifikasi pada navbar.
- Jumlah notifikasi belum dibaca.
- Daftar riwayat notifikasi.

Jenis notifikasi:
- Booking berhasil.
- Booking dibatalkan.
- Pesanan dikonfirmasi.
- Pesanan sedang disiapkan.
- Pesanan siap disajikan.
- Pesanan selesai.

Pelanggan dapat:
- Melihat notifikasi.
- Membaca notifikasi.
- Melihat riwayat notifikasi.
- Menandai notifikasi sebagai sudah dibaca.

Tidak ada integrasi WhatsApp, email, atau SMS untuk notifikasi transaksi.

## 5.16 Dashboard Admin

Dashboard admin menampilkan ringkasan aktivitas cafe.

### Ringkasan Statistik

- Total pelanggan.
- Total menu.
- Total booking.
- Total pesanan.
- Total pendapatan.

Pendapatan dihitung dari transaksi yang sudah berstatus Sudah Dibayar.

### Grafik

- Grafik transaksi harian.
- Grafik transaksi bulanan.

### Informasi Terbaru

- Booking terbaru.
- Pesanan terbaru.

### Navigasi Admin

1. Dashboard.
2. Kelola Kategori Menu.
3. Kelola Menu.
4. Kelola Meja.
5. Kelola Booking.
6. Kelola Pesanan.
7. Kelola Pembayaran.
8. Kelola Pelanggan.
9. Laporan Transaksi.
10. Pengaturan Profil.
11. Backup Database.
12. Logout.

## 5.17 Laporan Transaksi

Admin dapat melihat laporan transaksi melalui dashboard.

Fitur:
- Filter tanggal mulai.
- Filter tanggal akhir.
- Total transaksi.
- Total pendapatan.
- Detail transaksi.
- Status pembayaran.
- Status booking.
- Status pesanan.

Admin dapat mengekspor laporan dalam format:
- PDF.
- Excel.

Library yang direkomendasikan:
- DomPDF untuk PDF.
- PhpSpreadsheet untuk Excel.

Pendapatan laporan dihitung berdasarkan transaksi yang sudah dibayar.

## 5.18 Pengaturan Informasi Cafe

Admin dapat mengelola informasi cafe melalui dashboard.

Data:
- Nama cafe.
- Logo.
- Deskripsi.
- Alamat.
- Nomor telepon.
- Banner homepage.

Perubahan informasi akan langsung ditampilkan di halaman utama.

Jam operasional tidak dapat diubah melalui dashboard.

## 5.19 Backup Database

Admin dapat melakukan backup database melalui dashboard.

Fitur:
- Tombol Backup Database.
- Sistem menghasilkan file SQL.
- Admin dapat mengunduh file `.sql`.
- Admin menyimpan file backup secara manual.

Tidak termasuk restore database maupun backup otomatis terjadwal.

---

# 6. User Interface dan Navigasi

## 6.1 Desain Visual

Konsep desain: **Modern Minimalist**.

Karakteristik:
- Tampilan bersih.
- Layout sederhana.
- Navigasi mudah dipahami.
- Warna dominan putih dan cream.
- Aksen cokelat.
- Tipografi modern dan mudah dibaca.
- Komponen konsisten.
- Responsive untuk desktop, tablet, dan smartphone.

Framework UI menggunakan Bootstrap 5.

## 6.2 Navigasi Pelanggan

Menu navigasi pelanggan:

1. Home.
2. Menu Makanan.
3. Menu Minuman.
4. Keranjang.
5. Booking Meja.
6. Riwayat Transaksi.
7. Notifikasi.
8. Profil.
9. Logout.

## 6.3 Navigasi Admin

Menu navigasi admin:

1. Dashboard.
2. Kelola Kategori Menu.
3. Kelola Menu.
4. Kelola Meja.
5. Kelola Booking.
6. Kelola Pesanan.
7. Kelola Pembayaran.
8. Kelola Pelanggan.
9. Laporan Transaksi.
10. Pengaturan Profil.
11. Backup Database.
12. Logout.

---

# 7. Non-Functional Requirements

## 7.1 Performance

- Halaman harus dapat dimuat dengan cepat pada lingkungan localhost.
- Query database harus efisien.
- Gunakan pagination untuk tabel data yang panjang.
- Optimalkan ukuran gambar menu.
- Hindari query berulang yang tidak diperlukan.

## 7.2 Security

- Password menggunakan `password_hash()` dan `password_verify()`.
- Database menggunakan PDO.
- Semua query input pengguna menggunakan prepared statements.
- Validasi data dilakukan di sisi server.
- Output HTML harus di-escape untuk mencegah XSS.
- Gunakan perlindungan CSRF pada form yang mengubah data.
- Session ID diperbarui setelah login.
- Terapkan pemeriksaan role untuk setiap halaman admin.
- Validasi ekstensi, MIME type, ukuran, dan nama file upload.
- Jangan menyimpan password dalam bentuk teks biasa.
- File konfigurasi database tidak boleh dapat diakses langsung melalui browser.

Tidak ada pembatasan percobaan login khusus pada versi awal.

## 7.3 Reliability

- Booking dan pesanan disimpan secara atomik.
- Gunakan database transaction.
- Gunakan foreign key.
- Cegah duplikasi kode transaksi.
- Cegah double booking.
- Simpan riwayat pembatalan dan perubahan pesanan.

## 7.4 Compatibility

Aplikasi harus berjalan pada:
- XAMPP.
- Laragon.
- Browser modern.
- PHP 8.2 atau versi lebih baru yang masih didukung.
- MySQL atau MariaDB dengan dukungan transaksi InnoDB.

## 7.5 Timezone

Gunakan timezone:

`Asia/Jakarta`

Seluruh validasi waktu booking harus menggunakan waktu WIB, bukan bergantung pada timezone default server.

---

# 8. Business Rules

| ID | Aturan |
|---|---|
| BR-01 | Pelanggan harus login untuk melakukan transaksi. |
| BR-02 | Satu transaksi wajib berisi booking meja dan pesanan menu. |
| BR-03 | Satu booking menggunakan satu meja. |
| BR-04 | Durasi booking adalah 2 jam. |
| BR-05 | Booking hanya untuk hari yang sama. |
| BR-06 | Jam operasional 09.00–22.00 WIB. |
| BR-07 | Slot booking dimulai setiap jam. |
| BR-08 | Meja harus aktif dan memiliki kapasitas cukup. |
| BR-09 | Meja yang memiliki booking overlap tidak dapat dipilih. |
| BR-10 | Booking dikonfirmasi otomatis setelah transaksi berhasil. |
| BR-11 | Pembayaran dilakukan di kasir. |
| BR-12 | Status pembayaran awal adalah Belum Dibayar. |
| BR-13 | Pelanggan dapat membatalkan transaksi minimal 2 jam sebelum booking. |
| BR-14 | Admin dapat membatalkan transaksi dengan alasan wajib. |
| BR-15 | Pembatalan booking juga membatalkan pesanan terkait. |
| BR-16 | Menu tidak tersedia tidak dapat ditambahkan ke keranjang. |
| BR-17 | Keranjang tersimpan tanpa batas waktu. |
| BR-18 | Pelanggan tidak dapat mengubah pesanan setelah konfirmasi. |
| BR-19 | Admin dapat mengubah detail pesanan. |
| BR-20 | Setiap transaksi memiliki satu kode unik. |
| BR-21 | Pendapatan dihitung dari transaksi yang sudah dibayar. |
| BR-22 | Jam operasional tidak dapat diubah melalui dashboard. |
| BR-23 | Semua antarmuka menggunakan Bahasa Indonesia. |

---

# 9. Rekomendasi Database

Database: `cozycafe`

Tabel utama yang direkomendasikan:

## 9.1 users

Menyimpan akun pelanggan dan admin.

Kolom:
- id.
- name.
- email.
- phone.
- password.
- role.
- created_at.
- updated_at.

Role menggunakan nilai `customer` atau `admin`.

Admin default dibuat langsung melalui database dan tidak memiliki fitur registrasi admin.

## 9.2 categories

Menyimpan kategori menu.

Kolom:
- id.
- name.
- description.
- created_at.
- updated_at.

## 9.3 menus

Menyimpan data menu.

Kolom:
- id.
- category_id.
- name.
- description.
- price.
- image.
- availability_status.
- created_at.
- updated_at.

## 9.4 cafe_tables

Menyimpan data meja.

Kolom:
- id.
- table_number.
- area.
- capacity.
- status.
- created_at.
- updated_at.

## 9.5 carts

Menyimpan keranjang pelanggan.

Kolom:
- id.
- user_id.
- created_at.
- updated_at.

## 9.6 cart_items

Menyimpan detail item keranjang.

Kolom:
- id.
- cart_id.
- menu_id.
- quantity.
- note.
- created_at.
- updated_at.

## 9.7 transactions

Menyimpan data utama transaksi.

Kolom:
- id.
- transaction_code.
- user_id.
- total_amount.
- payment_status.
- created_at.
- updated_at.

Kode transaksi harus unik.

## 9.8 bookings

Menyimpan informasi booking meja.

Kolom:
- id.
- transaction_id.
- table_id.
- booking_date.
- start_time.
- end_time.
- guest_count.
- status.
- created_at.
- updated_at.

Tambahkan indeks yang mendukung pemeriksaan jadwal booking.

## 9.9 orders

Menyimpan informasi pesanan.

Kolom:
- id.
- transaction_id.
- order_status.
- subtotal.
- created_at.
- updated_at.

## 9.10 order_items

Menyimpan detail menu dalam pesanan.

Kolom:
- id.
- order_id.
- menu_id.
- menu_name_snapshot.
- price_snapshot.
- quantity.
- note.
- subtotal.
- created_at.
- updated_at.

Snapshot nama dan harga digunakan agar riwayat transaksi tetap akurat meskipun data menu berubah.

## 9.11 notifications

Menyimpan notifikasi pelanggan.

Kolom:
- id.
- user_id.
- transaction_id.
- title.
- message.
- is_read.
- created_at.

## 9.12 cancellation_histories

Menyimpan riwayat pembatalan.

Kolom:
- id.
- transaction_id.
- canceled_by.
- reason.
- created_at.

## 9.13 order_change_histories

Menyimpan riwayat perubahan pesanan oleh admin.

Kolom:
- id.
- order_id.
- admin_id.
- change_description.
- old_total.
- new_total.
- created_at.

## 9.14 cafe_settings

Menyimpan informasi cafe.

Kolom:
- id.
- cafe_name.
- logo.
- description.
- address.
- phone.
- banner.
- updated_at.

Jam operasional ditetapkan di konfigurasi aplikasi.

---

# 10. Arsitektur dan Struktur Project

Gunakan arsitektur modular PHP Native dengan pemisahan controller, model, view, dan konfigurasi.

Struktur direktori yang direkomendasikan:

```text
CozyCafe/
│
├── config/
│   └── database.php
│
├── controllers/
│   ├── AuthController.php
│   ├── MenuController.php
│   ├── BookingController.php
│   ├── OrderController.php
│   ├── CartController.php
│   ├── PaymentController.php
│   ├── NotificationController.php
│   └── AdminController.php
│
├── models/
│   ├── User.php
│   ├── Menu.php
│   ├── Category.php
│   ├── CafeTable.php
│   ├── Booking.php
│   ├── Order.php
│   ├── Transaction.php
│   ├── Cart.php
│   └── Notification.php
│
├── views/
│   ├── layouts/
│   ├── auth/
│   ├── customer/
│   └── admin/
│
├── admin/
│   └── index.php
│
├── customer/
│   └── index.php
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
├── database/
│   └── cozycafe.sql
│
├── uploads/
│   └── menu/
│
├── vendor/
│
└── index.php
```

Library yang dapat digunakan:
- Bootstrap 5.
- DomPDF.
- PhpSpreadsheet.

Gunakan Composer untuk mengelola library PHP eksternal.

---

# 11. Acceptance Criteria

Produk dianggap memenuhi kebutuhan MVP apabila:

## Autentikasi
- Pelanggan dapat registrasi dan login.
- Admin dapat login menggunakan akun yang telah disediakan.
- Password tersimpan secara aman.
- Role pelanggan tidak dapat membuka dashboard admin.

## Menu
- Admin dapat menambah, mengubah, dan menghapus menu.
- Admin dapat mengelola kategori.
- Pelanggan dapat melihat seluruh menu.
- Pelanggan dapat mencari dan memfilter menu.
- Menu tidak tersedia tidak dapat dipesan.

## Keranjang
- Pelanggan dapat menambah dan menghapus item.
- Pelanggan dapat mengubah kuantitas.
- Catatan item dapat disimpan.
- Keranjang tetap tersedia setelah logout dan login kembali.

## Booking
- Pelanggan dapat memilih jumlah tamu.
- Sistem menampilkan meja dengan kapasitas mencukupi.
- Slot yang sudah lewat tidak ditampilkan.
- Sistem menolak booking di luar jam operasional.
- Sistem mencegah booking yang bertabrakan.
- Booking berhasil dikonfirmasi otomatis.

## Checkout
- Pelanggan dapat melihat ringkasan sebelum checkout.
- Booking dan pesanan tersimpan dengan satu kode transaksi.
- Kegagalan penyimpanan salah satu bagian membatalkan seluruh transaksi.
- Keranjang dikosongkan setelah checkout berhasil.

## Pesanan dan Pembayaran
- Admin dapat memperbarui status pesanan.
- Admin dapat mengubah detail pesanan.
- Total transaksi dihitung ulang setelah perubahan.
- Admin dapat mengubah status pembayaran.
- Status pembayaran terpisah dari status pesanan.

## Pembatalan
- Pelanggan hanya dapat membatalkan transaksi sesuai batas waktu.
- Admin wajib mengisi alasan pembatalan.
- Meja yang dibatalkan kembali tersedia.
- Riwayat pembatalan tersimpan.

## Riwayat dan Notifikasi
- Pelanggan dapat melihat seluruh transaksi dalam satu halaman.
- Detail booking dan pesanan dapat dibuka.
- Notifikasi muncul sesuai perubahan status.
- Notifikasi dapat ditandai sudah dibaca.

## Dashboard dan Laporan
- Dashboard menampilkan statistik.
- Grafik transaksi harian dan bulanan tersedia.
- Admin dapat mengelola seluruh data operasional.
- Laporan dapat diekspor ke PDF dan Excel.
- Backup database menghasilkan file SQL yang dapat diunduh.

---

# 12. Risiko dan Mitigasi

| Risiko | Mitigasi |
|---|---|
| Double booking | Pemeriksaan overlap dan transaksi database atomik. |
| Dua pelanggan checkout bersamaan | Recheck ketersediaan meja sebelum commit. |
| Keranjang berisi menu tidak tersedia | Validasi ulang menu ketika checkout. |
| Perubahan harga menu | Simpan snapshot harga pada order_items. |
| Data booking tidak konsisten | Gunakan foreign key dan database transaction. |
| Akses tidak sah ke dashboard | Validasi session dan role pada setiap halaman admin. |
| Upload file berbahaya | Validasi MIME type, ukuran, dan nama file. |
| Kehilangan data lokal | Backup SQL manual secara berkala. |
| Perubahan pesanan tidak terlacak | Simpan order_change_histories. |

---

# 13. Prioritas Pengembangan

## Prioritas 1 — Core Application

- Database dan konfigurasi.
- Registrasi dan login.
- Manajemen role.
- Homepage.
- Manajemen menu dan kategori.
- Manajemen meja.
- Keranjang.
- Booking meja.
- Checkout terintegrasi.
- Pemeriksaan konflik jadwal.
- Riwayat transaksi.

## Prioritas 2 — Operasional Cafe

- Dashboard admin.
- Pengelolaan pesanan.
- Pengelolaan pembayaran.
- Pembatalan transaksi.
- Notifikasi.
- Pengelolaan pelanggan.
- Pengaturan informasi cafe.

## Prioritas 3 — Pelaporan dan Administrasi

- Laporan transaksi.
- Export PDF.
- Export Excel.
- Grafik transaksi.
- Backup database.
- Pengujian keamanan dan validasi.

---

# 14. Asumsi dan Keputusan Implementasi

1. CozyCafe merupakan aplikasi lokal untuk satu cafe.
2. Sistem menggunakan satu akun admin default yang dibuat langsung di database.
3. Semua pelanggan harus memiliki akun.
4. Pemesanan hanya untuk dine-in.
5. Booking dan pesanan selalu menjadi satu transaksi.
6. Satu transaksi hanya menggunakan satu meja.
7. Booking berlangsung selama dua jam.
8. Jam operasional adalah 09.00–22.00 WIB setiap hari.
9. Booking hanya berlaku untuk hari yang sama.
10. Pembayaran dilakukan langsung di kasir.
11. Keranjang disimpan di database tanpa batas waktu.
12. Semua transaksi memiliki kode unik.
13. Pelanggan tidak dapat mengedit transaksi setelah checkout.
14. Admin dapat mengubah pesanan dan sistem menyimpan riwayat perubahan.
15. Pendapatan dihitung dari transaksi yang sudah dibayar.
16. Jam operasional tetap dan tidak dapat diubah melalui dashboard.
17. Bahasa aplikasi hanya Bahasa Indonesia.
18. Tidak ada integrasi eksternal untuk notifikasi.
19. Aplikasi berjalan secara lokal menggunakan XAMPP atau Laragon.
20. Backup database dilakukan secara manual melalui dashboard.

---

# 15. Definition of Done

Pengembangan CozyCafe dinyatakan selesai apabila:

- Seluruh fitur prioritas MVP telah berfungsi.
- Pelanggan dapat melakukan booking dan pemesanan dalam satu transaksi.
- Sistem mampu mencegah double booking.
- Admin dapat mengelola seluruh kebutuhan operasional.
- Pembayaran dapat dicatat oleh admin.
- Riwayat transaksi tersimpan dan dapat diakses.
- Notifikasi berjalan sesuai perubahan status.
- Laporan transaksi dapat diunduh.
- Backup database dapat dibuat.
- Tidak terdapat kesalahan kritis dalam alur transaksi.
- Aplikasi dapat dijalankan melalui localhost menggunakan XAMPP atau Laragon.
- Sistem telah melewati pengujian fungsional dan keamanan dasar.

---

# PENUTUP

CozyCafe dirancang sebagai aplikasi web sederhana, terintegrasi, dan mudah digunakan untuk mendukung operasional cafe.

Fokus utama pengembangan adalah menggabungkan proses booking meja dan pemesanan makanan/minuman dalam satu transaksi, mengurangi kesalahan pencatatan manual, mencegah bentrok jadwal, serta memberikan kemudahan bagi admin dalam mengelola operasional cafe.

Dokumen ini menjadi acuan utama untuk tahap selanjutnya, yaitu perancangan database, pembuatan UI/UX, implementasi PHP Native, dan pengujian aplikasi.