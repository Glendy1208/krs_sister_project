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
    <title>Profil Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Navbar -->
    <?php
    include('layouts/navbar.php');
    ?>
    
    <!-- Main Content -->
    <main class="flex-grow container mx-auto p-4">
        <div class="bg-white shadow rounded-lg p-6 max-w-md mx-auto">
            <h2 class="text-2xl font-bold mb-4 text-center text-blue-600">Profil Mahasiswa</h2>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">NIM</label>
                <p class="bg-gray-100 p-2 rounded border">123456789</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Nama</label>
                <p class="bg-gray-100 p-2 rounded border">John Doe</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-auto">
        <p>© 2024 SIAKAD Mahasiswa. All rights reserved.</p>
    </footer>
</body>
</html>
