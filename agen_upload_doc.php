<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses tidak sah.");
}

$agen_id = intval($_POST['agen_id']);
$kategori = $_POST['kategori'];
$kategori_valid = ['KTP', 'NPWP', 'Surat Kontrak'];

if (!in_array($kategori, $kategori_valid)) {
    die("Kategori tidak valid.");
}

if (!isset($_FILES['dokumen']) || $_FILES['dokumen']['error'] !== UPLOAD_ERR_OK) {
    die("File gagal diupload.");
}

$ext = strtolower(pathinfo($_FILES['dokumen']['name'], PATHINFO_EXTENSION));
$allowed = ['pdf', 'jpg', 'jpeg', 'png'];

if (!in_array($ext, $allowed)) {
    die("Format file tidak diperbolehkan.");
}

$upload_dir = "assets/docs_agen/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$new_filename = uniqid('agen_', true) . '.' . $ext;
$destination = $upload_dir . $new_filename;

if (move_uploaded_file($_FILES['dokumen']['tmp_name'], $destination)) {
    $stmt = $conn->prepare("INSERT INTO dokumen_agen (agen_id, kategori, nama_file, path_file) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $agen_id, $kategori, $_FILES['dokumen']['name'], $destination);
    $stmt->execute();
    $stmt->close();

    header("Location: agen_detail?id=$agen_id&upload=success");
    exit;
} else {
    die("Gagal menyimpan file.");
}
