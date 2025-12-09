<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'terapis') {
    header("Location: ../login.php");
    exit;
}
$version = time(); // cache-buster
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Terapis - Klinik Cantikku</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/style.css?v=<?= $version ?>" rel="stylesheet">
  <style>
    body { background-color: #fff5f7; color: #444; }
    .sidebar { background: #ffd6e0; height: 100vh; position: fixed; width: 250px; }
    .sidebar a { color: #d63384; text-decoration: none; display: block; padding: 12px 20px; }
    .sidebar a:hover, .sidebar a.active { background: #f8bbd0; border-radius: 8px; }
    .content { margin-left: 250px; padding: 20px; }
    .btn-pink { background-color: #d63384; color: white; }
    .btn-pink:hover { background-color: #c2185b; color: white; }
  </style>
</head>
<body>
  <div class="d-flex">
    <!-- SIDEBAR -->
    <div class="sidebar p-3 d-flex flex-column justify-content-between">
      <div>
        <h5 class="text-center fw-bold mb-4 text-pink">🌸 Terapis Panel</h5>
        <a href="#" class="active" onclick="loadPage('home')"><i class="bi bi-house"></i> Dashboard</a>
        <a href="#" onclick="loadPage('cari_pasien')"><i class="bi bi-search-heart"></i> Cari Pasien</a>
		
      </div>

      <div class="border-top pt-3">
        <div class="dropdown text-center">
          <a href="#" class="dropdown-toggle text-decoration-none text-pink" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['nama'] ?? 'Terapis'); ?>
          </a>
          <ul class="dropdown-menu">
			<button class="btn btn-pink" onclick="pairBluetoothPrinter()">
    🔗 		Pair Printer Bluetooth
			</button>
            <li><a class="dropdown-item" href="#" onclick="loadPage('profile')">Profil</a></li>
            <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Keluar</a></li>
			
          </ul>
        </div>
      </div>
    </div>

    <!-- KONTEN -->
    <div class="content flex-grow-1" id="content">
      <div class="text-center p-5">
        <h3>Selamat Datang, <?= htmlspecialchars($_SESSION['nama'] ?? 'Terapis'); ?> 💖</h3>
        <p>Pilih menu di sebelah kiri untuk memulai.</p>
      </div>
    </div>
  </div>

  <!-- SCRIPT -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="pages/input_tindakan.js?v=<?= $version ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
  function loadPage(page) {
    $("#content").html('<div class="text-center py-5"><div class="spinner-border text-pink"></div><p class="mt-2">Memuat...</p></div>');
    $.ajax({
      url: "pages/" + page + ".php?v=<?= $version ?>",
      success: function(res) {
        $("#content").html(res);
        $(".sidebar a").removeClass("active");
        $(`.sidebar a:contains(${page == 'home' ? 'Dashboard' : page == 'cari_pasien' ? 'Cari Pasien' : 'Riwayat'})`).addClass("active");
      },
      error: function() {
        $("#content").html('<div class="alert alert-danger">Gagal memuat halaman!</div>');
      }
    });
  }

  // Bersihkan semua cache jQuery AJAX agar tidak ambil versi lama
  $.ajaxSetup({ cache: false });
  </script>
  <script>
function pairBluetoothPrinter() {
    navigator.bluetooth.requestDevice({
        filters: [{ namePrefix: "RPP" }],
        optionalServices: [0xFFE0]
    })
    .then(device => {
        localStorage.setItem("bt_printer_id", device.id);
        Swal.fire("Berhasil!", "Printer RPP02N berhasil dipasangkan!", "success");
    })
    .catch(err => {
        Swal.fire("Gagal", "Tidak dapat menemukan printer RPP02N", "error");
        console.error(err);
    });
}
</script>
</body>
</html>
