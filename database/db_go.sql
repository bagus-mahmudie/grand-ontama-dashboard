-- Public import schema derived from db_go.sql supplied by the project owner.
-- Import into a NEW, empty database. No existing tables are dropped.
-- Original accounts, password hashes, personal records and operational data are omitted.

SET NAMES utf8mb4;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('superadmin','admin') DEFAULT 'admin',
  `status` enum('pending','approved') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `agen` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `foto_agen` varchar(255) DEFAULT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `tempat_tanggal_lahir` varchar(100) DEFAULT NULL,
  `jenis_kelamin` enum('Laki-Laki','Perempuan') DEFAULT NULL,
  `status_pernikahan` enum('Belum Menikah','Menikah','Cerai') DEFAULT NULL,
  `agama` varchar(50) DEFAULT NULL,
  `kewarganegaraan` varchar(50) DEFAULT NULL,
  `alamat_ktp` text DEFAULT NULL,
  `alamat_domisili` text DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tipe_agen` enum('Internal','Eksternal') DEFAULT NULL,
  `total_transaksi` int(11) DEFAULT 0,
  `tanggal_bergabung` varchar(10) DEFAULT NULL,
  `status` enum('Aktif','Resigned') DEFAULT 'Aktif',
  `nama_kontak_darurat` varchar(100) DEFAULT NULL,
  `no_kontak_darurat` varchar(20) DEFAULT NULL,
  `hubungan_dengan_pembeli` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `catatan_customer` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `isi` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `tempat_tanggal_lahir` varchar(100) DEFAULT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `jenis_kelamin` enum('Laki-Laki','Perempuan') DEFAULT NULL,
  `status_pernikahan` enum('Menikah','Belum Menikah','Cerai') DEFAULT NULL,
  `agama` varchar(50) DEFAULT NULL,
  `kewarganegaraan` varchar(50) DEFAULT NULL,
  `alamat_ktp` text DEFAULT NULL,
  `alamat_domisili` text DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `nama_perusahaan` varchar(100) DEFAULT NULL,
  `lama_bekerja` varchar(50) DEFAULT NULL,
  `nama_kontak_darurat` varchar(100) DEFAULT NULL,
  `hubungan_kontak_darurat` varchar(50) DEFAULT NULL,
  `no_hp_kontak_darurat` varchar(20) DEFAULT NULL,
  `foto_profil` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `dokumen_agen` (
  `id` int(11) NOT NULL,
  `agen_id` int(11) NOT NULL,
  `kategori` enum('KTP','NPWP','Surat Kontrak') NOT NULL,
  `nama_file` varchar(255) DEFAULT NULL,
  `path_file` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `dokumen_customer` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `kategori` enum('Pribadi','Pembelian','KPR','Pembayaran','Legalitas') NOT NULL,
  `nama_file` varchar(255) DEFAULT NULL,
  `path_file` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pemesanan` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `properti_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `metode_pembayaran` enum('Cash Keras','Cash Bertahap','KPR') NOT NULL,
  `harga_deal` bigint(20) NOT NULL,
  `status_pemesanan` enum('Booking','Terjual','Terjual Eksternal') DEFAULT 'Booking',
  `tanggal_pesan` date NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `agen_id` int(11) DEFAULT NULL,
  `lama_cicilan` int(11) DEFAULT NULL,
  `nama_bank` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `properti` (
  `id` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `nomor_unit` varchar(50) NOT NULL,
  `lb_lt` varchar(20) DEFAULT NULL,
  `hadap` varchar(20) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `nama_type` varchar(100) DEFAULT NULL,
  `luas_bangunan` int(11) DEFAULT NULL,
  `luas_lahan` int(11) DEFAULT NULL,
  `cash_keras` bigint(20) DEFAULT NULL,
  `cash_bertahap` bigint(20) DEFAULT NULL,
  `cash_kpr` bigint(20) DEFAULT NULL,
  `status` enum('Tersedia','Terjual','Booking','Terjual Eksternal') DEFAULT 'Tersedia',
  `kamar_tidur` tinyint(4) DEFAULT NULL,
  `kamar_mandi` tinyint(4) DEFAULT NULL,
  `luas_m2` varchar(50) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `agen`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `catatan_customer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `dokumen_agen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agen_id` (`agen_id`);

ALTER TABLE `dokumen_customer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `properti_id` (`properti_id`),
  ADD KEY `admin_id` (`admin_id`);

ALTER TABLE `properti`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `agen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `catatan_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `dokumen_agen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `dokumen_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `pemesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `properti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `catatan_customer`
  ADD CONSTRAINT `catatan_customer_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE;

ALTER TABLE `dokumen_agen`
  ADD CONSTRAINT `dokumen_agen_ibfk_1` FOREIGN KEY (`agen_id`) REFERENCES `agen` (`id`) ON DELETE CASCADE;

ALTER TABLE `dokumen_customer`
  ADD CONSTRAINT `dokumen_customer_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE;

ALTER TABLE `pemesanan`
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`properti_id`) REFERENCES `properti` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemesanan_ibfk_3` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE SET NULL;


