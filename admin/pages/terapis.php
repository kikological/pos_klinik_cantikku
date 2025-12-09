<?php
include_once '../../includes/db.php';
?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-pink"><i class="bi bi-person-badge"></i> Data Terapis</h4>
    <button class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#modalTambah">
      <i class="bi bi-plus-circle"></i> Tambah Terapis
    </button>
  </div>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle" id="tabel-terapis">
          <thead class="table-pink text-center">
            <tr>
              <th width="5%">#</th>
              <th>Nama</th>
              <th>Username</th>
              <th>Role</th>
              <th width="20%">Aksi</th>
            </tr>
          </thead>
          <tbody></tbody>
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
        <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Tambah Terapis</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formTambahTerapis">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
            <div class="form-text text-muted">Username harus unik.</div>
          </div>

          <div class="mb-3 position-relative">
            <label class="form-label">Password</label>
            <div class="input-group">
              <input type="password" name="password" id="tambah_password" class="form-control" required minlength="6">
              <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="#tambah_password">
                <i class="bi bi-eye-slash"></i>
              </button>
            </div>
            <div class="form-text text-muted">Minimal 6 karakter.</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="">-- Pilih Role --</option>
              <option value="terapis">Terapis</option>
              <option value="admin">Admin</option>
            </select>
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
        <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Terapis</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditTerapis">
        <input type="hidden" name="id_user" id="edit_id">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" id="edit_nama" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" id="edit_username" class="form-control" required>
            <div class="form-text text-muted">Username harus unik.</div>
          </div>

          <div class="mb-3 position-relative">
            <label class="form-label">Password (Opsional)</label>
            <div class="input-group">
              <input type="password" name="password" id="edit_password" class="form-control" placeholder="Kosongkan jika tidak ingin mengganti password">
              <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="#edit_password">
                <i class="bi bi-eye-slash"></i>
              </button>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" id="edit_role" class="form-select" required>
              <option value="terapis">Terapis</option>
              <option value="admin">Admin</option>
            </select>
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
$(document).ready(function() {
  const apiUrl = "/pos_klinik_cantikku/admin/proses/terapis_proses.php";

  // === TAMPIL DATA ===
  function loadTerapis() {
    $.ajax({
      url: apiUrl,
      type: "POST",
      data: { aksi: "tampil" },
      dataType: "json",
      success: function(res) {
        $("#tabel-terapis tbody").html(res.html);
      },
      error: function() {
        $("#tabel-terapis tbody").html("<tr><td colspan='5' class='text-center text-danger'>Gagal memuat data.</td></tr>");
      }
    });
  }
  loadTerapis();

  // === TAMBAH ===
  $("#formTambahTerapis").on("submit", function(e) {
    e.preventDefault();
    $.ajax({
      url: apiUrl,
      type: "POST",
      data: $(this).serialize() + "&aksi=simpan",
      dataType: "json",
      success: function(res) {
        if (res.status === "ok") {
          Swal.fire({ icon: "success", title: "Berhasil!", text: res.message, timer: 1500, showConfirmButton: false });
          $("#modalTambah").modal("hide");
          loadTerapis();
          $("#formTambahTerapis")[0].reset();
        } else Swal.fire("Gagal!", res.message, "error");
      }
    });
  });

  // === EDIT ===
  $(document).on("click", ".btn-edit", function() {
    $("#edit_id").val($(this).data("id"));
    $("#edit_nama").val($(this).data("nama"));
    $("#edit_username").val($(this).data("username"));
    $("#edit_role").val($(this).data("role"));
    $("#edit_password").val("");
    $("#modalEdit").modal("show");
  });

  // === UPDATE ===
  $("#formEditTerapis").on("submit", function(e) {
    e.preventDefault();
    $.ajax({
      url: apiUrl,
      type: "POST",
      data: $(this).serialize() + "&aksi=edit",
      dataType: "json",
      success: function(res) {
        if (res.status === "ok") {
          Swal.fire({ icon: "success", title: "Diperbarui!", text: res.message, timer: 1500, showConfirmButton: false });
          $("#modalEdit").modal("hide");
          loadTerapis();
        } else Swal.fire("Gagal!", res.message, "error");
      }
    });
  });

  // === HAPUS ===
  $(document).on("click", ".btn-hapus", function() {
    const id = $(this).data("id");
    Swal.fire({
      title: "Yakin ingin menghapus?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, hapus",
      cancelButtonText: "Batal"
    }).then((r) => {
      if (r.isConfirmed) {
        $.ajax({
          url: apiUrl,
          type: "POST",
          data: { aksi: "hapus", id_user: id },
          dataType: "json",
          success: function(res) {
            if (res.status === "ok") {
              Swal.fire("Berhasil!", res.message, "success");
              loadTerapis();
            } else Swal.fire("Gagal!", res.message, "error");
          }
        });
      }
    });
  });

  // === CEK USERNAME REAL-TIME ===
  $(document).on("input", "input[name='username']", function () {
    const username = $(this).val().trim();
    const inputField = $(this);

    if (username.length < 3) {
      inputField.removeClass("is-valid is-invalid");
      inputField.next(".invalid-feedback").remove();
      return;
    }

    $.ajax({
      url: apiUrl,
      type: "POST",
      data: { aksi: "cek_username", username },
      dataType: "json",
      success: function (res) {
        if (res.exists) {
          inputField.addClass("is-invalid").removeClass("is-valid");
          if (inputField.next(".invalid-feedback").length === 0) {
            inputField.after('<div class="invalid-feedback">Username sudah digunakan!</div>');
          }
        } else {
          inputField.addClass("is-valid").removeClass("is-invalid");
          inputField.next(".invalid-feedback").remove();
        }
      }
    });
  });

  // === TOGGLE PASSWORD VISIBILITY ===
  $(document).on("click", ".toggle-pass", function() {
    const target = $(this).data("target");
    const input = $(target);
    const icon = $(this).find("i");
    if (input.attr("type") === "password") {
      input.attr("type", "text");
      icon.removeClass("bi-eye-slash").addClass("bi-eye");
    } else {
      input.attr("type", "password");
      icon.removeClass("bi-eye").addClass("bi-eye-slash");
    }
  });
});
</script>

<style>
.text-pink { color: #e75480; }
.btn-pink { background-color: #e75480; color: white; border: none; }
.btn-pink:hover { background-color: #d34b73; color: white; }
.table-pink { background-color: #ffe6ef; color: #e75480; }
</style>
