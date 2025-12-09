<?php
require_once __DIR__ . '/../../includes/db.php';
header("Content-Type: application/json");

session_start();

// ===============================
// Ambil data dari POST
// ===============================
$id_pasien = intval($_POST['id_pasien'] ?? 0);
$layanan_id = $_POST['layanan_id'] ?? [];
$harga      = $_POST['harga'] ?? [];
$qty        = $_POST['qty'] ?? [];
$diskon     = floatval($_POST['diskon'] ?? 0);
$total      = floatval($_POST['total'] ?? 0);

// Debug jika semua kosong
if ($id_pasien <= 0 || empty($layanan_id)) {
    echo json_encode([
        "success" => false,
        "message" => "Data cart kosong atau pasien tidak valid",
        "debug"   => $_POST
    ]);
    exit;
}

// Hitung subtotal
$subtotal = 0;
for ($i = 0; $i < count($layanan_id); $i++) {
    $subtotal += floatval($harga[$i]) * intval($qty[$i]);
}

// ===============================
// SIMPAN TRANSAKSI
// ===============================
$id_user = $_SESSION['id_user'] ?? 1;

$stmt = $conn->prepare("
    INSERT INTO transaksi (id_user, id_pasien, subtotal, diskon, total, tanggal)
    VALUES (?, ?, ?, ?, ?, NOW())
");
$stmt->bind_param("iiddd", $id_user, $id_pasien, $subtotal, $diskon, $total);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Gagal simpan transaksi"]);
    exit;
}

$id_transaksi = $stmt->insert_id;

// ===============================
// SIMPAN DETAIL
// ===============================
$detail = $conn->prepare("
    INSERT INTO detail_transaksi (id_transaksi, id_layanan, qty, harga, subtotal)
    VALUES (?, ?, ?, ?, ?)
");

for ($i = 0; $i < count($layanan_id); $i++) {
    $idL = intval($layanan_id[$i]);
    $h   = floatval($harga[$i]);
    $q   = intval($qty[$i]);
    $sub = $h * $q;

    $detail->bind_param("iiidd", $id_transaksi, $idL, $q, $h, $sub);
    $detail->execute();
}


$set = $conn->query("SELECT print_mode FROM pengaturan_klinik LIMIT 1")->fetch_assoc();
$print_mode = $set['print_mode'] ?? 'usb';

echo json_encode([
    "success" => true,
    "message" => "Tindakan berhasil disimpan",
    "id_transaksi" => $id_transaksi,
    "print_mode" => $print_mode
]);

?>