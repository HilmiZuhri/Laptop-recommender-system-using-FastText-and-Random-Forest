from flask import Flask, request, jsonify
import fasttext
import pickle
import re
import numpy as np
import joblib
import string

app = Flask(__name__)

# ====== Load Model ======
fasttext_model = fasttext.load_model("api/fasttext_model.bin")
rf_classifier = joblib.load("api/random_forest_model.joblib")

# ====== Preprocessing sederhana ======
def preprocess_text(text):
    text = text.lower().strip()
    text = re.sub(r'[^\w\s]', '', text)  # hapus simbol
    return text

# ====== API Endpoint ======
@app.route("/predict", methods=["POST"])
def predict():
    data = request.get_json()
    if not data or "deskripsi" not in data:
        return jsonify({"error": "Masukkan deskripsi laptop"}), 400

    deskripsi = data["deskripsi"]
    clean_text = preprocess_text(deskripsi)

    # Ekstraksi fitur dari FastText
    vector = np.array(fasttext_model.get_sentence_vector(clean_text)).reshape(1, -1)

    # Prediksi dengan Random Forest
    prediksi = rf_classifier.predict(vector)[0]

    return jsonify({"kategori": prediksi})

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)