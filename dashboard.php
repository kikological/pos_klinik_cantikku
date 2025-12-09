<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
  header("Location: login.php");
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
  

  <!-- Icon -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Custom Style -->
  <style>
    :root {
      --pink-soft: #f8c9d4;
      --pink-light: #fde4ec;
      --pink-dark: #e49ab0;
      --text-dark: #444;
    }
    body {
      background-color: var(--pink-light);
      font-family: 'Poppins', sans-serif;
    }
    .sidebar {
      width: 250px;
      height: 100vh;
      position: fixed;
      background-color: var(--pink-dark);
      color: #fff;
      transition: all 0.3s;
    }
    .sidebar .nav-link {
      color: #fff;
      font-weight: 500;
    }
    .sidebar .nav-link.active, 
    .sidebar .nav-link:hover {
      background-color: var(--pink-soft);
      color: #333;
    }
    .topbar {
      background-color: #fff;
      height: 60px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      padding: 10px 20px;
      margin-left: 250px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    #content {
      margin-left: 250px;
      padding: 30px;
    }
    .btn-pink {
      background-color: var(--pink-dark);
      color: white;
    }
    .btn-pink:hover {
      background-color: var(--pink-soft);
      color: #333;
    }
    .sidebar-header {
      text-align: center;
      padding: 20px 0;
      background-color: var(--pink-soft);
      color: #333;
      font-weight: bold;
      font-size: 1.2rem;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar d-flex flex-column p-3">
    <div class="sidebar-header">
      💖 Klinik Cantikku
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a href="#" class="nav-link menu-link active" data-page="pages/admin/dashboard_home.php">
          <i class="bi bi-house"></i> Dashboard
        </a>
      </li>
      <li>
        <a href="#" class="nav-link menu-link" data-page="pages/admin/layanan.php">
          <i class="bi bi-heart"></i> Layanan & Harga
        </a>
      </li>
      <li>
        <a href="#" class="nav-link menu-link" data-page="pages/admin/laporan.php">
          <i class="bi bi-bar-chart"></i> Laporan Transaksi
        </a>
      </li>
      <li>
        <a href="#" class="nav-link menu-link" data-page="pages/admin/terapis.php">
          <i class="bi bi-people"></i> Data Terapis
        </a>
      </li>
      <li>
        <a href="#" class="nav-link menu-link" data-page="pages/admin/pengaturan.php">
          <i class="bi bi-gear"></i> Pengaturan Klinik
        </a>
      </li>
    </ul>

    <hr>
    <div class="mt-auto">
      <a href="logout.php" class="btn btn-light w-100">
        <i class="bi bi-box-arrow-right"></i> Logout
      </a>
    </div>
  </div>

  <!-- Topbar -->
  <div class="topbar">
    <h5 class="m-0">Dashboard Admin</h5>
    <span>👩‍💼 <?= $_SESSION['user_name'] ?? 'Admin' ?></span>
  </div>

  <!-- Content -->
  <main id="content">
    <!-- Halaman dimuat lewat AJAX -->
    <div class="text-center mt-5">
      <div class="spinner-border text-danger" role="status">
        <span class="visually-hidden">Memuat...</span>
      </div>
      <p>Memuat halaman...</p>
    </div>
  </main>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <!-- Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Script AJAX loadPage -->
  <script>
    function loadPage(page) {
      $("#content").html(`
        <div class='text-center mt-5'>
          <div class='spinner-border text-danger' role='status'></div>
          <p>Memuat halaman...</p>
        </div>
      `);
      $.get(page, function(data) {
        $("#content").html(data);
      }).fail(function() {
        $("#content").html("<div class='alert alert-danger'>Gagal memuat halaman.</div>");
      });
    }

    $(document).ready(function() {
      // Halaman awal
      loadPage('pages/admin/dashboard_home.php');

      // Navigasi klik menu
      $(".menu-link").click(function(e) {
        e.preventDefault();
        $(".menu-link").removeClass("active");
        $(this).addClass("active");
        const page = $(this).data("page");
        loadPage(page);
      });
    });
  </script>

</body>
</html>
