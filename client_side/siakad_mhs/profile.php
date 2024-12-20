<?php
session_start();
include('../env.php');
// Cek apakah user sudah login
if (!isset($_SESSION['nim'])) {
    header("Location: ../login.php");
    exit();
}

// NIM dari session
$nim = $_SESSION['nim'];
$data_mahasiswa = null;
$error_message = null;

// Ambil data dari API menggunakan cURL
$api_url = "$BASE_URL/mahasiswa/" . $nim;
$ch = curl_init($api_url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Proses respons dari API
if ($http_code == 200) {
    $result = json_decode($response, true);
    $data_mahasiswa = $result['data'];
} else {
    $error_message = "Tidak Ada Matakuliah Yang DiambilSSS.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Navbar -->
    <?php include('layouts/navbar.php'); ?>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto p-4">
        <div class="bg-white shadow rounded-lg p-6 max-w-md mx-auto">
            <h2 class="text-2xl font-bold mb-4 text-center text-blue-600">Profil Mahasiswa</h2>

            <?php if ($data_mahasiswa): ?>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">NIM</label>
                    <p class="bg-gray-100 p-2 rounded border"><?= htmlspecialchars($data_mahasiswa['nim']); ?></p>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Nama</label>
                    <p class="bg-gray-100 p-2 rounded border"><?= htmlspecialchars($data_mahasiswa['nama']); ?></p>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Semester Sekarang</label>
                    <p class="bg-gray-100 p-2 rounded border"><?= htmlspecialchars($data_mahasiswa['semester_now']); ?></p>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Besaran UKT</label>
                    <p class="bg-gray-100 p-2 rounded border">Rp <?= number_format($data_mahasiswa['besaran'], 0, ',', '.'); ?></p>
                </div>
            <?php else: ?>
                <p class="text-red-500 text-center"><?= $error_message; ?></p>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-auto">
        <p>© 2024 SIAKAD Mahasiswa. All rights reserved.</p>
    </footer>
</body>

</html>