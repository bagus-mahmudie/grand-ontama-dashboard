<?php
session_start();
require 'db.php';

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Process login request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Permintaan tidak sah (token CSRF tidak cocok).";
        header("Location: login");
        exit;
    }

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email !== '' && $password !== '') {
        $stmt = $conn->prepare("SELECT id, email, password, name, role, status FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if ($admin['status'] !== 'approved') {
                $_SESSION['error'] = "Akun Anda belum disetujui oleh superadmin.";
                header("Location: login");
                exit();
            }

            if (password_verify($password, $admin['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_role'] = $admin['role'];
                unset($_SESSION['csrf_token']);
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | Halaman Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/style-login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="assets/img/favicon.jpg" sizes="32x32" type="image/jpg">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div class="logo">
    <img src="assets/img/logo.png" alt="Logo Grandontama">
  </div>

  <div class="container">
    <div class="form-section">
      <div class="form-box">
        <h2>Selamat datang di Grandontama!</h2>
        <p class="subtitle">Masukkan email dan kata sandi Anda.</p>

        <?php if (isset($_SESSION['error'])): ?>
        <script>
            Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: '<?php echo addslashes($_SESSION["error"]); ?>',
            confirmButtonText: 'OK'
            });
        </script>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="" method="POST">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
          <label class="form-label">Alamat Email</label>
          <div class="input-group">
              <i class="fas fa-envelope"></i>
              <input type="email" name="email" class="form-control" placeholder="Masukkan Email" required autofocus>
          </div>

          <label class="form-label">Kata Sandi</label>
          <div class="input-group">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" class="form-control" placeholder="Masukkan Kata Sandi" required>
          </div>

          <div class="options">
              <label><input type="checkbox"> Ingat saya</label>
              <a href="#">Lupa Kata Sandi?</a>
          </div>

          <button type="submit" name="login" class="login-btn">Masuk</button>
        </form>

        <div class="bottom-text">
          Belum punya akun? <a href="register">Daftar</a>
        </div>

      </div>
    </div>

    <div class="image-section"></div>
  </div>
</body>
</html>
