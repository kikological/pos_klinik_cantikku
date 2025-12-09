<?php
$conn = mysqli_connect("localhost", "root", "", "klinik_cantikku");
if (!$conn) {
  die("Koneksi database gagal: " . mysqli_connect_error());
}
// Supaya karakter non-latin & emoji aman
$conn->set_charset('utf8mb4');
?>
