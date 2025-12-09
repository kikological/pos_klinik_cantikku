<?php
require_once __DIR__ . '/../../includes/db.php';
header('Content-Type: application/json');

$nama           = trim($_POST['nama'] ?? '');
$no_hp          = trim($_POST['no_hp'] ?? '');
$alamat         = trim($_POST['alamat'] ?? '');
$tanggal_lahir  = $_POST['tanggal_lahir'] ?? null;
$jenis_kelamin  = $_POST['jenis_kelamin'] ?? null;

if ($nama === '') {
  echo json_encode(["status" => "error", "message" => "Nama wajib diisi."]);
  exit;
}

// Cek duplikasi nomor HP
if ($no_hp !== '') {
  $cek = $conn->prepare("SELECT id_pasien FROM pasien WHERE no_hp = ?");
  $cek->bind_param("s", $no_hp);
  $cek->execute();
  $result = $cek->get_result();
  if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Nomor HP sudah terdaftar."]);
    exit;
  }
  $cek->close();
}

$stmt = $conn->prepare("
  INSERT INTO pasien (nama, no_hp, alamat, tanggal_lahir, jenis_kelamin)
  VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("sssss", $nama, $no_hp, $alamat, $tanggal_lahir, $jenis_kelamin);

if ($stmt->execute()) {
  echo json_encode([
    "status" => "ok",
    "message" => "Pasien berhasil ditambahkan.",
    "id_pasien" => $stmt->insert_id,
    "nama" => $nama
  ]);
} else {
  echo json_encode(["status" => "error", "message" => "Gagal menambahkan pasien: " . $conn->error]);
}

$stmt->close();
?>
