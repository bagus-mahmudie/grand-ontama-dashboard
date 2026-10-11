<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'superadmin') {
    header("Location: dashboard");
    exit();
}

require 'db.php';

// Aksi Approve atau Pending
if (isset($_GET['id']) && isset($_GET['aksi'])) {
    $id = intval($_GET['id']);
    $aksi = $_GET['aksi'] === 'izinkan' ? 'approved' : 'pending';

    $stmt = $conn->prepare("UPDATE admin SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $aksi, $id);
    $stmt->execute();

    header("Location: admin_list");
    exit();
}

// Ambil semua data admin
$stmt = $conn->prepare("SELECT id, name, email, status, role FROM admin");
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | Kelola Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="icon" href="assets/img/favicon.jpg" sizes="32x32" type="image/jpg">

</head>
<body class="bg-light">

<div class="container-fluid py-5 px-4 px-md-5">

    <a href="dashboard" class="btn btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
    </a>
    
    <h2 class="fw-bold mb-4">Kelola Admin</h2>

    <div class="table-responsive">
    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Status</th>
                <th>Role</th>
                <th colspan="2" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td>
                        <span class="badge bg-<?= $row['status'] === 'approved' ? 'success' : 'secondary' ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($row['role']) ?></td>

                    <!-- Aksi: Kelola -->
                    <td class="align-middle">
                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
                            <?php if ($row['status'] === 'approved'): ?>
                                <form method="POST" action="update_role" class="d-flex align-items-center gap-2">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <select name="role" onchange="this.form.submit()" class="form-select form-select-sm w-auto">
                                        <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                                        <option value="superadmin" <?= $row['role'] === 'superadmin' ? 'selected' : '' ?>>superadmin</option>
                                    </select>
                                </form>
                                <?php if ($row['id'] != $_SESSION['admin_id']): ?>
                                    <a href="?id=<?= $row['id'] ?>&aksi=blokir" class="btn btn-sm btn-warning">Batalkan</a>
                                <?php else: ?>
                                    <span class="text-muted small">Tidak bisa blokir diri sendiri</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="?id=<?= $row['id'] ?>&aksi=izinkan" class="btn btn-sm btn-success">Setujui</a>
                            <?php endif; ?>
                        </div>
                    </td>

                    <!-- Aksi: Hapus -->
                    <td class="align-middle">
                        <?php if ($row['id'] != $_SESSION['admin_id']): ?>
                            <a href="admin_delete?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Yakin ingin menghapus admin ini?')">Hapus</a>
                        <?php else: ?>
                            <span class="text-muted small">Tidak bisa hapus diri sendiri</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
</div>

</div>
</body>
</html>
