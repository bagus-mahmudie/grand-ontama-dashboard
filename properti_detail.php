<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}
include 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM properti WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Properti tidak ditemukan.");
}

$properti = mysqli_fetch_assoc($result);
$pemesanan = mysqli_query($conn, "SELECT p.*, c.nama_lengkap AS nama_pelanggan, a.nama_lengkap AS nama_agen, a.no_hp, a.tipe_agen, a.tanggal_bergabung, a.status AS status_agen
    FROM pemesanan p
    JOIN customer c ON c.id = p.customer_id
    LEFT JOIN agen a ON a.id = p.agen_id
    WHERE p.properti_id = $id");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Detail Properti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="assets/img/favicon.jpg" sizes="32x32" type="image/jpg">
    <link href="assets/css/properti_detail.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/navbarstyle.css">
</head>
<body>

<?php 
$activePage = 'properti';
include 'navbar.php'; 
?>

<div class="container-fluid px-3">

    <div class="d-flex align-items-center justify-content-between bg-white shadow-sm p-3 mb-4 rounded-4">
        <div class="d-flex align-items-center gap-2">
        <button onclick="history.back()" class="btn btn-light rounded border">
            <i class="bi bi-arrow-left"></i>
      </button>
        <h5 class="mb-0 fw-semibold">Detail Properti</h5>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card p-3">
                <img src="assets/img/<?= htmlspecialchars($properti['gambar']) ?>" alt="Properti" >
                <div class="mt-3 d-flex justify-content-between align-items-start">
                    <div>
                        <h5><strong><?= htmlspecialchars($properti['unit'] . ' ' . $properti['nomor_unit']) ?></strong></h5>
                        <p class="text-muted small mb-2"><?= htmlspecialchars($properti['deskripsi']) ?></p>
                        <h6 class="text-primary">Rp <?= number_format($properti['cash_keras'], 0, ',', '.') ?></h6>
                    </div>
                    <span class="badge bg-<?= $properti['status'] === 'Terjual' ? 'success' : ($properti['status'] === 'Booking' ? 'warning text-dark' : 'secondary') ?>">
                        <?= htmlspecialchars($properti['status']) ?>
                    </span>
                </div>
                <div class="mt-2 d-flex gap-3 small">
                    <span><i class="bi bi-door-closed"></i> <?= $properti['kamar_tidur'] ?> Bedroom</span>
                    <span><i class="bi bi-droplet"></i> <?= $properti['kamar_mandi'] ?> Bathroom</span>
                    <span><i class="bi bi-fullscreen"></i> <?= $properti['luas_m2'] ?> m²</span>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="spec-box">
                <div class="spec-title">
                    Spesifikasi Bangunan
                </div>
                <div class="spec-row">
                    <div class="spec-label">Nama Unit</div>
                    <div class="spec-value"><?= $properti['unit'] ?></div>
                </div>
                <div class="spec-row">
                    <div class="spec-label">Nomor Unit</div>
                    <div class="spec-value"><?= $properti['nomor_unit'] ?></div>
                </div>
                <div class="spec-row">
                    <div class="spec-label">LB/LT</div>
                    <div class="spec-value"><?= $properti['lb_lt'] ?></div>
                </div>
                <div class="spec-row">
                    <div class="spec-label">Hadap</div>
                    <div class="spec-value"><?= $properti['hadap'] ?></div>
                </div>
                <div class="spec-row">
                    <div class="spec-label">Tipe</div>
                    <div class="spec-value"><?= $properti['type'] ?></div>
                </div>
                <div class="spec-row">
                    <div class="spec-label">Nama Tipe</div>
                    <div class="spec-value"><?= $properti['nama_type'] ?></div>
                </div>
                <div class="spec-row">
                    <div class="spec-label">Luas Bangunan</div>
                    <div class="spec-value"><?= $properti['luas_bangunan'] ?> m²</div>
                </div>
                <div class="spec-row">
                    <div class="spec-label">Luas Lahan</div>
                    <div class="spec-value"><?= $properti['luas_lahan'] ?> m²</div>
                </div>
            </div>
        </div>

    </div>

    <?php if ($pemesanan && mysqli_num_rows($pemesanan) > 0): ?>
    <div class="card p-3 mt-4">
        <h6 class="mb-3">Pemesanan Properti</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-rounded">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Pelanggan</th>
                    <th>Tanggal</th>
                    <th>Nama Tipe</th>
                    <th>Pembayaran</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <!-- <th>Action</th> -->
                </tr>
                </thead>
                <tbody>
                <?php $i = 1; while($row = mysqli_fetch_assoc($pemesanan)): ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal_pesan'])) ?></td>
                        <td><?= htmlspecialchars($properti['nama_type']) ?></td>
                        <td><?= htmlspecialchars($row['metode_pembayaran']) ?></td>
                        <td>Rp <?= number_format($row['harga_deal'], 0, ',', '.') ?></td>
                        <td><span class="badge bg-success"><?= $row['status_pemesanan'] ?></span></td>
                        
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card p-3 mt-4">
        <h6 class="mb-3">Agen Properti</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-rounded">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Agen</th>
                    <th>Nomor HP</th>
                    <th>Tipe Agen</th>
                    <th>Total Transaksi</th>
                    <th>Tanggal Bergabung</th>
                    <th>Status</th>
                    <!-- <th>Action</th> -->
                </tr>
                </thead>
                <tbody>
                <?php mysqli_data_seek($pemesanan, 0); $i = 1; while($row = mysqli_fetch_assoc($pemesanan)): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['nama_agen']) ?></td>
                        <td><?= htmlspecialchars($row['no_hp']) ?></td>
                        <td><?= htmlspecialchars($row['tipe_agen']) ?></td>
                        <td><?= htmlspecialchars($row['total_transaksi'] ?? '2') ?> Unit</td>
                        <td><?= date('d F Y', strtotime($row['tanggal_bergabung'])) ?></td>
                        <td><span class="badge bg-<?= $row['status_agen'] === 'Aktif' ? 'success' : 'secondary' ?>"><?= $row['status_agen'] ?></span></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
