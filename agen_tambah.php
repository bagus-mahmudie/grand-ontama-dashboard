<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = $_POST['nama_lengkap'];
    $no_hp = $_POST['no_hp'];
    $tipe_agen = $_POST['tipe_agen'];
    $total_transaksi = $_POST['total_transaksi'];
    $tanggal_bergabung = $_POST['tanggal_bergabung'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO agen (nama_lengkap, no_hp, tipe_agen, total_transaksi, tanggal_bergabung, status, foto_agen)
                            VALUES (?, ?, ?, ?, ?, ?, 'profile1.jpg')");
    $stmt->bind_param("sssiss", $nama_lengkap, $no_hp, $tipe_agen, $total_transaksi, $tanggal_bergabung, $status);

    if ($stmt->execute()) {
        header("Location: agen?success=tambah");
        exit();
    } else {
        $error = "Gagal menambahkan data.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | Tambah Agen</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="assets/img/favicon.jpg" sizes="32x32" type="image/jpg">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }
    .form-container {
      max-width: 420px;
      margin: 50px auto;
      background: white;
      padding: 32px;
      border-radius: 20px;
      box-shadow: 0 0 12px rgba(0,0,0,0.05);
    }
    .form-label {
      font-weight: 500;
      font-size: 14px;
    }
    .form-control, .form-select {
      border-radius: 10px;
      font-size: 14px;
    }
    .form-title {
      font-weight: 600;
      font-size: 18px;
    }
    .btn-simpan {
      width: 100%;
      border-radius: 999px;
      background-color: #589BFF;
      color: white;
      font-weight: 500;
    }
    .btn-simpan:hover {
      background-color: #4285f4;
    }
    .close-btn {
      position: absolute;
      top: 24px;
      right: 24px;
      font-size: 18px;
      color: #999;
      text-decoration: none;
    }
  </style>
</head>
<body>
<div class="form-container position-relative">
  <a href="agen" class="close-btn">&times;</a>
  <h5 class="form-title mb-4">Tambah Agen</h5>
  
  <?php if (isset($error)) : ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="mb-3">
      <label for="nama" class="form-label">Nama Agen</label>
      <input type="text" class="form-control" id="nama" name="nama_lengkap" required>
    </div>

    <div class="mb-3">
      <label for="no_hp" class="form-label">Nomor HP</label>
      <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="+62" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Tipe Agen</label>
      <select class="form-select" name="tipe_agen" required>
        <option value="Internal">Internal</option>
        <option value="Eksternal">Eksternal</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Total Transaksi</label>
      <select class="form-select" name="total_transaksi" required>
        <?php for ($i = 1; $i <= 20; $i++): ?>
          <option value="<?= $i ?>"><?= $i ?> Unit</option>
        <?php endfor; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Tanggal Bergabung</label>
      <input type="date" class="form-control" name="tanggal_bergabung" required>
    </div>

    <div class="mb-4">
      <label class="form-label">Status</label>
      <select class="form-select" name="status" required>
        <option value="Aktif">Aktif</option>
        <option value="Resigned">Resigned</option>
      </select>
    </div>

    <button type="submit" class="btn btn-simpan">Simpan</button>
  </form>
</div>
</body>
</html>
