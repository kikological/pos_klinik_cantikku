<?php
session_start();
include '../../includes/db.php';

$aksi = $_POST['aksi'] ?? $_GET['aksi'] ?? '';

if($aksi == 'cari_pasien'){
  $q = $_GET['q'];
  $data = [];
  $res = $db->query("SELECT * FROM pasien WHERE nama LIKE '%$q%' ORDER BY nama LIMIT 10");
  while($r = $res->fetch_assoc()) $data[] = $r;
  echo json_encode($data);

} elseif($aksi == 'tambah_pasien'){
  $nama = $_POST['nama'];
  $no_hp = $_POST['no_hp'];
  $alamat = $_POST['alamat'];
  $db->query("INSERT INTO pasien (nama,no_hp,alamat) VALUES ('$nama','$no_hp','$alamat')");
  echo json_encode(["status"=>"ok","message"=>"Pasien baru berhasil ditambahkan!"]);

} elseif($aksi == 'riwayat'){
  $id = $_GET['id_pasien'];
  $sql = "SELECT t.*, l.nama AS layanan, u.nama AS terapis 
          FROM transaksi t 
          JOIN layanan l ON t.id_layanan=l.id 
          JOIN users u ON t.id_terapis=u.id 
          WHERE id_pasien='$id' 
          ORDER BY t.tgl_transaksi DESC";
  $q = $db->query($sql);
  $html = '';
  if($q->num_rows>0){
    while($r = $q->fetch_assoc()){
      $html .= "<div>🗓️ ".date('d/m/Y', strtotime($r['tgl_transaksi']))." — <b>{$r['layanan']}</b> oleh {$r['terapis']}</div>";
    }
  } else {
    $html = "<div class='text-muted'>Belum ada riwayat tindakan.</div>";
  }
  echo json_encode(["html"=>$html]);

} elseif($aksi == 'simpan_transaksi'){
  $id_pasien = $_POST['id_pasien'];
  $id_layanan = $_POST['id_layanan'];
  $diskon = $_POST['diskon'];
  $id_terapis = $_SESSION['user_id'];

  $l = $db->query("SELECT * FROM layanan WHERE id='$id_layanan'")->fetch_assoc();
  $total = $l['harga'];
  $bayar = $total - $diskon;

  $db->query("INSERT INTO transaksi (id_pasien,id_layanan,id_terapis,total,diskon,bayar) VALUES ('$id_pasien','$id_layanan','$id_terapis','$total','$diskon','$bayar')");

  // ambil pengaturan untuk struk
  $set = $db->query("SELECT * FROM pengaturan LIMIT 1")->fetch_assoc();
  $p = $db->query("SELECT nama FROM pasien WHERE id='$id_pasien'")->fetch_assoc();

  echo json_encode([
    "status"=>"ok",
    "message"=>"Transaksi berhasil disimpan!",
    "data"=>[
      "nama_klinik"=>$set['nama_klinik'],
      "alamat"=>$set['alamat'],
      "telp"=>$set['telp'],
      "footer_struk"=>$set['footer_struk'],
      "pasien"=>$p['nama'],
      "layanan"=>$l['nama'],
      "total"=>number_format($total,0,',','.'),
      "diskon"=>number_format($diskon,0,',','.'),
      "bayar"=>number_format($bayar,0,',','.')
    ]
  ]);
}
