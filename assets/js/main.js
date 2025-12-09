/* ============================================================
   main.js - Klinik Cantikku POS
   Versi fix by ChatGPT (path AJAX & auto load)
   ============================================================ */

$(document).ready(function () {
  const mainContent = $("#main-content");

  // Fungsi bantu untuk deteksi base URL relatif (agar AJAX tidak error path)
  function getBasePath() {
    // Jika file berjalan dari /admin/, tetap gunakan prefix admin/
    const path = window.location.pathname;
    if (path.includes("/admin/")) {
      return "admin/";
    }
    return "";
  }

  // =====================================
  // FUNGSI LOADING
  // =====================================
  function showLoading() {
    mainContent.html(`
      <div class="d-flex justify-content-center align-items-center" style="height:60vh;">
        <div class="text-center">
          <div class="spinner-border text-pink mb-3" style="width:3rem;height:3rem;color:#e75480" role="status"></div>
          <div class="fw-semibold text-muted">Memuat halaman...</div>
        </div>
      </div>
    `);
  }

  // =====================================
  // LOAD HALAMAN AJAX
  // =====================================
  function loadPage(page) {
    showLoading();
    $.ajax({
      url: getBasePath() + "pages/" + page + ".php",
      type: "GET",
      success: function (data) {
        mainContent.html(data);

        if (page === "layanan") {
          loadLayananTable();
        }
      },
      error: function () {
        mainContent.html(`
          <div class="alert alert-danger m-4" role="alert">
            <i class="bi bi-exclamation-triangle"></i>
            Gagal memuat halaman <strong>${page}</strong>.
          </div>
        `);
      },
    });
  }

  // =====================================
  // KLIK MENU SIDEBAR
  // =====================================
  $(document).on("click", ".menu-link", function (e) {
    e.preventDefault();
    const page = $(this).data("page");
    $(".menu-link").removeClass("active");
    $(this).addClass("active");
    loadPage(page);
  });

  // =====================================
  // FORM AJAX UMUM (semua form yang class="ajax-form")
  // =====================================
  $(document).on("submit", ".ajax-form", function (e) {
    e.preventDefault();

    const form = $(this);
    const url = getBasePath() + form.attr("action");
    const data = form.serialize();

    Swal.fire({
      title: "Menyimpan...",
      html: "Mohon tunggu sebentar",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    $.post(url, data, function (res) {
      Swal.close();
      try {
        const response = typeof res === "string" ? JSON.parse(res) : res;
        if (response.status === "ok" || response.status === "success") {
          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: response.message || "Data berhasil disimpan!",
            timer: 1800,
            showConfirmButton: false,
          });

          if (typeof loadLayananTable === "function") {
            setTimeout(() => loadLayananTable(), 1500);
          }

          form.trigger("reset");
          $("#id_layanan").val("");
        } else {
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: response.message || "Terjadi kesalahan saat menyimpan data.",
          });
        }
      } catch (err) {
        Swal.fire({
          icon: "error",
          title: "Kesalahan!",
          text: "Format respon tidak valid dari server.",
        });
      }
    }).fail(() => {
      Swal.close();
      Swal.fire({
        icon: "error",
        title: "Gagal!",
        text: "Tidak dapat menghubungi server.",
      });
    });
  });

  // ============================================================
  //  BAGIAN KHUSUS: CRUD LAYANAN
  // ============================================================

  function loadLayananTable() {
    $.ajax({
      url: getBasePath() + "proses/layanan_proses.php",
      type: "POST",
      data: { aksi: "tampil" },
      success: function (res) {
        try {
          const data = JSON.parse(res);
          $("#tabel-layanan tbody").html(data.html);
        } catch (e) {
          $("#tabel-layanan tbody").html("<tr><td colspan='4' class='text-center text-muted'>Gagal memuat data layanan</td></tr>");
        }
      },
      error: function () {
        $("#tabel-layanan tbody").html("<tr><td colspan='4' class='text-center text-danger'>Error memuat data!</td></tr>");
      },
    });
  }

  // Tambah / Update Layanan
  $(document).on("click", "#btnSimpanLayanan", function (e) {
    e.preventDefault();
    const id = $("#id_layanan").val();
    const nama = $("#nama_layanan").val();
    const harga = $("#harga").val();

    if (nama === "" || harga === "") {
      Swal.fire("Oops!", "Nama layanan dan harga wajib diisi!", "warning");
      return;
    }

    $.post(getBasePath() + "proses/layanan_proses.php", {
      aksi: "simpan",
      id: id,
      nama: nama,
      harga: harga,
    }, function (res) {
      try {
        const response = JSON.parse(res);
        if (response.status === "ok") {
          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: response.message,
            timer: 1500,
            showConfirmButton: false,
          });
          $("#form-layanan")[0].reset();
          $("#id_layanan").val("");
          loadLayananTable();
        } else {
          Swal.fire("Gagal!", response.message, "error");
        }
      } catch (err) {
        Swal.fire("Error!", "Respon server tidak valid!", "error");
      }
    });
  });

  // Edit Layanan
  $(document).on("click", ".btn-edit", function () {
    const id = $(this).data("id");
    $.get(getBasePath() + "proses/layanan_proses.php", { aksi: "detail", id: id }, function (res) {
      try {
        const data = JSON.parse(res);
        $("#id_layanan").val(data.id_layanan);
        $("#nama_layanan").val(data.nama_layanan);
        $("#harga").val(data.harga);
      } catch (err) {
        Swal.fire("Error!", "Gagal memuat data layanan!", "error");
      }
    });
  });

  // Hapus Layanan
  $(document).on("click", ".btn-hapus", function () {
    const id = $(this).data("id");
    Swal.fire({
      title: "Hapus layanan ini?",
      text: "Data yang dihapus tidak bisa dikembalikan!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#e75480",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Ya, hapus!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(getBasePath() + "proses/layanan_proses.php", { aksi: "hapus", id: id }, function (res) {
          const response = JSON.parse(res);
          if (response.status === "ok") {
            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: response.message,
              timer: 1500,
              showConfirmButton: false,
            });
            loadLayananTable();
          } else {
            Swal.fire("Gagal!", response.message, "error");
          }
        });
      }
    });
  });
  
  // ============================================================
  //  CETAK STRUK
  // ============================================================
  $(document).on("click", "#btnPrintStruk", function () {
    window.print();
  });

  // ============================================================
  //  LOAD DASHBOARD DEFAULT
  // ============================================================
  if ($("#main-content").length > 0) {
    loadPage("dashboard_home"); // halaman default setelah login
  }
});
