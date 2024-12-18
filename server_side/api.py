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
