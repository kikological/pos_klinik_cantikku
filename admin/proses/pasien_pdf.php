<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Ambil pengaturan klinik
$q = mysqli_query($conn, "SELECT * FROM pengaturan_klinik LIMIT 1");
$info = mysqli_fetch_assoc($q);

$nama_klinik = $info['nama_klinik'] ?? "Klinik Cantikku";
$alamat      = $info['alamat'] ?? "";
$no_hp       = $info['no_hp'] ?? "";
$logo = $info['logo'] ?? "";

$baseUrl = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
$projectFolder = "/pos_klinik_cantikku"; // ⬅️ ganti jika folder kamu berbeda

$logo_url = $baseUrl . $projectFolder . "/uploads/" . $logo;

// Ambil data pasien
$sql = "SELECT * FROM pasien ORDER BY nama ASC";
$q2  = mysqli_query($conn, $sql);

// HTML PDF
$html = '
<style>
  body { font-family: sans-serif; }

  .header {
    background: #ffe4ef;
    color: #e75480;
    padding: 45px;
    margin-bottom: 20px;
  }

  .logo {
    float:left;
    width:118px;
    height:70px;
    border-radius:8px;
    margin-right:15px;
    background:white;
    padding:5px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }

  th {
    background: #ffe4ef;
    color: #e75480;
    padding: 8px;
    border: 1px solid #f7c8d8;
  }

  td {
    padding: 7px;
    border: 1px solid #f7c8d8;
    font-size: 13px;
  }

  .pink-row {
    background: #fff6fa;
  }
</style>

<div class="header">
  <img src="'.$logo_url.'" class="logo">
  <div style="font-size:22px; font-weight:bold;">'.$nama_klinik.'</div>
  <div>'.$alamat.'</div>
  <div>Kontak: '.$no_hp.'</div>
</div>

<h3 style="color:#e75480; text-align:center; margin-top:10px;">
  LAPORAN DATA PASIEN
</h3>

<table>
  <tr>
    <th>No. Reg</th>
    <th>Nama</th>
    <th>No HP</th>
    <th>JK</th>
    <th>Umur</th>
    <th>Alamat</th>
  </tr>
';

while ($r = mysqli_fetch_assoc($q2)) {

  $umur = "-";
  if (!empty($r['tanggal_lahir'])) {
    $umur = date_diff(date_create($r['tanggal_lahir']), date_create('today'))->y . " th";
  }

  $html .= '
    <tr class="pink-row">
      <td>'.$r['no_register'].'</td>
      <td>'.htmlspecialchars($r['nama']).'</td>
      <td>'.$r['no_hp'].'</td>
      <td>'.$r['jenis_kelamin'].'</td>
      <td>'.$umur.'</td>
      <td>'.htmlspecialchars($r['alamat']).'</td>
    </tr>
  ';
}

$html .= '</table>';

// DOMPDF CONFIG
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4','landscape');
$dompdf->render();
$dompdf->stream("data_pasien.pdf", ["Attachment" => false]);
