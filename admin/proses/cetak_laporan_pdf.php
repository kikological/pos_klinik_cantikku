<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/fpdf186/fpdf.php'; // pastikan path FPDF kamu benar

$bulan = $_POST['bulan'] ?? date('m');
$tahun = $_POST['tahun'] ?? date('Y');

$sql = "
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
  LEFT JOIN users u ON t.id_user = u.id_user
  LEFT JOIN pasien p ON t.id_pasien = p.id_pasien
  LEFT JOIN detail_transaksi d ON t.id_transaksi = d.id_transaksi
  LEFT JOIN layanan l ON d.id_layanan = l.id_layanan
  WHERE MONTH(t.tanggal) = '$bulan' AND YEAR(t.tanggal) = '$tahun'
  ORDER BY t.tanggal DESC
";

$q = mysqli_query($conn, $sql);

// Inisialisasi PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// Header
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Laporan Transaksi Ayusasa Mom & Baby Care', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, 'Periode: ' . date('F', mktime(0, 0, 0, $bulan, 10)) . " $tahun", 0, 1, 'C');
$pdf->Ln(5);

// Header tabel
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(231, 84, 128);
$pdf->SetTextColor(255);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Tanggal', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Pasien', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Terapis', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Layanan', 1, 0, 'C', true);
$pdf->Cell(10, 8, 'Qty', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Harga', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Subtotal', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Diskon', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Total', 1, 1, 'C', true);

// Isi tabel
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0);
$no = 1;
$total_semua = 0;

while ($r = mysqli_fetch_assoc($q)) {
  $pdf->Cell(10, 8, $no++, 1, 0, 'C');
  $pdf->Cell(30, 8, date('d/m/Y', strtotime($r['tanggal'])), 1);
  $pdf->Cell(35, 8, $r['nama_pasien'], 1);
  $pdf->Cell(35, 8, $r['nama_terapis'], 1);
  $pdf->Cell(40, 8, $r['nama_layanan'], 1);
  $pdf->Cell(10, 8, $r['qty'], 1, 0, 'C');
  $pdf->Cell(25, 8, number_format($r['harga'], 0, ',', '.'), 1, 0, 'R');
  $pdf->Cell(25, 8, number_format($r['subtotal'], 0, ',', '.'), 1, 0, 'R');
  $pdf->Cell(20, 8, number_format($r['diskon'], 0, ',', '.'), 1, 0, 'R');
  $pdf->Cell(25, 8, number_format($r['total'], 0, ',', '.'), 1, 1, 'R');
  $total_semua += $r['total'];
}

// Total akhir
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(230, 8, 'TOTAL PENDAPATAN', 1, 0, 'R', true);
$pdf->Cell(25, 8, 'Rp ' . number_format($total_semua, 0, ',', '.'), 1, 1, 'R', true);

// Output
$pdf->Output('I', "Laporan_Transaksi_{$bulan}_{$tahun}.pdf");
?>
