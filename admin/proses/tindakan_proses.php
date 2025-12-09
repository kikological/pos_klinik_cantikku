<?php
include '../../includes/db.php';
session_start();

$id_terapis = $_SESSION['user_id'];
$id_pasien = $_POST['id_pasien'];
$id_layanan = $_POST['id_layanan'];
$diskon = $_POST['diskon'] ?? 0;

// ambil harga layanan
$r = $db->query("SELECT harga FROM layanan WHERE id='$id_layanan'")->fetch_assoc();
$total = $r['harga'] ?? 0;

$db->query("INSERT INTO transaksi (id_pasien,id_terapis,id_layanan,total,diskon) 
VALUES ('$id_pasien','$id_terapis','$id_layanan','$total','$diskon')");

$id_transaksi = $db->insert_id;

echo json_encode(["status"=>"ok","message"=>"Tindakan berhasil disimpan!","id"=>$id_transaksi]);
