<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$q = $conn->query("SELECT id_pasien, no_register, nama, no_hp FROM pasien ORDER BY nama ASC");
$out = [];
while($r=$q->fetch_assoc()) $out[] = $r;
echo json_encode(['success'=>true,'data'=>$out]);
