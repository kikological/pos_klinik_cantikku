<?php
require_once __DIR__ . '/../../includes/db.php';
header('Content-Type: application/json');

// Ambil input
$nama       = mysqli_real_escape_string($conn, $_POST['nama_klinik'] ?? '');
$alamat     = mysqli_real_escape_string($conn, $_POST['alamat'] ?? '');
$no_hp      = mysqli_real_escape_string($conn, $_POST['no_hp'] ?? '');
$email      = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
$instagram  = mysqli_real_escape_string($conn, $_POST['instagram'] ?? '');
$facebook   = mysqli_real_escape_string($conn, $_POST['facebook'] ?? '');
$tiktok     = mysqli_real_escape_string($conn, $_POST['tiktok'] ?? '');

$print_mode      = mysqli_real_escape_string($conn, $_POST['print_mode'] ?? 'usb');
$printer_name    = mysqli_real_escape_string($conn, $_POST['printer_name'] ?? '');
$template_struk  = mysqli_real_escape_string($conn, $_POST['template_struk'] ?? 'A');

// Ambil data lama (jika ada)
$cek = mysqli_query($conn, "SELECT * FROM pengaturan_klinik LIMIT 1");
$dataLama = mysqli_fetch_assoc($cek);

// Default logo lama (kalau tidak upload logo baru)
$logoFile = $dataLama['logo'] ?? '';

// Upload logo baru jika ada
if (!empty($_FILES['logo']['name'])) {
    $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
    $newName = 'logo_' . time() . '.' . $ext;
    $target = __DIR__ . '/../../includes/' . $newName;

    if (move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {

        // hapus logo lama
        if (!empty($dataLama['logo'])) {
            $old = __DIR__ . '/../../includes/' . $dataLama['logo'];
            if (file_exists($old)) unlink($old);
        }

        $logoFile = $newName;
    }
}

// UPDATE jika sudah ada row
if ($dataLama) {

    $sql = "
        UPDATE pengaturan_klinik SET
            nama_klinik     = '$nama',
            alamat          = '$alamat',
            no_hp           = '$no_hp',
            email           = '$email',
            instagram       = '$instagram',
            facebook        = '$facebook',
            tiktok          = '$tiktok',
            logo            = '$logoFile',
            print_mode      = '$print_mode',
            printer_name    = '$printer_name',
            template_struk  = '$template_struk'
        WHERE id = {$dataLama['id']}
    ";

} else {

    // INSERT baru jika tabel masih kosong
    $sql = "
        INSERT INTO pengaturan_klinik
        (nama_klinik, alamat, no_hp, email, instagram, facebook, tiktok, logo, print_mode, printer_name, template_struk)
        VALUES
        ('$nama', '$alamat', '$no_hp', '$email', '$instagram', '$facebook', '$tiktok', '$logoFile', '$print_mode', '$printer_name', '$template_struk')
    ";
}

$ok = mysqli_query($conn, $sql);

if (!$ok) {
    echo json_encode([
        "status" => "error",
        "message" => "SQL ERROR: " . mysqli_error($conn)
    ]);
    exit;
}

echo json_encode([
    "status" => "ok",
    "message" => "Pengaturan klinik berhasil disimpan."
]);
?>
