<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');
$id = intval($_GET['id'] ?? 0);
if($id<=0){ echo json_encode(['success'=>false]); exit; }

$t = $conn->query("SELECT t.*, p.nama AS nama_pasien, p.no_register, pk.nama_klinik, pk.alamat, pk.no_hp AS klinik_hp, pk.instagram 
FROM transaksi t
LEFT JOIN pasien p ON t.id_pasien=p.id_pasien
LEFT JOIN pengaturan_klinik pk ON 1
WHERE t.id_transaksi=$id")->fetch_assoc();

$detQ = $conn->query("SELECT d.*, l.nama_layanan FROM detail_transaksi d LEFT JOIN layanan l ON d.id_layanan=l.id_layanan WHERE d.id_transaksi=$id");
$detail = [];
while($r=$detQ->fetch_assoc()) $detail[]=$r;

echo json_encode(['success'=>true,'trans'=>$t,'detail'=>$detail]);
