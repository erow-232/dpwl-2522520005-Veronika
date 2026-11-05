# Pengembangan UTS DPWL: Manajemen Data Penyewa

## Fitur yang ditambahkan
- Menu navigasi **Manajemen Penyewa** pada sidebar admin.
- Halaman tabel dan formulir tambah/ubah data penyewa.
- CRUD data `t_penyewa` melalui pola MVC (Controller, Model, View).
- Query menggunakan MySQLi prepared statement untuk operasi berbasis input.
- Password baru disimpan dengan `password_hash()`; tidak pernah ditampilkan kembali.
- Validasi NIK 16 digit, nama, nomor HP, alamat, dan password di sisi server.
- Upload foto KTP dan SIM A: JPEG/PNG/WEBP valid, maksimal 3 MB per berkas, nama file acak, tidak menggunakan nama file dari pengguna.
- Proteksi halaman admin, token CSRF untuk POST, penghapusan melalui POST, dan pola Post-Redirect-Get.

## Instalasi
1. Buat salinan/cadangan folder proyek asli terlebih dahulu.
2. Ekstrak folder proyek ke direktori web server, misalnya `C:\laragon\www\dpwl-2522500040`.
3. Import database bahan dosen (`localhost.sql`) melalui phpMyAdmin.
4. Pastikan nama database di `application/config/database.php` sesuai database hasil import (default proyek: `db_dpwl_dika`).
5. Pastikan tabel `t_penyewa` memiliki kolom: `user_plg`, `sandi`, `nik`, `nm_lngkp`, `no_hp`, `almt_lngkp`, `ft_ktp`, `ft_sim_a`. Kolom foto sebaiknya bertipe VARCHAR yang dapat menyimpan nama file.
6. Pastikan folder `uploads/penyewa` dapat ditulis oleh PHP. Folder ini sudah disertakan.
7. Login sebagai admin, lalu buka menu **Manajemen Penyewa**.

## Pengujian yang perlu dilakukan
- Login admin dan akses menu; akses tanpa login harus diarahkan ke halaman login.
- Tambah penyewa dengan data valid; coba NIK kurang/lebih dari 16 digit dan username duplikat.
- Pastikan password tersimpan sebagai hash (awalan umum `$2y$` atau format hash PHP lain), bukan teks biasa.
- Ubah data tanpa mengganti password; lalu uji penggantian password.
- Upload KTP/SIM A valid; coba file bukan gambar dan file di atas 3 MB.
- Hapus data; jika masih direferensikan transaksi dan foreign key mencegah penghapusan, aplikasi menampilkan pesan gagal yang aman.
- Uji fitur lama: login admin/penyewa, manajemen kendaraan, katalog, keranjang, dan checkout.

## Catatan database
ZIP bahan yang diterima tidak menyertakan `localhost.sql`. Karena itu struktur aktual database belum dapat diverifikasi langsung. Implementasi ini mengikuti kolom `t_penyewa` yang sudah dipakai oleh model profil/registrasi bawaan. Cocokkan dengan SQL dosen sebelum demonstrasi dan ekspor ulang database final setelah pengujian.

## Git
Jalankan dari folder proyek:
```bash
git add .
git commit -m "Tambah CRUD manajemen data penyewa"
git push
```
Setelah selesai, ekspor database final melalui phpMyAdmin (Export > SQL) dan sertakan berkas `.sql` pada repositori sesuai instruksi dosen.
