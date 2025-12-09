<?php
require_once __DIR__ . '/../../includes/db.php';
$id_pasien = intval($_GET['id'] ?? 0);

$q = $conn->query("
  SELECT t.tanggal, l.nama_layanan, t.total
  FROM transaksi t
  LEFT JOIN detail_transaksi d ON t.id_transaksi = d.id_transaksi
  LEFT JOIN layanan l ON d.id_layanan = l.id_layanan
  WHERE t.id_pasien = $id_pasien
  ORDER BY t.tanggal DESC
");
?>
<div class="card shadow-sm">
  <div class="card-body">
    <h6 class="text-pink fw-bold mb-3"><i class="bi bi-clock-history"></i> Riwayat Pasien</h6>
    <?php if ($q->num_rows > 0): ?>
      <table class="table table-sm align-middle">
        <thead class="table-pink text-center">
          <tr>
            <th>Tanggal</th>
            <th>Layanan</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php while($r = $q->fetch_assoc()): ?>
            <tr>
              <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
              <td><?= htmlspecialchars($r['nama_layanan']) ?></td>
              <td>Rp<?= number_format($r['total'], 0, ',', '.') ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="text-muted">Belum ada riwayat tindakan.</div>
    <?php endif; ?>
  </div>
</div>
