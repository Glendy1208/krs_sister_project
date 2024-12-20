<?php
session_start();
include('../env.php');
// Cek apakah user sudah login
if (!isset($_SESSION['nim'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil data mata kuliah dari API
$api_url = "$BASE_URL/matakuliah";
$ch = curl_init($api_url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$matakuliah_data = [];

if ($http_code == 200) {
    $result = json_decode($response, true);
    $matakuliah_data = $result['data'];
} else {
    $error_message = "Gagal mengambil data mata kuliah. Silakan coba lagi.";
}
// var_dump($_SESSION);
// die();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mata Kuliah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Navbar -->
    <?php
    include('layouts/navbar.php');
    ?>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto p-4">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-xl font-bold mb-4">Tambah Mata Kuliah</h3>
            <form action="submit_krs.php" method="POST">
                <table class="w-full table-auto border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-4 py-2">
                                Pilih
                            </th>
                            <th class="border border-gray-300 px-4 py-2">Mata Kuliah</th>
                            <th class="border border-gray-300 px-4 py-2">Hari</th>
                            <th class="border border-gray-300 px-4 py-2">Jam</th>
                            <th class="border border-gray-300 px-4 py-2">Ruangan</th>
                            <th class="border border-gray-300 px-4 py-2">Kelas</th>
                            <th class="border border-gray-300 px-4 py-2">Bobot (SKS)</th>
                            <th class="border border-gray-300 px-4 py-2">Jenis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($matakuliah_data)): ?>
                            <?php foreach ($matakuliah_data as $matkul): ?>
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <input type="checkbox" class="select-item-tambah" name="matkul[]" value="<?= htmlspecialchars($matkul['id_matkul']); ?>" />
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($matkul['nama_matkul']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($matkul['nama_hari']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($matkul['jam']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($matkul['nama_ruangan']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($matkul['nama_kelas']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($matkul['sks']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($matkul['tipe_matkul']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-red-500 py-4">
                                    <?= htmlspecialchars($error_message ?? "Tidak ada data mata kuliah."); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <button type="submit" class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Tambah</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-auto">
        <p>&copy; 2024 SIAKAD Mahasiswa. All rights reserved.</p>
    </footer>
</body>

</html>