<?php
include_once '../../includes/db.php';
$set = $db->query("SELECT * FROM pengaturan LIMIT 1")->fetch_assoc();
?>

<div class="container-fluid">
  <h4 class="fw-bold text-pink mb-4"><i class="bi bi-gear"></i> Pengaturan Klinik</h4>

  <form id="formPengaturan">
    <input type="hidden" name="aksi" value="simpan">

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Nama Klinik</label>
          <input type="text" name="nama_klinik" class="form-control" value="<?= $set['nama_klinik'] ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Alamat</label>
          <textarea name="alamat" class="form-control"><?= $set['alamat'] ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">No. Telepon</label>
          <input type="text" name="telp" class="form-control" value="<?= $set['telp'] ?>">
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Instagram</label>
            <input type="text" name="instagram" class="form-control" value="<?= $set['instagram'] ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Facebook</label>
            <input type="text" name="facebook" class="form-control" value="<?= $set['facebook'] ?>">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Footer Struk</label>
          <textarea name="footer_struk" class="form-control" rows="2"><?= $set['footer_struk'] ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Pilih Printer POS</label>
          <select name="printer" class="form-select">
            <option value="<?= $set['printer'] ?>"><?= $set['printer'] ?></option>
            <option value="EPSON TM-T82">EPSON TM-T82</option>
            <option value="Bluetooth Printer">Bluetooth Printer</option>
            <option value="Thermal Printer 58mm">Thermal Printer 58mm</option>
            <option value="Thermal Printer 80mm">Thermal Printer 80mm</option>
          </select>
        </div>

        <div class="text-end">
          <button class="btn btn-pink px-4"><i class="bi bi-save"></i> Simpan Perubahan</button>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
$(document).ready(function(){
  $("#formPengaturan").submit(function(e){
    e.preventDefault();
    $.post("proses/pengaturan_proses.php", $(this).serialize(), function(res){
      alert(res.message);
    }, "json");
  });
});
</script>
