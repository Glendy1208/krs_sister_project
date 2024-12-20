<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate form inputs
    print_r($_POST);
    $penyetor = trim($_POST['penyetor']);
    $jumlah_uang = isset($_POST['jumlah_uang']) ? (int) $_POST['jumlah_uang'] : 0;

    // Basic input validation
    if (empty($penyetor) || $jumlah_uang <= 0) {
        $error_message = "Nama penyetor atau jumlah uang tidak valid.";
    } else {
        // Data yang akan dikirim ke API
        $data = json_encode([
            "penyetor" => $penyetor,
            "jumlah_uang" => $jumlah_uang,
            "nim" => $_SESSION['nim']
        ]);

        // Inisialisasi cURL
        $ch = curl_init("http://app:5000/payment");
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
            // Pembayaran berhasil
            header('Location: index_krs.php');
            exit();
        }
        $error_message = $result['message'] ?? "Pembayaran gagal. Periksa data Anda.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayar UKT</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">
        <?php if (isset($error_message)): ?>
            <p class="text-red-500 text-center mb-4"><?= $error_message; ?></p>
        <?php endif; ?>
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Formulir Pembayaran UKT</h2>
        <form action="" method="POST" class="space-y-4">
            <div>
                <label for="penyetor" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input readonly type="text" id="penyetor" name="penyetor" class="mt-1 w-full px-4 py-2 border rounded-lg" value="<?= $_SESSION['nama'] ?>" required>
            </div>
            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                <input type="text" id="alamat" name="alamat" class="mt-1 w-full px-4 py-2 border rounded-lg" placeholder="Masukkan alamat">
            </div>
            <div>
                <label for="no_telp" class="block text-sm font-medium text-gray-700">Nomor Telpon</label>
                <input type="text" id="no_telp" name="no_telp" class="mt-1 w-full px-4 py-2 border rounded-lg" placeholder="Masukkan nomor telpon">
            </div>
            <div>
                <label for="nomorkartu" class="block text-sm font-medium text-gray-700">Nomor Kartu</label>
                <input type="text" id="nomorkartu" name="nomorkartu" class="mt-1 w-full px-4 py-2 border rounded-lg" placeholder="Masukkan nomor kartu">
            </div>
            <div>
                <label for="cvv" class="block text-sm font-medium text-gray-700">CVV</label>
                <input type="text" id="cvv" name="cvv" class="mt-1 w-full px-4 py-2 border rounded-lg" placeholder="Masukkan CVV">
            </div>
            <div>
                <label for="jumlah_uang" class="block text-sm font-medium text-gray-700">Jumlah Uang</label>
                <input readonly type="number" id="jumlah_uang" name="jumlah_uang" class="mt-1 w-full px-4 py-2 border rounded-lg" value="3000000" required>
            </div>
            <button type="submit" class="w-full py-2 px-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Kirim</button>
        </form>
    </div>
</body>

</html>