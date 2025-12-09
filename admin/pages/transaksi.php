<?php
require_once __DIR__ . '/../../includes/db.php';

// === Fungsi bantu: nama bulan Indonesia ===
function bulanIndo($bulan)
{
  $nama_bulan = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ];
  return $nama_bulan[(int)$bulan] ?? $bulan;
}

// === Ambil 10 transaksi terbaru ===
$q = mysqli_query($conn, "
  SELECT 
    t.id_transaksi,
    t.tanggal,
    p.nama,
    u.nama AS nama_terapis,
    t.subtotal, t.diskon, t.total
  FROM transaksi t
  LEFT JOIN pasien p ON t.id_pasien = p.id_pasien
  LEFT JOIN users u ON t.id_user = u.id_user
  ORDER BY t.tanggal DESC
  LIMIT 10
");

// === Ambil semua transaksi untuk modal ===
$all = mysqli_query($conn, "
  SELECT 
    t.id_transaksi,
    t.tanggal,
    p.nama,
    u.nama AS nama_terapis,
    t.subtotal, t.diskon, t.total
  FROM transaksi t
  LEFT JOIN pasien p ON t.id_pasien = p.id_pasien
  LEFT JOIN users u ON t.id_user = u.id_user
  ORDER BY t.tanggal DESC
");

// === Ambil data statistik 12 bulan terakhir ===
$data_chart = [
  'labels' => [],
  'jumlah_transaksi' => [],
  'total_pendapatan' => []
];

for ($i = 11; $i >= 0; $i--) {
  $periode = date('Y-m', strtotime("-$i months"));
  $bulan_num = date('n', strtotime("-$i months"));
  $tahun_label = date('Y', strtotime("-$i months"));
  $label_bulan = bulanIndo($bulan_num) . " " . $tahun_label;

  $query = mysqli_query($conn, "
    SELECT 
      COUNT(*) AS jml_transaksi, 
      COALESCE(SUM(total),0) AS total_pendapatan 
    FROM transaksi 
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$periode'
  ");
  $r = mysqli_fetch_assoc($query);
  $data_chart['labels'][] = $label_bulan;
  $data_chart['jumlah_transaksi'][] = (int)($r['jml_transaksi'] ?? 0);
  $data_chart['total_pendapatan'][] = (float)($r['total_pendapatan'] ?? 0);
}
?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-pink"><i class="bi bi-receipt"></i> Data Transaksi</h4>
    <small class="text-muted">Menampilkan 10 transaksi terakhir</small>
  </div>

  <!-- Tabel Transaksi -->
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle text-center">
          <thead class="table-pink">
            <tr>
              <th>#</th>
              <th>Tanggal</th>
              <th>Pasien</th>
              <th>Terapis</th>
              <th>Subtotal</th>
              <th>Diskon</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($q && mysqli_num_rows($q) > 0): $no = 1; ?>
              <?php while ($row = mysqli_fetch_assoc($q)): ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                  <td><?= htmlspecialchars($row['nama'] ?? '-'); ?></td>
                  <td><?= htmlspecialchars($row['nama_terapis'] ?? '-'); ?></td>
                  <td>Rp <?= number_format($row['subtotal'], 0, ',', '.'); ?></td>
                  <td>Rp <?= number_format($row['diskon'], 0, ',', '.'); ?></td>
                  <td><strong>Rp <?= number_format($row['total'], 0, ',', '.'); ?></strong></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-muted">Belum ada data transaksi.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="text-center mt-3">
        <button class="btn btn-outline-pink px-4" data-bs-toggle="modal" data-bs-target="#modalSemuaTransaksi">
          <i class="bi bi-list-ul"></i> Lihat Semua
        </button>
      </div>
    </div>
  </div>

  <!-- Grafik Jumlah Transaksi -->
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
      <h5 class="fw-bold text-pink mb-3"><i class="bi bi-bar-chart"></i> Jumlah Transaksi (12 Bulan Terakhir)</h5>
      <canvas id="chartJumlahTransaksi" height="100"></canvas>
    </div>
  </div>

  <!-- Grafik Total Pendapatan -->
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
      <h5 class="fw-bold text-pink mb-3"><i class="bi bi-graph-up"></i> Total Pendapatan (12 Bulan Terakhir)</h5>
      <canvas id="chartPendapatan" height="100"></canvas>
    </div>
  </div>
</div>

<!-- MODAL SEMUA TRANSAKSI -->
<div class="modal fade" id="modalSemuaTransaksi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content rounded-4 border-0">
      <div class="modal-header btn-pink text-white rounded-top-4">
        <h5 class="modal-title"><i class="bi bi-list-ul"></i> Semua Transaksi</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-striped align-middle text-center">
            <thead class="table-pink">
              <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Pasien</th>
                <th>Terapis</th>
                <th>Subtotal</th>
                <th>Diskon</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($all && mysqli_num_rows($all) > 0): $no = 1; ?>
                <?php while ($row = mysqli_fetch_assoc($all)): ?>
                  <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                    <td><?= htmlspecialchars($row['nama'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['nama_terapis'] ?? '-'); ?></td>
                    <td>Rp <?= number_format($row['subtotal'], 0, ',', '.'); ?></td>
                    <td>Rp <?= number_format($row['diskon'], 0, ',', '.'); ?></td>
                    <td><strong>Rp <?= number_format($row['total'], 0, ',', '.'); ?></strong></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="7" class="text-muted">Belum ada data transaksi.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function initGrafikTransaksi() {
  const labels = <?= json_encode($data_chart['labels']); ?>;
  const dataTransaksi = <?= json_encode($data_chart['jumlah_transaksi']); ?>;
  const dataPendapatan = <?= json_encode($data_chart['total_pendapatan']); ?>;

  // Chart Jumlah Transaksi
  new Chart(document.getElementById("chartJumlahTransaksi"), {
    type: "bar",
    data: {
      labels,
      datasets: [{
        label: "Jumlah Transaksi",
        data: dataTransaksi,
        backgroundColor: "rgba(231, 84, 128, 0.7)",
        borderColor: "#e75480",
        borderWidth: 2,
        borderRadius: 6
      }]
    },
    options: {
      scales: { y: { beginAtZero: true, title: { display: true, text: "Jumlah Transaksi" } } },
      plugins: { legend: { display: false } }
    }
  });

  // Chart Total Pendapatan
  new Chart(document.getElementById("chartPendapatan"), {
    type: "line",
    data: {
      labels,
      datasets: [{
        label: "Pendapatan (Rp)",
        data: dataPendapatan,
        fill: true,
        backgroundColor: "rgba(231, 84, 128, 0.15)",
        borderColor: "#e75480",
        tension: 0.3
      }]
    },
    options: {
      scales: { y: { beginAtZero: true, title: { display: true, text: "Pendapatan (Rp)" } } },
      plugins: { legend: { position: 'bottom' } }
    }
  });
}

// jalankan langsung bila halaman di-load langsung
document.addEventListener("DOMContentLoaded", initGrafikTransaksi);
</script>

<style>
.text-pink { color: #e75480 !important; }
.btn-pink { background-color: #e75480; color: white; border: none; }
.btn-pink:hover { background-color: #d34b73; color: white; }
.btn-outline-pink { color: #e75480; border: 1px solid #e75480; }
.btn-outline-pink:hover { background-color: #e75480; color: white; }
.table-pink { background-color: #ffe6ef; color: #e75480; }
</style>
