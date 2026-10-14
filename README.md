# TugasWeb-Pertemuan8-CRUD

Aplikasi CRUD Inventaris Barang untuk Tugas Rutin 8 Pemrograman Web. Dibuat dengan PHP Native, MySQL, PDO Singleton, prepared statements, foreign key, dan output escaping.

## Fitur
- Daftar produk dengan JOIN ke tabel kategori dan supplier
- Tambah produk dengan dropdown kategori dan supplier
- Edit produk dengan form yang sudah terisi
- Hapus produk dengan konfirmasi dan transaksi database
- Pencarian berdasarkan nama produk, kategori, atau supplier
- Validasi input, flash message, dan `htmlspecialchars()`
- PDO memakai `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, dan `EMULATE_PREPARES = false`

## Persyaratan
- Laragon atau XAMPP
- PHP 8+ dengan ekstensi PDO MySQL
- MySQL/MariaDB
- Browser

## Cara menjalankan di Laragon
1. Ekstrak folder `TugasWeb-Pertemuan8-CRUD` ke `C:\\laragon\\www\\`.
2. Jalankan **Apache** dan **MySQL** dari Laragon.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih tab **Import**, pilih file `schema.sql`, lalu klik **Import/Go**. File ini membuat database `inventaris_db`, tiga tabel, foreign key, dan data contoh.
5. Jika akun MySQL Anda bukan `root` tanpa password, sesuaikan `$username` dan `$password` di `config/database.php`.
6. Buka `http://localhost/TugasWeb-Pertemuan8-CRUD/`.

## Struktur folder
```text
TugasWeb-Pertemuan8-CRUD/
├── assets/
│   └── style.css
├── config/
│   └── database.php
├── schema.sql
├── index.php
├── create.php
├── edit.php
└── README.md
```

## Upload ke GitHub
1. Buat repository publik bernama **TugasWeb-Pertemuan8-CRUD**.
2. Upload semua isi folder ini, termasuk `schema.sql` dan `README.md`.
3. Tambahkan screenshot aplikasi di repository dan masukkan ke README jika sudah dijalankan.
4. Catatan: GitHub Pages tidak menjalankan PHP/MySQL. Repository ini untuk source code; agar aplikasi aktif online, gunakan hosting yang mendukung PHP dan MySQL.

## Catatan keamanan
Contoh kredensial di `config/database.php` ditujukan untuk lingkungan lokal. Jangan unggah password database produksi ke repository publik. Untuk hosting, gunakan environment variables atau konfigurasi rahasia dari panel hosting.
