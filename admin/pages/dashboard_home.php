<?php
require_once __DIR__ . '/../../includes/db.php';

// === Ambil data statistik dengan pengecekan error ===
function getCount($conn, $sql) {
  $res = mysqli_query($conn, $sql);
  if (!$res) return 0;
  $row = mysqli_fetch_assoc($res);
  return $row['jml'] ?? 0;
}

$total_layanan   = getCount($conn, "SELECT COUNT(*) AS jml FROM layanan");
$total_terapis   = getCount($conn, "SELECT COUNT(*) AS jml FROM users WHERE role='terapis'");
$total_pasien    = getCount($conn, "SELECT COUNT(*) AS jml FROM pasien");
$total_transaksi = getCount($conn, "SELECT COUNT(*) AS jml FROM transaksi");

$today_transaksi = getCount($conn, "
  SELECT COUNT(*) AS jml 
  FROM transaksi 
  WHERE DATE(tanggal) = CURDATE()
");

$month_transaksi = getCount($conn, "
  SELECT COUNT(*) AS jml 
  FROM transaksi 
  WHERE MONTH(tanggal) = MONTH(CURDATE()) 
    AND YEAR(tanggal) = YEAR(CURDATE())
");

// === GRAFIK TRANSAKSI 12 BULAN TERAKHIR ===
$data_chart = [
  'labels' => [],
  'jumlah' => [],
  'pendapatan' => []
];

// 🔹 Daftar nama bulan manual dalam Bahasa Indonesia
$nama_bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

for ($i = 11; $i >= 0; $i--) {
  $periode = date('Y-m', strtotime("-$i months"));
  $bulan_ke = (int)date('n', strtotime("-$i months"));
  $tahun_label = date('Y', strtotime("-$i months"));
  $label_bulan = $nama_bulan[$bulan_ke - 1] . " " . $tahun_label;

  $sql = "
    SELECT COUNT(*) AS jml_transaksi, COALESCE(SUM(total),0) AS total_pendapatan
    FROM transaksi
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$periode'
  ";
  $res = mysqli_query($conn, $sql);
  $row = $res ? mysqli_fetch_assoc($res) : ['jml_transaksi' => 0, 'total_pendapatan' => 0];

  $data_chart['labels'][] = $label_bulan;
  $data_chart['jumlah'][] = (int)$row['jml_transaksi'];
  $data_chart['pendapatan'][] = (float)$row['total_pendapatan'];
}
?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-pink"><i class="bi bi-speedometer2"></i> Dashboard</h4>
    <small class="text-muted"><?= date('l, d F Y'); ?></small>
  </div>

  <!-- Ringkasan Statistik -->
  <div class="row g-3 mb-4">
    <div class="col-md-4 col-sm-6">
      <div class="card shadow-sm border-0 rounded-4" style="background:#ffe4ec;">
        <div class="card-body text-center">
          <i class="bi bi-heart-pulse-fill fs-2 text-pink"></i>
          <h5 class="mt-2 fw-semibold">Layanan</h5>
          <h3 class="text-pink fw-bold"><?= $total_layanan; ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-4 col-sm-6">
      <div class="card shadow-sm border-0 rounded-4" style="background:#ffebf2;">
        <div class="card-body text-center">
          <i class="bi bi-person-badge-fill fs-2 text-pink"></i>
          <h5 class="mt-2 fw-semibold">Terapis</h5>
          <h3 class="text-pink fw-bold"><?= $total_terapis; ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-4 col-sm-6">
      <div class="card shadow-sm border-0 rounded-4" style="background:#fff0f5;">
        <div class="card-body text-center">
          <i class="bi bi-people-fill fs-2 text-pink"></i>
          <h5 class="mt-2 fw-semibold">Pasien</h5>
          <h3 class="text-pink fw-bold"><?= $total_pasien; ?></h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Transaksi Hari Ini & Bulan Ini -->
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body text-center py-4">
          <h6 class="fw-semibold text-muted">Transaksi Hari Ini</h6>
          <h2 class="fw-bold text-pink"><?= $today_transaksi; ?></h2>
          <p class="text-muted mb-0">Total tindakan & pembayaran hari ini</p>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body text-center py-4">
          <h6 class="fw-semibold text-muted">Transaksi Bulan Ini</h6>
          <h2 class="fw-bold text-pink"><?= $month_transaksi; ?></h2>
          <p class="text-muted mb-0">Total transaksi sepanjang bulan berjalan</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Grafik Transaksi 12 Bulan Terakhir -->
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
      <h5 class="fw-bold text-pink mb-3"><i class="bi bi-bar-chart"></i> Grafik Transaksi 12 Bulan Terakhir</h5>
      <canvas id="chartTransaksi" height="100"></canvas>
    </div>
  </div>

  <!-- Info Klinik -->
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
      <h5 class="text-pink fw-bold mb-3"><i class="bi bi-info-circle"></i> Tentang Klinik Cantikku</h5>
      <p class="mb-1">
        <strong>Klinik Cantikku</strong> adalah sistem Point of Sales sederhana untuk membantu manajemen klinik kecantikan
        dalam mengelola layanan, transaksi, data terapis, dan pasien dengan mudah.
      </p>
      <p class="text-muted mb-0">
        Gunakan menu di sidebar kiri untuk menambah layanan, melihat laporan transaksi, dan mengatur pengaturan klinik.
      </p>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById("chartTransaksi")?.getContext("2d");
if (ctx) {
  new Chart(ctx, {
    type: "bar",
    data: {
      labels: <?= json_encode($data_chart['labels']); ?>,
      datasets: [
        {
          label: "Jumlah Transaksi",
          data: <?= json_encode($data_chart['jumlah']); ?>,
          backgroundColor: "rgba(231, 84, 128, 0.6)",
          borderColor: "#e75480",
          borderWidth: 2,
          borderRadius: 6,
          yAxisID: 'y1',
        },
        {
          label: "Pendapatan (Rp)",
          data: <?= json_encode($data_chart['pendapatan']); ?>,
          backgroundColor: "rgba(255, 193, 204, 0.8)",
          borderColor: "#ff9bb3",
          borderWidth: 2,
          borderRadius: 6,
          yAxisID: 'y2',
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        y1: { beginAtZero: true, position: 'left', title: { display: true, text: 'Jumlah Transaksi' } },
        y2: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Pendapatan (Rp)' } }
      },
      plugins: { legend: { position: 'bottom' } }
    }
  });
}
</script>

<style>
  .text-pink { color: #e75480 !important; }
  .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
</style>
