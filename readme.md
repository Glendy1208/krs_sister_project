### API ENDPOINT
---
1. POST http://localhost:5000/login : login Mahasiswa
2. GET http://localhost:5000/mahasiswa/nim : mengambil data mahasiswa berdasarkan nim
3. GET http://localhost:5000/matakuliah : mengambil semua data jadwal matakuliah
4. POST http://localhost:5000/submit_krs : mennangani data inputan krs mahasiswa untuk dimasukkan ke dalam list matakuliah yang diambilnya
5. GET http://localhost:5000/matakuliah/nim/semester : mengambil semua jadwal yang diambil oleh mahasiswa tertentu berdasarkan nim dan semester
6. GET http://localhost:5000/matakuliah/id_matkul : Mengambil Data Mata Kuliah Berdasarkan ID Matkul
7. DELETE http://localhost:5000/matakuliah/id_matkul/jadwal/hapus : Menghapus matkul yang ada dijadwal mahasiswa