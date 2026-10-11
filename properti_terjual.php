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
    <title>Dashboard | Properti Terjual</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
include 'navbar.php'; ?>

<div class="container-fluid px-3">

<?php 

include 'db.php';
// Pagination setup
$perPage = 9;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Filter properti
$where = [];
if (!empty($_GET['unit'])) $where[] = "unit = '" . $conn->real_escape_string($_GET['unit']) . "'";
if (!empty($_GET['nomor_unit'])) $where[] = "nomor_unit = '" . $conn->real_escape_string($_GET['nomor_unit']) . "'";
if (!empty($_GET['lb_lt'])) $where[] = "lb_lt = '" . $conn->real_escape_string($_GET['lb_lt']) . "'";
if (!empty($_GET['hadap'])) $where[] = "hadap = '" . $conn->real_escape_string($_GET['hadap']) . "'";
if (!empty($_GET['tipe'])) $where[] = "tipe = '" . $conn->real_escape_string($_GET['tipe']) . "'";
if (!empty($_GET['nama_tipe'])) $where[] = "nama_tipe = '" . $conn->real_escape_string($_GET['nama_tipe']) . "'";
if (!empty($_GET['luas_bangunan'])) $where[] = "luas_bangunan = " . intval($_GET['luas_bangunan']);
if (!empty($_GET['luas_lahan'])) $where[] = "luas_lahan = " . intval($_GET['luas_lahan']);

// Hitung total untuk pagination
$sqlCount = "SELECT COUNT(*) as total FROM properti WHERE status IN ('Terjual', 'Terjual Eksternal')";
if (count($where)) {
    $sqlCount .= " AND " . implode(" AND ", $where);
}
$totalResult = $conn->query($sqlCount)->fetch_assoc();
$totalItems = $totalResult['total'];
$totalPages = ceil($totalItems / $perPage);
 ?>

<?php
function getDistinct($conn, $field) {
    $data = [];
    $res = $conn->query("SELECT DISTINCT $field FROM properti ORDER BY $field ASC");
    while ($row = $res->fetch_assoc()) $data[] = $row[$field];
    return $data;
}

$unitList = getDistinct($conn, 'unit');
$hadapList = getDistinct($conn, 'hadap');
?>

<div class="filter-bar mb-4">
    <h5 class="mb-3" style="font-size:20px; font-weight:bold;">Cari Properti</h5>

  <form method="GET" action="" id="filter-form">
    <div class="filter-row">
      <div>
        <label>Unit</label>
        <select name="unit" id="filter-unit">
          <option value="">Silahkan Pilih</option>
          <?php foreach ($unitList as $unit): ?>
            <option value="<?= $unit ?>" <?= ($_GET['unit'] ?? '') === $unit ? 'selected' : '' ?>><?= $unit ?></option>
          <?php endforeach; ?>
        </select>
      </div>
 <div>
        <label>Nomor Unit</label>
        <select name="nomor_unit" id="filter-nomor_unit"><option value="">Silahkan Pilih</option></select>
      </div>
      
      <div>
        <label>LB/LT</label>
        <select name="lb_lt" id="filter-lblt"><option value="">Silahkan Pilih</option></select>
      </div>
      <div>
        <label>Nama Tipe</label>
        <select name="nama_type" id="filter-nama_type"><option value="">Silahkan Pilih</option></select>
      </div>

      <div>
        <label>Tipe</label>
        <select name="type" id="filter-type"><option value="">Silahkan Pilih</option></select>
      </div>

     


      <div>
        <label>Hadap</label>
        <select name="hadap" onchange="document.getElementById('filter-form').submit()">
          <option value="">Silahkan Pilih</option>
          <?php foreach ($hadapList as $hadap): ?>
            <option value="<?= $hadap ?>" <?= ($_GET['hadap'] ?? '') === $hadap ? 'selected' : '' ?>><?= $hadap ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="align-self: flex-end; ">
        <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>"><button type="button" style="border-radius:10px;">Reset</button></a>
      </div>
    </div>
  </form>
</div>

<script>
function autoSubmit() {
  document.getElementById('filter-form').submit();
}

document.querySelectorAll('#filter-form select').forEach(select => {
  if (select.id !== "filter-hadap") {
    select.addEventListener('change', autoSubmit);
  }
});

