<?php
require_once __DIR__ . '/../../includes/db.php';

$id_pasien   = intval($_POST['id_pasien'] ?? 0);
$id_layanan  = intval($_POST['id_layanan'] ?? 0);

if ($id_pasien <= 0) {
  echo "<div class='alert alert-warning'>ID pasien tidak valid.</div>";
  exit;
}

// ambil nama pasien
$q = $conn->query("SELECT nama FROM pasien WHERE id_pasien=$id_pasien");
$nama_pasien = $q->fetch_assoc()['nama'] ?? "-";

// query dasar
$sql = "
  SELECT 
    t.tanggal,
    l.nama_layanan,
    u.nama AS terapis
  FROM transaksi t
  JOIN detail_transaksi d ON d.id_transaksi = t.id_transaksi
  JOIN layanan l ON l.id_layanan = d.id_layanan
  LEFT JOIN users u ON u.id_user = t.id_user
  WHERE t.id_pasien = $id_pasien
";

if ($id_layanan > 0) {
  $sql .= " AND l.id_layanan = $id_layanan ";
}

$sql .= " ORDER BY t.tanggal DESC ";

$result = $conn->query($sql);

// jika kosong
if (!$result || $result->num_rows == 0) {
  echo "<div class='alert alert-info'>Tidak ada riwayat tindakan.</div>";
  exit;
}

// tampilkan tabel
echo "

<table class='table table-bordered table-hover'>
  <thead class='table-pink text-center'>
    <tr>
      <th width='150'>Tanggal</th>
      <th>Layanan</th>
      <th width='180'>Terapis</th>
    </tr>
  </thead>
  <tbody>
";

while ($r = $result->fetch_assoc()) {

  echo "
  <tr>
    <td>". date('d/m/Y', strtotime($r['tanggal'])) ."</td>
    <td>". htmlspecialchars($r['nama_layanan']) ."</td>
    <td>". htmlspecialchars($r['terapis'] ?? '-') ."</td>
  </tr>
  ";
}

echo "</tbody></table>";
