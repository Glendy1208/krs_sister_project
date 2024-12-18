import mysql.connector
from flask_bcrypt import Bcrypt
import random

bcrypt = Bcrypt()

def seed_database():
    # Koneksi ke database MySQL
    conn = mysql.connector.connect(
        host="localhost",
        user="root",      # Ganti dengan username MySQL Anda
        password="",      # Ganti dengan password MySQL Anda
        database="sister"
    )
    cursor = conn.cursor()

    # Menghapus data lama dengan urutan yang benar
    print("Menghapus data lama...")
    cursor.execute('DELETE FROM jadwal')
    cursor.execute('DELETE FROM semester_history')
    cursor.execute('DELETE FROM mahasiswa')  
    cursor.execute('DELETE FROM ukt')        
    cursor.execute('DELETE FROM matakuliah')
    cursor.execute('DELETE FROM jenis_matkul')
    cursor.execute('DELETE FROM waktu')
    cursor.execute('DELETE FROM ruangan')
    cursor.execute('DELETE FROM kelas')
    cursor.execute('DELETE FROM hari')

    # Tambahkan data ke tabel ukt
    print("Menambahkan data ukt...")
    cursor.execute('INSERT INTO ukt (id_ukt, besaran) VALUES (%s, %s)', (1, 3000000))

    # Tambahkan data ke tabel mahasiswa
    print("Menambahkan data mahasiswa...")
    hashed_password = bcrypt.generate_password_hash("rahasia123").decode('utf-8')
    cursor.execute('''
        INSERT INTO mahasiswa (nim, nama, password, semester_now, ukt_id)
        VALUES (%s, %s, %s, %s, %s)
    ''', (220411100076, 'Glendy Hernandez', hashed_password, 5, 1))

    #tambahkan data ke tabel semester_history
    print("Menambahkan data semester history...")
    cursor.execute('''
        INSERT INTO semester_history (angka_semester, paid, nim_fk)
        VALUES (%s, %s, %s)
    ''', (5,1,220411100076))

    # Seeder tabel waktu
    print("Menambahkan data tabel waktu...")
    waktu_data = ["07:00", "09:30", "13:00"]
    for jam in waktu_data:
        cursor.execute('INSERT INTO waktu (jam) VALUES (%s)', (jam,))

    # Seeder tabel ruangan
    print("Menambahkan data tabel ruangan...")
    ruangan_data = ["F203", "F206", "F304", "F306", "F404", "F406"]
    for ruangan in ruangan_data:
        cursor.execute('INSERT INTO ruangan (nama_ruangan) VALUES (%s)', (ruangan,))

    # Seeder tabel kelas
    print("Menambahkan data tabel kelas...")
    kelas_data = ["A", "B"]
    for nama_kelas in kelas_data:
        cursor.execute('INSERT INTO kelas (nama_kelas) VALUES (%s)', (nama_kelas,))

    # Seeder tabel hari
    print("Menambahkan data tabel hari...")
    hari_data = ["Senin", "Selasa", "Rabu", "Kamis"]
    for nama_hari in hari_data:
        cursor.execute('INSERT INTO hari (nama_hari) VALUES (%s)', (nama_hari,))

    # Seeder tabel jenis_matkul
    print("Menambahkan data tabel jenis_matkul...")
    jenis_matkul_data = ["P", "W"]
    for tipe_matkul in jenis_matkul_data:
        cursor.execute('INSERT INTO jenis_matkul (tipe_matkul) VALUES (%s)', (tipe_matkul,))

    # Ambil ID dari tabel-tabel yang baru di-seed
    print("Mengambil data ID untuk referensi...")
    cursor.execute('SELECT id_waktu FROM waktu')
    waktu_ids = [row[0] for row in cursor.fetchall()]

    cursor.execute('SELECT id_ruangan FROM ruangan')
    ruangan_ids = [row[0] for row in cursor.fetchall()]

    cursor.execute('SELECT id_kelas FROM kelas')
    kelas_ids = [row[0] for row in cursor.fetchall()]

    cursor.execute('SELECT id_jenis_matkul FROM jenis_matkul')
    jenis_matkul_ids = [row[0] for row in cursor.fetchall()]

    cursor.execute('SELECT id_hari FROM hari')
    hari_ids = [row[0] for row in cursor.fetchall()]

    # Seeder tabel matakuliah
    print("Menambahkan data tabel matakuliah...")
    matkul_names = [
        "Pemrograman Web", "Struktur Data", "Basis Data", "Jaringan Komputer", "Sistem Operasi",
        "Pemrograman Mobile", "Kecerdasan Buatan", "Algoritma dan Pemrograman", "Matematika Diskrit", "Etika Profesi"
    ]
    sks_options = [3, 4]
    used_slots = set()  # Untuk mencegah bentrok waktu, ruangan, dan hari

    for matkul_name in matkul_names:
        while True:
            waktu_id = random.choice(waktu_ids)
            ruangan_id = random.choice(ruangan_ids)
            kelas_id = random.choice(kelas_ids)
            jenis_matkul_id = random.choice(jenis_matkul_ids)
            hari_id = random.choice(hari_ids)
            slot = (hari_id, waktu_id, ruangan_id)  # Kombinasi untuk mencegah bentrok

            if slot not in used_slots:
                used_slots.add(slot)
                cursor.execute('''
                    INSERT INTO matakuliah (waktu_id, kelas_id, jenis_matkul_id, hari_id, ruangan_id, nama_matkul, sks)
                    VALUES (%s, %s, %s, %s, %s, %s, %s)
                ''', (waktu_id, kelas_id, jenis_matkul_id, hari_id, ruangan_id, matkul_name, random.choice(sks_options)))
                break

    # Commit perubahan dan tutup koneksi
    conn.commit()
    conn.close()

    print("Seeder berhasil dijalankan.")
