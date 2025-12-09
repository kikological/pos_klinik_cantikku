<?php
require_once __DIR__ . '/../../includes/db.php';
header('Content-Type: application/json');

$no_register    = trim($_POST['no_register'] ?? '');
$nama           = trim($_POST['nama'] ?? '');
$no_hp          = trim($_POST['no_hp'] ?? '');
$alamat         = trim($_POST['alamat'] ?? '');
$tanggal_lahir  = $_POST['tanggal_lahir'] ?? null;
$jenis_kelamin  = $_POST['jenis_kelamin'] ?? null;

if ($no_register === "" || $nama === "") {
  echo json_encode(["status" => "error", "message" => "No Register dan Nama wajib diisi."]);
  exit;
}

// Cek No Register duplikat
$stmt = $conn->prepare("SELECT id_pasien FROM pasien WHERE no_register = ?");
$stmt->bind_param("s", $no_register);
$stmt->execute();
$cek = $stmt->get_result();
if ($cek->num_rows > 0) {
  echo json_encode(["status" => "error", "message" => "No Register sudah terdaftar!"]);
  exit;
}
$stmt->close();

// Cek HP duplikat
if ($no_hp !== "") {
  $stmt = $conn->prepare("SELECT id_pasien FROM pasien WHERE no_hp = ?");
  $stmt->bind_param("s", $no_hp);
  $stmt->execute();
  $cekHP = $stmt->get_result();

  if ($cekHP->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Nomor HP sudah digunakan pasien lain!"]);
    exit;
  }
  $stmt->close();
}

// INSERT pasien
$stmt = $conn->prepare("
  INSERT INTO pasien (no_register, nama, no_hp, alamat, tanggal_lahir, jenis_kelamin)
  VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("ssssss", $no_register, $nama, $no_hp, $alamat, $tanggal_lahir, $jenis_kelamin);

if ($stmt->execute()) {
  echo json_encode([
    "status" => "ok",
    "message" => "Pasien berhasil ditambahkan.",
    "id_pasien" => $stmt->insert_id
  ]);
} else {
  echo json_encode(["status" => "error", "message" => "Gagal menambahkan pasien: " . $conn->error]);
}

$stmt->close();
?>
