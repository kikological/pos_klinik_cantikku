<?php
require_once __DIR__ . '/../../includes/db.php';
header("Content-Type: application/json");

// ambil id
$id = intval($_POST['id'] ?? 0);

if ($id<=0) {
  echo json_encode(["status"=>"error","message"=>"ID tidak valid"]);
  exit;
}

// pastikan tidak dipakai transaksi
$cek = $conn->query("SELECT id_transaksi FROM transaksi WHERE id_pasien=$id");
if ($cek->num_rows > 0) {
  echo json_encode([
    "status"=>"error",
    "message"=>"Tidak dapat menghapus karena pasien sudah memiliki riwayat transaksi!"
  ]);
  exit;
}

$delete = $conn->query("DELETE FROM pasien WHERE id_pasien=$id");

echo json_encode([
  "status" => $delete ? "ok" : "error",
  "message" => $delete ? "Pasien berhasil dihapus." : "Gagal menghapus: ".$conn->error
]);
