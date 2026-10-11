<?php
// Koneksi database
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}
include 'db.php';
function getStatusClass($status) {
  return match ($status) {
    'Terjual Eksternal' => 'badge-status bg-tersedia text-light',
    'Booking' => 'badge-status bg-booking text-dark',
    'Terjual' => 'badge-status bg-terjual text-light',
    default => 'badge-status bg-secondary text-dark'
  };
}

$sql = "
  SELECT p.*, 
         c.nama_lengkap, c.no_hp, c.foto_profil,
         pr.nama_type, pr.nomor_unit, pr.gambar
  FROM pemesanan p
  JOIN customer c ON p.customer_id = c.id
  JOIN properti pr ON p.properti_id = pr.id
  ORDER BY p.created_at DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | Kelola Pemesanan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="icon" href="assets/img/favicon.jpg" sizes="32x32" type="image/jpg">
  <link href="assets/css/pemesananstyle.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/navbarstyle.css">

</head>
<body>
<?php 
$activePage = 'pemesanan';
include 'navbar.php'; 
?>
<div class="container-fluid px-3">
  <?php if (isset($_GET['add']) && $_GET['add'] === 'success'): ?>
  <div class="alert alert-success">Pemesanan berhasil ditambahkan.</div>
  <?php endif; ?>

  <?php if (isset($_GET['update']) && $_GET['update'] == 'success'): ?>
  <div class="alert alert-success">Status pemesanan berhasil diperbarui.</div>
  <?php endif; ?>

  <?php if (isset($_GET['delete']) && $_GET['delete'] === 'success'): ?>
  <div class="alert alert-success">Pemesanan berhasil dihapus.</div>
  <?php endif; ?>
    
    <div class="bg-white shadow-sm rounded-4 p-4">
          <div class="table-toolbar">
              <input type="text" id="searchInput" class="search-input" placeholder="Cari Data Pemesanan">
              <!-- Tombol Modal -->
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Pemesanan</button>
              <!-- Modal Tambah Pemesanan -->
              <div class="modal fade" id="modalTambah" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <form action="pemesanan_simpan" method="post">
                      <div class="modal-header">
                      <h5 class="modal-title" id="modalTambahAgenLabel">Tambah Pesanan</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                        <div class="modal-body">
                        <?php
                          include 'form/form_tambah_pesanan.php';
                        ?>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

          </div>          

          <div class="table-responsive">
            <table class="table-pemesanan" id="pemesananTable">
              <thead>
                <tr>
                  <th><input type="checkbox"></th>
                  <th>ID Pesanan</th>
                  <th>Nama Pelanggan</th>
                  <th>Nomor HP</th>
                  <th>Nama Tipe</th>
                  <th>Nomor Unit</th>
                  <th>Pembayaran</th>
                  <th>Jumlah</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                  <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="data-row">
                      <td><input type="checkbox"></td>
                      <td>#<?= htmlspecialchars($row['id']) ?></td>
                      <td>
                        <img src="assets/img/uploads/<?= htmlspecialchars($row['foto_profil'] ?: 'profile2.png') ?>" alt="Foto customer" class="profile-img">
                        <?= htmlspecialchars($row['nama_lengkap']) ?>
                      </td>
                      <td><?= htmlspecialchars($row['no_hp']) ?></td>
                      <td>
                        <img src="assets/img/<?= htmlspecialchars($row['gambar']) ?>" class="profile-img">
                        <?= htmlspecialchars($row['nama_type']) ?>
                      </td>
                      <td><?= htmlspecialchars($row['nomor_unit']) ?></td>
                      <td><?= htmlspecialchars($row['metode_pembayaran']) ?></td>
                      <td>Rp <?= number_format($row['harga_deal'], 0, ',', '.') ?></td>
                      <td><span class="<?= getStatusClass($row['status_pemesanan']) ?>"><?= htmlspecialchars($row['status_pemesanan']) ?></span></td>
                      <td>
                        <a href="pemesanan_edit?id=<?= $row['id'] ?>" class="btn-action"><i class="bi bi-pencil"></i></a>
                        <a href="pemesanan_hapus?id=<?= $row['id'] ?>" class="btn-action" onclick="return confirm('Yakin ingin menghapus pemesanan ini?')"><i class="bi bi-trash"></i></a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="9" class="text-center">Tidak ada data pemesanan.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.getElementById('searchInput').addEventListener('keyup', function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('.data-row');
    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(filter) ? '' : 'none';
    });
  });
</script>

</body>
</html>
