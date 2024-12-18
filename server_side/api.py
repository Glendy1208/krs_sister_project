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
        host="localhost",
        user="root",      # Ganti dengan username MySQL Anda
        password="",      # Ganti dengan password MySQL Anda
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
        SELECT m.nim, m.nama, m.semester_now, m.ukt_id, u.besaran
        FROM mahasiswa m
        JOIN ukt u ON m.ukt_id = u.id_ukt
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
