<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include 'db.php';

// Ambil data agen + jumlah transaksi dari pemesanan
$query = "SELECT 
            a.id,
            a.nama_lengkap,
            a.no_hp,
            a.email,
            a.tipe_agen,
            a.status,
            a.foto_agen,
            a.tanggal_bergabung,
            COUNT(p.id) AS total_transaksi
          FROM agen a
          LEFT JOIN pemesanan p ON p.agen_id = a.id
          GROUP BY a.id
          ORDER BY a.nama_lengkap ASC";

$result = mysqli_query($conn, $query);

$activePage = 'agen';
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | Kelola Agen</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="icon" href="assets/img/favicon.jpg" sizes="32x32" type="image/jpg">
  <link rel="stylesheet" href="assets/css/navbarstyle.css">
  <link rel="stylesheet" href="assets/css/pemesananstyle.css">
</head>
<body>

<div class="container-fluid px-3">
    <div class="bg-white shadow-sm rounded-4 p-4">

  <?php if (isset($_GET['delete']) && $_GET['delete'] === 'success'): ?>
    <div class="alert alert-success">Data agen berhasil dihapus.</div>
  <?php endif; ?>

  <div class="table-toolbar">
    <input type="text" class="search-input" placeholder="Cari nama agen...">
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahAgen">+ Tambah Agen</button>
  </div>
  <div class="table-responsive">
    <table class="table-pemesanan">
      <thead>
        <tr>
          <th><input type="checkbox"></th>
          <th>Nama Lengkap</th>
          <th>No. HP</th>
          <th>Tipe Agen</th>
          <th>Total Transaksi</th>
          <th>Tanggal Bergabung</th>
          <th>Status</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <tr>
              <td><input type="checkbox"></td>
              <td>
                <div class="d-flex align-items-center">
                  <img src="assets/img/uploads/<?= htmlspecialchars($row['foto_agen'] ?: 'profile1.jpg') ?>" alt="Foto Agen" class="profile-img">
                  <?= htmlspecialchars($row['nama_lengkap']) ?>
                </div>
              </td>
              <td><?= htmlspecialchars($row['no_hp']) ?></td>
              <td>
                <span class="badge-status <?= $row['tipe_agen'] === 'Internal' ? 'bg-tipe-internal' : 'bg-tipe-eksternal' ?>">
                    <?= $row['tipe_agen'] ?>
                </span>
              </td>
              <td><?= (int)$row['total_transaksi'] ?> Unit</td>
              <td><?= date('d-m-Y', strtotime($row['tanggal_bergabung'])) ?></td>
              <td>
                <span class="badge-status <?= $row['status'] === 'Aktif' ? 'bg-status-aktif' : 'bg-status-nonaktif' ?>">
                    <?= htmlspecialchars($row['status']) ?>
                </span>
              </td>
              <td>
                <div class="d-flex justify-content-center gap-2">
                  <a href="agen_detail?id=<?= $row['id'] ?>" class="btn-action" title="Detail"><i class="bi bi-eye"></i></a>
                  <a href="agen_hapus?id=<?= $row['id'] ?>" class="btn-action text-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus agen ini?')">
                    <i class="bi bi-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" class="text-center text-muted">Data agen belum tersedia.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>

<!-- Modal Tambah Agen -->
<div class="modal fade" id="modalTambahAgen" tabindex="-1" aria-labelledby="modalTambahAgenLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width: 500px;">
    <div class="modal-content">
      <form method="POST" action="agen_tambah" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTambahAgenLabel">Tambah Agen</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">

          <div class="mb-3">
            <label for="nama_lengkap" class="form-label">Nama Agen</label>
            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required autocomplete="name">
          </div>

          <div class="mb-3">
            <label for="no_hp" class="form-label">Nomor HP</label>
            <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="+62" required autocomplete="tel">
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email (Opsional)</label>
            <input type="email" class="form-control" id="email" name="email" autocomplete="email">
          </div>

          <div class="mb-3">
            <label for="tipe_agen" class="form-label">Tipe Agen</label>
            <select class="form-select" id="tipe_agen" name="tipe_agen" required>
              <option value="Internal">Internal</option>
              <option value="Eksternal">Eksternal</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="tanggal_bergabung" class="form-label">Tanggal Bergabung</label>
            <input type="date" class="form-control" id="tanggal_bergabung" name="tanggal_bergabung" required>
          </div>

          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
              <option value="Aktif">Aktif</option>
              <option value="Resigned">Resigned</option>
            </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary w-100">Simpan Agen</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.querySelector('.search-input');
  const tableRows = document.querySelectorAll('.table-pemesanan tbody tr');

  searchInput.addEventListener('keyup', function () {
    const keyword = this.value.toLowerCase();

    tableRows.forEach(row => {
      const nama = row.querySelector('td:nth-child(2)').innerText.toLowerCase();
      if (nama.includes(keyword)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  });
});
</script>

</body>
</html>
