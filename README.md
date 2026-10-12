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

SQL siap impor tersedia di [`database/db_go.sql`](database/db_go.sql). File ini berasal dari dump proyek yang telah dibersihkan: struktur tabel, indeks, dan relasi dipertahankan; akun, hash password, data pribadi, serta data operasional asli tidak disertakan.

1. Buat database kosong bernama `db_go` melalui phpMyAdmin.
2. Pilih database tersebut, buka tab **Import**, lalu pilih `database/db_go.sql`. SQL ditujukan untuk database kosong dan tidak menghapus tabel yang sudah ada.
3. Salin template konfigurasi sesuai tabel di bawah, lalu isi host, nama database `db_go`, username, dan password MySQL lokal Anda pada `db.php`.
4. Dari folder proyek, jalankan `php database/create-admin.php` untuk membuat akun lokal dengan password pilihan Anda. Tidak ada akun atau password bawaan di repository.

Alternatif impor melalui terminal MySQL, setelah database kosong dibuat:

```sql
USE db_go;
SOURCE database/db_go.sql;
```

Jalankan perintah `SOURCE` dari folder proyek. Pada XAMPP Windows, PHP CLI tersedia di `C:\xampp\php\php.exe`.


## Konfigurasi privat

Salin template berikut ke nama file lokalnya, lalu isi nilai untuk lingkungan Anda:

| Template | File lokal |
| --- | --- |
| `db.example.php` | `db.php` |

File konfigurasi lokal diabaikan oleh Git. Jangan memasukkan password, API key, atau data akun ke source code.

## Catatan repository

Dependensi terpasang, cache, file `.env`, data database operasional, dan unggahan pengguna tidak disertakan. Nama merek dan aset pihak ketiga tetap mengikuti hak pemiliknya. Tidak ada lisensi penggunaan ulang yang diberikan melalui repository ini.
