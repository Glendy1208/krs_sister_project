<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['nim'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil NIM dari session
$nim = $_SESSION['nim'];

// Ambil daftar mata kuliah yang dipilih
$matkul_ids = $_POST['matkul'] ?? [];

if (empty($matkul_ids)) {
    $_SESSION['error_message'] = "Silakan pilih mata kuliah terlebih dahulu.";
    header("Location: create_krs.php");
    exit();
}

// Kirim data ke API
$api_url = "http://localhost:5000/submit_krs";
$data = json_encode([
    "nim" => $nim,
    "matkul_ids" => $matkul_ids
]);

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200) {
    $_SESSION['success_message'] = "Mata kuliah berhasil ditambahkan.";
    header("Location: create_krs.php");
} else {
    $_SESSION['error_message'] = "Gagal menambahkan mata kuliah. Silakan coba lagi.";
    header("Location: create_krs.php");
}
exit();
