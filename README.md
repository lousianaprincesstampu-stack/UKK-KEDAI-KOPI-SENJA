# Kedai Kopi Senja

## 1. Ringkasan

Kedai Kopi Senja adalah sistem informasi kedai kopi untuk mengelola menu, bahan baku, resep, stok, pesanan, dan laporan penjualan.

Sistem memiliki tiga pengguna:
- Manajer: mengelola data kafe melalui web.
- Kasir: melayani pesanan melalui web.
- Pelanggan: memesan dan melihat status pesanan melalui aplikasi Android.

## 2. Teknologi

Web dan Backend
- PHP 8.5.9
- Laravel 13.42.0
- MySQL
- Bootstrap 5.3.3
- Bootstrap Icons

Aplikasi Android
- Dart
- Flutter
- HTTP
- SharedPreferences

## 3. Cara Menjalankan Server

Persiapan menggunakan Windows dan Laragon.

1. Clone repository:
   `git clone https://github.com/lousianaprincesstampu-stack/UKK-KEDAI-KOPI-SENJA.git`
2. Masuk ke folder proyek.
3. Jalankan `composer install`.
4. Salin `.env.example` menjadi `.env`.
5. Buat database MySQL bernama `db_cafe`.
6. Sesuaikan konfigurasi database pada `.env`.
7. Jalankan `php artisan key:generate`.
8. Jalankan migrasi dan seeder:
   `php artisan migrate --seed`

Untuk data stok demo:

`php artisan db:seed --class=DemoStockSeeder`

Konfigurasi layanan harga bahan pada `.env`:

`LAYANAN_URL=https://ukksmkn1katapang.nextit.co.id/kafe`

`LAYANAN_API_KEY=lVWMc6V2SXhkeXjRMioax61WUuRhy2VW`

API key diperoleh dari penyelenggara UKK

Import bahan, menu, dan resep dilakukan melalui halaman Import CSV setelah login sebagai Manajer.

Jalankan server:

`php artisan serve`

Alamat server lokal: `http://127.0.0.1:8000`

## 4. Cara Memasang APK

File APK tersedia di `apk/app-debug.apk`.

Langkah pemasangan:
1. Unduh file APK dari repository.
2. Buka file APK melalui perangkat Android.
3. Izinkan pemasangan jika diminta.
4. Selesaikan instalasi.

Alamat server Flutter diatur pada `pelanggan_kedai_kopi_senja/lib/services/api_service.dart`.

Android Emulator menggunakan `http://10.0.2.2:8000`. Untuk HP fisik, gunakan IP lokal komputer yang menjalankan Laravel.

## 5. Akun Uji

Manajer
- Username: `manager`
- Password: `manager321`

Kasir
- Username: `kasir`
- Password: `kasir321`

Pelanggan
Pelanggan dapat membuat akun sendiri melalui aplikasi Android menggunakan nama, nomor HP, dan password.

## 6. Letak Berkas

- Migration: `database/migrations/`
- Seeder: `database/seeders/`
- Controller: `app/Http/Controllers/`
- Tampilan web: `resources/views/`
- API: `routes/api.php`
- Import CSV: `app/Services/ImportCsvService.php`
- Controller import: `app/Http/Controllers/Manager/ImportCsvController.php`
- Integrasi harga bahan: `app/Services/LayananUkkService.php`
- Konfigurasi layanan: `config/services.php`
- Source Flutter: `pelanggan_kedai_kopi_senja/`
- APK: `apk/app-debug.apk`

Perintah seeder stok demo:

`php artisan db:seed --class=DemoStockSeeder`

## 7. Keputusan Anda

