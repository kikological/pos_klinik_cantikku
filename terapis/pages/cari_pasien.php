<?php
require_once __DIR__ . '/../../includes/db.php';
?>

<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="text-pink mb-3">
      <i class="bi bi-search-heart"></i> Cari Pasien
    </h5>

    <div class="input-group mb-3">

      <input type="text" id="keyword" class="form-control" placeholder="Masukkan nama atau nomor HP pasien...">

      <button id="btnCari" class="btn btn-pink btn-cari-kecil">
          <i class="bi bi-search"></i> Cari
      </button>

      <button id="btnTambahPasien" class="btn btn-outline-pink btn-tambah-kecil ms-2" data-bs-toggle="modal" data-bs-target="#modalTambahPasien">
          <i class="bi bi-person-plus"></i> Tambah Pasien
      </button>

    </div>

    <div id="hasilCari"></div>
  </div>
</div>

<!-- Modal Tambah Pasien -->
<div class="modal fade" id="modalTambahPasien" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-header bg-pink text-white">
        <h5 class="modal-title"><i class="bi bi-person-plus"></i> Tambah Pasien Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="formTambahPasien">

          <div class="mb-3">
            <label>No. Register</label>
            <input type="text" name="no_register" class="form-control" pattern="[0-9]+" required>
          </div>

          <div class="mb-3">
            <label>Nama Pasien</label>
            <input type="text" name="nama" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>No HP</label>
            <input type="text" name="no_hp" class="form-control" pattern="[0-9]+" required>
          </div>

          <div class="mb-3">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control"></textarea>
          </div>

          <div class="mb-3">
            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-control">
          </div>

          <div class="mb-3">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-select" required>
              <option value="">-- Pilih --</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-pink">
              <i class="bi bi-save"></i> Simpan
            </button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

<script>
$(document).ready(function () {
	
	
  // ───────────────────────────────
  // Hanya angka untuk input No register
  // ───────────────────────────────
  $("#no_register").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, '');
  });

  // ───────────────────────────────
  // Hanya angka untuk input HP
  // ───────────────────────────────
  $("#no_hp").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, '');
  });

  // ───────────────────────────────
  // Show / Hide tombol Tambah Pasien
  // ───────────────────────────────
  function hideTambahBtn() {
    $("#btnTambahPasien").stop().fadeOut(150);
  }

  function showTambahBtn() {
    $("#btnTambahPasien").stop().fadeIn(200);
  }

  // ───────────────────────────────
  // Tombol Cari ditekan
  // ───────────────────────────────
  $("#btnCari").click(function () {
    let keyword = $("#keyword").val().trim();

    hideTambahBtn();

    if (keyword === "") {
      $("#hasilCari").html('<div class="alert alert-warning">Masukkan kata kunci pencarian.</div>');
      return;
    }

    $("#hasilCari").html('<div class="text-center text-muted py-2 fade">🔍 Mencari...</div>');

    $.ajax({
      url: "proses/cari_pasien.php",
      method: "POST",
      data: { keyword: keyword },

      success: function (res) {

        if (res.includes("Tidak ditemukan")) {
          showTambahBtn();
        } else {
          hideTambahBtn();
        }

        $("#hasilCari").hide().html(res).fadeIn(250);
      },

      error: function () {
        $("#hasilCari").html('<div class="alert alert-danger">Terjadi kesalahan saat mencari data.</div>');
        showTambahBtn();
      }
    });
  });

  // ───────────────────────────────
  // Submit Form Tambah Pasien
  // ───────────────────────────────
  $("#formTambahPasien").submit(function (e) {
    e.preventDefault();

    $.ajax({
      url: "proses/tambah_pasien.php",   // ✔ FIX PATH BENAR
      type: "POST",
      data: $(this).serialize(),
      dataType: "json",

      success: function (res) {

        if (res.status === "ok") {
          Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: res.message,
            timer: 1600,
            showConfirmButton: false
          });

          $("#formTambahPasien")[0].reset();
          $("#modalTambahPasien").modal("hide");

          setTimeout(() => {
			$("#content").load("pages/split_view.php?id=" + res.id_pasien);
		  }, 1200);

        } else {
          Swal.fire("Gagal", res.message, "error");
        }
      },

      error: function (xhr) {
        Swal.fire("Error", "Terjadi kesalahan:\n" + xhr.responseText, "error");
      }
    });

  });

});
</script>

<style>
.btn-cari-kecil, 
.btn-tambah-kecil {
    padding: 6px 16px !important;
    white-space: nowrap;
    border-radius: 8px;
    font-size: 0.9rem;
    height: 38px;
}

.text-pink { color: #e75480 !important; }
.btn-pink { background-color: #e75480; color: white; }
.btn-outline-pink { border: 1px solid #e75480; color: #e75480; }
.btn-pink:hover, .btn-outline-pink:hover { background-color: #d9486f; color: white; }
.bg-pink { background-color: #e75480 !important; }
</style>
