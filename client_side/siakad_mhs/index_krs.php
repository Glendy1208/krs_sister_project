<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['nim'])) {
    header("Location: ../login.php");
    exit();
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

        <!-- KRS Table Box -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-xl font-bold mb-4">Mata Kuliah yang Diambil</h3>
            <table class="w-full table-auto border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-4 py-2">
                            <input type="checkbox" id="select-all" />
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
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <input type="checkbox" class="select-item" />
                        </td>
                        <td class="border border-gray-300 px-4 py-2">Pemrograman Web</td>
                        <td class="border border-gray-300 px-4 py-2">Senin</td>
                        <td class="border border-gray-300 px-4 py-2">08:00 - 10:00</td>
                        <td class="border border-gray-300 px-4 py-2">Lab 1</td>
                        <td class="border border-gray-300 px-4 py-2">A</td>
                        <td class="border border-gray-300 px-4 py-2">3</td>
                        <td class="border border-gray-300 px-4 py-2">W</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <input type="checkbox" class="select-item" />
                        </td>
                        <td class="border border-gray-300 px-4 py-2">Basis Data</td>
                        <td class="border border-gray-300 px-4 py-2">Selasa</td>
                        <td class="border border-gray-300 px-4 py-2">10:00 - 12:00</td>
                        <td class="border border-gray-300 px-4 py-2">Ruang 204</td>
                        <td class="border border-gray-300 px-4 py-2">B</td>
                        <td class="border border-gray-300 px-4 py-2">4</td>
                        <td class="border border-gray-300 px-4 py-2">P</td>
                    </tr>
                </tbody>
            </table>
            <button class="mt-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Hapus Mata Kuliah</button>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-auto">
        <p>&copy; 2024 SIAKAD Mahasiswa. All rights reserved.</p>
    </footer>
</body>
</html>