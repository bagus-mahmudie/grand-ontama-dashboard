<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Ambil data umum
$existing_customer_id = $_POST['existing_customer_id'] ?? null;
$existing_agen_id = $_POST['existing_agen_id'] ?? null;

$properti_id = $_POST['properti_id'];
$metode_pembayaran = $_POST['metode_pembayaran'];
$harga_deal = $_POST['harga_deal'];
$status_pemesanan = $_POST['status_pemesanan'];
$tanggal_pesan = $_POST['tanggal_pesan'];
$catatan = $_POST['catatan'] ?? null;
$lama_cicilan = $_POST['lama_cicilan'] ?? null;
$nama_bank    = $_POST['bank_kpr'] ?? null;


// ==========================
// Tangani AGEN
// ==========================

$agen_id = null;

if (empty($existing_agen_id)) {
    $agen_nama = $_POST['agen_nama_lengkap'] ?? '';
    $agen_no_hp = $_POST['agen_no_hp'] ?? '';
    $agen_email = $_POST['agen_email'] ?? '';
    $agen_tipe = $_POST['agen_tipe_agen'] ?? '';

    // Validasi agen baru
    if (!empty($agen_nama) && !empty($agen_no_hp) && !empty($agen_email) && !empty($agen_tipe)) {
        $stmt = $conn->prepare("INSERT INTO agen (nama_lengkap, no_hp, email, tipe_agen, status, created_at) VALUES (?, ?, ?, ?, 'Aktif', NOW())");
        $stmt->bind_param("ssss", $agen_nama, $agen_no_hp, $agen_email, $agen_tipe);
        if ($stmt->execute()) {
            $agen_id = $stmt->insert_id;
        } else {
            echo "Gagal menyimpan agen baru: " . $conn->error;
            exit();
        }
        $stmt->close();
    } else {
        echo "Silakan isi data agen baru dengan lengkap atau pilih agen lama.";
        exit();
    }
} else {
    $agen_id = $existing_agen_id;
}

// ==========================
// Tangani CUSTOMER
// ==========================

// Tangani customer baru
$nama_lengkap = $_POST['customer_nama_lengkap'] ?? '';
$no_hp = $_POST['customer_no_hp'] ?? '';
$email = $_POST['customer_email'] ?? '';
$nik = $_POST['customer_nik'] ?? '';

if (!empty($nama_lengkap) && !empty($no_hp) && !empty($email) && !empty($nik)) {
    $stmt = $conn->prepare("INSERT INTO customer (nama_lengkap, no_hp, email, nik, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssss", $nama_lengkap, $no_hp, $email, $nik);
    if ($stmt->execute()) {
        $customer_id = $stmt->insert_id;
    } else {
        echo "Gagal menyimpan customer baru: " . $conn->error;
        exit();
    }
    $stmt->close();
} else {
    echo "Silakan isi data customer dengan lengkap.";
    exit();
}


// ==========================
// Simpan PEMESANAN
// ==========================

$stmt = $conn->prepare("INSERT INTO pemesanan 
(customer_id, properti_id, admin_id, agen_id, metode_pembayaran, harga_deal, status_pemesanan, tanggal_pesan, catatan, lama_cicilan, nama_bank, created_at, updated_at) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

$stmt->bind_param(
    "iiiisdsssis", 
    $customer_id, $properti_id, $admin_id, $agen_id, $metode_pembayaran, $harga_deal, 
    $status_pemesanan, $tanggal_pesan, $catatan, $lama_cicilan, $nama_bank
);


if ($stmt->execute()) {
    // Update status properti
    if ($status_pemesanan === 'Booking') {
        $conn->query("UPDATE properti SET status = 'Booking' WHERE id = $properti_id");
    } elseif ($status_pemesanan === 'Tersedia') {
        $conn->query("UPDATE properti SET status = 'Tersedia' WHERE id = $properti_id");
    } elseif ($status_pemesanan === 'Terjual') {
        $conn->query("UPDATE properti SET status = 'Terjual' WHERE id = $properti_id");
    } elseif ($status_pemesanan === 'Terjual Eksternal') {
        $conn->query("UPDATE properti SET status = 'Terjual Eksternal' WHERE id = $properti_id");
    }

    header("Location: pemesanan?success=1");
    exit();
} else {
    echo "Gagal menyimpan pemesanan: " . $stmt->error;
}
$stmt->close();

?>
