<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}
include 'db.php';

$id = $_GET['id'] ?? 0;
$query = $conn->prepare("SELECT p.*, c.nama_lengkap, c.no_hp, c.email, c.nik, pr.unit, pr.nomor_unit FROM pemesanan p
JOIN customer c ON p.customer_id = c.id
JOIN properti pr ON p.properti_id = pr.id
WHERE p.id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$pem = $result->fetch_assoc();

$units = $conn->query("SELECT DISTINCT unit FROM properti ORDER BY unit");
$unit_aktif = $pem['unit'];
$unitOptions = $conn->query("SELECT id, nomor_unit FROM properti WHERE unit = '$unit_aktif'");
$daftar_agen = mysqli_query($conn, "SELECT id, nama_lengkap FROM agen WHERE status = 'Aktif'");
$pemesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pemesanan WHERE id = $_GET[id]"));
$agen_id_terpilih = $pemesanan['agen_id'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Pemesanan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="assets/img/favicon.jpg" sizes="32x32" type="image/jpg">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .form-container {
        max-width: 700px;
        margin: 1.5rem auto;
        background: #fff;
        padding: 1.5rem;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
      }

      @media (max-width: 576px) {
        .form-container {
          padding: 1rem;
          margin: 1rem;
        }

        .form-container .btn {
          width: 100%;
        }
        .form-control,
        .form-select {
          min-height: 42px;
        }
      }
  </style>
</head>

<body>
<div class="form-container p-0 rounded-4 overflow-hidden border ">
  <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom bg-light">
    <h5 class="m-0">Edit Pemesanan</h5>
    <button type="button" class="btn-close" aria-label="Tutup" onclick="history.back()"></button>
  </div>
  <div class="p-4">

  <form action="pemesanan_update" method="post">
    <input type="hidden" name="id" value="<?= $pem['id'] ?>">

    <label class="form-label">Data Customer</label>
    <div class="row">
      <div class="col-md-6 mb-2">
        <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap" value="<?= $pem['nama_lengkap'] ?>" required>
      </div>
      <div class="col-md-6 mb-2">
        <input type="text" name="no_hp" class="form-control" placeholder="No HP" value="<?= $pem['no_hp'] ?>" required>
      </div>
      <div class="col-md-6 mb-2">
        <input type="email" name="email" class="form-control" placeholder="Email" value="<?= $pem['email'] ?>" required>
      </div>
      <div class="col-md-6 mb-2">
        <input type="text" name="nik" class="form-control" placeholder="NIK" value="<?= $pem['nik'] ?>" required>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Pilih Unit</label>
      <select id="unit" class="form-select" required>
        <option value="">-- Pilih Unit --</option>
        <?php while($u = $units->fetch_assoc()): ?>
          <option value="<?= $u['unit'] ?>" <?= $u['unit'] == $unit_aktif ? 'selected' : '' ?>><?= $u['unit'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Nomor Unit</label>
      <select name="properti_id" id="nomor_unit" class="form-select" required>
        <?php while($nu = $unitOptions->fetch_assoc()): ?>
          <option value="<?= $nu['id'] ?>" <?= $nu['id'] == $pem['properti_id'] ? 'selected' : '' ?>><?= $nu['nomor_unit'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Metode Pembayaran</label>
      <select name="metode_pembayaran" class="form-select" required>
        <?php foreach (['Cash Keras', 'Cash Bertahap', 'KPR'] as $metode): ?>
          <option value="<?= $metode ?>" <?= $pem['metode_pembayaran'] == $metode ? 'selected' : '' ?>><?= $metode ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Field KPR: Muncul hanya jika KPR -->
    <div class="mb-3" id="kprFields" style="display:none;">
      <label class="form-label">Lama Cicilan</label>
      <select name="lama_cicilan" id="lama_cicilan" class="form-select">
        <option value="">-- Pilih Lama Cicilan --</option>
        <option value="5"  <?= ($pem['lama_cicilan'] == 5)  ? "selected" : "" ?>>5 Tahun</option>
        <option value="10" <?= ($pem['lama_cicilan'] == 10) ? "selected" : "" ?>>10 Tahun</option>
        <option value="15" <?= ($pem['lama_cicilan'] == 15) ? "selected" : "" ?>>15 Tahun</option>
      </select>
      <br>
      <label class="form-label">Pilih Bank</label>
      <select name="bank_kpr" id="bank_kpr" class="form-select">
        <option value="">-- Pilih Bank --</option>
        <option value="PKS"  <?= ($pem['nama_bank'] == "PKS")  ? "selected" : "" ?>>PKS</option>
        <option value="OCBC"  <?= ($pem['nama_bank'] == "OCBC")  ? "selected" : "" ?>>OCBC</option>
        <option value="Panin"  <?= ($pem['nama_bank'] == "Panin")  ? "selected" : "" ?>>Panin</option>
        <option value="BTN"  <?= ($pem['nama_bank'] == "BTN")  ? "selected" : "" ?>>BTN</option>
        <option value="BSI"  <?= ($pem['nama_bank'] == "BSI")  ? "selected" : "" ?>>BSI</option>
        <!-- Tambahkan bank lain jika perlu -->
      </select>
    </div>


    <div class="mb-3">
    <label for="agen_id" class="form-label">Pilih Agen Properti</label>
        <select name="agen_id" id="agen_id" class="form-select" required>
            <option value="">-- Pilih Agen --</option>
            <?php while($row = mysqli_fetch_assoc($daftar_agen)): ?>
                <option value="<?= $row['id']; ?>" <?= ($row['id'] == $agen_id_terpilih) ? 'selected' : '' ?>>
                    <?= $row['nama_lengkap']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Harga Deal</label>
      <input type="number" name="harga_deal" class="form-control" value="<?= $pem['harga_deal'] ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Status Pemesanan</label>
      <select name="status_pemesanan" class="form-select" required>
        <?php foreach (['Booking', 'Terjual', 'Terjual Eksternal'] as $stat): ?>
          <option value="<?= $stat ?>" <?= $pem['status_pemesanan'] == $stat ? 'selected' : '' ?>><?= $stat ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Tanggal Pemesanan</label>
      <input type="date" name="tanggal_pesan" class="form-control" value="<?= $pem['tanggal_pesan'] ?>" required>
    </div>

    <!-- <button type="submit" class="btn btn-success w-100">Update Pesanan</button> -->
    <div class="d-grid d-md-flex justify-content-md-end mt-4">
      <button type="submit" name="submit" class="btn btn-primary">Update Pesanan</button>
    </div>

  </form>
</div>

<script>
  $(document).ready(function() {
    $('#unit').on('change', function() {
      const unit = $(this).val();
      $('#nomor_unit').html('<option value="">Loading...</option>');
      if (unit) {
        $.get('get_nomor_unit', { unit: unit }, function(data) {
          let options = '<option value="">-- Pilih Nomor Unit --</option>';
          data.forEach(function(item) {
            options += `<option value="${item.id}">${item.nomor_unit}</option>`;
          });
          $('#nomor_unit').html(options);
        }, 'json');
      } else {
        $('#nomor_unit').html('<option value="">-- Pilih Nomor Unit --</option>');
      }
    });
  });
</script>
<script>
$(document).ready(function() {
  function toggleKPRFields() {
    var metode = $('[name="metode_pembayaran"]').val();
    if (metode === 'KPR') {
      $('#kprFields').slideDown();
      $('#kprFields select').attr('required', true);
    } else {
      $('#kprFields').slideUp();
      $('#kprFields select').val('').attr('required', false);
    }
  }
  // Saat load halaman, langsung cek (untuk mode edit)
  toggleKPRFields();
  // Saat select berubah
  $('[name="metode_pembayaran"]').change(toggleKPRFields);
});
</script>


</body>
</html>
