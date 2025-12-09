<?php
require_once __DIR__ . '/../../includes/db.php';

$keyword = trim($_POST['keyword'] ?? '');
if ($keyword === '') {
  echo "<div class='alert alert-warning'>Masukkan kata kunci terlebih dahulu.</div>";
  exit;
}

$q = $conn->query("
  SELECT * FROM pasien 
  WHERE nama_pasien LIKE '%$keyword%' OR no_hp LIKE '%$keyword%' 
  ORDER BY nama_pasien ASC
");

if (!$q || $q->num_rows === 0) {
  echo "
    <div class='alert alert-warning text-center'>
      <div>🚫 Tidak ditemukan pasien dengan kata kunci <b>" . htmlspecialchars($keyword) . "</b>.</div>
      <small class='text-muted d-block mb-2'>
        Jika pasien baru, silakan tambahkan melalui menu <b>Cari Pasien</b>.
      </small>
      <button class='btn btn-sm btn-pink mt-2' id='btnKeCariPasien'>
        <i class='bi bi-person-plus'></i> Buka Menu Cari Pasien
      </button>
    </div>

    <script>
      // gunakan delegation (global) supaya selalu aktif walau HTML dimuat via AJAX
      $(document).on('click', '#btnKeCariPasien', function(e){
        e.preventDefault();
        // cari container yang tersedia (terapis pakai #content, admin kadang #page-content)
        var targetSelector = $('#content').length ? '#content' : ($('#page-content').length ? '#page-content' : null);
        if (!targetSelector) {
          console.warn('Tidak menemukan container target untuk load halaman cari_pasien.php');
          return;
        }
        $(targetSelector).load('pages/cari_pasien.php', function(response, status){
          if (status === 'error') {
            console.error('Gagal load pages/cari_pasien.php:', response);
            alert('Gagal membuka menu Cari Pasien. Periksa console untuk detail.');
          }
        });
      });
    </script>
  ";
  exit;
}

echo "<ul class='list-group'>";
while ($r = $q->fetch_assoc()) {
  $nama = htmlspecialchars($r['nama_pasien']);
  $nohp = htmlspecialchars($r['no_hp']);
  $id = (int)$r['id_pasien'];
  echo "
    <li class='list-group-item d-flex justify-content-between align-items-center'>
      <span><b>{$nama}</b> ({$nohp})</span>
      <button class='btn btn-sm btn-pink tampilRiwayat' 
        data-id='{$id}' 
        data-nama='{$nama}'>
        <i class='bi bi-journal-text'></i> Tampilkan Riwayat
      </button>
    </li>
  ";
}
echo "</ul>";
?>
<script>
  // event delegation untuk tombol Tampilkan Riwayat
  $(document).on('click', '.tampilRiwayat', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    var nama = $(this).data('nama');
    var targetSelector = $('#content').length ? '#content' : ($('#page-content').length ? '#page-content' : null);
    if (!targetSelector) {
      console.warn('Tidak menemukan container untuk memuat riwayat pasien');
      return;
    }
    // muat halaman riwayat dengan param id_pasien (halaman riwayat akan menerima via GET)
    $(targetSelector).load('pages/riwayat.php?id_pasien=' + encodeURIComponent(id), function(response, status){
      if (status === 'error') {
        console.error('Gagal load riwayat.php:', response);
        alert('Gagal menampilkan riwayat. Periksa console untuk detail.');
      }
    });
  });
</script>
