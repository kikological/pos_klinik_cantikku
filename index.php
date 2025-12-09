<?php
session_start();

// Panggil koneksi dan pengaturan klinik
require_once __DIR__ . '/includes/db.php'; 
require_once __DIR__ . '/includes/setting_klinik.php'; // Ambil data dari tabel pengaturan_klinik

// Jika sudah login, arahkan ke dashboard sesuai role
if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] == 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    } elseif ($_SESSION['user_role'] == 'terapis') {
        header("Location: terapis/dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - <?= htmlspecialchars($klinik['nama_klinik'] ?? 'Klinik Cantikku'); ?></title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      background: linear-gradient(135deg, #ffd6e7, #ffe6f0);
      font-family: 'Poppins', sans-serif;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      padding: 40px;
      width: 100%;
      max-width: 400px;
    }
    .btn-pink {
      background-color: #e75480;
      color: white;
      transition: 0.3s;
    }
    .btn-pink:hover {
      background-color: #d24573;
      color: #fff;
    }
    .logo-img {
      width: 100px;
      height: auto;
      margin-bottom: 15px;
    }
    .logo-text {
      font-weight: 600;
      color: #e75480;
    }
  </style>
</head>
<body>

  <div class="login-card text-center">
    <!-- LOGO dari tabel pengaturan_klinik -->
    <?php if (!empty($klinik['logo'])): ?>
      <img src="uploads/<?= htmlspecialchars($klinik['logo']); ?>" alt="Logo Klinik" class="logo-img">
    <?php else: ?>
      <i class="bi bi-heart-fill text-pink fs-1"></i>
    <?php endif; ?>

    <h3 class="logo-text mb-3"><?= htmlspecialchars($klinik['nama_klinik'] ?? 'Klinik Cantikku'); ?></h3>

    <form id="loginForm">
      <div class="mb-3 text-start">
        <label class="form-label fw-semibold">Username</label>
        <input type="text" class="form-control" name="username" required placeholder="Masukkan username">
      </div>

      <div class="mb-4 text-start">
        <label class="form-label fw-semibold">Password</label>
        <input type="password" class="form-control" name="password" required placeholder="Masukkan password">
      </div>

      <button type="submit" class="btn btn-pink w-100 fw-semibold">Login</button>
    </form>

    <p class="mt-4 text-muted small">© <?= date('Y'); ?> <?= htmlspecialchars($klinik['nama_klinik'] ?? 'Klinik Cantikku'); ?></p>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <script>
  $(document).ready(function() {
    $("#loginForm").on("submit", function(e) {
      e.preventDefault();

      $.ajax({
        type: "POST",
        url: "proses_login.php",
        data: $(this).serialize(),
        dataType: "json",
        success: function(response) {
          if (response.status === "ok") {
            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: response.message
            }).then(() => {
              window.location.href = response.redirect;
            });
          } else {
            Swal.fire("Gagal!", response.message, "error");
          }
        },
        error: function(xhr, status, error) {
          Swal.fire("Gagal!", "Tidak dapat menghubungi server.", "error");
          console.log(xhr.responseText);
        }
      });
    });
  });
  </script>
</body>
</html>
