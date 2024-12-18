import mysql.connector
from flask_bcrypt import Bcrypt

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
    cursor.execute('DELETE FROM mahasiswa')  # Hapus data mahasiswa dulu
    cursor.execute('DELETE FROM ukt')        # Hapus data ukt setelahnya

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

    # Commit perubahan dan tutup koneksi
    conn.commit()
    conn.close()

    print("Seeder berhasil dijalankan.")
