<?php
require_once __DIR__ . '/../../includes/db.php';

// Ambil filter
$cari   = mysqli_real_escape_string($conn, $_POST['cari'] ?? '');
$jk     = mysqli_real_escape_string($conn, $_POST['jk'] ?? '');
$umur   = mysqli_real_escape_string($conn, $_POST['umur'] ?? '');

// Query dasar
$sql = "
  SELECT 
    id_pasien,
    no_register,
    nama,
    no_hp,
    alamat,
    tanggal_lahir,
    jenis_kelamin
  FROM pasien
  WHERE 1
";

// 🔍 FILTER: pencarian
if ($cari !== '') {
  $sql .= " AND (nama LIKE '%$cari%' 
            OR no_hp LIKE '%$cari%'
            OR no_register LIKE '%$cari%')";
}

// 🔍 FILTER: jenis kelamin
if ($jk !== '') {
  $sql .= " AND jenis_kelamin = '$jk' ";
}

$result = mysqli_query($conn, $sql);
$filtered = [];

// 🔍 FILTER umur (hitungan dilakukan di PHP untuk akurat)
if ($umur !== '') {
  while ($row = mysqli_fetch_assoc($result)) {

    $age = 0;
    if (!empty($row['tanggal_lahir'])) {
      $age = date_diff(date_create($row['tanggal_lahir']), date_create('today'))->y;
    }

    if ($umur === "anak" && $age <= 17) {
      $filtered[] = $row;
    }

    if ($umur === "dewasa" && $age >= 18) {
      $filtered[] = $row;
    }
  }

  // Jika filter umur digunakan → override result
  $rows = $filtered;

} else {
  // Tidak pakai filter umur → ambil result langsung
  $rows = [];
  mysqli_data_seek($result, 0);
  while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
  }
}

// Jika tidak ada data
if (count($rows) == 0) {
  echo "<div class='alert alert-info text-center'>Tidak ada data pasien ditemukan.</div>";
  exit;
}

// Tampilkan tabel
echo "
<table class='table table-bordered table-hover align-middle'>
  <thead class='table-pink text-center'>
    <tr>
      <th>No.Reg</th>
      <th>Nama</th>
      <th>No HP</th>
      <th>JK</th>
      <th>Umur</th>
      <th>Alamat</th>
      <th width='120'>Aksi</th>
    </tr>
  </thead>
  <tbody>
";

foreach ($rows as $r) {

  // Hitung umur
  $umurText = "-";
  if (!empty($r['tanggal_lahir'])) {
    $umurText = date_diff(
      date_create($r['tanggal_lahir']),
      date_create('today')
    )->y . " th";
  }

  echo "
    <tr>
      <td><b>{$r['no_register']}</b></td>
      <td>". htmlspecialchars($r['nama']) ."</td>
      <td>{$r['no_hp']}</td>
      <td class='text-center'>{$r['jenis_kelamin']}</td>
      <td class='text-center'>{$umurText}</td>
      <td>". htmlspecialchars($r['alamat']) ."</td>

      <td class='text-center'>
        <button class='btn btn-sm btn-warning btnEditPasien' data-id='{$r['id_pasien']}'>
          <i class='bi bi-pencil'></i>
        </button>
        <button class='btn btn-sm btn-danger btnHapusPasien' data-id='{$r['id_pasien']}'>
          <i class='bi bi-trash'></i>
        </button>
      </td>
    </tr>
  ";
}

echo "</tbody></table>";
