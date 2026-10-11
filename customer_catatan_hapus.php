<?php
session_start();
include 'db.php';

$id = $_GET['id'];
$customer_id = $_GET['customer_id'];

$stmt = $conn->prepare("DELETE FROM catatan_customer WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: customer_detail?id=$customer_id");
} else {
    echo "Gagal menghapus catatan.";
}
