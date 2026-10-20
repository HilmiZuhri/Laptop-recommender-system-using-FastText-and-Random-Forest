from pathlib import Path
import re

import fasttext
import joblib
import numpy as np
from flask import Flask, request, jsonify

app = Flask(__name__)

# ====== Direktori dan Model ======
BASE_DIR = Path(__file__).resolve().parent

FASTTEXT_PATH = BASE_DIR / "fasttext_model.bin"
RF_PATH = BASE_DIR / "random_forest_model.joblib"

if not FASTTEXT_PATH.exists():
    raise FileNotFoundError(f"Model FastText tidak ditemukan: {FASTTEXT_PATH}")

if not RF_PATH.exists():
    raise FileNotFoundError(f"Model Random Forest tidak ditemukan: {RF_PATH}")

fasttext_model = fasttext.load_model(str(FASTTEXT_PATH))
rf_classifier = joblib.load(RF_PATH)


# ====== Preprocessing ======
# Pertahankan preprocessing yang digunakan saat eksperimen model.
def preprocess_text(text):
    text = text.lower().strip()
    text = re.sub(r"[^\w\s]", "", text)
    return text


# ====== Health Check ======
@app.route("/", methods=["GET"])
def health_check():
    return jsonify({
        "status": "ok",
        "message": "Laptop Recommendation API is running"
    })


# ====== API Prediction ======
@app.route("/predict", methods=["POST"])
def predict():
    data = request.get_json(silent=True)

    if not isinstance(data, dict):
        return jsonify({"error": "Request harus berupa JSON"}), 400

    deskripsi = data.get("deskripsi")

    if not isinstance(deskripsi, str) or not deskripsi.strip():
        return jsonify({
            "error": "Masukkan deskripsi laptop yang valid"
        }), 400

    clean_text = preprocess_text(deskripsi)

    vector = np.asarray(
        fasttext_model.get_sentence_vector(clean_text),
        dtype=np.float32
    ).reshape(1, -1)

    prediksi = rf_classifier.predict(vector)[0]

    return jsonify({
        "kategori": str(prediksi)
    })


if __name__ == "__main__":
    app.run(
        host="0.0.0.0",
        port=5000,
        debug=False
    )