<?php
require_once __DIR__ . '/../../includes/db.php';
header("Content-Type: application/json");

$id            = intval($_POST['id'] ?? 0);
$no_register   = trim($_POST['no_register'] ?? '');
$nama          = trim($_POST['nama'] ?? '');
$no_hp         = trim($_POST['no_hp'] ?? '');
$alamat        = trim($_POST['alamat'] ?? '');
$tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
$jk            = $_POST['jenis_kelamin'] ?? '';

if ($id <= 0 || $nama == "") {
  echo json_encode(["status"=>"error","message"=>"Data tidak valid"]);
  exit;
}

// cek duplikasi no register
$cek = $conn->prepare("SELECT id_pasien FROM pasien WHERE no_register=? AND id_pasien!=?");
$cek->bind_param("si",$no_register,$id);
$cek->execute();
if ($cek->get_result()->num_rows > 0) {
  echo json_encode(["status"=>"error","message"=>"No register sudah digunakan"]);
  exit;
}

$stmt = $conn->prepare("
  UPDATE pasien SET
    no_register=?,
    nama=?,
    no_hp=?,
    alamat=?,
    tanggal_lahir=?,
    jenis_kelamin=?
  WHERE id_pasien=?
");

$stmt->bind_param("ssssssi",
  $no_register,$nama,$no_hp,$alamat,$tanggal_lahir,$jk,$id
);

if ($stmt->execute()) {
  echo json_encode(["status"=>"ok","message"=>"Perubahan berhasil disimpan"]);
} else {
  echo json_encode(["status"=>"error","message"=>$conn->error]);
}
