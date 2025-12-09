<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/fpdf186/fpdf.php';

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

// Konversi nama bulan ke Bahasa Indonesia
$namaBulan = [
  '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
  '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
  '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// Query data transaksi
$sql = "
  SELECT 
    t.tanggal,
    p.nama AS nama_pasien,
    u.nama AS nama_terapis,
    l.nama_layanan,
    d.qty,
    d.harga,
    d.subtotal,
    t.diskon,
    t.total
  FROM transaksi t
  LEFT JOIN users u ON t.id_user = u.id_user
  LEFT JOIN pasien p ON t.id_pasien = p.id_pasien
  LEFT JOIN detail_transaksi d ON t.id_transaksi = d.id_transaksi
  LEFT JOIN layanan l ON d.id_layanan = l.id_layanan
  WHERE MONTH(t.tanggal) = '$bulan' 
    AND YEAR(t.tanggal) = '$tahun'
  ORDER BY t.tanggal DESC
";
$result = $conn->query($sql);

// Buat PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'LAPORAN TRANSAKSI BULAN ' . strtoupper($namaBulan[$bulan]) . ' ' . $tahun, 0, 1, 'C');
$pdf->Ln(5);

// Header Tabel
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(231, 84, 128);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Tanggal', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Pasien', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Terapis', 1, 0, 'C', true);
$pdf->Cell(45, 8, 'Layanan', 1, 0, 'C', true);
$pdf->Cell(15, 8, 'Qty', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Harga', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Subtotal', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Diskon', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Total', 1, 1, 'C', true);

// Isi Data
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0);
$totalPendapatan = 0;
$no = 1;

while ($r = $result->fetch_assoc()) {
  $pdf->Cell(10, 8, $no++, 1, 0, 'C');
  $pdf->Cell(25, 8, date('d/m/Y', strtotime($r['tanggal'])), 1, 0, 'C');
  $pdf->Cell(35, 8, $r['nama_pasien'], 1, 0);
  $pdf->Cell(35, 8, $r['nama_terapis'], 1, 0);
  $pdf->Cell(45, 8, $r['nama_layanan'], 1, 0);
  $pdf->Cell(15, 8, $r['qty'], 1, 0, 'C');
  $pdf->Cell(25, 8, 'Rp' . number_format($r['harga'], 0, ',', '.'), 1, 0, 'R');
  $pdf->Cell(25, 8, 'Rp' . number_format($r['subtotal'], 0, ',', '.'), 1, 0, 'R');
  $pdf->Cell(25, 8, 'Rp' . number_format($r['diskon'], 0, ',', '.'), 1, 0, 'R');
  $pdf->Cell(25, 8, 'Rp' . number_format($r['total'], 0, ',', '.'), 1, 1, 'R');
  $totalPendapatan += $r['total'];
}

// Total Pendapatan
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(240, 8, 'TOTAL PENDAPATAN', 1, 0, 'R', true);
$pdf->Cell(25, 8, 'Rp' . number_format($totalPendapatan, 0, ',', '.'), 1, 1, 'R', true);

// Output
$pdf->Output('I', "Laporan_Transaksi_{$namaBulan[$bulan]}_{$tahun}.pdf");
exit;
?>
