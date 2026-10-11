<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include 'db.php';

// Validasi ID
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die("ID tidak valid.");
}

$id = (int)$_POST['id'];

// Ambil data input
$nama_lengkap = $_POST['nama_lengkap'];
$tempat_tanggal_lahir = $_POST['tempat_tanggal_lahir'];
$nik = $_POST['nik'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$status_pernikahan = $_POST['status_pernikahan'];
$agama = $_POST['agama'];
$kewarganegaraan = $_POST['kewarganegaraan'];
$alamat_ktp = $_POST['alamat_ktp'];
$alamat_domisili = $_POST['alamat_domisili'];
$no_hp = $_POST['no_hp'];
$email = $_POST['email'];
$pekerjaan = $_POST['pekerjaan'];
$nama_perusahaan = $_POST['nama_perusahaan'];
$lama_bekerja = $_POST['lama_bekerja'];
$nama_kontak_darurat = $_POST['nama_kontak_darurat'];
$hubungan_kontak_darurat = $_POST['hubungan_kontak_darurat'];
$no_hp_kontak_darurat = $_POST['no_hp_kontak_darurat'];

// Escape input untuk keamanan
$nama_lengkap = $conn->real_escape_string($nama_lengkap);
$tempat_tanggal_lahir = $conn->real_escape_string($tempat_tanggal_lahir);
$nik = $conn->real_escape_string($nik);
$jenis_kelamin = $conn->real_escape_string($jenis_kelamin);
$status_pernikahan = $conn->real_escape_string($status_pernikahan);
$agama = $conn->real_escape_string($agama);
$kewarganegaraan = $conn->real_escape_string($kewarganegaraan);
$alamat_ktp = $conn->real_escape_string($alamat_ktp);
$alamat_domisili = $conn->real_escape_string($alamat_domisili);
$no_hp = $conn->real_escape_string($no_hp);
$email = $conn->real_escape_string($email);
$pekerjaan = $conn->real_escape_string($pekerjaan);
$nama_perusahaan = $conn->real_escape_string($nama_perusahaan);
$lama_bekerja = $conn->real_escape_string($lama_bekerja);
$nama_kontak_darurat = $conn->real_escape_string($nama_kontak_darurat);
$hubungan_kontak_darurat = $conn->real_escape_string($hubungan_kontak_darurat);
$no_hp_kontak_darurat = $conn->real_escape_string($no_hp_kontak_darurat);

// Proses upload foto jika ada
if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
    // 1) Ambil nama file lama
    $stmt = $conn->prepare("SELECT foto_profil FROM customer WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($oldFoto);
    $stmt->fetch();
    $stmt->close();

    // 2) Hapus file lama jika ada
    $oldPath = __DIR__ . '/assets/img/uploads/' . $oldFoto;
    if ($oldFoto && file_exists($oldPath)) {
        unlink($oldPath);
    }

    // 3) Proses upload file baru
    $uploadDir   = 'assets/img/uploads/';
    $filename    = basename($_FILES['foto_profil']['name']);
    $ext         = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png'];
    $file_size   = $_FILES['foto_profil']['size'];

    if (in_array($ext, $allowed_ext) && $file_size < 2 * 1024 * 1024) {
        $new_filename = uniqid('foto_', true) . '.' . $ext;
        $targetPath   = $uploadDir . $new_filename;

        if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $targetPath)) {
            // 4) Update nama file di DB
            $conn->query("UPDATE customer SET foto_profil = '$new_filename' WHERE id = $id");
        }
    }
}

// Query update data customer
$sql = "UPDATE customer SET 
    nama_lengkap = '$nama_lengkap',
    tempat_tanggal_lahir = '$tempat_tanggal_lahir',
    nik = '$nik',
    jenis_kelamin = '$jenis_kelamin',
    status_pernikahan = '$status_pernikahan',
    agama = '$agama',
    kewarganegaraan = '$kewarganegaraan',
    alamat_ktp = '$alamat_ktp',
    alamat_domisili = '$alamat_domisili',
    no_hp = '$no_hp',
    email = '$email',
    pekerjaan = '$pekerjaan',
    nama_perusahaan = '$nama_perusahaan',
    lama_bekerja = '$lama_bekerja',
    nama_kontak_darurat = '$nama_kontak_darurat',
    hubungan_kontak_darurat = '$hubungan_kontak_darurat',
    no_hp_kontak_darurat = '$no_hp_kontak_darurat',
    updated_at = NOW()
    WHERE id = $id";

if ($conn->query($sql)) {
    echo "success";
} else {
    echo "failed: " . $conn->error;
}

?>
