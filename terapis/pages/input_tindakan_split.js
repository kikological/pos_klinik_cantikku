(function () {
  $(document).off(".splitTindakan");

  function hitungTotal() {
    let total = 0;
    $(".tindakan-item").each(function () {
      const harga = parseFloat($(this).find(".harga").val()) || 0;
      const qty = parseInt($(this).find(".qty").val()) || 1;
      total += harga * qty;
    });
    const diskon = parseFloat($("#diskon").val()) || 0;
    $("#total").val(total - diskon);
  }

  // === AUTO ISI HARGA SAAT PILIH LAYANAN ===
  $(document).on("change.splitTindakan", ".layanan-select", function () {
    const harga = $("option:selected", this).data("harga") || 0;
    $(this).closest(".tindakan-item").find(".harga").val(harga);
    hitungTotal();
  });

  // === HITUNG TOTAL OTOMATIS ===
  $(document).on("input.splitTindakan", ".qty, #diskon", hitungTotal);

  // === TAMBAH BARIS LAYANAN ===
  $(document).on("click.splitTindakan", "#addTindakan", function () {
    const newItem = $(".tindakan-item:first").clone();
    newItem.find("select").val("");
    newItem.find(".harga").val("");
    newItem.find(".qty").val("1");
    $("#daftarTindakan").append(newItem);
    hitungTotal();
  });

  // === HAPUS BARIS ===
  $(document).on("click.splitTindakan", ".removeItem", function () {
    if ($(".tindakan-item").length > 1) {
      $(this).closest(".tindakan-item").remove();
      hitungTotal();
    }
  });

  // === SUBMIT FORM ===
  $(document).on("submit.splitTindakan", "#formTindakanSplit", function (e) {
    e.preventDefault();

    const formData = $(this).serialize();
    const $btn = $(this).find("button[type='submit']");
    $btn.prop("disabled", true).html(
      `<span class="spinner-border spinner-border-sm"></span> Menyimpan...`
    );

    $.ajax({
      url: "proses/simpan_tindakan.php",
      method: "POST",
      data: formData,
      dataType: "json",
      success: function (res) {
        if (res.status === "ok") {
          Swal.fire({
            icon: "success",
            title: "Transaksi Berhasil!",
            text: res.message,
            showConfirmButton: false,
            timer: 1500
          });

          printStruk(res.struk);

          // reload ke halaman cari pasien setelah 2 detik
          setTimeout(() => {
            $("#content").load("pages/cari_pasien.php");
          }, 2000);
        } else {
          Swal.fire({
            icon: "warning",
            title: "Gagal",
            text: res.message || "Terjadi kesalahan saat menyimpan.",
          });
        }
      },
      error: function (xhr) {
        Swal.fire({
          icon: "error",
          title: "Kesalahan Server",
          html: xhr.responseText,
        });
      },
      complete: function () {
        $btn.prop("disabled", false).html(`<i class="bi bi-save"></i> Simpan & Cetak`);
      },
    });
  });

  // === CETAK STRUK ===
  function printStruk(data) {
    const w = window.open("", "", "width=300,height=600");
    let layananHTML = "";
    data.layanan.forEach((item) => {
      layananHTML += `
        <tr>
          <td>${item.nama_layanan}</td>
          <td>${item.qty}</td>
          <td class="text-end">Rp${Number(item.harga).toLocaleString("id-ID")}</td>
          <td class="text-end">Rp${Number(item.subtotal).toLocaleString("id-ID")}</td>
        </tr>`;
    });

    w.document.write(`
      <html><head><title>Struk Transaksi</title>
      <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #000; margin: 10px; }
        h4 { text-align: center; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; }
        .text-end { text-align: right; }
        hr { border: 0; border-top: 1px dashed #888; margin: 8px 0; }
      </style></head>
      <body>
        <h4>${data.nama_klinik}</h4>
        <small>${data.alamat}<br>Telp: ${data.telp}</small>
        <hr>
        <b>Pasien:</b> ${data.nama_pasien}<br>
        <b>Tanggal:</b> ${data.tanggal}
        <hr>
        <table><tbody>${layananHTML}</tbody></table>
        <hr>
        <table>
          <tr><td><b>Subtotal</b></td><td class="text-end">Rp${Number(data.subtotal).toLocaleString("id-ID")}</td></tr>
          <tr><td><b>Diskon</b></td><td class="text-end">Rp${Number(data.diskon).toLocaleString("id-ID")}</td></tr>
          <tr><td><b>Total</b></td><td class="text-end"><b>Rp${Number(data.total).toLocaleString("id-ID")}</b></td></tr>
        </table>
        <hr>
        <center>${data.footer}</center>
      </body></html>
    `);
    w.document.close();
    w.print();
  }
})();
