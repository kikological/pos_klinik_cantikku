<?php
header('Content-Type: application/json');
include __DIR__ . '/../../includes/db.php';

$aksi = $_POST['aksi'] ?? '';

if ($aksi === 'tampil') {
  $q = mysqli_query($conn, "
    SELECT t.*, p.nama_pasien, u.nama AS nama_terapis
    FROM transaksi t
    LEFT JOIN pasien p ON t.id_pasien = p.id_pasien
    LEFT JOIN users u ON t.id_user = u.id_user
    ORDER BY t.id_transaksi DESC
  ");
  $html = '';
  $no = 1;
  while ($r = mysqli_fetch_assoc($q)) {
    $html .= "
      <tr>
        <td class='text-center'>{$no}</td>
        <td>" . date('d-m-Y', strtotime($r['tanggal'])) . "</td>
        <td>{$r['nama_pasien']}</td>
        <td>{$r['nama_terapis']}</td>
        <td>Rp " . number_format($r['subtotal'], 0, ',', '.') . "</td>
        <td>Rp " . number_format($r['diskon'], 0, ',', '.') . "</td>
        <td>Rp " . number_format($r['total'], 0, ',', '.') . "</td>
        </td>
      </tr>
    ";
    $no++;
  }
  echo json_encode(["html" => $html]);
  exit;
}

elseif ($aksi === 'simpan') {
  $id_pasien = (int) $_POST['id_pasien'];
  $id_user = (int) $_POST['id_user'];
  $subtotal = (float) $_POST['subtotal'];
  $diskon = (float) $_POST['diskon'];
  $total = (float) $_POST['total'];

  if ($id_pasien <= 0 || $id_user <= 0 || $subtotal <= 0 || $total <= 0) {
    echo json_encode(["status" => "error", "message" => "Semua field wajib diisi!"]);
    exit;
  }

  $insert = mysqli_query($conn, "
    INSERT INTO transaksi (id_user, id_pasien, subtotal, diskon, total)
    VALUES ('$id_user', '$id_pasien', '$subtotal', '$diskon', '$total')
  ");

  if ($insert) {
    echo json_encode(["status" => "ok", "message" => "Transaksi berhasil disimpan!"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Gagal menyimpan: " . mysqli_error($conn)]);
  }
  exit;
}

elseif ($aksi === 'hapus') {
  $id = (int) $_POST['id_transaksi'];
  $hapus = mysqli_query($conn, "DELETE FROM transaksi WHERE id_transaksi=$id");
  echo json_encode(["status" => $hapus ? "ok" : "error"]);
  exit;
}

echo json_encode(["status" => "error", "message" => "Aksi tidak dikenal!"]);
