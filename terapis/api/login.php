<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username==='' || $password==='') {
  echo json_encode(['success'=>false,'message'=>'Username/Password wajib']);
  exit;
}

$stmt = $conn->prepare("SELECT id_user, nama, password_hash, user_role FROM users WHERE username=? LIMIT 1");
$stmt->bind_param("s",$username);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows==0) {
  echo json_encode(['success'=>false,'message'=>'User tidak ditemukan']);
  exit;
}
$user = $res->fetch_assoc();
if (!password_verify($password, $user['password_hash'])) {
  echo json_encode(['success'=>false,'message'=>'Password salah']);
  exit;
}

// sukses
echo json_encode([
  'success'=>true,
  'data'=>[
    'id_user'=>(int)$user['id_user'],
    'nama'=>$user['nama'],
    'role'=>$user['user_role']
  ]
]);
