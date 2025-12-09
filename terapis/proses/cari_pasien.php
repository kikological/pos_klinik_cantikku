<?php
require_once __DIR__ . '/../../includes/db.php';

$keyword = trim($_POST['keyword'] ?? '');

if ($keyword === '') {
  echo "<div class='alert alert-warning'>Masukkan kata kunci pencarian.</div>";
  exit;
}

$q = $conn->query("
  SELECT * FROM pasien
  WHERE 
  nama LIKE '%$keyword%' 
  OR no_hp LIKE '%$keyword%'
  OR no_register LIKE '%$keyword%'
  ORDER BY nama ASC
");

if (!$q || $q->num_rows == 0) {
  echo "
    <div class='alert alert-warning text-center'>
      Tidak ditemukan pasien dengan kata kunci <b>$keyword</b>.
      <br><br>
    </div>
  ";
  exit;
}

echo "<div class='fade-in'>";

while ($r = $q->fetch_assoc()) {

    // Hitung umur
    $umur = "-";
    if (!empty($r['tanggal_lahir']) && $r['tanggal_lahir'] !== "0000-00-00") {
        $lahir = new DateTime($r['tanggal_lahir']);
        $today = new DateTime();
        $umur = $today->diff($lahir)->y . " th";
    }

    // Jenis kelamin + ikon
    if ($r['jenis_kelamin'] === "L") {
        $jkIcon = "👨 Laki-laki";
    } elseif ($r['jenis_kelamin'] === "P") {
        $jkIcon = "👩 Perempuan";
    } else {
        $jkIcon = "❔ -";
    }

    $tglLahir = (!empty($r['tanggal_lahir']) && $r['tanggal_lahir'] !== "0000-00-00")
        ? date('d/m/Y', strtotime($r['tanggal_lahir']))
        : "-";

    echo "
    <div class='patient-card p-3 mb-3 rounded-3'>

        <!-- Header Row -->
        <div class='d-flex justify-content-between align-items-start'>
        
            <!-- NAMA & HP -->
            <div>
              <div class='patient-name fw-bold'>
                {$r['nama']} ({$r['no_register']})
              </div>
              <div class='text-muted small'>
                📞 {$r['no_hp']}
              </div>
            </div>

            <!-- TOMBOL PROSES -->
            <button class='btn btn-sm btn-pink prosesPasien' data-id='{$r['id_pasien']}'>
              <i class='bi bi-arrow-right-circle'></i> Proses
            </button>

        </div>

        <!-- Gender & Umur -->
        <div class='text-muted small mt-2'>
          {$jkIcon} | {$tglLahir} ({$umur})
        </div>

        <!-- Alamat -->
        <div class='text-muted small mt-1'>
          📍 {$r['alamat']}
        </div>
    </div>";
}

echo "</div>";
?>

<script>
$(document).off("click.prosesPasien").on("click.prosesPasien", ".prosesPasien", function(){
  const id = $(this).data("id");
  const $container = $("#content, #page-content").first(); // dukung 2 layout

  if (!$container.length) {
    alert("❌ Container target tidak ditemukan (pastikan ada elemen #content atau #page-content).");
    return;
  }

  $container.fadeOut(200, function() {
    $container.html('<div class="text-center py-5 text-muted">🔄 Memuat data pasien...</div>');
    $container.load("pages/split_view.php?id=" + id, function(response, status, xhr) {
      if (status === "error") {
        $container.html('<div class="alert alert-danger">Gagal memuat data: ' + xhr.statusText + '</div>');
      } else {
        $container.fadeIn(300);
      }
    });
  });
});
</script>

<style>
.fade-in { animation: fadeIn .500s ease; }
@keyframes fadeIn { from {opacity:0} to {opacity:1} }

/* Card elegan */
.patient-card {
  border: 1px solid #f7b6c9;
  box-shadow: 0px 2px 6px rgba(255, 192, 203, 0.35);
  background: #fff;
}

/* Nama warna pink */
.patient-name {
  color: #e75480;
  font-size: 1.05rem;
}

/* Tombol pink */
.btn-pink {
  background: #e75480;
  color: white;
  border: none;
}
.btn-pink:hover {
  background: #d9486f;
  color: white;
}
</style>
