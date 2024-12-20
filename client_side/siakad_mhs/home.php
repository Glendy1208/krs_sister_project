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
    <title>Home</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <?php
    include('layouts/navbar.php');
    include('../env.php');
    ?>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        <section class="bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Selamat Datang di SIAKAD</h1>
            <p class="text-gray-600 mb-6">Aplikasi Sistem Akademik Mahasiswa untuk pengisian KRS, pengelolaan data mahasiswa, dan informasi akademik lainnya.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Card 1 -->
                <div class="bg-blue-100 p-4 rounded-lg shadow">
                    <h2 class="text-xl font-semibold text-blue-700">Pengisian KRS</h2>
                    <p class="text-gray-600">Lakukan pengisian KRS untuk semester ini secara online dengan mudah.</p>
                    <a href="#" class="text-blue-500 hover:underline">Isi KRS</a>
                </div>

                <!-- Card 2 -->
                <div class="bg-green-100 p-4 rounded-lg shadow">
                    <h2 class="text-xl font-semibold text-green-700">Jadwal Kuliah</h2>
                    <p class="text-gray-600">Lihat jadwal kuliah Anda dengan cepat dan akurat.</p>
                    <a href="#" class="text-green-500 hover:underline">Lihat Jadwal</a>
                </div>

                <!-- Card 3 -->
                <div class="bg-yellow-100 p-4 rounded-lg shadow">
                    <h2 class="text-xl font-semibold text-yellow-700">Profil Mahasiswa</h2>
                    <p class="text-gray-600">Kelola data profil Anda dengan mudah.</p>
                    <a href="#" class="text-yellow-500 hover:underline">Edit Profil</a>
                </div>

                <!-- Card 4 -->
                <div class="bg-red-100 p-4 rounded-lg shadow">
                    <h2 class="text-xl font-semibold text-red-700">Informasi Akademik</h2>
                    <p class="text-gray-600">Dapatkan informasi terbaru mengenai akademik.</p>
                    <a href="#" class="text-red-500 hover:underline">Lihat Info</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-4">
        <div class="container mx-auto text-center text-sm">
            &copy; 2024 SIAKAD. All rights reserved.
        </div>
    </footer>
</body>

</html>