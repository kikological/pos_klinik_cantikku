<?php 
include_once '../../includes/db.php';

// Ambil semua data layanan
$query = mysqli_query($conn, "SELECT * FROM layanan ORDER BY id_layanan DESC");
?>

<div class="container-fluid">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-pink">
      <i class="bi bi-heart-pulse"></i> Data Layanan
    </h4>
    <button class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#modalTambah">
      <i class="bi bi-plus-circle"></i> Tambah Layanan
    </button>
  </div>

  <!-- TABEL DATA -->
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead class="table-pink text-center">
            <tr>
              <th width="5%">#</th>
              <th>Nama Layanan</th>
              <th>Harga (Rp)</th>
              <th width="20%">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($query) > 0): ?>
              <?php $no = 1; while ($row = mysqli_fetch_assoc($query)): ?>
                <tr>
                  <td class="text-center"><?= $no++; ?></td>
                  <td><?= htmlspecialchars($row['nama_layanan']); ?></td>
                  <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                  <td class="text-center">
                    <button 
                      class="btn btn-sm btn-outline-warning edit-layanan"
                      data-id="<?= $row['id_layanan']; ?>"
                      data-nama="<?= htmlspecialchars($row['nama_layanan']); ?>"
                      data-harga="<?= $row['harga']; ?>">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                    <button 
                      class="btn btn-sm btn-outline-danger delete-layanan"
                      data-id="<?= $row['id_layanan']; ?>">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center text-muted">
                  Belum ada data layanan.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- ======================= MODAL TAMBAH ======================= -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0">
      <div class="modal-header btn-pink text-white rounded-top-4">
        <h5 class="modal-title">
          <i class="bi bi-plus-circle"></i> Tambah Layanan
        </h5>
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

<!-- ======================= MODAL EDIT ======================= -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0">
      <div class="modal-header bg-warning text-white rounded-top-4">
        <h5 class="modal-title">
          <i class="bi bi-pencil-square"></i> Edit Layanan
        </h5>
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

<!-- ======================= SCRIPTS ======================= -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function () {

  // === TAMBAH LAYANAN ===
  $("#formTambahLayanan").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
      url: "../proses/layanan_proses.php",
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
          setTimeout(() => location.reload(), 1500);
        } else {
          Swal.fire("Gagal!", res.message, "error");
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        Swal.fire("Error", "Terjadi kesalahan pada server!", "error");
      }
    });
  });

  // === EDIT LAYANAN ===
  $(".edit-layanan").on("click", function () {
    $("#edit_id").val($(this).data("id"));
    $("#edit_nama").val($(this).data("nama"));
    $("#edit_harga").val($(this).data("harga"));
    $("#modalEdit").modal("show");
  });

  $("#formEditLayanan").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
      url: "../proses/layanan_proses.php",
      type: "POST",
      data: $(this).serialize() + "&aksi=simpan",
      dataType: "json",
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
          setTimeout(() => location.reload(), 1500);
        } else {
          Swal.fire("Gagal!", res.message, "error");
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        Swal.fire("Error", "Terjadi kesalahan pada server!", "error");
      }
    });
  });

  // === HAPUS LAYANAN ===
  $(".delete-layanan").on("click", function () {
    const id = $(this).data("id");

    Swal.fire({
      title: "Yakin hapus layanan ini?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, hapus",
      cancelButtonText: "Batal"
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "../proses/layanan_proses.php",
          type: "POST",
          data: { id_layanan: id, aksi: "hapus" },
          dataType: "json",
          success: function (res) {
            if (res.status === "ok") {
              Swal.fire("Berhasil!", res.message, "success");
              setTimeout(() => location.reload(), 1500);
            } else {
              Swal.fire("Gagal!", res.message, "error");
            }
          },
          error: function (xhr) {
            console.error(xhr.responseText);
            Swal.fire("Error", "Tidak dapat menghubungi server!", "error");
          }
        });
      }
    });
  });

});
</script>

<!-- ======================= STYLING ======================= -->
<style>
.text-pink {
  color: #e75480;
}
.btn-pink {
  background-color: #e75480;
  color: white;
  border: none;
}
.btn-pink:hover {
  background-color: #d34b73;
  color: white;
}
.table-pink {
  background-color: #ffe6ef;
  color: #e75480;
}
</style>
