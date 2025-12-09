<?php
include '../includes/db.php';
$id = $_GET['id'];
$q = $db->query("
  SELECT t.*, p.nama AS pasien, u.nama AS terapis, l.nama AS layanan, l.harga
  FROM transaksi t
  JOIN pasien p ON t.id_pasien=p.id
  JOIN users u ON t.id_terapis=u.id
  JOIN layanan l ON t.id_layanan=l.id
  WHERE t.id='$id'
")->fetch_assoc();

$subtotal = $q['total'];
$diskon = $q['diskon'];
$total_bayar = $subtotal - $diskon;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Struk Pembayaran</title>
<style>
body { font-family: monospace; font-size: 12px; }
hr { border: none; border-top: 1px dashed #000; }
</style>
</head>
<body onload="window.print()">
<center>
  <h3><?= htmlspecialchars(getenv('NAMA_KLINIK') ?: 'Klinik Cantikku') ?></h3>
  <small><?= date('d/m/Y H:i') ?></small>
</center>
<hr>
<b>Pasien:</b> <?= $q['pasien'] ?><br>
<b>Terapis:</b> <?= $q['terapis'] ?><br>
<hr>
<?= $q['layanan'] ?>  
<span style="float:right">Rp <?= number_format($subtotal,0,',','.') ?></span><br>
Diskon<span style="float:right">Rp <?= number_format($diskon,0,',','.') ?></span><br>
<hr>
<b>Total</b><span style="float:right">Rp <?= number_format($total_bayar,0,',','.') ?></span>
<hr>
<center>Terima kasih 💖<br>Semoga sehat & cantik selalu!</center>
</body>
</html>
