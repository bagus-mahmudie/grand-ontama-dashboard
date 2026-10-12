<?php
// Local setup only. Never expose account provisioning through HTTP.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/db.php';

function promptValue(string $label): string
{
    fwrite(STDERR, $label . ': ');
    $value = fgets(STDIN);
    if ($value === false) {
        throw new RuntimeException('Input tidak lengkap.');
    }
    return rtrim($value, "\r\n");
}

try {
    $identity = trim(promptValue('Email admin'));
    $password = promptValue('Password baru (minimal 12 karakter; input terlihat di terminal)');
    $confirmation = promptValue('Ulangi password');
    if ($identity === '' || strlen($identity) > 100) {
        throw new RuntimeException('Identitas akun kosong atau terlalu panjang.');
    }
    if (strlen($password) < 12 || strlen($password) > 72 || $password !== $confirmation) {
        throw new RuntimeException('Gunakan password 12–72 karakter dengan konfirmasi yang sama.');
    }
    if (!filter_var($identity, FILTER_VALIDATE_EMAIL)) { throw new RuntimeException('Email tidak valid.'); }
    $stmt = $conn->prepare('SELECT id FROM admin WHERE email = ?');
    $stmt->bind_param('s', $identity);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) { throw new RuntimeException('Email sudah digunakan.'); }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $name = 'Local Administrator';
    $stmt = $conn->prepare("INSERT INTO admin (email, password, name, role, status) VALUES (?, ?, ?, 'superadmin', 'approved')");
    $stmt->bind_param('sss', $identity, $hash, $name);
    $stmt->execute();
    unset($password, $confirmation, $hash);
    fwrite(STDOUT, "Akun lokal berhasil dibuat.\n");
} catch (Throwable $error) {
    fwrite(STDERR, "Akun belum dibuat. Periksa input, koneksi database, dan kemungkinan akun sudah ada.\n");
    exit(1);
}
