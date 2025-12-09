<div class="container-fluid">

  <h4 class="fw-bold text-pink mb-3">
    <i class="bi bi-people"></i> Data Pasien
  </h4>

  <!-- FILTER -->
  <div class="row g-2 mb-3">
    <div class="col-md-4">
      <input type="text" id="filterCari" class="form-control" placeholder="Cari nama / no HP / no register...">
    </div>
    <div class="col-md-3">
      <select id="filterJK" class="form-select">
        <option value="">Semua Jenis Kelamin</option>
        <option value="L">Laki-Laki</option>
        <option value="P">Perempuan</option>
      </select>
    </div>
    <div class="col-md-3">
      <select id="filterUmur" class="form-select">
        <option value="">Semua Umur</option>
        <option value="anak">Anak-anak (0-17)</option>
        <option value="dewasa">Dewasa (18+)</option>
      </select>
    </div>

<div class="col-md-2 d-flex gap-2 justify-content-end">

  <!-- Tombol PDF -->
  <a href="proses/pasien_pdf.php" target="_blank" 
     class="btn btn-outline-pink w-50" id="btnPdfPasien">
    <i class="bi bi-filetype-pdf"></i> PDF
  </a>

  <!-- Tombol Tambah -->
  <button class="btn btn-pink w-50" data-bs-toggle="modal" data-bs-target="#modalTambah">
    <i class="bi bi-person-plus"></i> Baru
  </button>

</div>
  </div>

  <!-- Container hasil load -->
  <div id="loadPasien">
    <div class="text-center py-5 text-muted">⏳ Memuat data...</div>
  </div>

</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-pink text-white">
        <h5 class="modal-title"><i class="bi bi-person-plus"></i> Tambah Pasien</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="formTambahPasien">

          <div class="mb-2">
            <label>No Register</label>
            <input type="text" name="no_register" class="form-control" required>
          </div>

          <div class="mb-2">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" required>
          </div>

          <div class="mb-2">
            <label>No HP</label>
            <input type="text" name="no_hp" class="form-control">
          </div>

          <div class="mb-2">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control"></textarea>
          </div>

          <div class="mb-2">
            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-control">
          </div>

          <div class="mb-2">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-select">
              <option value="">--Pilih--</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>

          <div class="text-end">
            <button class="btn btn-pink" type="submit">
              <i class="bi bi-save"></i> Simpan
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>


<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit">
  <div class="modal-dialog">
    <div class="modal-content" id="editContent">
      <!-- isi dimuat via ajax -->
    </div>
  </div>
</div>

<style>
  .text-pink { color: #e75480; }
  .btn-pink { background-color: #e75480; color: white; border: none; }
  .btn-pink:hover { background-color: #d34b73; color: white; }
  .table-pink { background-color: #ffe6ef; color: #e75480; }
  .modal {
	z-index: 9999 !important;
	}
</style>


<script>
function loadPasien() {
  $("#loadPasien").html("<div class='text-center py-4'>⏳ Memuat...</div>");

  $.post("proses/pasien_load.php", {
    cari: $("#filterCari").val(),
    jk: $("#filterJK").val(),
    umur: $("#filterUmur").val()
  }, function(res){
    $("#loadPasien").hide().html(res).fadeIn(200);
  });
}

$(document).ready(function(){

  loadPasien();

  $("#filterCari, #filterJK, #filterUmur").on("input change", function(){
    loadPasien();
  });

  // Tambah pasien
  $("#formTambahPasien").submit(function(e){
    e.preventDefault();

    $.ajax({
      url:"proses/pasien_tambah.php",
      type:"POST",
      data:$(this).serialize(),
      dataType:"json",
      success:function(res){
        if(res.status=="ok"){
          Swal.fire("Berhasil!", res.message, "success");
          $("#modalTambah").modal("hide");
          loadPasien();
        } else {
          Swal.fire("Gagal", res.message, "error");
        }
      }
    });
  });

  // Edit
  $(document).on("click",".btnEditPasien",function(){
    let id=$(this).data("id");
    $("#editContent").html("<div class='p-3 text-center'>⏳ Memuat formulir...</div>");

    $("#modalEdit").modal("show");
    $("#editContent").load("proses/pasien_edit_form.php?id="+id);
  });

  // Hapus pasien (FIX)
  $(document).on("click",".btnHapusPasien",function(){
    let id = $(this).data("id");

    Swal.fire({
        icon:"warning",
        title:"Hapus pasien?",
        text:"Data tidak dapat dikembalikan!",
        showCancelButton:true,
        confirmButtonText:"Ya, hapus"
    }).then(result=>{
        if(result.isConfirmed){

            $.ajax({
                url:"proses/pasien_hapus.php",
                type:"POST",
                data:{id:id},
                dataType:"json",
                success:function(r){
                    if(r.status=="ok"){
                        Swal.fire("Berhasil!", r.message, "success");
                        loadPasien();
                    } else {
                        Swal.fire("Gagal!", r.message, "error");
                    }
                },
                error:function(xhr){
                    Swal.fire("Error!", xhr.responseText, "error");
                }
            });

        }
    });
  });
  
  // CETAK PDF
$("#btnCetakPDF").click(function (e) {
  e.preventDefault();

  let cari  = $("#filterCari").val();
  let jk    = $("#filterJK").val();
  let umur  = $("#filterUmur").val();

  // buka tab baru ke file pdf
  window.open(
    "proses/pasien_pdf.php?cari=" + encodeURIComponent(cari)
      + "&jk=" + encodeURIComponent(jk)
      + "&umur=" + encodeURIComponent(umur),
    "_blank"
  );
});


});
</script>

