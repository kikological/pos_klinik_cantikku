<?php
require_once __DIR__ . '/../../includes/db.php';

$id = intval($_GET['id'] ?? 0);
$q  = $conn->query("SELECT * FROM pasien WHERE id_pasien=$id");

if (!$q || $q->num_rows==0) {
  echo "<div class='p-3'>Data tidak ditemukan.</div>";
  exit;
}

$r = $q->fetch_assoc();
?>

<div class="modal-header bg-warning text-white">
  <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Pasien</h5>
  <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
  <form id="formEditPasien">

    <input type="hidden" name="id" value="<?= $r['id_pasien'] ?>">

    <div class="mb-2">
      <label>No Register</label>
      <input type="text" name="no_register" class="form-control" value="<?= $r['no_register'] ?>" required>
    </div>

    <div class="mb-2">
      <label>Nama</label>
      <input type="text" name="nama" class="form-control" value="<?= $r['nama'] ?>" required>
    </div>

    <div class="mb-2">
      <label>No HP</label>
      <input type="text" name="no_hp" class="form-control" value="<?= $r['no_hp'] ?>">
    </div>

    <div class="mb-2">
      <label>Alamat</label>
      <textarea name="alamat" class="form-control"><?= $r['alamat'] ?></textarea>
    </div>

    <div class="mb-2">
      <label>Tanggal Lahir</label>
      <input type="date" name="tanggal_lahir" class="form-control" value="<?= $r['tanggal_lahir'] ?>">
    </div>

    <div class="mb-2">
      <label>Jenis Kelamin</label>
      <select name="jenis_kelamin" class="form-select">
        <option value="">--Pilih--</option>
        <option value="L" <?= $r['jenis_kelamin']=='L'?'selected':'' ?>>Laki-laki</option>
        <option value="P" <?= $r['jenis_kelamin']=='P'?'selected':'' ?>>Perempuan</option>
      </select>
    </div>

    <div class="text-end">
      <button type="submit" class="btn btn-warning">
        <i class="bi bi-save"></i> Simpan Perubahan
      </button>
    </div>

  </form>
</div>

<script>
$("#formEditPasien").submit(function(e){
  e.preventDefault();

  $.ajax({
    url:"proses/pasien_edit.php",
    type:"POST",
    data:$(this).serialize(),
    dataType:"json",
    success:function(res){
      if(res.status=="ok"){
        Swal.fire("Berhasil!", res.message, "success");
        $("#modalEdit").modal("hide");
        loadPasien();
      } else {
        Swal.fire("Gagal", res.message, "error");
      }
    }
  });
});
</script>
