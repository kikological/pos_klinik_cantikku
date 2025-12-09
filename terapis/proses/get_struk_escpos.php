<?php
// Ambil ID
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "ID tidak valid";
    exit;
}

require_once __DIR__ . '/../../includes/db.php';

// Ambil transaksi
$trans = $conn->query("
    SELECT t.*, p.nama AS nama_pasien, p.no_register
    FROM transaksi t
    LEFT JOIN pasien p ON t.id_pasien = p.id_pasien
    WHERE t.id_transaksi = $id
")->fetch_assoc();

if (!$trans) {
    echo "Transaksi tidak ditemukan";
    exit;
}

// Ambil detail
$detail = $conn->query("
    SELECT d.*, l.nama_layanan
    FROM detail_transaksi d
    LEFT JOIN layanan l ON l.id_layanan = d.id_layanan
    WHERE d.id_transaksi = $id
");

$set = $conn->query("SELECT * FROM pengaturan_klinik LIMIT 1")->fetch_assoc();

// ================
// Format pesan ESC/POS
// ================

$esc = "";
$esc .= "\x1B\x40"; // init printer
$esc .= "\x1B\x61\x01"; // center
$esc .= strtoupper($set['nama_klinik']) . "\n";
$esc .= $set['alamat'] . "\n";
$esc .= "WA: ".$set['no_hp']."\n";
$esc .= "-----------------------------\n";

$esc .= "\x1B\x61\x00"; // left
$esc .= "Nama : ".$trans['nama_pasien']."\n";
$esc .= "Reg  : ".$trans['no_register']."\n";
$esc .= "Tgl  : ".date("d/m/Y H:i", strtotime($trans['tanggal']))."\n";
$esc .= "-----------------------------\n";

while ($r = $detail->fetch_assoc()) {
    $esc .= strtoupper($r['nama_layanan']) . "\n";
    $esc .= "\x1B\x61\x02"; // right
    $esc .= "Rp " . number_format($r['subtotal'], 0, ',', '.') . "\n";
    $esc .= "\x1B\x61\x00"; // left
}

$esc .= "-----------------------------\n";
$esc .= "Subtotal : Rp ".number_format($trans['subtotal'], 0, ',', '.')."\n";
$esc .= "Diskon   : Rp ".number_format($trans['diskon'], 0, ',', '.')."\n";
$esc .= "TOTAL    : Rp ".number_format($trans['total'], 0, ',', '.')."\n";
$esc .= "-----------------------------\n";

$esc .= "\x1B\x61\x01";
$esc .= "Terima kasih!\n\n\n";

// base64 encode → dikirim ke JavaScript
$encoded = base64_encode($esc);

?>
<script>

// ================================
// Bluetooth UUID RPP02N BLE
// ================================
const SERVICE_UUID  = "000018f0-0000-1000-8000-00805f9b34fb";
const WRITE_UUID    = "00002af1-0000-1000-8000-00805f9b34fb";

async function printBluetooth() {
    try {
        // 1. Pilih device
        const device = await navigator.bluetooth.requestDevice({
            filters: [{
                services: [SERVICE_UUID]
            }]
        });

        // 2. Connect GATT
        const server = await device.gatt.connect();

        // 3. Ambil service
        const service = await server.getPrimaryService(SERVICE_UUID);

        // 4. Ambil characteristic write
        const characteristic = await service.getCharacteristic(WRITE_UUID);

        // 5. Decode ESC/POS
        const data = atob("<?= $encoded ?>");
        const buffer = new Uint8Array([...data].map(c => c.charCodeAt(0)));

        // 6. Kirim ke printer
        await characteristic.writeValue(buffer);

        alert("Berhasil mencetak via Bluetooth!");

        window.close();

    } catch (err) {
        alert("Gagal print BLE: " + err);
        console.error(err);
    }
}

printBluetooth();

</script>
