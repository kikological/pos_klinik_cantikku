<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$id_pasien = intval($_POST['id_pasien'] ?? 0);
$id_user   = intval($_POST['id_user'] ?? 0); // kirim id_user dari app
$diskon    = floatval($_POST['diskon'] ?? 0);
$cartJson  = $_POST['cart'] ?? '[]';

$cart = json_decode($cartJson, true);
if ($id_pasien<=0 || $id_user<=0 || !is_array($cart) || count($cart)==0) {
  echo json_encode(['success'=>false,'message'=>'Data cart kosong atau pasien tidak valid']);
  exit;
}

// hitung subtotal
$subtotal = 0;
foreach($cart as $it){
  $subtotal += floatval($it['harga'])*intval($it['qty']);
}
$total = max(0, $subtotal - $diskon);

// transaksi
$stmt = $conn->prepare("INSERT INTO transaksi (id_user,id_pasien,subtotal,diskon,total,tanggal) VALUES (?,?,?,?,?,NOW())");
$stmt->bind_param("iiddd",$id_user,$id_pasien,$subtotal,$diskon,$total);
if(!$stmt->execute()){
  echo json_encode(['success'=>false,'message'=>'Gagal menyimpan transaksi: '.$conn->error]);
  exit;
}
$id_trans = $stmt->insert_id;

// detail
$ins = $conn->prepare("INSERT INTO detail_transaksi (id_transaksi,id_layanan,qty,harga,subtotal) VALUES (?,?,?,?,?)");
foreach($cart as $it){
  $id_l = intval($it['id']);
  $qty  = intval($it['qty']);
  $harga= floatval($it['harga']);
  $sub  = $harga * $qty;
  $ins->bind_param("iiidd",$id_trans,$id_l,$qty,$harga,$sub);
  $ins->execute();
}

echo json_encode(['success'=>true,'message'=>'Tindakan disimpan','id_transaksi'=>$id_trans]);
