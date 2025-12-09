<div class="card shadow-sm border-0">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <ul class="nav nav-tabs" id="pasienTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="tindakan-tab" data-bs-toggle="tab" data-bs-target="#tindakan" type="button" role="tab">
            <i class="bi bi-clipboard2-pulse"></i> Input Tindakan
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat" type="button" role="tab">
            <i class="bi bi-clock-history"></i> Riwayat Pasien
          </button>
        </li>
      </ul>

      <button class="btn btn-outline-secondary btn-sm" id="btnKembali">
        <i class="bi bi-arrow-left"></i> Kembali ke Pencarian
      </button>
    </div>

    <div class="tab-content mt-3">
      <div class="tab-pane fade show active" id="tindakan">
        <div id="formTindakanContainer" class="fade-in">
          <div class="text-center text-muted py-4">🔄 Memuat form tindakan...</div>
        </div>
      </div>

      <div class="tab-pane fade" id="riwayat">
        <div id="riwayatContainer" class="fade-in">
          <div class="text-center text-muted py-4">🔄 Memuat riwayat...</div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(function() {
  const idPasien = <?= intval($_GET['id'] ?? 0) ?>;
  $("#formTindakanContainer").load("pages/input_tindakan.php?id=" + idPasien);
  $("#riwayatContainer").load("pages/riwayat_pasien.php?id=" + idPasien);

  $("#btnKembali").click(function() {
    $("#content, #page-content").fadeOut(200, function() {
      $(this).load("pages/cari_pasien.php").fadeIn(300);
    });
  });
});
</script>
