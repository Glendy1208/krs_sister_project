from flask import Blueprint, request, jsonify
import jwt
import datetime
import mysql.connector
from flask_bcrypt import Bcrypt
import requests

# Inisialisasi Bcrypt dan Blueprint
bcrypt = Bcrypt()
api_bp = Blueprint('api', __name__)

SECRET_KEY = 'mysecretkey'

# Koneksi Database
def get_db_connection():
    return mysql.connector.connect(
        host="mysql",
        user="chronospng",      # Ganti dengan username MySQL Anda
        password="chronospng",      # Ganti dengan password MySQL Anda
        database="sister"
    )
    #     return mysql.connector.connect(
    #     host="localhost",
    #     user="root",      # Ganti dengan username MySQL Anda
    #     password="",      # Ganti dengan password MySQL Anda
    #     database="sister"
    # )

@api_bp.route('/payment', methods=['POST'])
def payment():
    data = request.get_json()
    penyetor = data.get('penyetor')
    jumlah_uang = data.get('jumlah_uang')
    nim = data.get('nim')
    
    if not nim:
        return jsonify({"message": "NIM is required"}), 400

    # Prepare the data to be sent to the other service
    payload = {
        'penyetor': penyetor,
        'jumlah_uang': jumlah_uang
    }

    # Make a POST request to the external service
    response = requests.post(
        "http://bank:5001/api/pembayaran",
        json=payload,
        headers={'Content-Type': 'application/json'}
    )

    # Check the response status
    if response.status_code == 200:
        try:
            # Koneksi ke database
            conn = get_db_connection()  # Assuming this function exists for DB connection
            with conn.cursor() as cursor:
                # Update the payment status in the database
                cursor.execute('UPDATE semester_history SET paid = 1 WHERE nim_fk = %s', (nim,))
                
                # Commit the changes
                conn.commit()

            conn.close()
            
            return jsonify({"message": "Payment processed successfully", "data": response.json()}), 200
        except Exception as e:
            # Handle any exceptions that occur during DB operations
            return jsonify({"message": "Database error", "error": str(e)}), 500
    else:
        return jsonify({"message": "Failed to process payment", "error": response.text}), 500

@api_bp.route('/login', methods=['POST'])
def login():
    data = request.get_json()
    nim = data.get('nim')
    password = data.get('password')

    # Validasi input
    if not nim or not password:
        return jsonify({"message": "NIM and password are required"}), 40
    # Koneksi ke database
    conn = get_db_connection()
    cursor = conn.cursor()

    # Ambil password berdasarkan NIM
    cursor.execute('SELECT password FROM mahasiswa WHERE nim = %s', (nim,))
    result = cursor.fetchone()    
    cursor.execute('SELECT nama FROM mahasiswa WHERE nim = %s', (nim,))
    nama = cursor.fetchone()
    conn.close()

    if result:
        stored_password = result[0]
        # Verifikasi password
        if bcrypt.check_password_hash(stored_password, password):
            # Generate JWT token
            token = jwt.encode({
                'nim': nim,
                'exp': datetime.datetime.utcnow() + datetime.timedelta(hours=1)
            }, SECRET_KEY, algorithm='HS256')

            return jsonify({
                'message': 'Login successful',
                'token': token,
                'namamhs': nama
            }), 200
        else:
            return jsonify({"message": "Invalid password"}), 401
    else:
        return jsonify({"message": "User not found"}), 404

# Endpoint untuk Mengambil Data Mahasiswa Berdasarkan NIM
@api_bp.route('/mahasiswa/<int:nim>', methods=['GET'])
def get_mahasiswa(nim):
    # Koneksi ke database
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)  # Hasil query dalam format dictionary

    # Query JOIN antara mahasiswa dan ukt
    cursor.execute('''
        SELECT 
            m.nim, 
            m.nama, 
            m.semester_now, 
            m.ukt_id, 
            u.besaran, 
            sh.paid
        FROM mahasiswa m
        JOIN ukt u ON m.ukt_id = u.id_ukt
        JOIN semester_history sh ON m.nim = sh.nim_fk
        WHERE m.nim = %s
    ''', (nim,))

    # Ambil hasil query
    result = cursor.fetchone()
    conn.close()

    # Cek apakah data ditemukan
    if result:
        return jsonify({
            'message': 'Data retrieved successfully',
            'data': result
        }), 200
    else:
        return jsonify({"message": "Mahasiswa not found"}), 404

