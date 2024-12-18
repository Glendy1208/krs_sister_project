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
            <table class="w-full table-auto border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-4 py-2">
                            <input type="checkbox" id="select-all-tambah" />
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
                            <input type="checkbox" class="select-item-tambah" />
                        </td>
                        <td class="border border-gray-300 px-4 py-2">Jaringan Komputer</td>
                        <td class="border border-gray-300 px-4 py-2">Kamis</td>
                        <td class="border border-gray-300 px-4 py-2">09:00 - 11:00</td>
                        <td class="border border-gray-300 px-4 py-2">Lab 2</td>
                        <td class="border border-gray-300 px-4 py-2">A</td>
                        <td class="border border-gray-300 px-4 py-2">3</td>
                        <td class="border border-gray-300 px-4 py-2">P</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <input type="checkbox" class="select-item-tambah" />
                        </td>
                        <td class="border border-gray-300 px-4 py-2">Sistem Operasi</td>
                        <td class="border border-gray-300 px-4 py-2">Rabu</td>
                        <td class="border border-gray-300 px-4 py-2">13:00 - 15:00</td>
                        <td class="border border-gray-300 px-4 py-2">Ruang 301</td>
                        <td class="border border-gray-300 px-4 py-2">C</td>
                        <td class="border border-gray-300 px-4 py-2">3</td>
                        <td class="border border-gray-300 px-4 py-2">W</td>
                    </tr>
                </tbody>
            </table>
            <button class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Tambah</button>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-auto">
        <p>&copy; 2024 SIAKAD Mahasiswa. All rights reserved.</p>
    </footer>
</body>
</html>