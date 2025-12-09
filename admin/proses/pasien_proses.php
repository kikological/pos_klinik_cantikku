<?php
include '../../includes/db.php';

$aksi = $_GET['aksi'] ?? $_POST['aksi'] ?? '';

if($aksi == 'tambah'){
  $nama = $_POST['nama'];
  $tgl = $_POST['tanggal_lahir'] ?? null;
  $telp = $_POST['telepon'] ?? '';
  $db->query("INSERT INTO pasien (nama, tanggal_lahir, telepon) VALUES ('$nama', '$tgl', '$telp')");
  echo json_encode(["status"=>"ok","message"=>"Pasien baru berhasil ditambahkan!"]);

} elseif($aksi == 'cari'){
  $keyword = $_GET['keyword'] ?? '';
  $q = $db->query("SELECT * FROM pasien WHERE nama LIKE '%$keyword%' OR telepon LIKE '%$keyword%'");
  $html = '';
  if($q->num_rows > 0){
    while($r = $q->fetch_assoc()){
      $html .= "
      <div class='d-flex justify-content-between border-bottom py-2'>
        <div><b>{$r['nama']}</b><br><small>{$r['telepon']}</small></div>
        <button class='btn btn-sm btn-outline-pink' onclick='pilihPasien({$r['id']}, \"{$r['nama']}\")'>Pilih</button>
      </div>";
    }
  } else {
    $html = "<div class='text-muted'>Tidak ada pasien ditemukan.</div>";
  }
  echo json_encode(["html"=>$html]);

} elseif($aksi == 'riwayat'){
  $id_pasien = $_GET['id_pasien'];
  $q = $db->query("
    SELECT t.*, l.nama AS layanan
    FROM transaksi t 
    JOIN layanan l ON t.id_layanan = l.id
    WHERE id_pasien='$id_pasien'
    ORDER BY t.tgl_transaksi DESC
  ");
  $html = "<ul class='list-group'>";
  if($q->num_rows > 0){
    while($r = $q->fetch_assoc()){
      $html .= "<li class='list-group-item d-flex justify-content-between'>
        <span>{$r['layanan']}</span>
        <small>".date('d M Y', strtotime($r['tgl_transaksi']))."</small>
      </li>";
    }
  } else {
    $html .= "<li class='list-group-item text-muted'>Belum ada riwayat tindakan.</li>";
  }
  $html .= "</ul>";
  echo json_encode(["html"=>$html]);
}
