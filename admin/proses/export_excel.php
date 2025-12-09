<?php
// ================================================
// EXPORT LAPORAN TRANSAKSI KE EXCEL (.xlsx)
// ================================================
require_once __DIR__ . '/../../vendor/autoload.php';

// Ambil filter bulan & tahun dari form
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

// Query transaksi lengkap dengan relasi
$query = "
  SELECT 
    t.tanggal,
    p.nama_pasien,
    u.nama AS nama_terapis,
    l.nama_layanan,
    d.qty,
    d.harga,
    d.subtotal,
    t.diskon,
    t.total
  FROM transaksi t
  LEFT JOIN pasien p ON t.id_pasien = p.id_pasien
  LEFT JOIN users u ON t.id_user = u.id_user
  LEFT JOIN detail_transaksi d ON t.id_transaksi = d.id_transaksi
  LEFT JOIN layanan l ON d.id_layanan = l.id_layanan
  WHERE MONTH(t.tanggal) = '$bulan' AND YEAR(t.tanggal) = '$tahun'
  ORDER BY t.tanggal DESC
";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("❌ Query gagal: " . mysqli_error($conn));
}

// Buat file Excel menggunakan PHPSpreadsheet
require_once __DIR__ . '/../../includes/phpspreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// === Judul Laporan ===
$sheet->setCellValue('A1', 'Laporan Transaksi Klinik Cantikku');
$sheet->setCellValue('A2', 'Periode: ' . date('F', mktime(0, 0, 0, $bulan, 10)) . " $tahun");
$sheet->mergeCells('A1:J1');
$sheet->mergeCells('A2:J2');
$sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);

// === Header Kolom ===
$headers = ['No', 'Tanggal', 'Pasien', 'Terapis', 'Layanan', 'Qty', 'Harga', 'Subtotal', 'Diskon', 'Total'];
$col = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($col.'4', $h);
    $sheet->getStyle($col.'4')->getFont()->setBold(true);
    $sheet->getColumnDimension($col)->setAutoSize(true);
    $col++;
}

// === Isi Data ===
$rowNum = 5;
$no = 1;
$totalPendapatan = 0;
while ($r = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue("A$rowNum", $no++);
    $sheet->setCellValue("B$rowNum", date('d/m/Y H:i', strtotime($r['tanggal'])));
    $sheet->setCellValue("C$rowNum", $r['nama_pasien']);
    $sheet->setCellValue("D$rowNum", $r['nama_terapis']);
    $sheet->setCellValue("E$rowNum", $r['nama_layanan']);
    $sheet->setCellValue("F$rowNum", $r['qty']);
    $sheet->setCellValue("G$rowNum", $r['harga']);
    $sheet->setCellValue("H$rowNum", $r['subtotal']);
    $sheet->setCellValue("I$rowNum", $r['diskon']);
    $sheet->setCellValue("J$rowNum", $r['total']);
    $totalPendapatan += $r['total'];
    $rowNum++;
}

// === Baris Total ===
$sheet->setCellValue("I$rowNum", 'TOTAL');
$sheet->setCellValue("J$rowNum", $totalPendapatan);
$sheet->getStyle("I$rowNum:J$rowNum")->getFont()->setBold(true);

// === Gaya Border ===
$styleArray = [
    'borders' => [
        'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
    ],
];
$sheet->getStyle("A4:J$rowNum")->applyFromArray($styleArray);

// === Output ke browser ===
$filename = "Laporan_Transaksi_{$bulan}_{$tahun}.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
