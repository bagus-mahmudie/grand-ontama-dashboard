<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}
include 'db.php';
// Ambil data customer
$query = "
    SELECT 
        c.*, 
        p.nama_type, 
        ps.metode_pembayaran, 
        ps.harga_deal, 
        ps.status_pemesanan, 
        ps.tanggal_pesan, 
        ps.id AS id_pemesanan
    FROM customer c
    LEFT JOIN pemesanan ps ON c.id = ps.customer_id
    LEFT JOIN properti p ON ps.properti_id = p.id
    ORDER BY c.nama_lengkap ASC
";
$result = mysqli_query($conn, $query);
?>
<?php if (isset($_GET['updated'])): ?>
  <div class="alert alert-success text-center">
    ✅ Data customer berhasil diperbarui.
  </div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard | Kelola Customer</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="icon" href="assets/img/favicon.jpg" sizes="32x32" type="image/jpg">
  <link href="assets/css/customerstyle.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/navbarstyle.css">


</head>
<body>
<?php 
$activePage = 'customers';
include 'navbar.php'; 
?>

<div class="container-fluid px-3">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between
            bg-white shadow-sm p-3 mb-4 rounded-4 gap-2">
      <h4 class="mb-0"><strong>Customer</strong></h4>
      <input
        type="text"
        class="form-control form-control-sm w-100 w-sm-auto search-input"
        placeholder="Cari nama customer…"
        style="max-width:250px; font-size:16px;"
      >
    </div>

  <div class="row g-3">
    <?php while ($row = mysqli_fetch_assoc($result)) : 
      $status_label = match ($row['status_pemesanan']) {
          'Lunas' => 'Selesai',
          'Booking', 'DP' => 'Proses',
          default => 'Proses'
      };
    ?>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 customer-item">
  <div class="card shadow-sm rounded-4 border-1 h-100">
    <div class="card-body p-3">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center">
          <img src="assets/img/uploads/<?= htmlspecialchars($row['foto_profil'] ?: 'profile2.png') ?>" alt="Foto" width="50" class="rounded-circle me-3">
          <div>
            <small class="text-muted">Full Name</small>
            <div class="fw-semibold customer-name"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
          </div>
        </div>
        <div class="dropdown">
          <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-three-dots-vertical"></i>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="customer_detail?id=<?= $row['id'] ?>">Lihat Detail</a></li>
          </ul>
        </div>
      </div>

      <div class="d-flex justify-content-start gap-3 mb-3">
        <?php
        $no_wa = preg_replace('/[^0-9]/', '', $row['no_hp']);
        ?>
        <a href="https://wa.me/<?= $no_wa ?>" target="_blank" class="icon-btn" title="WhatsApp">
          <i class="bi bi-telephone-fill"></i>
        </a>
        <a href="mailto:<?= $row['email'] ?>" class="icon-btn" title="Email">
          <i class="bi bi-envelope-fill"></i>
        </a>
        <a href="customer_detail?id=<?= $row['id'] ?>" class="icon-btn" title="Dokumen">
          <i class="bi bi-file-earmark-text-fill"></i>
        </a>
      </div>


      <div class="mb-2">
        <small class="text-muted">Pesanan properti</small>
        <div class="d-flex justify-content-between align-items-center">
          <strong><?= $row['nama_type'] ?? '-' ?></strong>
          <?php
            $status_map = [
                'Booking' => ['Booking', 'bg-warning-subtle text-warning fw-normal rounded-pill px-3'],
                'Terjual Eksternal'   => ['Terjual Eksternal', 'bg-success-subtle text-success fw-normal rounded-pill px-3'],
                'Terjual'   => ['Terjual', 'bg-danger-subtle text-danger fw-normal rounded-pill px-3'],
            ];
            $status_raw = $row['status_pemesanan'] ?? 'Booking'; // default jika null
            $status_label = $status_map[$status_raw] ?? ['Unknown', 'bg-secondary-subtle text-dark'];

          ?>
          <span class="badge <?= $status_label[1] ?> rounded-pill px-3"><?= $status_label[0] ?></span>
          
        </div>
      </div>

      <div class="row mb-2">
        <div class="col-6">
          <small class="text-muted">Tanggal</small>
          <div><?= $row['tanggal_pesan'] !== '0000-00-00' ? date("n/j/Y", strtotime($row['tanggal_pesan'])) : '-' ?></div>
        </div>
        <div class="col-6">
          <small class="text-muted">Pembayaran</small>
          <div><?= $row['metode_pembayaran'] ?? '-' ?></div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-6">
          <small class="text-muted">Jumlah</small>
          <div><?= isset($row['harga_deal']) ? 'Rp ' . number_format($row['harga_deal'], 0, ',', '.') : '-' ?></div>
        </div>
        <div class="col-6">
          <small class="text-muted">ID Pesanan</small>
          <div><?= isset($row['id_pemesanan']) ? '#' . $row['id_pemesanan'] : '-' ?></div>
        </div>
      </div>

      <a href="customer_detail?id=<?= $row['id'] ?>" class="btn btn-outline-dark w-100 rounded-pill">
        Lihat detail →
      </a>
    </div>
  </div>
</div>

    <?php endwhile; ?>
  </div>
</div>

<!-- Bootstrap JS (opsional jika butuh fitur interaktif) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.querySelector('.search-input')
    .addEventListener('keyup', function(){
      const kw = this.value.toLowerCase();
      document.querySelectorAll('.customer-item').forEach(item => {
        const name = item.querySelector('.customer-name').innerText.toLowerCase();
        item.style.display = name.includes(kw) ? '' : 'none';
      });
    });
</script>
</body>
</html>
