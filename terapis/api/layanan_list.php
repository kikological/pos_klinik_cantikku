<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$q = $conn->query("SELECT id_layanan, nama_layanan, harga FROM layanan ORDER BY nama_layanan ASC");
$out = [];
while($r=$q->fetch_assoc()) $out[] = $r;
echo json_encode(['success'=>true,'data'=>$out]);
