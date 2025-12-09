<?php
require_once __DIR__ . '/../../includes/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "ERROR";
    exit;
}

$set = $conn->query("SELECT * FROM pengaturan_klinik LIMIT 1")->fetch_assoc();

$t = $conn->query("
    SELECT t.*, p.nama, p.no_register
    FROM transaksi t
    LEFT JOIN pasien p ON t.id_pasien = p.id_pasien
    WHERE t.id_transaksi = $id
")->fetch_assoc();

$d = $conn->query("
    SELECT d.*, l.nama_layanan 
    FROM detail_transaksi d
    LEFT JOIN layanan l ON l.id_layanan = d.id_layanan
    WHERE d.id_transaksi = $id
");

ob_start();
?>

[LOGO: includes/logo.png]

<?= $set['nama_klinik'] . "\n" ?>
<?= $set['alamat'] . "\n" ?>
IG: <?= $set['instagram'] ?> | WA: <?= $set['no_hp'] . "\n" ?>
------------------------------------------

Nama Pasien : <?= $t['nama'] . "\n" ?>
No.Reg      : <?= $t['no_register'] . "\n" ?>
Tanggal     : <?= date('d/m/Y H:i', strtotime($t['tanggal'])) . "\n" ?>
------------------------------------------
RINCIAN TINDAKAN
------------------------------------------
<?php while($r = $d->fetch_assoc()): ?>
<?= $r['nama_layanan'] ?>    <?= number_format($r['total'],0,',','.') . "\n" ?>
<?php endwhile; ?>
------------------------------------------
Subtotal    : <?= number_format($t['subtotal'],0,',','.') . "\n" ?>
Diskon      : <?= number_format($t['diskon'],0,',','.') . "\n" ?>
------------------------------------------
TOTAL       : <?= number_format($t['total'],0,',','.') . "\n" ?>
------------------------------------------
Terima kasih atas kunjungannya!
<?php

$html = ob_get_clean();
echo nl2br($html);
