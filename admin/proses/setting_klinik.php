$qKlinik = mysqli_query($conn, "SELECT * FROM pengaturan_klinik LIMIT 1");
$klinik = mysqli_fetch_assoc($qKlinik);