1. Nomor HP pelanggan harus diawali 08, terdiri dari 10–13 digit, hanya angka, dan unik.
2. Password pelanggan minimal 8 karakter.
3. Import CSV dipisah menjadi Import Bahan, Import Menu, dan Import Resep.
4. Satuan bahan dinormalisasi ke gram, mililiter, atau pcs.
5. Bahan yang tidak ditemukan saat import resep tidak dibuat otomatis.
6. Latte dan Mocha tidak tersedia jika bahan resepnya belum tersedia.
7. Metode pembayaran pelanggan adalah Tunai atau QRIS.
8. Manajer dan Kasir memiliki hak akses berbeda sesuai tugas masing-masing.

## 8. Lembar Self-Check

Keterangan: Ya berarti fitur sudah pernah diuji sesuai alur yang disebutkan. Belum dipastikan berarti seluruh ketentuan dalam butir tersebut belum terkonfirmasi diuji.

1. Pengelolaan bahan, menu, dan resep — Ya
   Pengujian tambah, ubah, hapus, dan nonaktifkan menu serta pengelolaan bahan dan resep.

2. Stok masuk dan batas minimum bahan — Ya
   Pengujian stok masuk dan tampilan daftar stok bahan menipis.

3. Pemesanan dan pengurangan stok bahan — Ya
   Pengujian pemesanan beberapa menu dan pengurangan stok bahan.

4. Penolakan pesanan saat stok tidak mencukupi — Ya
   Pengujian penolakan pesanan ketika stok bahan tidak mencukupi, termasuk kebutuhan gabungan beberapa menu.

5. Status ketersediaan menu berdasarkan stok — Ya
   Pengujian perubahan status menu menjadi tidak tersedia ketika bahan resep tidak mencukupi.

6. Perubahan status pesanan dan pembatalan — Ya
   Pengujian perubahan status pesanan dan pembatalan ketika pesanan masih berstatus Baru. Tombol pembatalan tidak tersedia setelah pesanan diproses.

7. Struk, biaya layanan, pajak, dan kestabilan struk lama — Ya
   Pengujian subtotal, biaya layanan 5%, pajak 10%, total pembayaran, dan struk pesanan lama setelah harga menu berubah.

8. Perhitungan HPP dan margin menu — Ya
   Pengujian perhitungan HPP dan margin berdasarkan resep.

9. Laporan dan pembatasan hak akses — Ya
   Pengujian laporan penjualan, stok menipis, margin, serta pembatasan akses Manajer dan Kasir.

10. Import CSV bahan, menu, dan resep — Ya
    Pengujian import data bahan, menu, dan resep melalui halaman Import CSV.

11. Integrasi API harga bahan — Ya
    Pengujian pengambilan data harga bahan dari layanan API.

12. Registrasi dan pengelolaan akun pelanggan — Tidak
    Pengujian registrasi, login, perubahan password, reset password oleh Kasir, dan logout.

13. Pemesanan pelanggan melalui aplikasi Android — Ya
    Pengujian pemilihan menu, jumlah pesanan, meja, metode pembayaran, dan checkout.

14. Riwayat, status, dan struk pesanan pelanggan — Ya
    Pengujian riwayat pesanan, pelacakan status, dan tampilan struk pelanggan.

## 9. Belum Selesai dan Keterbatasan

- Tidak ada fitur-fitur opsional
- APK yang tersedia masih berupa debug APK, bukan release APK.
- Alamat server harus disesuaikan jika aplikasi dijalankan pada HP fisik.
- API key harus dikonfigurasi melalui `.env`.
- Beberapa subpengujian pada lembar self-check masih perlu dipastikan.

## 10. Referensi dan Bantuan

Referensi:
- Laravel: https://laravel.com/docs
- Flutter: https://docs.flutter.dev/
- Dart: https://dart.dev/
- Bootstrap: https://getbootstrap.com/docs/
- Android: https://developer.android.com/

ChatGPT digunakan untuk membantu memahami kode, menjelaskan konsep, mencari kesalahan, memberikan saran perbaikan, dan menyusun dokumentasi. Kode disesuaikan dan diperiksa kembali selama pengembangan.