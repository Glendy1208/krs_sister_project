<?php
include('../env.php');

session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['nim'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil NIM dari session
$nim = $_SESSION['nim'];

// Ambil data mahasiswa untuk mendapatkan semester_now
$api_url = "$BASE_URL/mahasiswa/$nim";
$ch = curl_init($api_url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$semester_now = null;

if ($http_code == 200) {
    $result = json_decode($response, true);
    $semester_now = $result['data']['semester_now']; // Ambil semester_now dari data mahasiswa
} else {
    $error_message = "Gagal mengambil data mahasiswa. Silakan coba lagi.";
}

// Ambil data mata kuliah yang sudah dipilih mahasiswa pada semester_now
$api_url = "$BASE_URL/matakuliah/$nim/$semester_now"; // Gunakan semester_now di sini
$ch = curl_init($api_url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$matkul_sudah_diambil = [];

if ($http_code == 200) {
    $result = json_decode($response, true);
    $matkul_sudah_diambil = array_map(function($matkul) {
        return $matkul;
    }, $result['data']);
} else {
    $error_message = "Gagal mengambil data mata kuliah yang sudah diambil. Silakan coba lagi.";
}

// menghitung total KRS
$total_krs = 0;
foreach ($matkul_sudah_diambil as $matkul) {
    $total_krs += $matkul['sks'];
}

// Ambil daftar mata kuliah yang dipilih
$matkul_ids = $_POST['matkul'] ?? [];

if (empty($matkul_ids)) {
    $_SESSION['error_message'] = "Silakan pilih mata kuliah terlebih dahulu.";
    header("Location: create_krs.php");
    exit();
}

$matkul_details = [];

foreach ($matkul_ids as $matkul_id) {
    $api_url = "$BASE_URL/matakuliah/$matkul_id";
    $ch = curl_init($api_url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        $result = json_decode($response, true);
        $matkul_details[] = $result['data'];
    } else {
        $_SESSION['error_message'] = "Gagal mengambil data mata kuliah dengan ID $matkul_id. Silakan coba lagi.";
        header("Location: create_krs.php");
        exit();
    }
}

# Cek apakah total SKS yang diambil melebihi batas maksimal
$batas_sks = 24;
if ($total_krs + array_sum(array_column($matkul_details, 'sks')) > $batas_sks) {
    $_SESSION['error_message'] = "Total SKS yang diambil melebihi batas maksimal ($batas_sks SKS).";
    header("Location: create_krs.php");
    exit();
}

// Kirim data ke API
$api_url = "$BASE_URL/submit_krs";
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
    header("Location: index_krs.php");
} else {
    $_SESSION['error_message'] = "Gagal menambahkan mata kuliah. Silakan coba lagi.";
    header("Location: create_krs.php");
}
exit();
