<?php
session_start();
include 'db.php';

$customer_id = $_POST['customer_id'];
$judul = $_POST['judul'];
$isi = $_POST['isi'];

$stmt = $conn->prepare("INSERT INTO catatan_customer (customer_id, judul, isi) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $customer_id, $judul, $isi);

if ($stmt->execute()) {
    header("Location: customer_detail?id=$customer_id");
} else {
    echo "Gagal menambahkan catatan.";
}
