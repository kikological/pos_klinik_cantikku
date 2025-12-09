<?php
require_once __DIR__ . '/../../includes/db.php';
header("Content-Type: application/json");

$no_register   = trim($_POST['no_register'] ?? '');
$nama          = trim($_POST['nama'] ?? '');
$no_hp         = trim($_POST['no_hp'] ?? '');
$alamat        = trim($_POST['alamat'] ?? '');
$tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
$jk            = $_POST['jenis_kelamin'] ?? '';

if ($no_register === "" || $nama === "") {
  echo json_encode(["status"=>"error","message"=>"No register dan nama wajib diisi"]);
  exit;
}

// Cek duplikasi nomor register
$cek = $conn->prepare("SELECT id_pasien FROM pasien WHERE no_register=?");
$cek->bind_param("s",$no_register);
$cek->execute();
if ($cek->get_result()->num_rows > 0) {
  echo json_encode(["status"=>"error","message"=>"No register sudah digunakan"]);
  exit;
}

$stmt = $conn->prepare("
  INSERT INTO pasien(no_register,nama,no_hp,alamat,tanggal_lahir,jenis_kelamin)
  VALUES(?,?,?,?,?,?)
");
$stmt->bind_param("ssssss",$no_register,$nama,$no_hp,$alamat,$tanggal_lahir,$jk);

if ($stmt->execute()) {
  echo json_encode(["status"=>"ok","message"=>"Data pasien berhasil ditambahkan"]);
} else {
  echo json_encode(["status"=>"error","message"=>$conn->error]);
}
