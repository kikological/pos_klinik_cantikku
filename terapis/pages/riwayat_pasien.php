<?php
require_once __DIR__ . '/../../includes/db.php';

$id_pasien = intval($_GET['id'] ?? 0);

if ($id_pasien <= 0) {
  echo "<div class='alert alert-warning'>ID pasien tidak valid.</div>";
  exit;
}

// ambil data pasien
$p = $conn->query("SELECT nama FROM pasien WHERE id_pasien=$id_pasien");
$pasien = $p->fetch_assoc()['nama'] ?? '-';

// ambil daftar layanan
$list = $conn->query("SELECT id_layanan, nama_layanan FROM layanan ORDER BY nama_layanan ASC");
?>

<h5 class="text-pink mb-3">
  <i class="bi bi-clock-history"></i> Riwayat Tindakan untuk <b><?= htmlspecialchars($pasien) ?></b>
</h5>

<!-- FILTER -->
<div class="mb-3">
  <label class="form-label">Filter Layanan</label>
  <select id="filterLayanan" class="form-select">
    <option value="">Semua Layanan</option>
    <?php while($r = $list->fetch_assoc()): ?>
      <option value="<?= $r['id_layanan'] ?>"><?= htmlspecialchars($r['nama_layanan']) ?></option>
    <?php endwhile; ?>
  </select>
</div>

<!-- Container hasil -->
<div id="riwayatTableContainer">
  <div class='text-center py-4 text-muted'>⏳ Memuat data...</div>
</div>

<style>
.text-pink { color: #e75480 !important; }
.table-pink { background-color: #ffe6ef; color:#e75480; }
</style>

<script>
function loadRiwayat() {
  $("#riwayatTableContainer").html("<div class='text-center py-4'>⏳ Memuat...</div>");

  $.post("proses/riwayat_pasien.php", {
    id_pasien: "<?= $id_pasien ?>",
    id_layanan: $("#filterLayanan").val()
  }, function(res){
    $("#riwayatTableContainer").hide().html(res).fadeIn(150);
  });
}

$(document).ready(function(){
  loadRiwayat();
  $("#filterLayanan").change(loadRiwayat);
});
</script>
