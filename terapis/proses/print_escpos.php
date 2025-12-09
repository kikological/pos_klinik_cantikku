<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;

// ===============================
// AMBIL ID
// ===============================
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) die("ID tidak valid");

// ===============================
// PENGATURAN KLINIK
// ===============================
$set = $conn->query("SELECT * FROM pengaturan_klinik LIMIT 1")->fetch_assoc();

$printMode   = $set['print_mode'];               // usb / bluetooth
$printerName = $set['printer_name'] ?? "POS58";  // default jika kosong
$template    = $set['template_struk'] ?? "A";    // template struk

// ===============================
// DATA TRANSAKSI
// ===============================
$trans = $conn->query("
    SELECT t.*, p.nama AS nama_pasien, p.no_register
    FROM transaksi t
    LEFT JOIN pasien p ON t.id_pasien = p.id_pasien
    WHERE t.id_transaksi = $id
")->fetch_assoc();

if (!$trans) die("Transaksi tidak ditemukan");

// ===============================
// DETAIL TRANSAKSI
// ===============================
$detail = $conn->query("
    SELECT d.*, l.nama_layanan, d.subtotal 
    FROM detail_transaksi d
    LEFT JOIN layanan l ON l.id_layanan = d.id_layanan
    WHERE d.id_transaksi = $id
");

if ($printMode == "usb") {

    try {
        $connector = new WindowsPrintConnector($printerName);
        $printer   = new Printer($connector);

        // ========== LOGO ==========
        $logoPath = __DIR__ . "/../../includes/logo_mono.png";

        if (file_exists($logoPath)) {
            try {
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $logo = EscposImage::load($logoPath);
                $printer->bitImage($logo, Printer::IMG_DEFAULT);
                $printer->feed(1);
            } catch (Exception $e) {}
        }

        // ========== HEADER ==========
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_EMPHASIZED);
        $printer->text($set['nama_klinik'] . "\n");
        $printer->selectPrintMode();
        $printer->text($set['alamat'] . "\n");
        $printer->text("WA: {$set['no_hp']} | IG: {$set['instagram']}\n");
        $printer->text("--------------------------------\n");

        // ========== DATA PASIEN ==========
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Nama     : {$trans['nama_pasien']}\n");
        $printer->text("No.Reg   : {$trans['no_register']}\n");
        $printer->text("Tanggal  : " . date("d/m/Y H:i", strtotime($trans['tanggal'])) . "\n");
        $printer->text("--------------------------------\n");

        // ========== DETAIL ==========
        while ($row = $detail->fetch_assoc()) {
            $printer->setEmphasis(true);
            $printer->text($row['nama_layanan'] . "\n");
            $printer->setEmphasis(false);

            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text("Rp " . number_format($row['subtotal'], 0, ',', '.') . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
        }

        $printer->text("--------------------------------\n");

        // ========== TOTAL ==========
        $printer->setEmphasis(true);
        $printer->text("Subtotal : Rp " . number_format($trans['subtotal'], 0, ',', '.') . "\n");
        $printer->text("Diskon   : Rp " . number_format($trans['diskon'], 0, ',', '.') . "\n");
        $printer->text("TOTAL    : Rp " . number_format($trans['total'], 0, ',', '.') . "\n");
        $printer->setEmphasis(false);
        $printer->text("--------------------------------\n");

        // ========== FOOTER ==========
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Terima kasih atas kunjungannya!\n");
        $printer->feed(1);

        $printer->cut();
        $printer->close();

        echo "<script>window.close();</script>";
        exit;

    } catch (Exception $e) {
        die("Print error: " . $e->getMessage());
    }
}




// ===============================
// MODE BLUETOOTH (RawBT Android)
// Kirim ESC/POS ke JS untuk Bluetooth
// ===============================
if ($printMode == "bluetooth") {

    ob_start();
    echo "ESC/POS DATA WILL BE HERE FOR RAWBT";
    $rawData = base64_encode(ob_get_clean());

    ?>
    <script>
    const escData = "<?= $rawData ?>";
    window.location.href = "rawbt://print?base64=" + escData;
    </script>
    <?php
    exit;
}

?>
