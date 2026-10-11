# Grand Ontama Dashboard

Dashboard administrasi Grand Ontama untuk pengelolaan properti, pelanggan, agen, dan pemesanan.

**Status:** Live · [Buka website](https://grandontama.com/dashboard/login)

## Fitur

- Status ketersediaan dan penjualan properti
- Data pelanggan, catatan, serta dokumen pendukung
- Pengelolaan agen, pemesanan, dan hak akses admin

## Teknologi

PHP, MySQL, HTML, CSS, JavaScript.

## Menjalankan secara lokal

Gunakan server Apache/PHP, misalnya XAMPP. Tempatkan salinan proyek pada document root dan buka `login.php` melalui localhost. Aturan `.htaccess`, jika tersedia, membutuhkan Apache dengan konfigurasi yang sesuai.

## Database

Aplikasi membutuhkan MySQL. Dump database dan data operasional tidak disertakan; siapkan skema database yang sesuai sebelum menjalankan fitur dinamis. Repository ini belum menyediakan instalasi database otomatis.

## Konfigurasi privat

Salin template berikut ke nama file lokalnya, lalu isi nilai untuk lingkungan Anda:

| Template | File lokal |
| --- | --- |
| `db.example.php` | `db.php` |

File konfigurasi lokal diabaikan oleh Git. Jangan memasukkan password, API key, atau data akun ke source code.

## Catatan repository

Dependensi terpasang, cache, file `.env`, database, dan unggahan pengguna tidak disertakan. Nama merek dan aset pihak ketiga tetap mengikuti hak pemiliknya. Tidak ada lisensi penggunaan ulang yang diberikan melalui repository ini.
