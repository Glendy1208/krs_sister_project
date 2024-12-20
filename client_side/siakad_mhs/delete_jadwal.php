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

// Ambil ID mata kuliah yang dipilih dari form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_matkul = $_POST['jadwal'] ?? []; // Array ID mata kuliah yang dipilih
    $id_matkul = array_map('intval', $id_matkul);
    // Validasi input
    if (empty($id_matkul)) {
        $_SESSION['error_message'] = "Tidak ada mata kuliah yang dipilih.";
        header("Location: index_krs.php");
        exit();
    }

    // Panggil API untuk menghapus jadwal
    $api_delete_url = "$BASE_URL/jadwal/hapus";
    $payload = json_encode([
        'nim' => $nim,
        'id_matkul' => $id_matkul
    ]);

    $ch = curl_init($api_delete_url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Tangani respons dari API
    if ($http_code == 200) {
        $_SESSION['success_message'] = "Mata kuliah berhasil dihapus.";
    } else {
        $result = json_decode($response, true);
        $error_message = $result['message'] ?? "Terjadi kesalahan saat menghapus mata kuliah.";
        $_SESSION['error_message'] = $error_message;
    }

    // Redirect kembali ke halaman KRS
    header("Location: index_krs.php");
    exit();
}
?>
