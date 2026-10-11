<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}
?>
<?php
require_once __DIR__ . '/db.php';

$sqlPemesananDashboard = "
  SELECT p.*, 
         c.nama_lengkap, c.no_hp, c.foto_profil,
         pr.nama_type, pr.nomor_unit, pr.gambar
  FROM pemesanan p
  JOIN customer c ON p.customer_id = c.id
  JOIN properti pr ON p.properti_id = pr.id
  ORDER BY p.updated_at DESC LIMIT 5
";
$resultDashboard = $conn->query($sqlPemesananDashboard);

function getStatusClass($status) {
  return match ($status) {
    'Terjual Eksternal' => 'badge-status bg-success text-light',
    'Booking' => 'badge-status bg-warning text-dark',
    'Terjual' => 'badge-status bg-danger text-light',
    default => 'badge-status bg-secondary text-dark'
  };
}

// Total Properti
$totalProperti = $conn->query("SELECT COUNT(*) AS total FROM properti")->fetch_assoc()['total'];

// Total Properti Terjual
$totalTerjual = $conn->query("SELECT COUNT(*) AS total FROM properti WHERE status IN ('Terjual', 'Terjual Eksternal')")->fetch_assoc()['total'];

// Total Customer
$totalCustomer = $conn->query("SELECT COUNT(*) AS total FROM customer")->fetch_assoc()['total'];
?>
<?php

// Ambil total harga_deal per bulan
$monthlyRevenue = [];
$monthLabels = [];
for ($i = 1; $i <= 12; $i++) {
  $monthLabels[] = DateTime::createFromFormat('!m', $i)->format('F');
  $month = str_pad($i, 2, '0', STR_PAD_LEFT);
  $year = date('Y');
  $sql = "SELECT SUM(harga_deal) AS total FROM pemesanan WHERE MONTH(tanggal_pesan) = $month AND YEAR(tanggal_pesan) = $year";
  $result = $conn->query($sql);
  $data = $result->fetch_assoc();
  $monthlyRevenue[] = (int)($data['total'] ?? 0);
}
$revenueJson = json_encode($monthlyRevenue);
$labelJson = json_encode($monthLabels);

// Hitung total keseluruhan
$totalAll = array_sum($monthlyRevenue);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | Grand Ontama</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="icon" href="assets/img/favicon.jpg" sizes="64x64" type="image/jpg">
  <link rel="stylesheet" href="assets/css/dashboardstyle.css">
  <link rel="stylesheet" href="assets/css/navbarstyle.css">
  
</head>
<body>

