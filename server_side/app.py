from flask import Flask
from flask_cors import CORS
from seeder import seed_database
from api import api_bp

app = Flask(__name__)
CORS(app)

# Secret key untuk JWT
app.config['SECRET_KEY'] = 'mysecretkey'

# Panggil Seeder
print("Menjalankan seeder...")
seed_database()

# Register Blueprint API
app.register_blueprint(api_bp)

# Menjalankan aplikasi Flask
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)

