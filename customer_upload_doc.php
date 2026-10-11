<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses tidak sah.");
}

$customer_id = intval($_POST['customer_id']);
$kategori = $_POST['kategori'];

if (!isset($_FILES['dokumen']) || $_FILES['dokumen']['error'] !== UPLOAD_ERR_OK) {
    die("File tidak valid.");
}

$ext = strtolower(pathinfo($_FILES['dokumen']['name'], PATHINFO_EXTENSION));
$allowed = ['pdf', 'jpg', 'jpeg', 'png', 'docx'];

if (!in_array($ext, $allowed)) {
    die("Ekstensi file tidak diperbolehkan.");
}

$upload_dir = "assets/docs/";
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

$new_name = uniqid('doc_', true) . '.' . $ext;
$destination = $upload_dir . $new_name;

if (move_uploaded_file($_FILES['dokumen']['tmp_name'], $destination)) {
    $stmt = $conn->prepare("INSERT INTO dokumen_customer (customer_id, kategori, nama_file, path_file) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $customer_id, $kategori, $_FILES['dokumen']['name'], $destination);
    $stmt->execute();
    header("Location: customer_detail?id=$customer_id&upload=success");
    exit;
} else {
    die("Gagal upload file.");
}
