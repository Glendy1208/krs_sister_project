<?php
include('env.php');

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nim = $_POST['nim'];
    $password = $_POST['password'];

    // Data yang akan dikirim ke API
    $data = json_encode([
        "nim" => $nim,
        "password" => $password
    ]);

    // Inisialisasi cURL
    $ch = curl_init("$BASE_URL/login"); // Sesuaikan dengan URL API Flask Anda
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    // Eksekusi cURL dan ambil respons
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Parsing respons dari API
    $result = json_decode($response, true);

    if ($httpcode == 200) {
        // Login berhasil, simpan token dan NIM ke dalam session
        $_SESSION['nim'] = $nim;
        $_SESSION['token'] = $result['token'];
        $_SESSION['nama'] = $result['namamhs'][0];

        header("Location: siakad_mhs/home.php");
        exit();
    } else {
        // Login gagal
        $error_message = $result['message'] ?? "Login gagal. Periksa NIM dan password Anda.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Selamat Datang, silahkan login</h2>
        <?php if (isset($error_message)): ?>
            <p class="text-red-500 text-center mb-4"><?= $error_message; ?></p>
        <?php endif; ?>
        <form action="" method="POST" class="space-y-4">
            <div>
                <label for="nim" class="block text-sm font-medium text-gray-700">NIM</label>
                <input type="number" id="nim" name="nim" class="mt-1 w-full px-4 py-2 border rounded-lg" placeholder="Enter your NIM" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" class="mt-1 w-full px-4 py-2 border rounded-lg" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="w-full py-2 px-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Login</button>
        </form>
    </div>
</body>

</html>