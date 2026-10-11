<?php

include 'db.php';

$units = $conn->query("SELECT DISTINCT unit FROM properti ORDER BY unit");
$daftar_agen = mysqli_query($conn, "SELECT id, nama_lengkap FROM agen WHERE status = 'Aktif'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | Tambah Pemesanan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="assets/img/favicon.jpg" sizes="32x32" type="image/jpg">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .form-container {
      max-width: 700px;
      margin: 2rem auto;
      background: #fff;
      padding: 2rem;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }
    .section-divider {
      border-top: 1px dashed #ccc;
      margin: 1.5rem 0;
    }
  </style>
</head>

<body>
<div class="form-container">
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success text-center">
      Pesanan berhasil ditambahkan.
    </div>
  <?php endif; ?>

  <h5 class="mb-3">Tambah Pemesanan</h5>
  <form action="pemesanan_simpan" method="post">
    
    <h6>Data Customer</h6>
    <div class="row">
      <div class="col-md-6 mb-2">
        <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap" required>
      </div>
      <div class="col-md-6 mb-2">
        <input type="text" name="no_hp" class="form-control" placeholder="No HP" required>
      </div>
      <div class="col-md-6 mb-2">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
      </div>
      <div class="col-md-6 mb-2">
        <input type="text" name="nik" class="form-control" placeholder="NIK" required>
      </div>
    </div>

    <div class="section-divider"></div>

    <div class="mb-3">
      <label class="form-label">Pilih Unit</label>
      <select id="unit" class="form-select" required>
        <option value="">-- Pilih Unit --</option>
        <?php while($u = $units->fetch_assoc()): ?>
          <option value="<?= $u['unit'] ?>"><?= $u['unit'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Nomor Unit</label>
      <select name="properti_id" id="nomor_unit" class="form-select" required>
        <option value="">-- Pilih Nomor Unit --</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Metode Pembayaran</label>
      <select name="metode_pembayaran" class="form-select" required>
        <option value="">-- Pilih Metode --</option>
        <option value="Cash Keras">Cash Keras</option>
        <option value="Cash Bertahap">Cash Bertahap</option>
        <option value="KPR">KPR</option>
      </select>
    </div>

    <!-- Form Khusus KPR -->
    <div class="mb-3" id="kprFields" style="display: none;">
      <label class="form-label">Lama Cicilan</label>
      <select name="lama_cicilan" class="form-select">
        <option value="">-- Pilih Lama Cicilan --</option>
        <option value="5">5 Tahun</option>
        <option value="10">10 Tahun</option>
        <option value="15">15 Tahun</option>
      </select>
      <br>
      <label class="form-label">Pilih Bank</label>
      <select name="bank_kpr" class="form-select">
        <option value="">-- Pilih Bank --</option>
        <option value="BSI">PKS</option>
        <option value="OCBC">OCBC</option>
        <option value="Panin">Panin</option>
        <option value="BTN">BTN</option>
        <option value="BSI">BSI</option>
      </select>
    </div>


<h6 class="mt-4">Agen Properti</h6>
<div class="row">
    <!-- Kolom Pilih Agen Lama -->
    <div class="col-md-6 mb-3">
        <label for="existing_agen_id" class="form-label">Pilih Agen yang Sudah Ada</label>
        <select name="existing_agen_id" id="existing_agen_id" class="form-control">
            <option value="">-- Pilih Agen --</option>
            <?php
            $daftar_agen = mysqli_query($conn, "SELECT id, nama_lengkap FROM agen WHERE status = 'Aktif'");
            while ($row = mysqli_fetch_assoc($daftar_agen)):
            ?>
                <option value="<?= $row['id']; ?>"><?= $row['nama_lengkap']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- Tombol Toggle Agen Baru -->
    <div class="col-md-6 mb-3 d-flex align-items-end">
        <button type="button" id="toggleAgenBaru" class="btn btn-outline-primary w-100">+ Tambah Agen Baru</button>
    </div>
</div>

<!-- Form Agen Baru (disembunyikan awalnya) -->
<div id="formAgenBaru" style="display: none;">
    <h6 class="mt-3">Form Agen Baru</h6>
    <div class="row">
        <div class="col-md-6 mb-3">
            <input type="text" name="agen_nama_lengkap" class="form-control" placeholder="Nama Agen">
        </div>
        <div class="col-md-6 mb-3">
            <input type="text" name="agen_no_hp" class="form-control" placeholder="No HP">
        </div>
        <div class="col-md-6 mb-3">
            <input type="email" name="agen_email" class="form-control" placeholder="Email">
        </div>
        <div class="col-md-6 mb-3">
            <select name="agen_tipe_agen" class="form-control">
                <option value="">-- Pilih Status Agen --</option>
                <option value="Internal">Internal</option>
                <option value="Eksternal">Eksternal</option>
            </select>
        </div>
    </div>
</div>




    <div class="mb-3">
      <label class="form-label">Harga Deal</label>
      <input type="number" name="harga_deal" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Status Pemesanan</label>
      <select name="status_pemesanan" class="form-select" required>
        <option value="">-- Pilih Status --</option>
        <option value="Booking">Booking</option>
        <option value="Terjual">Terjual</option>
        <option value="Terjual Eksternal">Terjual Eksternal</option>
        <option value="Batal">Batal</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Tanggal Pemesanan</label>
      <input type="date" name="tanggal_pesan" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Simpan Pesanan</button>
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
  document.addEventListener("DOMContentLoaded", function() {
      const toggleBtn = document.getElementById("toggleAgenBaru");
      const formBaru = document.getElementById("formAgenBaru");
      const agenLamaSelect = document.getElementById("existing_agen_id");

      toggleBtn.addEventListener("click", function () {
          formBaru.style.display = formBaru.style.display === "none" ? "block" : "none";
          if (formBaru.style.display === "block") {
              agenLamaSelect.value = ""; // reset dropdown agen lama jika buka agen baru
          }
      });

      agenLamaSelect.addEventListener("change", function () {
          if (this.value !== "") {
              formBaru.style.display = "none"; // sembunyikan form agen baru jika pilih agen lama
          }
      });
  });
</script>
<script>
$(document).ready(function() {
  $('select[name="metode_pembayaran"]').change(function() {
    if ($(this).val() === 'KPR') {
      $('#kprFields').slideDown();
      $('#kprFields select').attr('required', true);
    } else {
      $('#kprFields').slideUp();
      $('#kprFields select').val('').attr('required', false);
    }
  });
});
</script>


</body>
</html>
