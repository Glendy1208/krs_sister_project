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
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">SIAKAD Mahasiswa</h1>
            <ul class="flex space-x-4">
                <li><a href="index.php" class="hover:underline">Home</a></li>
                <li><a href="profile.php" class="hover:underline">Profile</a></li>
                <li><a href="#" class="hover:underline">Jadwal</a></li>
                <li><a href="#" class="hover:underline">KRS</a></li>
                <li><a href="#" class="hover:underline">Logout</a></li>
            </ul>
        </div>
    </nav>

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
