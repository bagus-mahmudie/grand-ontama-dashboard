<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm p-3 mb-4 rounded">
  <div class="container-fluid">

    <!-- Logo kiri -->
    <a class="navbar-brand fw-bold" href="#">
      <img src="assets/img/logo.png" alt="Logo" height="40">
    </a>

    <!-- Toggle button for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNavbar" aria-controls="mobileNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>


    <!-- Offcanvas Navbar -->
    <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="mobileNavbar" aria-labelledby="mobileNavbarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileNavbarLabel">
          <img src="assets/img/logo.png" alt="Logo" style="width: 55px; height: 40px;">
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
      </div>
      <div class="offcanvas-body d-flex flex-column h-100">
        <ul class="nav flex-column">
          <li class="nav-item">
          <a class="nav-link ps-0 <?= ($activePage == 'dashboard') ? 'active text-light fw-semibold' : '' ?>" href="dashboard">Dashboard</a>
        </li><hr>
        <li class="nav-item">
          <a class="nav-link ps-0 <?= ($activePage == 'properti') ? 'active text-light fw-semibold' : '' ?>" href="properti">Properti</a>
        </li><hr>
        <li class="nav-item">
          <a class="nav-link ps-0 <?= ($activePage == 'pemesanan') ? 'active text-light fw-semibold' : '' ?>" href="pemesanan">Pemesanan</a>
        </li><hr>
        <li class="nav-item">
          <a class="nav-link ps-0 <?= ($activePage == 'customers') ? 'active text-light fw-semibold' : '' ?>" href="customers">Customer</a>
        </li><hr>
        <li class="nav-item">
          <a class="nav-link ps-0 <?= ($activePage == 'agen') ? 'active text-light fw-semibold' : '' ?>" href="agen">Agen</a>
        </li><hr>
        
        </ul>
       <div class="mt-auto pt-4 border-top">
          <div class="d-flex align-items-center gap-2">
            <img src="assets/img/uploads/profile1.jpg" class="rounded-circle" style="width: 40px; height: 40px;">
            <div class="flex-grow-1">
              <strong>Hi, <?= htmlspecialchars($_SESSION['admin_name']) ?></strong><br>
              <small><?= htmlspecialchars($_SESSION['admin_email'] ?? '-') ?></small>
            </div>
            <!-- Tombol Logout -->
            <a href="logout" class="btn btn-sm btn-outline-danger ms-auto" title="Keluar">
              <i class="bi bi-box-arrow-right"></i>
            </a>
          </div>
        </div>

      </div>
    </div>


    <!-- Menu center & profile kanan -->
    <div class="collapse navbar-collapse justify-content-between" id="navbarContent">

      <!-- Menu navigasi di tengah -->
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-3">
        <li class="nav-item">
          <a class="nav-link <?= ($activePage == 'dashboard') ? 'active text-light fw-semibold' : '' ?>" href="dashboard">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($activePage == 'properti') ? 'active text-light fw-semibold' : '' ?>" href="properti">Properti</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($activePage == 'pemesanan') ? 'active text-light fw-semibold' : '' ?>" href="pemesanan">Pemesanan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($activePage == 'customers') ? 'active text-light fw-semibold' : '' ?>" href="customers">Customer</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($activePage == 'agen') ? 'active text-light fw-semibold' : '' ?>" href="agen">Agen</a>
        </li>
      </ul>


      <!-- Notifikasi & Profile di kanan -->
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-light position-relative">
          🔔
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">1</span>
        </button>
        <div class="dropdown">
          <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
            <img src="assets/img/admin.png" alt="profile" width="40" height="40" class="rounded-circle me-2">
            <span>Hi, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">Profil</a></li>
            <li><a class="dropdown-item" href="admin_list">Pengaturan</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout">Keluar</a></li>
          </ul>
        </div>
      </div>

    </div>
    
  </div>
</nav>

