<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}
include 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM agen WHERE id = $id";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    die("Data agen tidak ditemukan.");
}

$agen = $result->fetch_assoc();
// Hitung total transaksi real-time dari tabel pemesanan
$totalRes = mysqli_query($conn, "
  SELECT COUNT(*) AS total 
  FROM pemesanan 
  WHERE agen_id = $id 
    AND status_pemesanan IN ('Terjual', 'Booking', 'Terjual Eksternal')
");
$totalRow = mysqli_fetch_assoc($totalRes);
$total_transaksi = $totalRow['total'];

// Ambil properti terakhir yang diproses oleh agen ini
$sql_properti_agen = "
    SELECT p.*, pr.nama_type, pr.unit, pr.nomor_unit, pr.gambar, pr.deskripsi, pr.kamar_tidur, pr.kamar_mandi, pr.luas_bangunan
    FROM pemesanan p
    JOIN properti pr ON p.properti_id = pr.id
    WHERE p.agen_id = $id
    ORDER BY p.created_at DESC
";
$result_properti_agen = $conn->query($sql_properti_agen);

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | Detail Agen</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="icon" href="assets/img/favicon.jpg" sizes="32x32" type="image/jpg">
  <link rel="stylesheet" href="assets/css/navbarstyle.css">
  <link href="assets/css/customerstyle.css" rel="stylesheet">
</head>
<body>
<?php
$activePage = 'agen';
include 'navbar.php';
?>
<div class="container-fluid px-3">

    <div class="d-flex align-items-center justify-content-between bg-white shadow-sm p-3 mb-4 rounded-4">
        <div class="d-flex align-items-center gap-2">
          <a href="agen" class="btn btn-light rounded border">
            <i class="bi bi-arrow-left"></i>
          </a>

          <h4 class="mb-0" style="margin-left:15px;"><?= htmlspecialchars($agen['nama_lengkap']) ?></h4>
        </div>
    </div>

  
  <div class="row g-4">
    
    <div class="col-lg-8">
      <div class="card p-4 mb-4">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <h5 class="mb-0">Informasi Pribadi</h5>
            <!-- Trigger Button -->
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editAgenModal">
              Edit Agen
            </button>
          </div>
        
          <div class="profile-row mb-4">
            <img src="assets/img/uploads/<?= htmlspecialchars($agen['foto_agen']) ?>" class="profile-img" alt="Foto Agen">
            <div class="row w-100">
              <div class="col-sm-6 mb-2">
                <div class="text-muted small">Nama Lengkap</div>
                <div class="fw-semibold"><?= htmlspecialchars($agen['nama_lengkap']) ?></div>
              </div>
              <div class="col-sm-6 mb-2 offset-mobile-fix" style="margin-left: -30px;" >
                <div class="text-muted small">Tempat & Tanggal Lahir</div>
                <div class="fw-semibold"><?= htmlspecialchars($agen['tempat_tanggal_lahir']) ?></div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">No. KTP (NIK)</div>
              <div class="fw-semibold"><?= htmlspecialchars($agen['nik']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Jenis Kelamin</div>
              <div class="fw-semibold"><?= htmlspecialchars($agen['jenis_kelamin']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Status Pernikahan</div>
              <div class="fw-semibold"><?= htmlspecialchars($agen['status_pernikahan']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Agama</div>
              <div class="fw-semibold"><?= htmlspecialchars($agen['agama']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Kewarganegaraan</div>
              <div class="fw-semibold"><?= htmlspecialchars($agen['kewarganegaraan']) ?></div>
            </div>
          </div>

          <h6 class="mt-4">Alamat & Domisili</h6>
          <div class="row">
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Alamat sesuai KTP</div>
              <div class="fw-semibold"><?= htmlspecialchars($agen['alamat_ktp']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Alamat domisili sekarang</div>
              <div class="fw-semibold"><?= htmlspecialchars($agen['alamat_domisili']) ?></div>
            </div>
          </div>

          <h6 class="mt-4">Kontak Aktif</h6>
          <div class="row">
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Nomor HP / WhatsApp</div>
              <div class="fw-semibold"><?= htmlspecialchars($agen['no_hp']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Email aktif</div>
              <div class="fw-semibold"><?= htmlspecialchars($agen['email']) ?></div>
            </div>
          </div>

          <h6 class="mt-4">Detail Keanggotaan Agen Properti</h6>
          <div class="row">
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Tipe Agen</div>
              <div class="fw-semibold"><?= htmlspecialchars($agen['tipe_agen']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Tanggal Bergabung</div>
              <div class="fw-semibold"><?php echo !empty($agen['tanggal_bergabung']) ? date('d M Y', strtotime($agen['tanggal_bergabung'])) : '-';?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Total Transaksi</div>
              <div class="fw-semibold"><?= $total_transaksi ?> Unit</div>
            </div>
          </div>

          <h6 class="mt-4">Kontak Darurat</h6>
          <div class="row">
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Nama Kontak Darurat</div>
              <div class="fw-semibold"><?= htmlspecialchars($agen['nama_kontak_darurat']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Hubungan dengan Pembeli</div>
              <div class="fw-semibold"><?= htmlspecialchars($agen['hubungan_dengan_pembeli']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">No. HP Kontak Darurat</div>
              <div class="fw-semibold"><?= htmlspecialchars($agen['no_kontak_darurat']) ?></div>
            </div>
          </div>

      </div>
    </div>

    <!-- Kolom kanan -->
    <div class="col-lg-4">
      <div class="document-container mb-4">
      <strong>Dokumen Agen</strong>
      <div class="accordion mt-3" id="accordionAgenDokumen">
        <?php
        $kategoriAgen = ['KTP', 'NPWP', 'Surat Kontrak'];
        foreach ($kategoriAgen as $i => $kategori):
          $docs = mysqli_query($conn, "SELECT * FROM dokumen_agen WHERE agen_id = $id AND kategori = '$kategori' ORDER BY uploaded_at DESC");
        ?>

        <!-- Accordion Unggah File -->
        <div class="accordion-item border-0">
            <h2 class="accordion-header" id="headingAgen<?= $i ?>">
              <button class="accordion-button collapsed fw-semibold d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAgen<?= $i ?>" aria-expanded="false" aria-controls="collapseAgen<?= $i ?>">
                <div>
                  <i class="bi bi-folder me-2"></i> Dokumen <?= $kategori ?>
                </div>
                <span class="badge bg-danger rounded-pill ms-2"><?= mysqli_num_rows($docs) ?></span>
              </button>
            </h2>
            <div id="collapseAgen<?= $i ?>" class="accordion-collapse collapse" aria-labelledby="headingAgen<?= $i ?>" data-bs-parent="#accordionAgenDokumen">
              <div class="accordion-body">
                <ul class="list-group list-group-flush mb-3">
                  <?php if (mysqli_num_rows($docs) > 0): ?>
                    <?php while($doc = mysqli_fetch_assoc($docs)): ?>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        <!-- Link dokumen di kiri -->
                        <a href="<?= $doc['path_file'] ?>" target="_blank" class="text-decoration-none">
                          📄 <?= htmlspecialchars($doc['nama_file']) ?>
                        </a>

                        <!-- Tanggal + tombol hapus di kanan -->
                        <div class="d-flex align-items-center">
                          <small class="text-muted me-2">
                            <?= date('d/m/Y', strtotime($doc['uploaded_at'])) ?>
                          </small>
                          <form action="agen_delete_doc" method="POST" class="m-0"
                                onsubmit="return confirm('Yakin ingin menghapus dokumen ini?');">
                            <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                            <input type="hidden" name="agen_id" value="<?= $agen['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger p-1">
                              <i class="bi bi-trash"></i>
                            </button>
                          </form>
                        </div>
                      </li>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <li class="list-group-item text-muted">Belum ada dokumen.</li>
                  <?php endif; ?>
                </ul>

                <form action="agen_upload_doc" method="POST" enctype="multipart/form-data" class="mt-3 text-center">
                  <input type="hidden" name="agen_id" value="<?= $agen['id'] ?>">
                  <input type="hidden" name="kategori" value="<?= $kategori ?>">
                  <label for="agenFileInput<?= $i ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-upload"></i> Unggah dokumen
                  </label>
                  <input type="file" name="dokumen" id="agenFileInput<?= $i ?>" class="d-none" onchange="this.form.submit()">
                  <div class="text-muted mt-2">Upload dari HP atau PC</div>
                </form>
              </div>
            </div>
        </div>

        <?php endforeach; ?>
      </div>
    </div>

    <div class="property-container scrollable-properties">
        <h6 class="mb-3"><strong>Properti yang Ditangani</strong></h6>
        <?php if ($result_properti_agen && $result_properti_agen->num_rows > 0): ?>
          <?php $count = 0; while ($row = $result_properti_agen->fetch_assoc()): $count++; ?>
            <div class="property-container mb-4 p-0">
              
              <div class="property-box d-flex align-items-center mb-0">
                <img src="assets/img/<?= htmlspecialchars($row['gambar']) ?>" class="property-img me-3" style="width: 140px; height: 140px; object-fit: cover; 
                  border-top-left-radius: 0.75rem; 
                  border-bottom-left-radius: 0.75rem; 
                  border-top-right-radius: 0; 
                  border-bottom-right-radius: 0;" alt="Properti">
                <div>
                  <div class="fw-bold fs-5 mb-1"><?= htmlspecialchars($row['unit'] . ' ' . $row['nomor_unit']) ?></div>
                  <div class="text-muted small mb-2"><?= htmlspecialchars($row['deskripsi']) ?></div>

                  <div class="text-muted small mb-2">
                    <i class="bi bi-door-closed"></i> <?= $row['kamar_tidur'] ?> KT &nbsp;
                    <i class="bi bi-droplet"></i> <?= $row['kamar_mandi'] ?> KM &nbsp;
                    <i class="bi bi-bounding-box"></i> <?= $row['luas_bangunan'] ?> m²
                  </div>
                </div>
              </div>
              
            </div>
          <?php endwhile; ?>
          
          <?php else: ?>
            <div class="text-muted">Belum ada properti yang ditangani oleh agen ini.</div>
          <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<!-- Modal Edit Agen -->
<div class="modal fade" id="editAgenModal" tabindex="-1" aria-labelledby="editAgenModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title fw-semibold" id="editAgenModalLabel">Edit Agen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>

      <form action="agen_update" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="id" value="<?= $agen['id'] ?>">

          <div class="mb-4 text-center">
            <img src="assets/img/uploads/<?= htmlspecialchars($agen['foto_agen']) ?>" class="profile-img mb-2" alt="Foto Agen" style="width:56px;height:56px;border-radius:50%;object-fit:cover;">
            <input type="file" name="foto_agen" class="form-control form-control-sm mx-auto" style="max-width:200px;">
          </div>

          <div class="row">
            <?php
              function selected($val1, $val2) { return $val1 == $val2 ? 'selected' : ''; }
            ?>
            <div class="col-md-6 mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" name="nama_lengkap" value="<?= $agen['nama_lengkap'] ?>" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Tempat & Tanggal Lahir</label>
              <input type="text" name="tempat_tanggal_lahir" value="<?= $agen['tempat_tanggal_lahir'] ?>" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">NIK</label>
              <input type="text" name="nik" value="<?= $agen['nik'] ?>" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Jenis Kelamin</label>
              <select name="jenis_kelamin" class="form-select">
                <option <?= selected($agen['jenis_kelamin'], 'Laki-laki') ?>>Laki-laki</option>
                <option <?= selected($agen['jenis_kelamin'], 'Perempuan') ?>>Perempuan</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Agama</label>
              <select name="agama" class="form-select">
                <option value="Islam" <?= selected($agen['agama'], 'Islam') ?>>Islam</option>
                <option value="Kristen" <?= selected($agen['agama'], 'Kristen') ?>>Kristen</option>
                <option value="Katolik" <?= selected($agen['agama'], 'Katolik') ?>>Katolik</option>
                <option value="Hindu" <?= selected($agen['agama'], 'Hindu') ?>>Hindu</option>
                <option value="Buddha" <?= selected($agen['agama'], 'Buddha') ?>>Buddha</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Status Pernikahan</label>
              <select name="status_pernikahan" class="form-select">
                <option <?= selected($agen['status_pernikahan'], 'Belum Menikah') ?>>Belum Menikah</option>
                <option <?= selected($agen['status_pernikahan'], 'Menikah') ?>>Menikah</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Kewarganegaraan</label>
              <select name="kewarganegaraan" class="form-select">
                <option value="WNI" <?= selected($agen['kewarganegaraan'], 'WNI') ?>>WNI</option>
                <option value="WNA" <?= selected($agen['kewarganegaraan'], 'WNA') ?>>WNA</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" value="<?= $agen['email'] ?>" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">No. HP</label>
              <input type="text" name="no_hp" value="<?= $agen['no_hp'] ?>" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Alamat KTP</label>
              <input type="text" name="alamat_ktp" value="<?= $agen['alamat_ktp'] ?>" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Alamat Domisili</label>
              <input type="text" name="alamat_domisili" value="<?= $agen['alamat_domisili'] ?>" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Tipe Agen</label>
              <select name="tipe_agen" class="form-select">
                <option <?= selected($agen['tipe_agen'], 'Internal') ?>>Internal</option>
                <option <?= selected($agen['tipe_agen'], 'Eksternal') ?>>Eksternal</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Tanggal Bergabung</label>
              <input type="date" name="tanggal_bergabung" class="form-control" value="<?= !empty($agen['tanggal_bergabung']) && $agen['tanggal_bergabung'] != '0000-00-00' ? date('Y-m-d', strtotime($agen['tanggal_bergabung'])) : '' ?>">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option <?= selected($agen['status'], 'Aktif') ?>>Aktif</option>
                <option <?= selected($agen['status'], 'Resigned') ?>>Resigned</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Nama Kontak Darurat</label>
              <input type="text" name="nama_kontak_darurat" value="<?= $agen['nama_kontak_darurat'] ?>" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Hubungan dengan Pembeli</label>
              <input type="text" name="hubungan_dengan_pembeli" value="<?= $agen['hubungan_dengan_pembeli'] ?>" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">No. HP Kontak Darurat</label>
              <input type="text" name="no_kontak_darurat" value="<?= $agen['no_kontak_darurat'] ?>" class="form-control">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap JS for modal functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function goBackOrRedirect(fallbackUrl) {
    if (window.history.length > 1) {
      window.history.back();
    } else {
      window.location.href = fallbackUrl;
    }
  }
</script>
</body>
</html>
