<?php
require_once __DIR__ . '/../../includes/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) die("Invalid ID");

$set = $conn->query("SELECT * FROM pengaturan_klinik LIMIT 1")->fetch_assoc();
$t = $conn->query("
    SELECT t.*, p.nama AS nama_pasien, p.no_register
    FROM transaksi t
    LEFT JOIN pasien p ON t.id_pasien = p.id_pasien
    WHERE id_transaksi = $id
")->fetch_assoc();

$d = $conn->query("
    SELECT d.*, l.nama_layanan
    FROM detail_transaksi d
    LEFT JOIN layanan l ON l.id_layanan = d.id_layanan
    WHERE d.id_transaksi = $id
");

$ESC = "\x1B";
$GS = "\x1D";

$raw  = $ESC . "@"; // init printer
$raw .= $ESC . "!" . "\x38"; // bold besar
$raw .= strtoupper($set['nama_klinik']) . "\n";
$raw .= $ESC . "!" . "\x01";
$raw .= $set['alamat'] . "\n";
$raw .= "WA: {$set['no_hp']}\n";
$raw .= "------------------------------\n";

$raw .= "Nama   : {$t['nama_pasien']}\n";
$raw .= "No.Reg : {$t['no_register']}\n";
$raw .= "Tanggal: " . date("d/m/Y H:i", strtotime($t['tanggal'])) . "\n";
$raw .= "------------------------------\n";

while ($r = $d->fetch_assoc()) {
    $raw .= $ESC . "!" . "\x08";
    $raw .= $r["nama_layanan"] . "\n";
    $raw .= $ESC . "!" . "\x00";
    $raw .= "                   Rp " . number_format($r["subtotal"],0,',','.') . "\n";
}

$raw .= "------------------------------\n";
$raw .= "Subtotal: Rp " . number_format($t['subtotal'],0,',','.') . "\n";
$raw .= "Diskon  : Rp " . number_format($t['diskon'],0,',','.') . "\n";
$raw .= "Total   : Rp " . number_format($t['total'],0,',','.') . "\n";
$raw .= "------------------------------\n";

$raw .= $ESC . "!" . "\x08";
$raw .= "Terima kasih 🙏\n\n\n";

$data64 = base64_encode($raw);
$url = "rawbt://print?data=" . urlencode($data64);

header("Location: $url");
