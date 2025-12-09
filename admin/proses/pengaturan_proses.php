<?php
include '../../includes/db.php';

$aksi = $_POST['aksi'] ?? '';

if($aksi == 'simpan'){
  $nama_klinik = $_POST['nama_klinik'];
  $alamat = $_POST['alamat'];
  $telp = $_POST['telp'];
  $instagram = $_POST['instagram'];
  $facebook = $_POST['facebook'];
  $footer_struk = $_POST['footer_struk'];
  $printer = $_POST['printer'];

  $cek = $db->query("SELECT * FROM pengaturan LIMIT 1");
  if($cek->num_rows > 0){
    $db->query("UPDATE pengaturan SET 
      nama_klinik='$nama_klinik',
      alamat='$alamat',
      telp='$telp',
      instagram='$instagram',
      facebook='$facebook',
      footer_struk='$footer_struk',
      printer='$printer'
    ");
  } else {
    $db->query("INSERT INTO pengaturan 
      (nama_klinik, alamat, telp, instagram, facebook, footer_struk, printer)
      VALUES ('$nama_klinik','$alamat','$telp','$instagram','$facebook','$footer_struk','$printer')
    ");
  }

  echo json_encode(["status"=>"ok","message"=>"Pengaturan klinik berhasil disimpan!"]);
}
