<?php
// proses_login.php - versi perbaikan
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
session_start();

include_once __DIR__ . '/includes/db.php'; // sesuaikan path jika perlu

try {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        exit;
    }

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $stored = $user['password'];

        $ok = false;

        // 1) Modern hash (password_hash)
        if (password_verify($password, $stored)) {
            $ok = true;
        }
        // 2) Legacy MD5 (contoh: db lama menyimpan md5)
        elseif (md5($password) === $stored) {
            $ok = true;
            // rehash ke password_hash dan update DB agar lebih aman
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $u_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id_user = ?");
            $u_stmt->bind_param("si", $newHash, $user['id_user']);
            $u_stmt->execute();
        }
        // 3) Plain text (jika ada yang disimpan plain)
        elseif ($password === $stored) {
            $ok = true;
            // rehash as well
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $u_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id_user = ?");
            $u_stmt->bind_param("si", $newHash, $user['id_user']);
            $u_stmt->execute();
        }

        if ($ok) {
            // set session — perbaiki nama field: id_user, nama, role/user_role
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['nama'] = $user['nama'];
            // some DBs call column 'role' others 'user_role' — fallback
            $_SESSION['user_role'] = $user['role'] ?? $user['user_role'] ?? 'terapis';

            $redirect = ($_SESSION['user_role'] === 'admin') ? 'admin/dashboard.php' : 'terapis/dashboard.php';
            echo json_encode(['status' => 'ok', 'message' => 'Login berhasil!', 'redirect' => $redirect]);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Password salah!']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Username tidak ditemukan!']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    exit;
}
