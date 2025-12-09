/* ===========================================================
   INPUT TINDAKAN – FINAL SUPPORT USB & BLUETOOTH PRINT
=========================================================== */

$(document).ready(function () {

    function hitungTotal() {
        let subtotal = 0;

        $(".tindakan-item").each(function () {
            let harga = parseFloat($(this).find(".harga").val() || 0);
            let qty   = parseInt($(this).find(".qty").val() || 0);
            subtotal += (harga * qty);
        });

        $("#subtotal").val(subtotal);

        let diskon = parseFloat($("#diskon").val() || 0);
        let total  = subtotal - diskon;
        if (total < 0) total = 0;

        $("#total").val(total);
    }

    $(document).on("change", ".layanan-select", function () {
        let harga = $("option:selected", this).data("harga") || 0;
        $(this).closest(".tindakan-item").find(".harga").val(harga);
        hitungTotal();
    });

    $(document).on("input", ".qty", hitungTotal);
    $("#diskon").on("input", hitungTotal);

    $(document).on("click", ".removeItem", function () {
        $(this).closest(".tindakan-item").remove();
        hitungTotal();
    });

    $("#addTindakan").click(function () {
        let layananOptions = $("#daftarTindakan select:first").html();
        let html = `
        <div class="row tindakan-item mb-2">
            <div class="col-md-6">
                <select name="layanan_id[]" class="form-select layanan-select" required>
                    ${layananOptions}
                </select>
            </div>

            <div class="col-md-3">
                <input type="text" name="harga[]" class="form-control harga" readonly>
            </div>

            <div class="col-md-2">
                <input type="number" name="qty[]" class="form-control qty" value="1" min="1">
            </div>

            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger btn-sm removeItem">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>`;
        $("#daftarTindakan").append(html);
    });

    /* ======================================================
       SUBMIT FORM → SIMPAN + CETAK OTOMATIS
    ====================================================== */
    $("#formTindakan").submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "proses/simpan_tindakan.php",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,

            success: function (res) {
                console.log("RAW RESPONSE:", res);

                let js = {};
                try {
                    js = (typeof res === "object") ? res : JSON.parse(res);
                } catch (e) {
                    Swal.fire("Error", "Server mengirim respon tidak valid:\n" + res, "error");
                    return;
                }

                let isSuccess = (js.success === true || js.status === "ok");
                if (!isSuccess) {
                    Swal.fire("Gagal", js.message || "Terjadi kesalahan!", "error");
                    return;
                }

                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: js.message || "Tindakan Berhasil Disimpan",
                    timer: 900,
                    showConfirmButton: false
                });

                setTimeout(() => {
                    if (js.print_mode === "usb") {
                        window.open("proses/print_escpos.php?id=" + js.id_transaksi, "_blank");
                    } else if (js.print_mode === "bluetooth") {
                        sendBluetoothPrint(js.id_transaksi);
                    }
                }, 400);

                setTimeout(() => {
                    $("#content").load("pages/cari_pasien.php");
                }, 900);
            },

            error: function (xhr) {
                Swal.fire("Error!", "Tidak dapat terhubung ke server!", "error");
                console.error(xhr.responseText);
            }
        });
    });

});

/* ===========================================================
    FUNGSI PRINT BLUETOOTH
=========================================================== */
async function sendBluetoothPrint(id_transaksi) {
    try {
        let savedDeviceId = localStorage.getItem("bt_printer_id");

        let device = null;

        if (savedDeviceId) {
            // reconnect ke printer yang sudah di-pair sebelumnya
            device = await navigator.bluetooth.requestDevice({
                filters: [{namePrefix: "RPP"}],
                optionalServices: [0xFFE0]
            });
        } else {
            // jika belum pernah connect, scan dulu
            device = await navigator.bluetooth.requestDevice({
                filters: [{namePrefix: "RPP"}],
                optionalServices: [0xFFE0]
            });
            localStorage.setItem("bt_printer_id", device.id);
        }

        const server = await device.gatt.connect();
        const service = await server.getPrimaryService(0xFFE0);
        const characteristic = await service.getCharacteristic(0xFFE1);

        // ambil data ESC/POS dari server
        let res = await fetch("proses/get_struk_escpos.php?id=" + id_transaksi);
        let json = await res.json();

        if (!json.status || !json.data) {
            Swal.fire("Error", "Format struk tidak valid!", "error");
            return;
        }

        // ubah base64 jadi Uint8Array
        let rawData = Uint8Array.from(atob(json.data), c => c.charCodeAt(0));

        // kirim ke printer dalam potongan data (maks 180 bytes)
        let chunkSize = 180;
        for (let i = 0; i < rawData.length; i += chunkSize) {
            let chunk = rawData.slice(i, i + chunkSize);
            await characteristic.writeValue(chunk);
        }

        Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: "Struk berhasil dicetak via Bluetooth!",
            timer: 1300,
            showConfirmButton: false
        });

    } catch (e) {
        Swal.fire("Error", "Printer Bluetooth gagal digunakan.\n" + e, "error");
        console.error(e);
    }
}