# Endpoint untuk Mengambil semua Data Matakuliah
@api_bp.route('/matakuliah', methods=['GET'])
def get_all_matakuliah():
    # Koneksi ke database
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)  # Hasil query dalam format dictionary

    # Query JOIN untuk mengambil data matakuliah
    cursor.execute('''
        SELECT 
            m.id_matkul,
            m.nama_matkul,
            m.sks,
            jm.tipe_matkul,
            w.jam,
            h.nama_hari,
            r.nama_ruangan,
            k.nama_kelas
        FROM matakuliah m
        JOIN jenis_matkul jm ON m.jenis_matkul_id = jm.id_jenis_matkul
        JOIN waktu w ON m.waktu_id = w.id_waktu
        JOIN hari h ON m.hari_id = h.id_hari
        JOIN ruangan r ON m.ruangan_id = r.id_ruangan
        JOIN kelas k ON m.kelas_id = k.id_kelas
    ''')

    # Ambil semua data hasil query
    result = cursor.fetchall()
    conn.close()

    # Kembalikan hasil dalam bentuk JSON
    return jsonify({
        'message': 'Data matakuliah retrieved successfully',
        'data': result
    }), 200

@api_bp.route('/submit_krs', methods=['POST'])
def submit_krs():
    data = request.get_json()
    nim = data.get('nim')
    matkul_ids = data.get('matkul_ids', [])
    # print(matkul_ids)
    # print(nim)
    # print(data)

    if not nim or not matkul_ids:
        return jsonify({"message": "NIM dan daftar matkul harus diisi"}), 400

    conn = get_db_connection()
    cursor = conn.cursor()

    try:
        # Cari semester_id berdasarkan nim dan semester_now
        cursor.execute('''
            SELECT sh.id_semester
            FROM semester_history sh
            JOIN mahasiswa m ON sh.nim_fk = m.nim
            WHERE m.nim = %s AND sh.angka_semester = m.semester_now
        ''', (nim,))
        semester_data = cursor.fetchone()

        if not semester_data:
            return jsonify({"message": "Semester history tidak ditemukan untuk mahasiswa ini"}), 404

        semester_id = semester_data[0]

        # Masukkan data ke tabel jadwal
        for matkul_id in matkul_ids:
            cursor.execute('''
                INSERT INTO jadwal (semester_id, matkul_id, aktif)
                VALUES (%s, %s, %s)
            ''', (semester_id, matkul_id, 1))

        conn.commit()
        return jsonify({"message": "Mata kuliah berhasil ditambahkan ke jadwal"}), 200

    except Exception as e:
        conn.rollback()
        return jsonify({"message": f"Terjadi kesalahan: {str(e)}"}), 500

    finally:
        cursor.close()
        conn.close()

# Endpoint untuk Mengambil Jadwal Matakuliah Berdasarkan NIM dan Semester
@api_bp.route('/matakuliah/<int:nim>/<int:semester>', methods=['GET'])
def get_jadwal_by_nim_and_semester(nim, semester):
    # Koneksi ke database
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)  # Hasil query dalam format dictionary

    try:
        # Langkah 1: Cari id_semester di tabel semester_history berdasarkan nim dan semester
        cursor.execute('''
            SELECT id_semester
            FROM semester_history
            WHERE nim_fk = %s AND angka_semester = %s
        ''', (nim, semester))
        semester_data = cursor.fetchone()

        if not semester_data:
            return jsonify({"message": "Semester history tidak ditemukan untuk mahasiswa ini"}), 404

        id_semester = semester_data['id_semester']

        # Langkah 2: Ambil semua matkul_id dari tabel jadwal berdasarkan id_semester
        cursor.execute('''
            SELECT matkul_id
            FROM jadwal
            WHERE semester_id = %s
        ''', (id_semester,))
        jadwal_data = cursor.fetchall()

        if not jadwal_data:
            return jsonify({"message": "Tidak ada jadwal untuk semester ini"}), 404

        # Ambil semua matkul_id dari hasil query
        matkul_ids = [row['matkul_id'] for row in jadwal_data]

        # Langkah 3: Ambil detail data matakuliah berdasarkan matkul_id
        format_strings = ','.join(['%s'] * len(matkul_ids))  # Membuat placeholder untuk query IN
        query = f'''
            SELECT 
                m.id_matkul,
                m.nama_matkul,
                m.sks,
                jm.tipe_matkul,
                w.jam,
                h.nama_hari,
                r.nama_ruangan,
                k.nama_kelas
            FROM matakuliah m
            JOIN jenis_matkul jm ON m.jenis_matkul_id = jm.id_jenis_matkul
            JOIN waktu w ON m.waktu_id = w.id_waktu
            JOIN hari h ON m.hari_id = h.id_hari
            JOIN ruangan r ON m.ruangan_id = r.id_ruangan
            JOIN kelas k ON m.kelas_id = k.id_kelas
            WHERE m.id_matkul IN ({format_strings})
        '''
        cursor.execute(query, matkul_ids)
        matkul_data = cursor.fetchall()

        # Return hasil data matakuliah
        return jsonify({
            "message": "Data retrieved successfully",
            "data": matkul_data
        }), 200

    except Exception as e:
        return jsonify({"message": f"Terjadi kesalahan: {str(e)}"}), 500

    finally:
        cursor.close()
        conn.close()

