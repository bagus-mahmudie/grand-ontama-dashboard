<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}
include 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM customer WHERE id = $id";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    die("Data customer tidak ditemukan.");
}

$customer = $result->fetch_assoc();

// Ambil data pemesanan properti customer
$sql_pemesanan = "
  SELECT p.*, 
         pr.unit, pr.nomor_unit, pr.nama_type, pr.gambar, pr.kamar_tidur, pr.kamar_mandi, pr.luas_bangunan, pr.deskripsi,
         a.nama_lengkap AS nama_agen
  FROM pemesanan p
  JOIN properti pr ON p.properti_id = pr.id
  LEFT JOIN agen a ON p.agen_id = a.id
  WHERE p.customer_id = $id
  ORDER BY p.created_at DESC
  LIMIT 1
";

$result_pemesanan = $conn->query($sql_pemesanan);
$pemesanan = $result_pemesanan && $result_pemesanan->num_rows > 0 ? $result_pemesanan->fetch_assoc() : null;

function statusBadge($status) {
    $base = 'd-block w-100 text-center py-2 rounded-3 fw-semibold';

    switch (strtolower($status)) {
        case 'booking':
            return "$base bg-warning-subtle text-warning";
        case 'terjual':
            return "$base bg-success-subtle text-success";
        case 'terjual eksternal':
            return "$base bg-success-subtle text-success";
        default:
            return "$base bg-secondary-subtle text-dark";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | Detail Customer</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="icon" href="assets/img/favicon.jpg" sizes="32x32" type="image/jpg">
  <link rel="stylesheet" href="assets/css/navbarstyle.css">
  <link href="assets/css/customerstyle.css" rel="stylesheet">
  <style>
    .profile-row {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .profile-img {
      width: 45px;
      height: 45px;
      object-fit: cover;
      border-radius: 50%;
    }
    .profile-info {
      flex-grow: 1;
    }
     @media (max-width: 576px) {
      .profile-row {
        flex-direction: column;
        align-items: flex-start;
      }
      .profile-img {
        margin-bottom: 0.5rem;
      }
      .offset-mobile-fix {
        margin-left: 0 !important;
      }
    }
  </style>
</head>
<body>
<?php
$activePage = 'customers';
include 'navbar.php';
?>

<div class="container-fluid px-3">

    <div class="d-flex align-items-center justify-content-between bg-white shadow-sm p-3 mb-4 rounded-4">
        <div class="d-flex align-items-center gap-2">
          <a href="customers" class="btn btn-light rounded border">
            <i class="bi bi-arrow-left"></i>
          </a>
          <h4 class="mb-0" style="margin-left:15px;"><?= htmlspecialchars($customer['nama_lengkap']) ?></h4>
        </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card p-4 mb-4">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <h5 class="mb-0">Informasi Pribadi</h5>
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editCustomerModal">
              Edit
            </button>
          </div>

          <div class="profile-row mb-4">
            <img src="assets/img/uploads/<?= htmlspecialchars($customer['foto_profil']) ?>" class="profile-img" alt="Foto Profil">
            <div class="row w-100">
              <div class="col-sm-6 mb-2">
                <div class="text-muted small">Nama Lengkap</div>
                <div class="fw-semibold"><?= htmlspecialchars($customer['nama_lengkap']) ?></div>
              </div>
              <div class="col-sm-6 mb-2 offset-mobile-fix" style="margin-left: -30px;" >
                  <div class="text-muted small">Tempat & Tanggal Lahir</div>
                  <div class="fw-semibold"><?= htmlspecialchars($customer['tempat_tanggal_lahir']) ?></div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">No. KTP (NIK)</div>
              <div class="fw-semibold"><?= htmlspecialchars($customer['nik']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Jenis Kelamin</div>
              <div class="fw-semibold"><?= htmlspecialchars($customer['jenis_kelamin']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Status Pernikahan</div>
              <div class="fw-semibold"><?= htmlspecialchars($customer['status_pernikahan']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Agama</div>
              <div class="fw-semibold"><?= htmlspecialchars($customer['agama']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Kewarganegaraan</div>
              <div class="fw-semibold"><?= htmlspecialchars($customer['kewarganegaraan']) ?></div>
            </div>
          </div>

          <h6 class="mt-4">Alamat & Domisili</h6>
          <div class="row">
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Alamat sesuai KTP</div>
              <div class="fw-semibold"><?= htmlspecialchars($customer['alamat_ktp']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Alamat domisili sekarang</div>
              <div class="fw-semibold"><?= htmlspecialchars($customer['alamat_domisili']) ?></div>
            </div>
          </div>

          <h6 class="mt-4">Kontak Aktif</h6>
          <div class="row">
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Nomor HP / WhatsApp</div>
              <div class="fw-semibold"><?= htmlspecialchars($customer['no_hp']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Email aktif</div>
              <div class="fw-semibold"><?= htmlspecialchars($customer['email']) ?></div>
            </div>
          </div>

          <h6 class="mt-4">Pekerjaan</h6>
          <div class="row">
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Pekerjaan / Profesi</div>
              <div class="fw-semibold"><?= htmlspecialchars($customer['pekerjaan']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Nama Perusahaan / Tempat Kerja</div>
              <div class="fw-semibold"><?= htmlspecialchars($customer['nama_perusahaan']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Lama Bekerja</div>
              <div class="fw-semibold"><?= htmlspecialchars($customer['lama_bekerja']) ?></div>
            </div>
          </div>

          <h6 class="mt-4">Kontak Darurat</h6>
          <div class="row">
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Nama Kontak Darurat</div>
              <div class="fw-semibold"><?= htmlspecialchars($customer['nama_kontak_darurat']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">Hubungan dengan Pembeli</div>
              <div class="fw-semibold"><?= htmlspecialchars($customer['hubungan_kontak_darurat']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
              <div class="text-muted small">No. HP Kontak Darurat</div>
              <div class="fw-semibold"><?= htmlspecialchars($customer['no_hp_kontak_darurat']) ?></div>
            </div>
          </div>

        </div>
      </div>

      <!-- UPLOAD DOCUMEN -->
      <div class="col-lg-4">

        <div class="document-container mb-4">
          <strong>Dokumen Customer</strong>
          <div class="accordion mt-3" id="accordionDokumen">
            <?php
            $kategoriList = ['Pribadi', 'Pembelian', 'KPR', 'Pembayaran', 'Legalitas'];
            foreach ($kategoriList as $i => $kategori):
              $docs = mysqli_query($conn, "SELECT * FROM dokumen_customer WHERE customer_id = $id AND kategori = '$kategori' ORDER BY uploaded_at DESC");
            ?>
              <div class="accordion-item border-0">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed fw-semibold d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $i ?>">
                    <div>
                      <i class="bi bi-folder me-2"></i> Dokumen <?= $kategori ?>
                    </div>
                    <span class="badge bg-danger rounded-pill ms-2"><?= mysqli_num_rows($docs) ?></span>
                  </button>

                </h2>
                <div id="collapse<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#accordionDokumen">
                  <div class="accordion-body">
                    <ul class="list-group list-group-flush mb-3">
                      <?php if (mysqli_num_rows($docs) > 0): ?>
                        <?php while($doc = mysqli_fetch_assoc($docs)): ?>
                          <li class="list-group-item d-flex justify-content-between align-items-center">
                            <!-- Link ke dokumen -->
                            <a href="<?= $doc['path_file'] ?>" target="_blank" class="text-decoration-none">
                              📄 <?= htmlspecialchars($doc['nama_file']) ?>
                            </a>
                            
                            <!-- Tanggal + tombol hapus -->
                            <div class="d-flex align-items-center">
                              <small class="text-muted me-2">
                                <?= date('d/m/Y', strtotime($doc['uploaded_at'])) ?>
                              </small>
                              <form action="customer_delete_doc" method="POST" class="m-0"
                                    onsubmit="return confirm('Yakin ingin menghapus dokumen ini?');">
                                <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                                <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>">
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

                    <!-- Form upload tersembunyi -->
                    <form action="customer_upload_doc" method="POST" enctype="multipart/form-data" class="mt-3">
                      <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>">
                      <input type="hidden" name="kategori" value="<?= $kategori ?>">
                      <div class="text-center">
                        <label for="fileInput<?= $i ?>" class="btn btn-outline-secondary">
                          <i class="bi bi-upload"></i> Unggah dokumen
                        </label>
                        <input type="file" name="dokumen" id="fileInput<?= $i ?>" class="d-none" onchange="this.form.submit()">
                        <div class="text-muted mt-2">Upload dari HP atau PC</div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>


        <?php if ($pemesanan): ?>
        <div class="property-container">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <strong>Properti</strong>
            <a href="pemesanan_edit?id=<?= $pemesanan['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
          </div>
          <div class="card shadow-sm rounded-4 p-3 mb-2">

            <div class="d-flex flex-column flex-md-row gap-3 mb-4">
              <img src="assets/img/<?= htmlspecialchars($pemesanan['gambar']) ?>" class="property-img w-100 w-md-auto" 
              style="
                height: 180px; 
                object-fit: cover; 
                border-radius: 0.75rem;
                border-bottom-left-radius: 0.75rem;
                border-top-right-radius: 0;
                border-bottom-right-radius: 0;
              " alt="Properti">


              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start mb-1">
                  <div>
                    <div class="text-primary fw-semibold">Rp <?= number_format($pemesanan['harga_deal'], 0, ',', '.') ?></div>
                    <div class="fw-bold fs-5 mb-1"><?= htmlspecialchars($pemesanan['unit'] . ' ' . $pemesanan['nomor_unit']) ?></div>
                  </div>
                </div>

                <div class="text-muted small mb-3 text-break">
                  <?= htmlspecialchars($pemesanan['deskripsi']) ?>
                </div>


                <div class="text-muted small d-flex align-items-center gap-3">
                  <span><i class="bi bi-door-closed"></i> <?= $pemesanan['kamar_tidur'] ?> Bedroom</span>
                  <span><i class="bi bi-droplet"></i> <?= $pemesanan['kamar_mandi'] ?> Bathroom</span>
                  <span><i class="bi bi-bounding-box"></i> <?= $pemesanan['luas_bangunan'] ?> m²</span>
                </div>
              </div>

            </div>

            <?php if ($pemesanan['metode_pembayaran'] === 'KPR'): ?>
              <div class="row mb-3">
                <div class="col-6">
                  <small class="text-muted d-block mb-1">Pembayaran</small>
                  <div class="fw-semibold"><?= htmlspecialchars($pemesanan['metode_pembayaran']) ?></div>
                <hr class="my-2">
                </div>
                <div class="col-6">
                  <small class="text-muted d-block mb-1">Lama Cicilan</small>
                  <div class="fw-semibold"><?= htmlspecialchars($pemesanan['lama_cicilan']) ?> Tahun</div>
                <hr class="my-2">
                </div>
                <div class="col-6 mt-3">
                  <small class="text-muted d-block mb-1">Agen properti</small>
                  <div class="fw-semibold"><?= htmlspecialchars($pemesanan['nama_agen']) ?: '-' ?></div>
                <hr class="my-2">
                </div>
                <div class="col-6 mt-3">
                  <small class="text-muted d-block mb-1">Bank</small>
                  <div class="fw-semibold"><?= htmlspecialchars($pemesanan['nama_bank']) ?></div>
                <hr class="my-2">
                </div>
                
              </div>
            <?php else: ?>
              <div class="mb-3">
                <small class="text-muted d-block mb-1">Pembayaran</small>
                <div class="fw-semibold"><?= htmlspecialchars($pemesanan['metode_pembayaran']) ?></div>
                <hr class="my-2">
                <small class="text-muted d-block mb-1">Agen properti</small>
                <div class="fw-semibold"><?= htmlspecialchars($pemesanan['nama_agen']) ?: '-' ?></div>
                <hr class="my-2">
              </div>
            <?php endif; ?>




            <div>
              <small class="text-muted">Status Pemesanan</small>
              <div class="<?= statusBadge($pemesanan['status_pemesanan']) ?>">
                <?= $pemesanan['status_pemesanan'] ?>
              </div>
            </div>

        </div>
        <?php endif; ?>
        
      </div>

      <!-- MODAL EDIT -->
      <?php
        $foto_path = 'assets/img/uploads/' . $customer['foto_profil'];
        $foto_src = (!empty($customer['foto_profil']) && file_exists($foto_path)) ? $foto_path : 'assets/img/uploads/profile1.jpg';
        ?>

        <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="editCustomerModalLabel">Edit Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>

              <form id="editCustomerForm" enctype="multipart/form-data">
                <div class="modal-body">
                  <input type="hidden" name="id" value="<?= $customer['id'] ?>">

                  <div class="mb-4 text-center">
                    <img src="<?= $foto_src ?>" alt="Foto Profil" class="profile-img mb-2" style="width:64px;height:64px;border-radius:50%;object-fit:cover;background:#eee;">
                    <div>
                      <input type="file" name="foto_profil" class="form-control form-control-sm mt-2 mx-auto" style="max-width: 200px;">

                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label>Nama Lengkap</label>
                      <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($customer['nama_lengkap']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>Tempat & Tanggal Lahir</label>
                      <input type="text" name="tempat_tanggal_lahir" class="form-control" value="<?= htmlspecialchars($customer['tempat_tanggal_lahir']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>No. KTP (NIK)</label>
                      <input type="text" name="nik" class="form-control" value="<?= htmlspecialchars($customer['nik']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>Jenis Kelamin</label>
                      <select name="jenis_kelamin" class="form-select">
                        <option <?= $customer['jenis_kelamin']=='Laki-Laki'?'selected':'' ?>>Laki-Laki</option>
                        <option <?= $customer['jenis_kelamin']=='Perempuan'?'selected':'' ?>>Perempuan</option>
                      </select>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>Status Pernikahan</label>
                      <select name="status_pernikahan" class="form-select">
                        <option <?= $customer['status_pernikahan']=='Menikah'?'selected':'' ?>>Menikah</option>
                        <option <?= $customer['status_pernikahan']=='Belum Menikah'?'selected':'' ?>>Belum Menikah</option>
                      </select>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>Agama</label>
                      <select name="agama" class="form-select">
                        <option <?= $customer['agama']=='Islam'?'selected':'' ?>>Islam</option>
                        <option <?= $customer['agama']=='Kristen'?'selected':'' ?>>Kristen</option>
                        <option <?= $customer['agama']=='Katolik'?'selected':'' ?>>Katolik</option>
                        <option <?= $customer['agama']=='Hindu'?'selected':'' ?>>Hindu</option>
                        <option <?= $customer['agama']=='Budha'?'selected':'' ?>>Budha</option>
                        <option <?= $customer['agama']=='Konghucu'?'selected':'' ?>>Konghucu</option>
                      </select>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>Kewarganegaraan</label>
                      <select name="kewarganegaraan" class="form-select">
                        <option value="WNI" <?= $customer['kewarganegaraan']=='WNI'?'selected':'' ?>>WNI</option>
                        <option value="WNA" <?= $customer['kewarganegaraan']=='WNA'?'selected':'' ?>>WNA</option>
                      </select>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>Alamat KTP</label>
                      <input type="text" name="alamat_ktp" class="form-control" value="<?= htmlspecialchars($customer['alamat_ktp']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>Alamat Domisili</label>
                      <input type="text" name="alamat_domisili" class="form-control" value="<?= htmlspecialchars($customer['alamat_domisili']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>Nomor HP / WhatsApp</label>
                      <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($customer['no_hp']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>Email Aktif</label>
                      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['email']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>Pekerjaan</label>
                      <input type="text" name="pekerjaan" class="form-control" value="<?= htmlspecialchars($customer['pekerjaan']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>Nama Perusahaan</label>
                      <input type="text" name="nama_perusahaan" class="form-control" value="<?= htmlspecialchars($customer['nama_perusahaan']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>Lama Bekerja</label>
                      <input type="text" name="lama_bekerja" class="form-control" value="<?= htmlspecialchars($customer['lama_bekerja']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>Nama Kontak Darurat</label>
                      <input type="text" name="nama_kontak_darurat" class="form-control" value="<?= htmlspecialchars($customer['nama_kontak_darurat']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>Hubungan dengan Pembeli</label>
                      <select name="hubungan_kontak_darurat" class="form-select">
                        <option <?= $customer['hubungan_kontak_darurat']=='Orang Tua'?'selected':'' ?>>Orang Tua</option>
                        <option <?= $customer['hubungan_kontak_darurat']=='Suami/Istri'?'selected':'' ?>>Suami/Istri</option>
                        <option <?= $customer['hubungan_kontak_darurat']=='Saudara'?'selected':'' ?>>Saudara</option>
                        <option <?= $customer['hubungan_kontak_darurat']=='Paman'?'selected':'' ?>>Paman</option>
                        <option <?= $customer['hubungan_kontak_darurat']=='Teman'?'selected':'' ?>>Teman</option>
                      </select>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label>No HP Kontak Darurat</label>
                      <input type="text" name="no_hp_kontak_darurat" class="form-control" value="<?= htmlspecialchars($customer['no_hp_kontak_darurat']) ?>">
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

    </div>
</div>

<div class="card shadow-sm rounded-4 p-4 mb-4">
      <div class="d-flex flex-column flex-sm-row align-items-start gap-2 mb-3">
        <strong class="me-auto">Catatan</strong>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahCatatan">
          + Tambah Catatan
        </button>
      </div>

      <!-- Modal Tambah Catatan -->
      <div class="modal fade" id="modalTambahCatatan" tabindex="-1" aria-labelledby="modalTambahCatatanLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form action="customer_catatan_tambah" method="POST">
              <div class="modal-header">
                <h5 class="modal-title" id="modalTambahCatatanLabel">Tambah Catatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>">
                <div class="mb-3">
                  <label class="form-label">Judul</label>
                  <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Isi Catatan</label>
                  <textarea name="isi" class="form-control" rows="4" required></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>


      <div class="notes-wrapper d-flex gap-3 overflow-auto">
        <?php
        $notes = mysqli_query($conn, "SELECT * FROM catatan_customer WHERE customer_id = $id ORDER BY created_at DESC");
        while ($note = mysqli_fetch_assoc($notes)):
        ?>
          <div class="note-card flex-shrink-0 position-relative" style="width: 250px;">
            <!-- Tombol edit dan hapus di kanan atas -->
            <div class="position-absolute top-0 end-0 p-2 d-flex gap-2">
              <a href="#" class="text-muted small" title="Edit"
                data-bs-toggle="modal" data-bs-target="#modalEditCatatan<?= $note['id'] ?>">
                <i class="bi bi-pencil-square"></i>
              </a>
              <a href="customer_catatan_hapus?id=<?= $note['id'] ?>&customer_id=<?= $id ?>"
                class="text-muted small" title="Hapus"
                onclick="return confirm('Yakin ingin menghapus catatan ini?')">
                <i class="bi bi-trash"></i>
              </a>
            </div>

            <!-- Isi catatan -->
            <div class="text-muted small"><?= date('d/m/y H:i', strtotime($note['created_at'])) ?></div>
            <div class="fw-semibold mb-1"><?= htmlspecialchars($note['judul']) ?></div>
            <p class="small text-muted mb-2"><?= nl2br(htmlspecialchars(substr($note['isi'], 0, 100))) ?>...</p>
          </div>


          <!-- Modal Edit Catatan -->
          <div class="modal fade" id="modalEditCatatan<?= $note['id'] ?>" tabindex="-1" aria-labelledby="modalEditCatatanLabel<?= $note['id'] ?>" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <form action="customer_catatan_edit" method="POST">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalEditCatatanLabel<?= $note['id'] ?>">Edit Catatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $note['id'] ?>">
                    <input type="hidden" name="customer_id" value="<?= $id ?>">
                    <div class="mb-3">
                      <label class="form-label">Judul</label>
                      <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($note['judul']) ?>" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Isi Catatan</label>
                      <textarea name="isi" class="form-control" rows="4" required><?= htmlspecialchars($note['isi']) ?></textarea>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
      </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("editCustomerForm");

    form.addEventListener("submit", function (e) {
      e.preventDefault(); // MENCEGAH SUBMIT BIASA

      const formData = new FormData(form);

      fetch("customer_update", {
        method: "POST",
        body: formData
      })
      .then(res => res.text())
      .then(response => {
        if (response.trim() === "success") {
          const modal = bootstrap.Modal.getInstance(document.getElementById("editCustomerModal"));
          modal.hide();
          location.reload();
        } else {
          alert("Gagal memperbarui data: " + response);
        }
      })
      .catch(err => {
        console.error("Error:", err);
        alert("Terjadi kesalahan jaringan.");
      });
    });
  });
</script>

</body>
</html>