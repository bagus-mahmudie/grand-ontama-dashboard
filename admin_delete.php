<?php
session_start();

$role = isset($_SESSION['admin_role']) ? trim($_SESSION['admin_role']) : null;
$current_user_id = $_SESSION['admin_id'] ?? null;

if ($role !== 'superadmin') {
    die("Akses ditolak: Anda bukan superadmin");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID admin tidak valid");
}

$id_to_delete = intval($_GET['id']);
if ($id_to_delete === intval($current_user_id)) {
    die("Tidak bisa menghapus akun sendiri.");
}

include 'db.php';
$stmt = $conn->prepare("DELETE FROM admin WHERE id = ?");
$stmt->bind_param("i", $id_to_delete);
$stmt->execute();
$stmt->close();

header("Location: admin_list?status=deleted");
exit();
