<?php
require_once __DIR__ . '/db.php';

// Ambil data pengaturan klinik
$q = mysqli_query($conn, "SELECT * FROM pengaturan_klinik LIMIT 1");
$klinik = mysqli_fetch_assoc($q);

// Default kalau belum ada
if (!$klinik) {
  $klinik = [
    'nama_klinik' => 'Klinik Cantikku',
    'alamat' => 'Belum diatur',
    'no_hp' => '-',
    'email' => '-',
    'instagram' => '-',
    'facebook' => '-',
    'tiktok' => '-',
    'logo' => 'logo.png'
  ];
}

// Cek apakah logo ada di folder uploads
$logo_path = (file_exists(__DIR__ . '/../uploads/' . $klinik['logo']) && !empty($klinik['logo']))
  ? '/pos_klinik_cantikku/uploads/' . $klinik['logo']
  : '/pos_klinik_cantikku/assets/img/default_logo.png';
?>
