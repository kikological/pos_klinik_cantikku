<?php
require_once __DIR__ . '/../../includes/db.php';

// Ambil data klinik
$q = mysqli_query($conn, "SELECT * FROM pengaturan_klinik LIMIT 1");
$data = mysqli_fetch_assoc($q);
?>

<div class="container-fluid">
  <h4 class="fw-bold text-pink mb-4"><i class="bi bi-gear"></i> Pengaturan Klinik</h4>

  <form id="formKlinik" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nama Klinik</label>
        <input type="text" name="nama_klinik" class="form-control" value="<?= $data['nama_klinik']; ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">No. HP / Kontak</label>
        <input type="text" name="no_hp" class="form-control" value="<?= $data['no_hp']; ?>" required>
      </div>
      <div class="col-md-12">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control" rows="2" required><?= $data['alamat']; ?></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= $data['email']; ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Instagram</label>
        <input type="text" name="instagram" class="form-control" value="<?= $data['instagram']; ?>">
      </div>

      <div class="col-md-6">
        <label class="form-label">Facebook</label>
        <input type="text" name="facebook" class="form-control" value="<?= $data['facebook']; ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">TikTok</label>
        <input type="text" name="tiktok" class="form-control" value="<?= $data['tiktok']; ?>">
      </div>

      <div class="col-md-6">
        <label class="form-label">Logo Klinik</label>
        <input type="file" name="logo" class="form-control">
        <?php if (!empty($data['logo'])): ?>
          <img src="../uploads/<?= $data['logo']; ?>" alt="Logo" width="120" class="mt-2 border rounded">
        <?php endif; ?>
      </div>
<div class="col-md-6">
  <label class="form-label">Mode Printer</label>
  <select name="print_mode" class="form-select">
      <option value="usb" <?= $data['print_mode']=='usb'?'selected':'' ?>>USB / LAN Printer</option>
      <option value="bluetooth" <?= $data['print_mode']=='bluetooth'?'selected':'' ?>>Bluetooth POS</option>
  </select>
  <button type="button" class="btn btn-outline-pink w-100 mt-2" onclick="testBluetoothPrint()">
    🔍 Tes Print Bluetooth
</button>
</div>

<div class="col-md-6">
  <label class="form-label">Nama Printer / Share Name</label>
  <input type="text" name="printer_name" class="form-control" 
         placeholder="Contoh: POS-RAW atau BT-PRINTER"
         value="<?= $data['printer_name']; ?>">
</div>

<div class="col-md-6">
  <label class="form-label">Template Struk</label>
  <select name="template_struk" class="form-select">
      <option value="A" <?= $data['template_struk']=='A'?'selected':'' ?>>Template A (Lengkap)</option>
      <option value="B" <?= $data['template_struk']=='B'?'selected':'' ?>>Template B (Ringkas)</option>
  </select>
</div>


      <div class="col-md-12">
        <button class="btn btn-pink w-100 mt-3" type="submit"><i class="bi bi-save"></i> Simpan Pengaturan</button>
      </div>
    </div>
  </form>
</div>

<script>
$(document).ready(function() {
  $("#formKlinik").on("submit", function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
      url: "proses/pengaturan_klinik_proses.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function(res) {
        if (res.status === "ok") {
          Swal.fire("Berhasil!", res.message, "success");
        } else {
          Swal.fire("Gagal!", res.message, "error");
        }
      }
    });
  });
});
</script>

<script>
async function testBluetoothPrint() {
    Swal.fire({
        title: "Menghubungkan ke Printer...",
        html: "Pastikan printer RPP02N menyala",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        // Scan device (RPP02N biasanya namePrefix "RPP")
        const device = await navigator.bluetooth.requestDevice({
            filters: [{ namePrefix: "RPP" }],
            optionalServices: [0xFFE0]
        });

        // simpan device ID agar tidak pairing ulang
        localStorage.setItem("bt_printer_id", device.id);

        const server = await device.gatt.connect();
        const service = await server.getPrimaryService(0xFFE0);
        const charac = await service.getCharacteristic(0xFFE1);

        // isi test print ESC/POS
        const text = "=== TEST PRINT ===\nPrinter Bluetooth OK\nKlinik Cantikku\n\n";

        const encoder = new TextEncoder();
        const data = encoder.encode(text);

        // chunk 180 bytes
        const chunkSize = 180;
        for (let i = 0; i < data.length; i += chunkSize) {
            await charac.writeValue(data.slice(i, i + chunkSize));
        }

        Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: "Printer berhasil mencetak test struk.",
            timer: 1500,
            showConfirmButton: false
        });
    } catch (err) {
        Swal.fire("Gagal", "Tidak dapat mencetak via Bluetooth:\n" + err, "error");
        console.error(err);
    }
}
</script>

<style>
  .text-pink { color: #e75480; }
  .btn-pink { background-color: #e75480; color: white; border: none; }
  .btn-pink:hover { background-color: #d34b73; color: white; }
  .table-pink { background-color: #ffe6ef; color: #e75480; }
</style>
