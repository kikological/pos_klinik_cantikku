<?php
include '../includes/db.php';
include '../includes/functions.php'; // opsional, jika punya fungsi format tanggal
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

$namaBulan = [
  1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",
  7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember"
];

$q = $db->query("
  SELECT t.*, p.nama AS nama_pasien, u.nama AS nama_terapis
  FROM transaksi t
  JOIN pasien p ON t.id_pasien = p.id
  JOIN users u ON t.id_terapis = u.id
  WHERE MONTH(t.tgl_transaksi)='$bulan' AND YEAR(t.tgl_transaksi)='$tahun'
  ORDER BY t.tgl_transaksi DESC
");

$q_total = $db->query("
  SELECT SUM(total - diskon) AS total 
  FROM transaksi 
  WHERE MONTH(tgl_transaksi)='$bulan' AND YEAR(tgl_transaksi)='$tahun'
");
$total = $q_total->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Transaksi <?= $namaBulan[$bulan] . " $tahun" ?></title>
<style>
body { font-family: Arial, sans-serif; font-size: 12px; }
h2 { text-align:center; }
table { width:100%; border-collapse: collapse; margin-top:20px; }
th, td { border:1px solid #000; padding:6px; text-align:left; }
th { background:#f8c9d4; }
tfoot td { font-weight: bold; }
</style>
</head>
<body onload="window.print()">

<h2>Laporan Transaksi - <?= $namaBulan[$bulan] . " $tahun" ?></h2>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Nama Pasien</th>
      <th>Terapis</th>
      <th>Total</th>
      <th>Diskon</th>
      <th>Tanggal</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $no=1;
    if ($q->num_rows > 0) {
      while($r = $q->fetch_assoc()){
        echo "
        <tr>
          <td>$no</td>
          <td>{$r['nama_pasien']}</td>
          <td>{$r['nama_terapis']}</td>
          <td>Rp " . number_format($r['total'],0,',','.') . "</td>
          <td>Rp " . number_format($r['diskon'],0,',','.') . "</td>
          <td>" . date('d M Y H:i', strtotime($r['tgl_transaksi'])) . "</td>
        </tr>
        ";
        $no++;
      }
    } else {
      echo "<tr><td colspan='6' align='center'>Tidak ada transaksi.</td></tr>";
    }
    ?>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="5" align="right">Total Pendapatan</td>
      <td>Rp <?= number_format($total,0,',','.') ?></td>
    </tr>
  </tfoot>
</table>

</body>
</html>
