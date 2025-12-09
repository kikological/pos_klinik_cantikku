<?php
require_once __DIR__ . '/../../includes/db.php';

$id_pasien = intval($_GET['id'] ?? 0);
$q = $conn->query("SELECT * FROM pasien WHERE id_pasien = $id_pasien");
$pasien = $q ? $q->fetch_assoc() : null;

if (!$pasien) {
  echo "<div class='alert alert-danger'>Data pasien tidak ditemukan.</div>";
  exit;
}

$q_layanan = $conn->query("SELECT * FROM layanan ORDER BY nama_layanan ASC");
?>
<div class="card shadow-sm">
  <div class="card-body">
    <h6 class="text-pink mb-3">
      <i class="bi bi-clipboard2-pulse"></i> Input Tindakan untuk <b><?= htmlspecialchars($pasien['nama']) ?></b>
    </h6>

    <form id="formTindakan">
      <input type="hidden" name="id_pasien" value="<?= $id_pasien ?>">

      <div id="daftarTindakan">
        <div class="row tindakan-item mb-2">
          <div class="col-md-6">
            <select name="layanan_id[]" class="form-select layanan-select" required>
              <option value="">-- Pilih Layanan --</option>
              <?php while ($r = $q_layanan->fetch_assoc()): ?>
                <option value="<?= $r['id_layanan'] ?>" data-harga="<?= $r['harga'] ?>">
                  <?= htmlspecialchars($r['nama_layanan']) ?> - Rp<?= number_format($r['harga'],0,',','.') ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-3">
            <input type="text" name="harga[]" class="form-control harga" placeholder="Harga" readonly>
          </div>
          <div class="col-md-2">
            <input type="number" name="qty[]" class="form-control qty" value="1" min="1">
          </div>
          <div class="col-md-1 text-end">
            <button type="button" class="btn btn-danger btn-sm removeItem"><i class="bi bi-x"></i></button>
          </div>
        </div>
      </div>

      <button type="button" id="addTindakan" class="btn btn-outline-pink mb-3">
        <i class="bi bi-plus-circle"></i> Tambah Layanan
      </button>

      <div class="mb-3">
        <label>Diskon (Rp)</label>
        <input type="number" id="diskon" name="diskon" class="form-control" value="0" min="0">
      </div>

      <div class="mb-3">
        <label>Total Bayar</label>
        <input type="text" id="total" name="total" class="form-control" readonly>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-pink"><i class="bi bi-save"></i> Simpan & Cetak</button>
      </div>
    </form>
  </div>
</div>

<script src="pages/input_tindakan.js"></script>
