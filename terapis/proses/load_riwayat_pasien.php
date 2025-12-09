<?php
require_once __DIR__ . '/../../includes/db.php';

$id_pasien = intval($_POST['id_pasien'] ?? 0);
$bulan = intval($_POST['bulan'] ?? 0);
$tahun = intval($_POST['tahun'] ?? 0);

if ($id_pasien <= 0) {
  echo "<div class='alert alert-danger'>ID pasien tidak valid.</div>";
  exit;
}

// Buat query filter
$where = "WHERE t.id_pasien = $id_pasien";
if ($bulan > 0) $where .= " AND MONTH(t.tanggal) = $bulan";
if ($tahun > 0) $where .= " AND YEAR(t.tanggal) = $tahun";

$q = $conn->query("
  SELECT p.nama_pasien, l.nama_layanan, t.tanggal
  FROM detail_transaksi d
  JOIN transaksi t ON d.id_transaksi = t.id_transaksi
  JOIN pasien p ON t.id_pasien = p.id_pasien
  JOIN layanan l ON d.id_layanan = l.id_layanan
  $where
  ORDER BY t.tanggal DESC
");

$bulanOptions = '';
for ($i = 1; $i <= 12; $i++) {
  $selected = ($i == $bulan) ? 'selected' : '';
  $bulanOptions .= "<option value='$i' $selected>" . date('F', mktime(0,0,0,$i,1)) . "</option>";
}

$tahunSekarang = date('Y');
$tahunOptions = '';
for ($t = $tahunSekarang; $t >= $tahunSekarang - 5; $t--) {
  $selected = ($t == $tahun) ? 'selected' : '';
  $tahunOptions .= "<option value='$t' $selected>$t</option>";
}

echo "
<div class='d-flex justify-content-between align-items-center mb-3'>
  <h6 class='text-pink'><i class='bi bi-person-badge'></i> Riwayat: <b>{$conn->query("SELECT nama_pasien FROM pasien WHERE id_pasien=$id_pasien")->fetch_assoc()['nama_pasien']}</b></h6>
  <div>
    <select id='filterBulan' class='form-select form-select-sm d-inline-block' style='width:auto;'>
      <option value=''>Semua Bulan</option>
      $bulanOptions
    </select>
    <select id='filterTahun' class='form-select form-select-sm d-inline-block' style='width:auto;'>
      <option value=''>Semua Tahun</option>
      $tahunOptions
    </select>
  </div>
</div>
";

if (!$q || $q->num_rows === 0) {
  echo "<div class='alert alert-info'>Belum ada tindakan untuk pasien ini pada periode tersebut.</div>";
  exit;
}

echo "
<table class='table table-bordered table-striped align-middle'>
  <thead class='table-light'>
    <tr>
      <th style='width:60px;'>No</th>
      <th>Nama Pasien</th>
      <th>Tindakan</th>
      <th>Tanggal</th>
    </tr>
  </thead>
  <tbody>
";

$no = 1;
while ($r = $q->fetch_assoc()) {
  echo "
    <tr>
      <td>{$no}</td>
      <td>{$r['nama_pasien']}</td>
      <td>{$r['nama_layanan']}</td>
      <td>" . date('d/m/Y H:i', strtotime($r['tanggal'])) . "</td>
    </tr>
  ";
  $no++;
}

echo "</tbody></table>";
?>
