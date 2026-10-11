<?php
session_start();
include 'db.php';

$id = $_POST['id'];
$customer_id = $_POST['customer_id'];
$judul = $_POST['judul'];
$isi = $_POST['isi'];

$stmt = $conn->prepare("UPDATE catatan_customer SET judul = ?, isi = ? WHERE id = ?");
$stmt->bind_param("ssi", $judul, $isi, $id);

if ($stmt->execute()) {
    header("Location: customer_detail?id=$customer_id");
} else {
    echo "Gagal mengedit catatan.";
}
