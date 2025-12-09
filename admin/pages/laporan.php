<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="text-pink mb-3">
      <i class="bi bi-clipboard-data"></i> Laporan Transaksi
    </h5>

    <div class="row mb-3 align-items-end">
      <div class="col-md-3">
        <label>Bulan</label>
        <select id="bulan" class="form-select">
          <?php
          $bulan_indonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
          ];
          $bulan_sekarang = date('n');
          foreach ($bulan_indonesia as $num => $nama) {
            $selected = ($num == $bulan_sekarang) ? 'selected' : '';
            echo "<option value='$num' $selected>$nama</option>";
          }
          ?>
        </select>
      </div>

      <div class="col-md-3">
        <label>Tahun</label>
        <select id="tahun" class="form-select">
          <?php
          $tahun_sekarang = date('Y');
          for ($t = 2022; $t <= $tahun_sekarang; $t++) {
            $selected = ($t == $tahun_sekarang) ? 'selected' : '';
            echo "<option value='$t' $selected>$t</option>";
          }
          ?>
        </select>
      </div>
<br><br>
<div class="d-flex justify-content-between mb-3">
  <button class="btn btn-pink" id="btnTampil">
    <i class="bi bi-eye"></i> Tampilkan
  </button>

  <a id="btnPDF" href="#" target="_blank" class="btn btn-outline-danger">
    <i class="bi bi-file-earmark-pdf"></i> Cetak Laporan ke PDF
  </a>
</div>
    </div>

    <div id="hasilLaporan">
      <div class="text-center text-muted py-3">
        Silakan pilih bulan dan tahun, lalu klik <b>Tampilkan</b>.
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  function loadLaporan() {
    const bulan = $("#bulan").val();
    const tahun = $("#tahun").val();
    $("#hasilLaporan").html('<div class="text-center py-4 text-muted">⏳ Memuat data...</div>');
    $.post("proses/laporan_proses.php", { bulan, tahun }, function(res){
      $("#hasilLaporan").html(res);
    }).fail(function(){
      $("#hasilLaporan").html('<div class="alert alert-danger">Gagal memuat laporan.</div>');
    });
  }

  $("#btnTampil").click(loadLaporan);

  // === CETAK PDF ===
$("#btnPDF").click(function(e){
  e.preventDefault();
  const bulan = $("#bulan").val();
  const tahun = $("#tahun").val();
  window.open(`proses/laporan_pdf.php?bulan=${bulan}&tahun=${tahun}`, '_blank');
});

  // Muat otomatis saat halaman dibuka
  loadLaporan();
});
</script>

<style>
.text-pink { color: #d63384 !important; }
.table-pink { background-color: #ffd6e0 !important; }
.btn-pink {
  background-color: #d63384;
  color: white;
  border: none;
}
.btn-pink:hover {
  background-color: #b02a6d;
  color: white;
}
.btn-outline-pink {
  border: 1px solid #d63384;
  color: #d63384;
  background: transparent;
}
.btn-outline-pink:hover {
  background-color: #d63384;
  color: white;
}
</style>