// Dinamis berdasarkan unit
function loadDependentFilters() {
  const unit = document.getElementById('filter-unit').value;
  if (!unit) return;

  fetch('get_filter_options?unit=' + encodeURIComponent(unit))
    .then(res => res.json())
    .then(data => {
        const nomorUnit = document.getElementById('filter-nomor_unit');
        const lblt = document.getElementById('filter-lblt');
        const type = document.getElementById('filter-type');
      const namaType = document.getElementById('filter-nama_type');

      const fields = [
          {select: nomorUnit, data: data.nomor_unit, selected: '<?= $_GET['nomor_unit'] ?? '' ?>'},
          {select: lblt, data: data.lb_lt, selected: '<?= $_GET['lb_lt'] ?? '' ?>'},
          {select: type, data: data.type, selected: '<?= $_GET['type'] ?? '' ?>'},
          {select: namaType, data: data.nama_type, selected: '<?= $_GET['nama_type'] ?? '' ?>'},
      ];

      fields.forEach(f => {
        f.select.innerHTML = '<option value="">Silahkan Pilih</option>';
        f.data.forEach(val => {
          f.select.innerHTML += `<option value="${val}" ${val === f.selected ? 'selected' : ''}>${val}</option>`;
        });
      });
    });
}

// Load saat pertama kali
window.addEventListener('load', loadDependentFilters);
</script>


  <!-- Grid Properti -->
  <div class="row g-4" style="background-color: #fff; padding: 0px 10px; margin:20px 2px; border-radius: 1rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);">
    <div  class="mb-4 d-flex align-items-center" style="margin-bottom: 0px;">
      <a href="properti" class="btn btn-light rounded border">
            <i class="bi bi-arrow-left"></i>
      </a>
      <h4 class="mb-3" style="font-size:20px; font-weight:bold; margin-top:20px; margin-left:15px;">Properti Terjual</h4>
    </div>
    <?php
        include 'db.php';

        // Ambil filter dari URL
        $where = [];
        if (!empty($_GET['unit'])) $where[] = "unit = '" . $conn->real_escape_string($_GET['unit']) . "'";
        if (!empty($_GET['nomor_unit'])) $where[] = "nomor_unit = '" . $conn->real_escape_string($_GET['nomor_unit']) . "'";
        if (!empty($_GET['lb_lt'])) $where[] = "lb_lt = '" . $conn->real_escape_string($_GET['lb_lt']) . "'";
        if (!empty($_GET['hadap'])) $where[] = "hadap = '" . $conn->real_escape_string($_GET['hadap']) . "'";
        if (!empty($_GET['tipe'])) $where[] = "tipe = '" . $conn->real_escape_string($_GET['tipe']) . "'";
        if (!empty($_GET['nama_tipe'])) $where[] = "nama_tipe = '" . $conn->real_escape_string($_GET['nama_tipe']) . "'";
        if (!empty($_GET['luas_bangunan'])) $where[] = "luas_bangunan = " . intval($_GET['luas_bangunan']);
        if (!empty($_GET['luas_lahan'])) $where[] = "luas_lahan = " . intval($_GET['luas_lahan']);

        $sql = "SELECT * FROM properti WHERE status IN ('Terjual', 'Terjual Eksternal')";
        if (count($where)) {
            $sql .= " AND " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY id ASC LIMIT $offset, $perPage";


        $result = $conn->query($sql);
        if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>


  <div class="col-md-6 col-lg-4 mb-4">
    <a href="properti_detail?id=<?= $row['id'] ?>" class="text-decoration-none text-dark">
        <div class="card property-card h-100">
            <div class="row g-0 h-100">
                <div class="col-md-5">
                    <img src="assets/img/<?= htmlspecialchars($row['gambar']) ?>" class="property-image img-fluid h-100 w-100" alt="<?= $row['unit'] ?>">
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
                        <h5 class="card-title"><?= $row['unit'] . ' ' . $row['nomor_unit'] ?></h5>
                        <p class="card-text"><?= $row['deskripsi'] ?></p>
                        <div class="mt-auto pt-2 pb-3 property-info-bar d-flex justify-content-between small">
                            <span><i class="bi bi-door-closed"></i> <?= $row['kamar_tidur'] ?> Bedroom</span>
                            <span><i class="bi bi-droplet"></i> <?= $row['kamar_mandi'] ?> Bathroom</span>
                            <span><i class="bi bi-bounding-box"></i> <?= $row['luas_m2'] ?> m²</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    </div>
<?php
    endwhile;
else:
?>
    <p class="text-muted">Tidak ada properti tersedia.</p>
<?php endif; ?>


  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap Icons (optional) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php if ($totalPages > 1): ?>
  <div class="d-flex justify-content-center overflow-auto mt-4 mb-4">
    <ul class="pagination flex-nowrap mb-0">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
          <a class="page-link px-3 py-2 <?= $i == $page ? 'bg-primary text-white' : '' ?>" 
             href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
             style="min-width: 40px; border-radius: 8px; margin: 0 4px;">
            <?= $i ?>
          </a>
        </li>
      <?php endfor; ?>
    </ul>
  </div>
<?php endif; ?>

</body>


</html>
