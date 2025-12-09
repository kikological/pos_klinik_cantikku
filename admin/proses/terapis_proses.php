<?php 
header('Content-Type: application/json');
include __DIR__ . '/../../includes/db.php';

$aksi = $_POST['aksi'] ?? '';

if ($aksi === 'tampil') {
    $q = mysqli_query($conn, "SELECT * FROM users ORDER BY id_user DESC");
    if (!$q) {
        echo json_encode(["html" => "<tr><td colspan='5' class='text-center text-danger'>Query error: " . mysqli_error($conn) . "</td></tr>"]);
        exit;
    }

    $html = '';
    $no = 1;
    while ($r = mysqli_fetch_assoc($q)) {
        $html .= "
        <tr>
          <td class='text-center'>{$no}</td>
          <td>" . htmlspecialchars($r['nama']) . "</td>
          <td>" . htmlspecialchars($r['username']) . "</td>
          <td class='text-center'>" . htmlspecialchars($r['role']) . "</td>
          <td class='text-center'>
            <button class='btn btn-sm btn-outline-warning btn-edit'
                    data-id='{$r['id_user']}'
                    data-nama=\"" . htmlspecialchars($r['nama'], ENT_QUOTES) . "\"
                    data-username=\"" . htmlspecialchars($r['username'], ENT_QUOTES) . "\"
                    data-role='{$r['role']}'>
              <i class='bi bi-pencil'></i>
            </button>
            <button class='btn btn-sm btn-outline-danger btn-hapus'
                    data-id='{$r['id_user']}'>
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
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    if ($nama === '' || $username === '' || $role === '') {
        echo json_encode(["status" => "error", "message" => "Semua field wajib diisi!"]);
        exit;
    }

    $cek = mysqli_query($conn, "SELECT 1 FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        echo json_encode(["status" => "error", "message" => "Username sudah digunakan!"]);
        exit;
    }

    $insert = mysqli_query($conn, "INSERT INTO users (nama, username, password, role) VALUES ('$nama', '$username', '$password', '$role')");
    echo json_encode($insert
        ? ["status" => "ok", "message" => "Terapis berhasil ditambahkan!"]
        : ["status" => "error", "message" => "Gagal menambah data: " . mysqli_error($conn)]);
    exit;
}

elseif ($aksi === 'edit') {
    $id = (int) $_POST['id_user'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $password = $_POST['password'] ?? '';

    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET nama='$nama', username='$username', role='$role', password='$hash' WHERE id_user=$id";
    } else {
        $query = "UPDATE users SET nama='$nama', username='$username', role='$role' WHERE id_user=$id";
    }

    $update = mysqli_query($conn, $query);
    echo json_encode($update
        ? ["status" => "ok", "message" => "Data terapis berhasil diperbarui!"]
        : ["status" => "error", "message" => "Gagal memperbarui: " . mysqli_error($conn)]);
    exit;
}

elseif ($aksi === 'hapus') {
    $id = (int)$_POST['id_user'];
    $hapus = mysqli_query($conn, "DELETE FROM users WHERE id_user=$id");
    echo json_encode($hapus
        ? ["status" => "ok", "message" => "Terapis berhasil dihapus!"]
        : ["status" => "error", "message" => "Gagal menghapus: " . mysqli_error($conn)]);
    exit;
}

elseif ($aksi === 'cek_username') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $q = mysqli_query($conn, "SELECT 1 FROM users WHERE username='$username' LIMIT 1");
    echo json_encode(["exists" => mysqli_num_rows($q) > 0]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Aksi tidak dikenal!"]);
