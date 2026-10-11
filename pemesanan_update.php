<?php
include 'db.php';

$id = $_POST['id'];
$nama = $_POST['nama_lengkap'];
$no_hp = $_POST['no_hp'];
$email = $_POST['email'];
$nik = $_POST['nik'];
$properti_id = $_POST['properti_id'];
$metode = $_POST['metode_pembayaran'];
$harga = $_POST['harga_deal'];
$status = $_POST['status_pemesanan'];
$tanggal = $_POST['tanggal_pesan'];
$agen_id = $_POST['agen_id'];
$catatan = $_POST['catatan'] ?? null;
$lama_cicilan = $_POST['lama_cicilan'] ?? null;
$nama_bank    = $_POST['bank_kpr'] ?? null;


// ✅ VALIDASI WAJIB ISI
if (
    empty($properti_id) || 
    empty($metode) || 
    empty($harga) || 
    empty($status) || 
    empty($tanggal) || 
    empty($agen_id)
) {
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
    Swal.fire({
        icon: 'warning',
        title: 'Data Tidak Lengkap',
        text: 'Mohon lengkapi semua data wajib, termasuk pilihan agen properti.',
        confirmButtonText: 'Kembali'
    }).then(() => {
        window.history.back();
    });
    </script>
    </body>
    </html>
    ";
    exit;
}

// Ambil customer_id dan properti_id lama
$get_info = $conn->prepare("SELECT customer_id, properti_id FROM pemesanan WHERE id = ?");
$get_info->bind_param("i", $id);
$get_info->execute();
$res = $get_info->get_result();
$data = $res->fetch_assoc();

$customer_id = $data['customer_id'] ?? null;
$old_properti_id = $data['properti_id'] ?? null;

// Cek properti apakah sudah digunakan oleh pemesanan lain
$cek_stmt = $conn->prepare("SELECT id FROM pemesanan WHERE properti_id = ? AND status_pemesanan IN ('Terjual', 'Terjual Eksternal') AND id != ?");
$cek_stmt->bind_param("ii", $properti_id, $id);
$cek_stmt->execute();
$cek_result = $cek_stmt->get_result();

if ($cek_result->num_rows > 0) {
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal Memperbarui',
        text: 'Properti ini sudah digunakan dan tidak dapat dipilih!',
        confirmButtonText: 'Kembali'
    }).then(() => {
        window.location.href = 'pemesanan_edit?id=$id';
    });
    </script>
    </body>
    </html>
    ";
    exit;
}

// Update data customer
if ($customer_id) {
    $stmt = $conn->prepare("UPDATE customer SET nama_lengkap=?, no_hp=?, email=?, nik=? WHERE id=?");
    $stmt->bind_param("ssssi", $nama, $no_hp, $email, $nik, $customer_id);
    $stmt->execute();
}

// Jika properti lama diganti, status properti lama dikembalikan
if ($old_properti_id != $properti_id) {
    $conn->query("UPDATE properti SET status = 'Tersedia' WHERE id = $old_properti_id");
}

// Update data pemesanan (dengan agen_id)
$stmt = $conn->prepare("UPDATE pemesanan SET 
    properti_id=?, 
    metode_pembayaran=?, 
    harga_deal=?, 
    status_pemesanan=?, 
    tanggal_pesan=?, 
    agen_id=?, 
    catatan=?, 
    lama_cicilan=?, 
    nama_bank=?,
    updated_at=NOW() 
    WHERE id=?");
$stmt->bind_param(
    "isdsssissi", // Perhatikan urutan tipe datanya!
    $properti_id, $metode, $harga, $status, $tanggal, $agen_id, $catatan, $lama_cicilan, $nama_bank, $id
);

$stmt->execute();

// Update status properti
if ($status === 'Tersedia') {
    $conn->query("UPDATE properti SET status = 'Tersedia' WHERE id = $properti_id");
} elseif ($status === 'Booking') {
    $conn->query("UPDATE properti SET status = 'Booking' WHERE id = $properti_id");
} elseif ($status === 'Terjual') {
    $conn->query("UPDATE properti SET status = 'Terjual' WHERE id = $properti_id");
} elseif ($status === 'Terjual Eksternal') {
    $conn->query("UPDATE properti SET status = 'Terjual Eksternal' WHERE id = $properti_id");
} 

// Redirect
header("Location: pemesanan?update=success");
exit;
