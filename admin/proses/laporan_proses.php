<?php
include __DIR__ . '/../../includes/db.php';

// Ambil filter bulan & tahun
$bulan = $_POST['bulan'] ?? date('m');
$tahun = $_POST['tahun'] ?? date('Y');

// Daftar nama bulan Indonesia
$nama_bulan = [
  1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
  5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
  9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

// Query laporan transaksi
$sql = "
  SELECT 
    t.id_transaksi,
    t.tanggal,
    p.nama AS nama,
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

$q = mysqli_query($conn, $sql);
if (!$q) {
  die("❌ Query gagal dijalankan: " . mysqli_error($conn));
}

$total = 0;
?>

<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="text-pink mb-3">
      📅 Laporan Transaksi Bulan <?= $nama_bulan[(int)$bulan] ?? 'Bulan Tidak Dikenal'; ?> <?= $tahun ?>
    </h5>

    <table class="table table-bordered align-middle">
      <thead class="table-pink text-center">
        <tr>
          <th>#</th>
          <th>Tanggal</th>
          <th>Pasien</th>
          <th>Terapis</th>
          <th>Layanan</th>
          <th>Qty</th>
          <th>Harga</th>
          <th>Subtotal</th>
          <th>Diskon</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($q) > 0): $no = 1; ?>
          <?php while($r = mysqli_fetch_assoc($q)): 
            $harga = (float)($r['harga'] ?? 0);
            $subtotal = (float)($r['subtotal'] ?? 0);
            $diskon = (float)($r['diskon'] ?? 0);
            $totalTrans = (float)($r['total'] ?? 0);
            $total += $totalTrans;
          ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
            <td><?= htmlspecialchars($r['nama'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['nama_terapis'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['nama_layanan'] ?? '-') ?></td>
            <td class="text-center"><?= (int)($r['qty'] ?? 0) ?></td>
            <td class="text-end">Rp<?= number_format($harga, 0, ',', '.') ?></td>
            <td class="text-end">Rp<?= number_format($subtotal, 0, ',', '.') ?></td>
            <td class="text-end">Rp<?= number_format($diskon, 0, ',', '.') ?></td>
            <td class="text-end"><b>Rp<?= number_format($totalTrans, 0, ',', '.') ?></b></td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="10" class="text-center text-muted">Tidak ada data transaksi untuk periode ini.</td></tr>
        <?php endif; ?>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="9" class="text-end">Total Pendapatan</th>
          <th class="text-end">Rp<?= number_format((float)$total, 0, ',', '.') ?></th>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
