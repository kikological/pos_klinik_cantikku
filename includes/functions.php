<?php
// Pastikan file ini tidak bisa diakses langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location: ../login.php");
    exit;
}

// 🔒 Cek login
function cekLogin($role = null) {
    session_start();
    if (!isset($_SESSION['user_role'])) {
        header("Location: ../login.php");
        exit;
    }
    if ($role && $_SESSION['user_role'] != $role) {
        header("Location: ../login.php");
        exit;
    }
}

// 💰 Format angka ke Rupiah
function formatRupiah($angka) {
    return 'Rp' . number_format($angka, 0, ',', '.');
}

// 📅 Format tanggal Indonesia (contoh: 28 Oktober 2025)
function formatTanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecah = explode('-', substr($tanggal, 0, 10));
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}

// 🕒 Format waktu + tanggal
function formatTanggalWaktu($datetime) {
    return formatTanggal($datetime) . ' ' . date('H:i', strtotime($datetime));
}

// 🧾 Cetak struk sederhana (HTML)
function generateStruk($data) {
    /*
    $data = [
        'nama_klinik' => 'Klinik Cantikku',
        'tanggal' => '2025-10-28 10:15:00',
        'pasien' => 'Sinta Dewi',
        'layanan' => 'Facial Glow',
        'harga' => 150000,
        'diskon' => 10000,
        'total' => 140000
    ];
    */
    ob_start();
    ?>
    <div style="font-family: monospace; font-size: 12px; width: 230px;">
        <center>
            <h4><?= strtoupper($data['nama_klinik']) ?></h4>
            <small><?= formatTanggalWaktu($data['tanggal']) ?></small>
        </center>
        <hr>
        <b>Pasien:</b> <?= $data['pasien'] ?><br>
        <b>Layanan:</b> <?= $data['layanan'] ?><br>
        <b>Harga:</b> <?= formatRupiah($data['harga']) ?><br>
        <b>Diskon:</b> <?= formatRupiah($data['diskon']) ?><br>
        <hr>
        <b>Total:</b> <?= formatRupiah($data['total']) ?><br>
        <hr>
        <center><small>Terima kasih telah berkunjung 💕</small></center>
    </div>
    <?php
    return ob_get_clean();
}

// 🧮 Hitung total setelah diskon
function hitungTotal($harga, $diskon = 0) {
    $total = $harga - $diskon;
    return $total < 0 ? 0 : $total;
}

// 🧹 Amankan input dari XSS
function aman($str) {
    global $db;
    return htmlspecialchars($db->real_escape_string(trim($str)));
}
?>
