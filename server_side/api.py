from flask import Blueprint, request, jsonify
import jwt
import datetime
import mysql.connector
from flask_bcrypt import Bcrypt

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

@api_bp.route('/login', methods=['POST'])
def login():
    data = request.get_json()
    nim = data.get('nim')
    password = data.get('password')

    # Validasi input
    if not nim or not password:
        return jsonify({"message": "NIM and password are required"}), 400

    # Koneksi ke database
    conn = get_db_connection()
    cursor = conn.cursor()

    # Ambil password berdasarkan NIM
    cursor.execute('SELECT password FROM mahasiswa WHERE nim = %s', (nim,))
    result = cursor.fetchone()
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
                'token': token
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
