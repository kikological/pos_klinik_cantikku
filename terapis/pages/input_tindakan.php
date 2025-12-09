<?php
require_once __DIR__ . '/../../includes/db.php';

/* ========================
   AMBIL ID PASIEN
======================== */
$id_pasien = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pasien = null;

if ($id_pasien > 0) {
  $q_pasien = $conn->query("SELECT * FROM pasien WHERE id_pasien='$id_pasien' LIMIT 1");
  if ($q_pasien && $q_pasien->num_rows > 0) {
    $pasien = $q_pasien->fetch_assoc();
  }
}

if (!$pasien) {
  echo "<div class='alert alert-danger text-center'>
          ❌ Data pasien tidak ditemukan atau ID tidak valid.<br>
          <button class='btn btn-sm btn-pink mt-2' onclick=\"$('#page-content').load('pages/cari_pasien.php')\">
            <i class='bi bi-arrow-left'></i> Kembali ke Cari Pasien
          </button>
        </div>";
  exit;
}

/* ========================
   AMBIL LAYANAN
======================== */
$q_layanan = $conn->query("SELECT * FROM layanan ORDER BY nama_layanan ASC");

/* ========================
   AMBIL PRINT CONFIG
======================== */
$qcfg = $conn->query("SELECT * FROM print_config LIMIT 1");
$config = $qcfg->fetch_assoc();

$print_mode    = $config['mode'] ?? 'usb';       // usb / bluetooth
$print_template = $config['template'] ?? 'html_b';
?>

<div class="card shadow-sm border-0 rounded-4">
  <div class="card-body">
    <h5 class="text-pink mb-3">
      <i class="bi bi-clipboard2-pulse"></i> Input Tindakan untuk 
      <b><?= htmlspecialchars($pasien['nama'] ?? '(Tanpa Nama)'); ?></b>
    </h5>

    <form id="formTindakan" autocomplete="off">
      <input type="hidden" id="id_pasien" name="id_pasien" value="<?= $id_pasien ?>">

      <div id="daftarTindakan">
        <div class="row tindakan-item mb-2">
          <div class="col-md-6">
            <select name="layanan_id[]" class="form-select layanan-select" required>
              <option value="">-- Pilih Layanan --</option>
              <?php while ($r = $q_layanan->fetch_assoc()): ?>
                <option value="<?= $r['id_layanan'] ?>" data-harga="<?= $r['harga'] ?>">
                  <?= htmlspecialchars($r['nama_layanan']) ?> - Rp<?= number_format($r['harga'], 0, ',', '.') ?>
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
            <button type="button" class="btn btn-danger btn-sm removeItem">
              <i class="bi bi-x"></i>
            </button>
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
        <button type="submit" class="btn btn-pink px-4">
          <i class="bi bi-save"></i> Simpan & Cetak
        </button>
      </div>
    </form>
  </div>
</div>

<!-- SWEETALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- INPUT TINDAKAN SCRIPT -->
<script src="pages/input_tindakan.js?v=<?= time() ?>"></script>

<script>
  // Ambil konfigurasi printer dari database
  let PRINT_MODE = "<?= $print_mode ?>";          // usb / bluetooth
  let PRINT_TEMPLATE = "<?= $print_template ?>";  // html_a / html_b
</script>

<style>
  .text-pink { color: #e75480 !important; }
  .btn-pink {
    background-color: #e75480;
    color: #fff;
    border: none;
  }
  .btn-pink:hover { background-color: #d9486f; color: #fff; }
  .btn-outline-pink {
    border: 1px solid #e75480;
    color: #e75480;
  }
  .btn-outline-pink:hover {
    background-color: #e75480;
    color: white;
  }
  .is-invalid {
    border-color: #ff6b81 !important;
    background-color: #ffe6ea !important;
  }
</style>