<?php $activePage = 'dashboard';
include 'navbar.php'; 
$properti_tersedia = mysqli_query($conn, "SELECT * FROM properti WHERE status = 'Tersedia' ORDER BY id DESC LIMIT 3");?>

  <!-- Main Content -->
  <div class="container-fluid px-3" style="background-color:#F9F9F9">
    <div class="row g-4">
      

      <!-- Revenue Chart -->
      <div class="col-lg-8">
        <div class="row g-4 mb-3">

          <!-- Total Properti -->
          <div class="col-md-4">
            <div class="card shadow-sm rounded-4 border-0 p-3 h-100">
              <div class="d-flex justify-content-between align-items-center">
                <strong>Total Properti</strong>
              </div>
              <h2 class="fw-bold mt-2"><?= $totalProperti ?></h2>
              <p class="text-muted"><?= $totalProperti ?> properti aktif saat ini</p>
              <a href="properti_total" class="btn btn-light w-100 d-flex justify-content-between align-items-center">
                <span>Lihat detail</span> <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>

          <!-- Properti Terjual -->
          <div class="col-md-4">
            <div class="card shadow-sm rounded-4 border-0 p-3 h-100">
              <div class="d-flex justify-content-between align-items-center">
                <strong>Properti Terjual</strong>
              </div>
              <h2 class="fw-bold mt-2"><?= $totalTerjual ?></h2>
              <p class="text-muted"><?= $totalTerjual ?> properti sudah terjual</p>
              <a href="properti_terjual" class="btn btn-light w-100 d-flex justify-content-between align-items-center">
                <span>Lihat detail</span> <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>

          <!-- Total Customer -->
          <div class="col-md-4">
            <div class="card shadow-sm rounded-4 border-0 p-3 h-100">
              <div class="d-flex justify-content-between align-items-center">
                <strong>Total Customer</strong>
              </div>
              <h2 class="fw-bold mt-2"><?= $totalCustomer ?></h2>
              <p class="text-muted"><?= $totalCustomer ?> pelanggan terdaftar</p>
              <a href="customers" class="btn btn-light w-100 d-flex justify-content-between align-items-center">
                <span>Lihat detail</span> <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>

        </div>

        <?php
        $bulanLabels = [];
        $revenueData = [];

        for ($i = 5; $i >= 0; $i--) {
            $timestamp = strtotime("-$i months");
            $bulan = date('m', $timestamp);       // bulan numerik (01–12)
            $tahun = date('Y', $timestamp);       // tahun 4 digit
            $bulanLabels[] = date('M Y', $timestamp); // e.g. "Jun 2025"

            // Query total harga_deal per bulan
            $stmt = $conn->prepare("
                SELECT SUM(harga_deal) AS total 
                FROM pemesanan 
                WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?
            ");
            $stmt->bind_param("ii", $bulan, $tahun);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            $total = $result['total'] ?? 0;
            $revenueData[] = (int)$total;
        }
        ?>

        <div class="card p-4 border-0 shadow-sm rounded-4" style="margin-top:20px; ">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h6 class="mb-1">Revenue Overview <i class="bi bi-info-circle"></i></h6>
                <h4 class="fw-bold text-dark">Rp <?= number_format($totalAll, 0, ',', '.') ?></h4>
              </div>
              <div class="text-muted">
                <i class="bi bi-calendar"></i>  <?= date('F Y') ?>
              </div>
            </div>
            <canvas id="revenueCurveChart" height="465"></canvas>
        </div>
      </div>
      <!-- Kartu Properti -->
      <div class="col-lg-4">
        <div class="card p-4 border-0 shadow-sm rounded-4" style="border-radius:12px;">
          <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h4 style="font-weight:bold;" class="section-title mb-0">Properti</h4>
            </div>

            <!-- Card Properti Statik 1 -->
            <div class="col-12 mb-3">
              <div class="card property-card h-100">
                <div class="row g-0 h-100">
                  <div class="col-md-5">
                    <img src="assets/img/luxora-a01.jpg" class="property-image img-fluid w-100" alt="Unit A1">
                  </div>
                  <div class="col-md-7">
                    <div class="card-body d-flex flex-column h-100">
                      <h6 class="text-primary">
                        Rp 1.180.500.000
                        <span class="float-end status-tersedia">Tersedia</span>
                      </h6>
                      <h5 class="card-title mb-1">Luxora</h5>
                      <p class="card-text small mb-2">Hunian mezzanine modern dengan 3 kamar, ideal untuk keluarga, tata ruang lapang, harga terjangkau.</p>
                      <div class="mt-auto pt-2 pb-3 property-info-bar d-flex justify-content-between small">
                        <span><i class="bi bi-door-closed"></i> 3 Bedroom</span>
                        <span><i class="bi bi-droplet"></i> 3 Bathroom</span>
                        <span><i class="bi bi-bounding-box"></i> 110 m²</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card Properti Statik 2 -->
            <div class="col-12 mb-3">
              <div class="card property-card h-100">
                <div class="row g-0 h-100">
                  <div class="col-md-5">
                    <img src="assets/img/verona-a01.jpg" class="property-image img-fluid w-100" alt="Unit B2">
                  </div>
                  <div class="col-md-7">
                    <div class="card-body d-flex flex-column h-100">
                      <h6 class="text-primary">
                        Rp 1.023.000.000
                        <span class="float-end status-tersedia">Tersedia</span>
                      </h6>
                      <h5 class="card-title mb-1">Verona</h5>
                      <p class="card-text small mb-2">Rumah mezzanine eksklusif, 3 kamar tidur, desain elegan, material premium, privasi dan kenyamanan terjamin.</p>
                      <div class="mt-auto pt-2 pb-3 property-info-bar d-flex justify-content-between small">
                        <span><i class="bi bi-door-closed"></i> 3 Bedroom</span>
                        <span><i class="bi bi-droplet"></i> 2 Bathroom</span>
                        <span><i class="bi bi-bounding-box"></i> 96 m²</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card Properti Statik 3 -->
            <div class="col-12 mb-3">
              <div class="card property-card h-100">
                <div class="row g-0 h-100">
                  <div class="col-md-5">
                    <img src="assets/img/melodia-a01.jpg" class="property-image img-fluid " alt="Unit C3">
                  </div>
                  <div class="col-md-7">
                    <div class="card-body d-flex flex-column h-100">
                      <h6 class="text-primary">
                        Rp 963.000.000
                        <span class="float-end status-tersedia">Tersedia</span>
                      </h6>
                      <h5 class="card-title mb-1">Melodia</h5>
                      <p class="card-text small mb-2">Kediaman mezzanine luas dengan 2 kamar, fleksibel, sirkulasi udara optimal, kualitas terpercaya.</p>
                      <div class="mt-auto pt-2 pb-3 property-info-bar d-flex justify-content-between small">
                        <span><i class="bi bi-door-closed"></i> 2 Bedroom</span>
                        <span><i class="bi bi-droplet"></i> 2 Bathroom</span>
                        <span><i class="bi bi-bounding-box"></i> 84 m²</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>


      <!-- Tabel Riwayat -->
      <div class="col-12">
        <div class="card p-4 border-0 shadow-sm rounded-4">
            <div class="d-flex justify-content-between mb-3">
              <h5 class="mb-0">Data Pemesanan Terbaru</h5>
              <a href="pemesanan" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
            </div>

            <div class="table-responsive">
              <table class="table-pemesanan" id="dashboardPemesanan">
                <thead>
                  <tr>
                    <th><input type="checkbox"></th>
                    <th>ID Pesanan</th>
                    <th>Nama Pelanggan</th>
                    <th>No HP</th>
                    <th>Nama Tipe</th>
                    <th>Nomor Unit</th>
                    <th>Pembayaran</th>
                    <th>Harga Deal</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($resultDashboard && $resultDashboard->num_rows > 0): ?>
                    <?php while ($row = $resultDashboard->fetch_assoc()): ?>
                      <tr class="data-row">
                        <td><input type="checkbox"></td>
                        <td>#<?= htmlspecialchars($row['id']) ?></td>
                        <td>
                          <img src="assets/img/uploads/<?= htmlspecialchars($row['foto_profil'] ?: 'profile2.png') ?>" class="profile-img">
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
                       
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr><td colspan="10" class="text-center text-muted">Belum ada pemesanan.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

        </div>
      </div>


    </div>
  </div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('revenueCurveChart').getContext('2d');
  const revenueChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= $labelJson ?>,
      datasets: [{
        label: 'Rp',
        data: <?= $revenueJson ?>,
        borderRadius: 20,
        backgroundColor: function(context) {
          const index = context.dataIndex;
          const max = Math.max(...context.chart.data.datasets[0].data);
          return context.dataset.data[index] === max ? 'rgba(13, 110, 253, 1)' : 'rgba(13, 110, 253, 0.3)';
        },
        hoverBackgroundColor: 'rgba(13, 110, 253, 0.8)',
        barThickness: 40,
      }]
    },
    options: {
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function(context) {
              return 'Rp ' + context.parsed.y.toLocaleString();
            }
          }
        }
      },
      scales: {
        y: {
          display: false, // ❌ Sembunyikan sumbu Y
          grid: { display: false }
        },
        x: {
          grid: { display: false }
        }
      }
    }
  });
</script>


</body>
</html>
