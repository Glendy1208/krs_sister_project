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

// Langkah 1: Panggil API untuk mendapatkan semester_now mahasiswa
$api_mahasiswa_url = "$BASE_URL/mahasiswa/$nim";
$ch = curl_init($api_mahasiswa_url);

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
    $semester_now = $result['data']['semester_now'] ?? null;
    $paid = $result['data']['paid'] ?? null;
    if ($paid == null || $paid == 0) {
        header("Location: ./form_pembayaran_ukt.php");
        exit();
    }
} else {
    $error_message = "belum mengambil KRS";
}

// Langkah 2: Panggil API untuk mendapatkan data jadwal mahasiswa berdasarkan NIM dan semester_now
$jadwal_data = [];

if ($semester_now !== null) {
    $api_jadwal_url = "$BASE_URL/matakuliah/$nim/$semester_now";
    $ch = curl_init($api_jadwal_url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        $result = json_decode($response, true);
        $jadwal_data = $result['data'];
    } else {
        $error_message = "Belum mengambil KRS.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengisian KRS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Navbar -->
    <?php
    include('layouts/navbar.php');
    ?>
    
    <!-- Main Content -->
    <main class="flex-grow container mx-auto p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-blue-600">Kartu Rencana Studi (KRS)</h2>
            <a href="create_krs.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Tambah Mata Kuliah</a>
        </div>

        <?php 
        if (isset($_SESSION['error_message'])) {
            $pesan = $_SESSION['error_message'];
            echo "<div class='bg-red-500 text-white p-4 text-center'>$pesan</div>";
            unset($_SESSION['error_message']);
        }
        elseif (isset($_SESSION['success_message'])){
            $pesan = $_SESSION['success_message'];
            echo "<div class='bg-green-500 text-white p-4 mb-4 rounded'>$pesan</div>";
            unset($_SESSION['success_message']);
        }
        ?>
        <!-- KRS Table Box -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-xl font-bold mb-4">Mata Kuliah yang Diambil</h3>
            <form id="deleteForm" action="delete_jadwal.php" method="POST">
                <table class="w-full table-auto border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-4 py-2">Pilih</th>
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
                        <?php if (!empty($jadwal_data)): ?>
                            <?php $total = 0; ?>
                            <?php foreach ($jadwal_data as $jadwal): ?>
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <input type="checkbox" class="select-item" name="jadwal[]" value="<?= htmlspecialchars($jadwal['id_matkul']); ?>" />
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($jadwal['nama_matkul']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($jadwal['nama_hari']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($jadwal['jam']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($jadwal['nama_ruangan']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($jadwal['nama_kelas']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($jadwal['sks']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($jadwal['tipe_matkul']); ?></td>
                                </tr>
                                <?php $total += $jadwal["sks"]; ?>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="6" class="border border-gray-300 px-4 py-2 text-right"><b>Total SKS</b></td>
                                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($total); ?></td>
                                <td class="border border-gray-300 px-4 py-2"></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-red-500 py-4">
                                    <?= htmlspecialchars($error_message ?? "Tidak ada data mata kuliah."); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <!-- Tombol hapus -->
                <button type="submit" class="mt-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Hapus Mata Kuliah</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-auto">
        <p>&copy; 2024 SIAKAD Mahasiswa. All rights reserved.</p>
    </footer>
</body>

</html>