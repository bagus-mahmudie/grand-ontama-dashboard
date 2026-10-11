<?php
session_start();
require 'db.php';

// Hanya superadmin yang diizinkan
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'superadmin') {
    header("Location: dashboard");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $role = $_POST['role'] ?? '';

    $allowedRoles = ['admin', 'superadmin'];

    if ($id > 0 && in_array($role, $allowedRoles)) {

        if ($id === $_SESSION['admin_id']) {
        $_SESSION['error'] = "Anda tidak bisa mengubah role diri sendiri.";
        header("Location: admin_list");
        exit();
}
        // Cek apakah admin yang dimaksud berstatus approved
        $checkStmt = $conn->prepare("SELECT status FROM admin WHERE id = ?");
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult && $checkResult->num_rows === 1) {
            $admin = $checkResult->fetch_assoc();
            if ($admin['status'] !== 'approved') {
                $_SESSION['error'] = "Role hanya bisa diubah untuk admin yang sudah disetujui.";
                header("Location: admin_list");
                exit();
            }

            // Lanjut update role
            $stmt = $conn->prepare("UPDATE admin SET role = ? WHERE id = ?");
            $stmt->bind_param("si", $role, $id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Role berhasil diperbarui.";
            } else {
                $_SESSION['error'] = "Gagal memperbarui role.";
            }

        } else {
            $_SESSION['error'] = "Admin tidak ditemukan.";
        }

    } else {
        $_SESSION['error'] = "Data tidak valid.";
    }
}

header("Location: admin_list");
exit();
?>
