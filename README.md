# Sistem Informasi Pendaftaran Servis Laptop (Web & Mobile)
**Politeknik Negeri Banjarmasin** | **Versi Dokumen:** 1.0 | **Status:** Draft (Juli 2026)  
**Disusun oleh:** Dzaki Ahmad Andreaz (NIM: C030324115)  

---

## Latar Belakang & Masalah
Masalah utama yang sering dihadapi pada tempat servis laptop adalah kurangnya transparansi komunikasi antara pihak toko dan pelanggan. Pelanggan biasanya harus menghubungi teknisi satu per satu via WhatsApp atau telepon hanya untuk menanyakan status pengerjaan atau biaya perbaikan. Di sisi lain, catatan keluhan, nomor seri, dan nota yang dikelola teknisi masih dicatat secara manual atau terpisah-pisah sehingga rawan terselip atau hilang.

Untuk mengatasi hal tersebut, dirancang sebuah sistem terintegrasi yang berjalan di dua platform sekaligus:
1. **Sisi Web (Laravel):** Digunakan oleh Admin atau Teknisi untuk memantau semua antrean, memasukkan rincian biaya, serta mencatat detail perbaikan.
2. **Sisi Mobile (Flutter):** Digunakan oleh Pelanggan (*Customer*) untuk mendaftarkan unit laptopnya secara mandiri serta memantau status servis secara *real-time* dari rumah.

---

## Spesifikasi Teknis (Tech Stack)

| Komponen | Teknologi yang Digunakan |
| :--- | :--- |
| **Backend & Web Admin** | Framework Laravel (PHP), Blade Template, Tailwind CSS |
| **Database** | MySQL |
| **Authentication API** | Laravel Sanctum *(Token-based authentication)* |
| **Mobile Application** | Flutter (Dart) |
| **State Management** | Riverpod |
| **HTTP/API Client** | Dio |
| **Otorisasi & Role** | `spatie/laravel-permission` *(RBAC)* |
| **Penyimpanan File** | Laravel Local Storage System (direktori `storage/app/public`) |

---

## Peran Pengguna & Hak Akses (User Roles)

### 1. Customer (Pelanggan) - *Mobile App*
* Mendaftarkan akun baru, login, dan mengisi formulir pendaftaran servis laptop.
* Memantau status perbaikan, melihat catatan teknisi, serta melihat rincian biaya perbaikan.
* Melihat akumulasi riwayat pengeluaran biaya servis pribadi melalui halaman dashboard.

### 2. Admin / Teknisi - *Web App*
* Login ke dasbor web admin.
* Melihat seluruh daftar antrean unit laptop yang masuk.
* Memperbarui status pengerjaan perangkat, menginput nominal biaya servis, dan menulis catatan perbaikan.
* Melihat seluruh data akun pengguna yang terdaftar di sistem.

---

## Alur Bisnis Utama (Business Process)

### 1. Alur Pendaftaran & Login Pelanggan
* Pelanggan membuka aplikasi Flutter lalu mengisi nama lengkap, alamat email, dan password pada menu daftar akun.
* Setelah terverifikasi unik oleh database, pelanggan login menggunakan akun tersebut.
* Jika login cocok, sistem akan memberikan token keamanan (Laravel Sanctum) agar pengguna tidak perlu login ulang di kemudian hari.

### 2. Alur Mengajukan Servis dan Memantau Status (Sisi Pelanggan)
* Pada dashboard aplikasi mobile, pelanggan mengisi formulir pendaftaran laptop baru dengan memasukkan nama laptop, nomor seri $(S/N)$, dan keluhan kerusakan.
* Setelah dikirim, sistem secara otomatis memberikan status awal berupa **Pending** (menunggu antrean) ke dalam riwayat servis pelanggan.
* Pelanggan dapat membuka menu "Lihat Detail" untuk memantau perubahan status dan rincian biaya yang harus disiapkan setelah laptop diperiksa oleh teknisi.

### 3. Alur Pengelolaan Data (Sisi Admin/Teknisi)
* Admin atau teknisi login ke website backend toko.
* Admin mengubah status antrean menjadi **Proses** ketika laptop mulai dibongkar atau diperbaiki.
* Begitu perbaikan selesai, admin menginput nominal biaya perbaikan (`total_cost`), menulis komponen yang diganti pada kolom catatan (`technician_notes`), lalu mengubah status menjadi **Selesai**.
* Ketika pelanggan mengambil laptopnya di toko, admin akan memperbarui status akhir data menjadi **Diambil**.

---

## Struktur Data Utama (Database Entities)

### 1. Tabel Utama
* **`users`**: Menggandeng atribut seperti `id`, `name`, `email`, `password`, dan `role`. Berelasi satu pelanggan bisa memiliki banyak data servis (*HasMany*).
* **`laptop_services`**: Mencatat data transaksi servis perangkat seperti `id`, `user_id`, `device_name`, `serial_number`, `phone_number`, `complaints`, `status`, `total_cost`, dan `technician_notes`. Relasi dihubungkan menggunakan foreign key `user_id` dengan metode *cascade delete* demi kebersihan database.

### 2. Tabel Sistem (Bawaan Framework Laravel)
* **`migrations`**: Mencatat riwayat skema pembuatan struktur database.
* **`personal_access_tokens`**: Menyimpan data token login rahasia untuk menyambungkan aplikasi Flutter ke API Laravel.
* **`sessions` & `password_reset_tokens`**: Menyimpan data sesi aktif admin di browser serta token sementara untuk fitur reset password.
* **`cache` & `cache_locks`**: Menyimpan memori sementara agar loading performa website admin lebih cepat.
* **`jobs`, `job_batches`, & `failed_jobs`**: Mengatur antrean tugas di balik layar (*background processing*) pada sistem web Laravel.

---

## Batasan Sistem & Fase Pengembangan 1
* **Out of Scope:** Sistem belum mendukung integrasi pembayaran digital (*payment gateway* seperti Midtrans), manajemen stok suku cadang toko (*inventory*), serta fitur *live chat* langsung antara teknisi and pelanggan di dalam aplikasi. Pembayaran dilakukan secara tunai di toko saat pengambilan barang.
* **Konektivitas:** Aplikasi mobile memerlukan koneksi internet yang stabil agar data dari HP bisa terkirim langsung ke alamat IP server lokal backend Laravel.

---
*Proyek ini dikembangkan sebagai syarat pemenuhan nilai UAS & Project-Based Learning (PBL) Semester 4, Jurusan Teknik Informatika, Politeknik Negeri Banjarmasin.*