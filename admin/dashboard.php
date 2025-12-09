<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
  header("Location: ../index.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - Klinik Cantikku</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- CSS Kustom -->
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
    body {
      background-color: #f9d2df;
      font-family: 'Poppins', sans-serif;
      overflow-x: hidden;
    }

    .main-content {
      margin-left: 250px; /* lebar sidebar */
      padding: 30px;
      min-height: 100vh;
    }

    .topbar {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
    }

    .topbar h5 {
      color: #e75480;
      font-weight: 600;
    }

    .content-card {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 992px) {
      .main-content {
        margin-left: 0;
      }
      nav {
        position: relative !important;
        width: 100% !important;
        height: auto !important;
      }
    }

  </style>
</head>

<body>

  <!-- Sidebar -->
  <?php include  '../includes/sidebar.php'; ?>

  <!-- Konten utama -->
  <div class="main-content">
    <div class="topbar">
      <h5><i class="bi bi-speedometer2 me-2"></i> Dashboard Admin</h5>
      <div>
        <i class="bi bi-calendar3 text-muted me-2"></i>
        <small class="text-muted"><?= date('l, d F Y'); ?></small>
      </div>
    </div>

    <div class="content-card" id="page-content">
      <?php include_once __DIR__ . '/pages/dashboard_home.php'; ?>
    </div>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  
  <!-- SweetAlert2 (WAJIB untuk halaman pasien) -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 
  <!-- Bootstrap Bundle (sudah termasuk Popper) -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<script>
$(document).ready(function () {
  // Klik menu sidebar
  $(".nav-link[data-page]").on("click", function (e) {
    e.preventDefault();
    const page = $(this).data("page");

    // Ubah status aktif
    $(".nav-link").removeClass("active bg-white text-pink");
    $(this).addClass("active bg-white text-pink");

    // Animasi loading
    $("#page-content").html(`
      <div class="text-center py-5">
        <div class="spinner-border text-pink"></div>
        <p class="mt-2">Memuat...</p>
      </div>
    `);

    // Load konten via AJAX
    $("#page-content").load("pages/" + page, function () {
      // Jalankan script tambahan jika perlu
      if (page === "transaksi.php" && typeof initGrafikTransaksi === "function") {
        // Delay sedikit agar canvas sempat dimuat
        setTimeout(() => initGrafikTransaksi(), 200);
      }
    });
  });
});
</script>

<!-- Tetap biarkan ini di bawah -->
<script>
const BASE_URL = "<?php echo '/pos_klinik_cantikku/'; ?>";
</script>

<script>
$(document).on("hidden.bs.modal", function () {
  $(".modal-backdrop").remove();
  $("body").removeClass("modal-open");
});
</script>
  
</body>
</html>
