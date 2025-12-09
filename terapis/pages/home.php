<?php
if (!isset($_SESSION)) session_start();
?>
<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="text-pink mb-3"><i class="bi bi-house"></i> Dashboard</h5>
    <div class="text-center p-4">
      <h4>Selamat Datang, <?= htmlspecialchars($_SESSION['nama'] ?? 'Terapis'); ?> 💖</h4>
      <p class="text-muted">Pilih menu Cari Pasien atau Riwayat Pasien dari sidebar.</p>
    </div>
  </div>
</div>
