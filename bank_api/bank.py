from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route('/api/pembayaran', methods=['POST'])
def pembayaran():
    data = request.get_json()

    # Validasi input
    if not data:
        return jsonify({"error": "Data tidak boleh kosong."}), 400

    required_fields = ["penyetor", "jumlah_uang"]
    for field in required_fields:
        if field not in data:
            return jsonify({"error": f"{field} wajib diisi."}), 400

    penyetor = data.get("penyetor")
    jumlah_uang = data.get("jumlah_uang")

    # Proses pembayaran
    if jumlah_uang == 3000000:
        return jsonify({"message": f"Pembayaran diterima. Terima kasih {penyetor}!"}), 200
    else:
        return jsonify({"message": "Jumlah uang tidak sesuai untuk pembayaran."}), 200

if __name__ == '__main__':
    app.run(debug=True)

# Menjalankan aplikasi Flask
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5001, debug=True)

