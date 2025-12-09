<?php 
if (!isset($_SESSION)) session_start();

// Koneksi database
require_once __DIR__ . '/db.php';

// Ambil data klinik dari tabel pengaturan_klinik
$q = mysqli_query($conn, "SELECT * FROM pengaturan_klinik LIMIT 1");
$klinik = mysqli_fetch_assoc($q);

// Jika belum ada data, gunakan default
if (!$klinik) {
  $klinik = [
    'nama_klinik' => 'Klinik Cantikku',
    'logo' => 'default_logo.png'
  ];
}

// Tentukan lokasi file logo di folder /uploads/
$logo_filename = $klinik['logo'] ?? 'default_logo.png';
$logo_path = '../uploads/' . $logo_filename;

// Jika file logo tidak ditemukan, gunakan default
if (!file_exists(__DIR__ . '/../uploads/' . $logo_filename)) {
  $logo_path = '../uploads/default_logo.png';
}
?>

<nav class="d-flex flex-column flex-shrink-0 p-3 bg-pink" 
     style="width: 250px; height: 100vh; position: fixed; border-right: 2px solid #f0a3b1;">
  
  <!-- Header Sidebar -->
  <div class="sidebar-header text-center mb-3">
    <img src="<?= htmlspecialchars($logo_path); ?>" 
         alt="Logo Klinik" 
         class="img-fluid mb-2 border border-2 border-light shadow-sm" 
         style="max-width: 150px; height: auto;">
  </div>
  <hr>

  <!-- Menu Navigasi -->
  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item">
      <a href="dashboard.php" 
         class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : 'text-dark'; ?>">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
      </a>
    </li>

    <li><a href="#" class="nav-link text-dark menu-link" data-page="layanan.php"><i class="bi bi-heart-pulse me-2"></i> Layanan</a></li>
    <li><a href="#" class="nav-link text-dark menu-link" data-page="terapis.php"><i class="bi bi-person-badge me-2"></i> Terapis</a></li>
	<li><a href="#" class="nav-link text-dark menu-link" data-page="pasien.php"><i class="bi bi-people-fill me-2"></i> Pasien</a></li>
    <li><a href="#" class="nav-link text-dark menu-link" data-page="transaksi.php"><i class="bi bi-receipt me-2"></i> Transaksi</a></li>
    <li><a href="#" class="nav-link text-dark menu-link" data-page="laporan.php"><i class="bi bi-graph-up me-2"></i> Laporan</a></li>
    <li><a href="#" class="nav-link text-dark menu-link" data-page="pengaturan_klinik.php"><i class="bi bi-gear"></i> Pengaturan Klinik</a></li>
  </ul>

  <hr>

  <!-- Profil User -->
  <div class="dropdown">
    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" 
       id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
      <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png" 
           alt="User" width="32" height="32" class="rounded-circle me-2 border border-dark">
      <strong><?= $_SESSION['nama'] ?? 'Admin'; ?></strong>
    </a>
    <ul class="dropdown-menu dropdown-menu-light text-small shadow" aria-labelledby="dropdownUser">
      <li><a class="dropdown-item" href="#">Profil</a></li>
      <li><a class="dropdown-item" href="#">Pengaturan</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item text-danger" href="../logout.php">
        <i class="bi bi-box-arrow-right"></i> Keluar
      </a></li>
    </ul>
  </div>
</nav>

<style>
  .bg-pink { background-color: #ffe6ef !important; }
  .text-pink { color: #e75480 !important; }
  .nav-link.active {
    background-color: #e75480 !important;
    color: white !important;
    border-radius: 10px;
  }
  .nav-link:hover {
    background-color: #f8b8ca !important;
    color: #fff !important;
  }
</style>
