<?php
session_start();
require 'db.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($email && $password) {
    // Tambahkan field 'status' ke SELECT
    $stmt = $conn->prepare("SELECT id, email, password, name, role, status FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Tambahkan pengecekan status Aktif
        if ($admin['status'] !== 'approved') {
            $_SESSION['error'] = "Akun Anda belum disetujui oleh superadmin.";
            header("Location: login");
            exit();
        }

        // Verifikasi password
        if (password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_role'] = $admin['role'];
            header("Location: dashboard");
            exit();
        }
    }

    $_SESSION['error'] = "Email atau password salah!";
    header("Location: login");
    exit();
} else {
    $_SESSION['error'] = "Harap isi email dan password!";
    header("Location: login");
    exit();
}

// Debugging aktif
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
