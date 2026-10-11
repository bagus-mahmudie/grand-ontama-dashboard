<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | Properti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="assets/img/favicon.jpg" sizes="32x32" type="image/jpg">
    <link href="assets/css/propertistyle.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/navbarstyle.css">
</head>
<body>
<?php 
$activePage = 'properti';
include 'navbar.php'; 
?>
<?php include 'db.php'; 

$properti_tersedia = mysqli_query($conn, "SELECT * FROM properti WHERE status IN ('Tersedia', 'Booking') ORDER BY id DESC LIMIT 3");
$properti_terjual = mysqli_query($conn, "SELECT * FROM properti WHERE status IN ('Terjual', 'Terjual Eksternal') ORDER BY id ASC LIMIT 3");
$penjualan_terbaru = mysqli_query($conn, "SELECT * FROM properti WHERE status IN ('Terjual', 'Terjual Eksternal') ORDER BY id DESC LIMIT 2");
$top_agen = mysqli_query($conn, "SELECT 
    a.id, 
    a.nama_lengkap, 
    a.foto_agen, 
    a.tipe_agen, 
    COUNT(p.id) AS total_transaksi 
FROM agen a 
LEFT JOIN pemesanan p ON p.agen_id = a.id 
WHERE a.status = 'Aktif' 
GROUP BY a.id 
ORDER BY total_transaksi DESC 
LIMIT 5");

$total = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM properti"));
$tersedia = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM properti WHERE status IN ('Tersedia', 'Booking')"));
$terjual = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM properti WHERE status IN ('Terjual', 'Terjual Eksternal')"));

$bulan_ini = date('Y-m');
$bulan_lalu = date('Y-m', strtotime('-1 month'));

// Penjualan bulan ini
$penjualan_bulan_ini = mysqli_num_rows(mysqli_query($conn, "
    SELECT p.id 
    FROM pemesanan p 
    JOIN properti pr ON p.properti_id = pr.id
    WHERE pr.status IN ('Terjual', 'Terjual Eksternal')
    AND DATE_FORMAT(p.tanggal_pesan, '%Y-%m') = '$bulan_ini'
"));

// Penjualan bulan lalu
$penjualan_bulan_lalu = mysqli_num_rows(mysqli_query($conn, "
    SELECT p.id 
    FROM pemesanan p 
    JOIN properti pr ON p.properti_id = pr.id
    WHERE pr.status IN ('Terjual', 'Terjual Eksternal')
    AND DATE_FORMAT(p.tanggal_pesan, '%Y-%m') = '$bulan_lalu'
"));

// Hitung persentase kenaikan
if ($penjualan_bulan_lalu > 0) {
    $persentase_kenaikan = (($penjualan_bulan_ini - $penjualan_bulan_lalu) / $penjualan_bulan_lalu) * 100;
    $persentase_kenaikan = round($persentase_kenaikan, 1);
} else {
    $persentase_kenaikan = $penjualan_bulan_ini > 0 ? 100 : 0;
}

?>

<div class="container-fluid px-3">
  <!-- Layout utama -->
  <div class="row">
    <!-- Kolom Kiri: Statistik dan Properti -->
    <div class="col-lg-8">
      <div class="row g-3 mb-4">
  <!-- Total Properti -->
  <div class="col-md-4">
    <div class="stat-card">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="d-flex align-items-center">
            <strong>Total Properti</strong>
          </div>
        </div>
      </div>
      <h4><?= $total ?></h4>
      <small>Properti aktif saat ini</small>
      <a href="properti_total" class="detail-btn d-flex justify-content-between align-items-center text-decoration-none text-dark">
        <span>Lihat detail</span>
        <i class="bi bi-arrow-right"></i>
        </a>
    </div>
  </div>

  <!-- Properti Tersedia -->
  <div class="col-md-4">
    <div class="stat-card">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="d-flex align-items-center">
            <strong>Properti tersedia</strong>
          </div>
        </div>
        <!-- <i class="bi bi-three-dots menu-icon"></i> -->
      </div>
      <h4>
        <?= $tersedia ?>
      </h4>
      <small><?= $tersedia ?> properti masih tersedia</small>
      <a href="properti_tersedia" class="detail-btn d-flex justify-content-between align-items-center text-decoration-none text-dark">
        <span>Lihat detail</span>
        <i class="bi bi-arrow-right"></i>
        </a>
    </div>
  </div>

  <!-- Properti Terjual -->
  <div class="col-md-4">
    <div class="stat-card">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="d-flex align-items-center">
            <strong>Properti Terjual</strong>
          </div>
        </div>
      </div>
      <div class="d-flex align-items-center gap-2">
        <h4 class="mb-0"><?= $terjual ?></h4>
        <span class="badge 
            <?= $persentase_kenaikan >= 0 ? 'trend-up' : 'trend-down' ?> 
            text-white d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill">
            <i class="bi <?= $persentase_kenaikan >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' ?>"></i>
            <?= abs($persentase_kenaikan) ?>%
        </span>
      </div>


        <small><?= $terjual ?> properti sudah terjual</small>
      <a href="properti_terjual" class="detail-btn d-flex justify-content-between align-items-center text-decoration-none text-dark">
        <span>Lihat detail</span>
        <i class="bi bi-arrow-right"></i>
        </a>
    </div>
  </div>
</div>


      <!-- Dua kolom horizontal: Properti tersedia & terjual -->
      <div class="row">
        <div class="col-md-6">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 style="margin-top:20px; font-weight:bold;" class="section-title mb-0">Properti Tersedia</h4>
          </div>
          <?php while($row = mysqli_fetch_assoc($properti_tersedia)): ?>
          <div class="col-12 mb-3">
            <div class="card property-card h-100">
              <div class="row g-0 h-100 align-items-stretch">
                <div class="col-md-5 d-flex p-0">
                  <img src="assets/img/<?= htmlspecialchars($row['gambar']) ?>" class="property-image img-fluid  w-100" alt="<?= $row['unit'] ?>">
                </div>
                <div class="col-md-7">
                  <div class="card-body d-flex flex-column h-100">
                    <h6 class="text-primary">
                      Rp <?= number_format($row['cash_keras'], 0, ',', '.') ?>
                      <span class="float-end 
                                <?php 
                                    switch ($row['status']) {
                                        case 'Tersedia':
                                            echo 'status-tersedia';
                                            break;
                                        case 'Booking':
                                            echo 'status-booking';
                                            break;
                                        case 'Terjual Eksternal':
                                            echo 'status-terjual-eks';
                                            break;
                                        case 'Terjual':
                                        default:
                                            echo 'status-terjual';
                                            break;
                                    }
                                ?>">
                                <?= $row['status'] ?>
                        </span>
                    </h6>
                    <h5 class="card-title mb-1"><?= $row['unit'] . ' ' . $row['nomor_unit'] ?></h5>
                    <p class="card-text small mb-2"><?= $row['deskripsi'] ?></p>
                    <div class="mt-auto pt-2 pb-3 property-info-bar d-flex justify-content-between small">
                      <span><i class="bi bi-door-closed"></i> <?= $row['kamar_tidur'] ?> Bedroom</span>
                      <span><i class="bi bi-droplet"></i> <?= $row['kamar_mandi'] ?> Bathroom</span>
                      <span><i class="bi bi-bounding-box"></i> <?= $row['luas_m2'] ?> m²</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        </div>

        <div class="col-md-6">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 style="margin-top:20px; font-weight:bold;" class="section-title mb-0">Properti Terjual</h4>
          </div>
          <?php while($row = mysqli_fetch_assoc($properti_terjual)): ?>
          <div class="col-12 mb-3">
            <div class="card property-card h-100">
              <div class="row g-0 h-100">
                <div class="col-md-5">
                <img src="assets/img/<?= htmlspecialchars($row['gambar']) ?>" class="property-image img-fluid" alt="<?= $row['unit'] ?>">
                </div>
                <div class="col-md-7">
                  <div class="card-body d-flex flex-column h-100">
                    <h6 class="text-primary">
                      Rp <?= number_format($row['cash_keras'], 0, ',', '.') ?>
                      <span class="float-end 
                                <?php 
                                    switch ($row['status']) {
                                        case 'Tersedia':
                                            echo 'status-tersedia';
                                            break;
                                        case 'Booking':
                                            echo 'status-booking';
                                            break;
                                        case 'Terjual Eksternal':
                                            echo 'status-terjual-eks';
                                            break;
                                        case 'Terjual':
                                        default:
                                            echo 'status-terjual';
                                            break;
                                    }
                                ?>">
                                <?= $row['status'] ?>
                            </span>
                    </h6>
                    <h5 class="card-title mb-1"><?= $row['unit'] . ' ' . $row['nomor_unit'] ?></h5>
                    <p class="card-text small mb-2"><?= $row['deskripsi'] ?></p>
                    <div class="mt-auto pt-2 pb-3 property-info-bar d-flex justify-content-between small">
                      <span><i class="bi bi-door-closed"></i> <?= $row['kamar_tidur'] ?> Bedroom</span>
                      <span><i class="bi bi-droplet"></i> <?= $row['kamar_mandi'] ?> Bathroom</span>
                      <span><i class="bi bi-bounding-box"></i> <?= $row['luas_m2'] ?> m²</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>

    <!-- Kolom Kanan: Top Agen dan Penjualan Terbaru -->
    <div class="col-lg-4">
        <div class="top-agent-card mb-4">
            <div class="section-title">
                Top Agen
                <i class="bi bi-info-circle info-icon" title="Agen properti dengan kinerja terbaik"></i>
            </div>
            <?php while ($agen = mysqli_fetch_assoc($top_agen)): ?>
                <a href="<?php echo 'agen_detail?id=' . htmlspecialchars($agen['id']); ?>" class="text-decoration-none text-dark">
                <div class="agent-list-item">
                    <img src="assets/img/uploads/<?= htmlspecialchars($agen['foto_agen'] ?: 'profile1.jpg' ) ?>" alt="<?= htmlspecialchars($agen['nama_lengkap']) ?>" style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
                    <div class="info">
                        <div><?= htmlspecialchars($agen['nama_lengkap']) ?></div>
                        <small><?= htmlspecialchars($agen['tipe_agen']) ?> | <?= (int)$agen['total_transaksi'] ?> unit</small>
                    </div>
                    <i class="bi bi-arrow-right"></i>
                </div>
                </a>
            <?php endwhile; ?>
        </div>


      <div class="side-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 style="margin-top:0px; font-weight:bold;" class="section-title mb-0">Penjualan Terbaru</h5>
            <i class="bi bi-info-circle text-muted" title="Unit properti terbaru yang berhasil terjual"></i>
        </div>
        <?php while($row = mysqli_fetch_assoc($penjualan_terbaru)): ?>
            <div class="col-12 mb-3">
            <div class="card property-card h-100">
                <div class="row g-0 h-100">
                <div class="col-md-5">
                <img src="assets/img/<?= htmlspecialchars($row['gambar']) ?>" class="property-image img-fluid" alt="<?= $row['unit'] ?>">
                </div>
                <div class="col-md-7">
                    <div class="card-body d-flex flex-column h-100">
                    <h6 class="text-primary">
                        Rp <?= number_format($row['cash_keras'], 0, ',', '.') ?>
                        <span class="float-end 
                                <?php 
                                    switch ($row['status']) {
                                        case 'Tersedia':
                                            echo 'status-tersedia';
                                            break;
                                        case 'Booking':
                                            echo 'status-booking';
                                            break;
                                        case 'Terjual Eksternal':
                                            echo 'status-terjual-eks';
                                            break;
                                        case 'Terjual':
                                        default:
                                            echo 'status-terjual';
                                            break;
                                    }
                                ?>">
                                <?= $row['status'] ?>
                            </span>
                    </h6>
                    <h5 class="card-title mb-1"><?= $row['unit'] . ' ' . $row['nomor_unit'] ?></h5>
                    <p class="card-text small mb-2"><?= $row['deskripsi'] ?></p>
                    <div class="mt-auto pt-2 pb-3 property-info-bar d-flex justify-content-between small">
                        <span><i class="bi bi-door-closed"></i> <?= $row['kamar_tidur'] ?> Bedroom</span>
                        <span><i class="bi bi-droplet"></i> <?= $row['kamar_mandi'] ?> Bathroom</span>
                        <span><i class="bi bi-bounding-box"></i> <?= $row['luas_m2'] ?> m²</span>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        <?php endwhile; ?>
        </div>

    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap Icons (optional) -->
</body>
</html>