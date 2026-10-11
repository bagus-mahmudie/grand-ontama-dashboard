<?php
session_start();
require 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);

    if ($password !== $confirm) {
        $message = "Password dan konfirmasi tidak cocok.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Cek apakah email sudah digunakan
        $stmt = $conn->prepare("SELECT id FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $message = "Email sudah terdaftar.";
        } else {
            // Tambahkan status dan role default
            $status = 'pending';
            $role = 'admin';

            $stmt = $conn->prepare("INSERT INTO admin (name, email, password, status, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $hashedPassword, $status, $role);
            
            if ($stmt->execute()) {
                $message = "Registrasi berhasil. Menunggu persetujuan admin.";
            } else {
                $message = "Registrasi gagal: " . $stmt->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="card p-4 shadow" style="min-width: 350px;">
        <h4 class="mb-3 text-center">Registrasi Admin</h4>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="confirm" class="form-label">Konfirmasi Password</label>
                <input type="password" name="confirm" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Daftar</button>
            <div class="text-center mt-3">
                <a href="login">Sudah punya akun? Login</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
