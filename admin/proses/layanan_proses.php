<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// File ini berada di admin/proses/
include __DIR__ . '/../../includes/db.php';

$aksi = $_POST['aksi'] ?? $_GET['aksi'] ?? '';

if ($aksi === 'tampil') {
    $q = mysqli_query($conn, "SELECT * FROM layanan ORDER BY id_layanan DESC");
    $html = '';
    $no = 1;
    while ($r = mysqli_fetch_assoc($q)) {
        $html .= "
        <tr>
          <td class='text-center'>{$no}</td>
          <td>" . htmlspecialchars($r['nama_layanan']) . "</td>
          <td>Rp " . number_format($r['harga'], 0, ',', '.') . "</td>
          <td class='text-center'>
            <button class='btn btn-sm btn-outline-warning btn-edit'
                    data-id='{$r['id_layanan']}'
                    data-nama=\"" . htmlspecialchars($r['nama_layanan'], ENT_QUOTES) . "\"
                    data-harga='{$r['harga']}'>
              <i class='bi bi-pencil'></i>
            </button>
            <button class='btn btn-sm btn-outline-danger btn-hapus'
                    data-id='{$r['id_layanan']}'>
              <i class='bi bi-trash'></i>
            </button>
          </td>
        </tr>";
        $no++;
    }
    echo json_encode(["html" => $html]);
    exit;
}

elseif ($aksi === 'simpan') {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama_layanan'] ?? '');
    $harga = (int) ($_POST['harga'] ?? 0);

    if ($nama === '' || $harga <= 0) {
        echo json_encode(["status" => "error", "message" => "Nama dan harga wajib diisi!"]);
        exit;
    }

    $insert = mysqli_query($conn, "INSERT INTO layanan (nama_layanan, harga) VALUES ('$nama', '$harga')");
    if ($insert) {
        echo json_encode(["status" => "ok", "message" => "Layanan berhasil ditambahkan!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menambah layanan: " . mysqli_error($conn)]);
    }
    exit;
}


elseif ($aksi === 'edit') {
    $id    = (int) ($_POST['id_layanan'] ?? 0);
    $nama  = mysqli_real_escape_string($conn, $_POST['nama_layanan'] ?? '');
    $harga = (int) ($_POST['harga'] ?? 0);

    if ($id <= 0 || $nama === '' || $harga <= 0) {
        echo json_encode(["status" => "error", "message" => "Data tidak valid untuk update."]);
        exit;
    }

    $update = mysqli_query($conn, "UPDATE layanan SET nama_layanan='$nama', harga='$harga' WHERE id_layanan=$id");
    if ($update) {
        echo json_encode(["status" => "ok", "message" => "Layanan berhasil diperbarui!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal memperbarui layanan: " . mysqli_error($conn)]);
    }
    exit;
}

elseif ($aksi === 'hapus') {
    $id = (int) ($_POST['id_layanan'] ?? 0);
    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "ID layanan tidak valid."]);
        exit;
    }
    $hapus = mysqli_query($conn, "DELETE FROM layanan WHERE id_layanan=$id");
    if ($hapus) {
        echo json_encode(["status" => "ok", "message" => "Layanan berhasil dihapus!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menghapus: " . mysqli_error($conn)]);
    }
    exit;
}

echo json_encode(["status" => "error", "message" => "Aksi tidak dikenal!"]);
