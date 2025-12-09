<?php
include_once '../../includes/db.php';
?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-pink"><i class="bi bi-heart-pulse"></i> Data Layanan</h4>
    <button class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#modalTambah">
      <i class="bi bi-plus-circle"></i> Tambah Layanan
    </button>
  </div>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle" id="tabel-layanan">
          <thead class="table-pink text-center">
            <tr>
              <th width="5%">#</th>
              <th>Nama Layanan</th>
              <th>Harga (Rp)</th>
              <th width="20%">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <!-- Data akan dimuat via AJAX -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0">
      <div class="modal-header btn-pink text-white rounded-top-4">
        <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Tambah Layanan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formTambahLayanan">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Layanan</label>
            <input type="text" name="nama_layanan" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Harga (Rp)</label>
            <input type="number" name="harga" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-pink w-100">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0">
      <div class="modal-header bg-warning text-white rounded-top-4">
        <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Layanan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditLayanan">
        <input type="hidden" name="id_layanan" id="edit_id">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Layanan</label>
            <input type="text" name="nama_layanan" id="edit_nama" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Harga (Rp)</label>
            <input type="number" name="harga" id="edit_harga" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning w-100">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
  const apiUrl = "/pos_klinik_cantikku/admin/proses/layanan_proses.php";

  // === TAMPILKAN DATA ===
  function loadLayanan() {
    $.ajax({
      url: apiUrl,
      type: "POST",
      data: { aksi: "tampil" },
      dataType: "json",
      success: function (res) {
        $("#tabel-layanan tbody").html(res.html);
      },
      error: function () {
        $("#tabel-layanan tbody").html(
          "<tr><td colspan='4' class='text-center text-danger'>Gagal memuat data.</td></tr>"
        );
      }
    });
  }
  loadLayanan();

  // === TAMBAH LAYANAN ===
$("#formTambahLayanan").on("submit", function (e) {
  e.preventDefault();
  $.ajax({
    url: apiUrl,
    type: "POST",
    data: $(this).serialize() + "&aksi=simpan",
    dataType: "json",
    success: function (res) {
      if (res.status === "ok") {
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: res.message,
          timer: 1500,
          showConfirmButton: false
        });
        $("#modalTambah").modal("hide");
        $(".modal-backdrop").remove(); // ← hapus backdrop
        $("body").removeClass("modal-open"); // ← pastikan body normal lagi
        loadLayanan();
        $("#formTambahLayanan")[0].reset();
      } else {
        Swal.fire("Gagal!", res.message, "error");
      }
    },
    error: function () {
      Swal.fire("Error", "Terjadi kesalahan pada server!", "error");
    }
  });
});

  // === EDIT (TAMPILKAN DATA DI MODAL) ===
$(document).on("click", ".btn-edit", function () {
  const id = $(this).data("id");
  const nama = $(this).data("nama");
  const harga = $(this).data("harga");

  $("#edit_id").val(id);
  $("#edit_nama").val(nama);
  $("#edit_harga").val(harga);
  $("#modalEdit").modal("show");
});

// === EDIT (TAMPILKAN DATA DI MODAL) ===
$(document).on("click", ".btn-edit", function () {
  const id = $(this).data("id");
  const nama = $(this).data("nama");
  const harga = $(this).data("harga");

  // Isi modal edit
  $("#edit_id").val(id);
  $("#edit_nama").val(nama);
  $("#edit_harga").val(harga);

  // Tampilkan modal
  $("#modalEdit").modal("show");
});

// === UPDATE DATA ===
$(document).on("submit", "#formEditLayanan", function (e) {
  e.preventDefault();

  const id = $("#edit_id").val();
  const nama = $("#edit_nama").val();
  const harga = $("#edit_harga").val();

  $.ajax({
    url: "/pos_klinik_cantikku/admin/proses/layanan_proses.php",
    type: "POST",
    dataType: "json",
    data: {
      aksi: "edit",
      id_layanan: id,
      nama_layanan: nama,
      harga: harga
    },
    success: function (res) {
      if (res.status === "ok") {
        Swal.fire({
          icon: "success",
          title: "Diperbarui!",
          text: res.message,
          timer: 1500,
          showConfirmButton: false
        });
        $("#modalEdit").modal("hide");
        loadLayanan();
      } else {
        Swal.fire("Gagal!", res.message, "error");
      }
    },
    error: function (xhr) {
      Swal.fire("Error", "Gagal terhubung ke server!", "error");
      console.error(xhr.responseText);
    }
  });
});




  // === HAPUS ===
  $(document).on("click", ".btn-hapus", function () {
    const id = $(this).data("id");
    Swal.fire({
      title: "Yakin ingin menghapus?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, hapus",
      cancelButtonText: "Batal"
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: apiUrl,
          type: "POST",
          data: { aksi: "hapus", id_layanan: id },
          dataType: "json",
          success: function (res) {
            if (res.status === "ok") {
              Swal.fire("Berhasil!", res.message, "success");
              loadLayanan();
            } else {
              Swal.fire("Gagal!", res.message, "error");
            }
          },
          error: function () {
            Swal.fire("Error", "Tidak dapat menghubungi server!", "error");
          }
        });
      }
    });
  });
});
</script>

<style>
  .text-pink { color: #e75480; }
  .btn-pink { background-color: #e75480; color: white; border: none; }
  .btn-pink:hover { background-color: #d34b73; color: white; }
  .table-pink { background-color: #ffe6ef; color: #e75480; }
  
  .modal {
	z-index: 9999 !important;
	}
</style>