# Endpoint untuk Mengambil Data Mata Kuliah Berdasarkan ID Matkul
@api_bp.route('/matakuliah/<int:id_matkul>', methods=['GET'])
def get_matakuliah_by_id(id_matkul):
    # Koneksi ke database
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)  # Hasil query dalam format dictionary

    try:
        # Query untuk mengambil data mata kuliah berdasarkan id_matkul
        cursor.execute('''
            SELECT 
                m.id_matkul,
                m.nama_matkul,
                m.sks,
                jm.tipe_matkul,
                w.jam,
                h.nama_hari,
                r.nama_ruangan,
                k.nama_kelas
            FROM matakuliah m
            JOIN jenis_matkul jm ON m.jenis_matkul_id = jm.id_jenis_matkul
            JOIN waktu w ON m.waktu_id = w.id_waktu
            JOIN hari h ON m.hari_id = h.id_hari
            JOIN ruangan r ON m.ruangan_id = r.id_ruangan
            JOIN kelas k ON m.kelas_id = k.id_kelas
            WHERE m.id_matkul = %s
        ''', (id_matkul,))

        # Ambil hasil query
        matkul_data = cursor.fetchone()

        # Cek jika mata kuliah ditemukan
        if matkul_data:
            return jsonify({
                "message": "Data mata kuliah ditemukan",
                "data": matkul_data
            }), 200
        else:
            return jsonify({"message": "Mata kuliah tidak ditemukan"}), 404

    except Exception as e:
        return jsonify({"message": f"Terjadi kesalahan: {str(e)}"}), 500

    finally:
        cursor.close()
        conn.close()
        
# Endpoint untuk Menghapus Data Mata Kuliah yang Diambil
@api_bp.route('/jadwal/hapus', methods=['DELETE'])
def delete_jadwal():
    data = request.get_json()  # Ambil data JSON dari body request
    # print(data)
    nim = data.get('nim')  # NIM mahasiswa
    id_matkul_list = data.get('id_matkul')  # List ID mata kuliah

    # Validasi input
    if not nim or not id_matkul_list:
        return jsonify({"message": "NIM dan ID mata kuliah wajib diisi"}), 400

    # Koneksi ke database
    conn = get_db_connection()
    cursor = conn.cursor()

    try:
        # Ambil ID semester saat ini dari mahasiswa berdasarkan NIM
        cursor.execute('''
            SELECT sh.id_semester
            FROM mahasiswa m
            JOIN semester_history sh ON m.nim = sh.nim_fk
            WHERE m.nim = %s AND sh.angka_semester = m.semester_now
        ''', (nim,))
        semester_data = cursor.fetchone()
        if not semester_data:
            print("Semester tidak ditemukan")
            return jsonify({"message": "Semester tidak ditemukan untuk mahasiswa ini"}), 404

        semester_id = semester_data[0]
        # Loop untuk menghapus jadwal berdasarkan id_matkul
        print(id_matkul_list)
        for id_matkul in id_matkul_list:
            cursor.execute('''
                DELETE FROM jadwal
                WHERE semester_id = %s AND matkul_id = %s AND aktif = 1
            ''', (semester_id, id_matkul))

        conn.commit()  # Simpan perubahan

        return jsonify({"message": "Jadwal berhasil dihapus"}), 200

    except Exception as e:
        conn.rollback()  # Batalkan perubahan jika terjadi error
        return jsonify({"message": f"Terjadi kesalahan: {str(e)}"}), 500

    finally:
        cursor.close()
        conn.close()
