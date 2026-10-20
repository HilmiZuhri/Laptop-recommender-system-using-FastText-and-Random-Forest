import os
from pathlib import Path

import gdown

BASE_DIR = Path(__file__).resolve().parent

MODELS = {
    "fasttext_model.bin": os.environ.get("FASTTEXT_FILE_ID"),
    "random_forest_model.joblib": os.environ.get("RF_FILE_ID"),
}

for filename, file_id in MODELS.items():
    if not file_id:
        raise RuntimeError(
            f"Environment variable untuk {filename} belum diatur."
        )

    destination = BASE_DIR / filename

    # Hindari download ulang jika file sudah tersedia.
    if destination.exists() and destination.stat().st_size > 0:
        print(f"Model sudah tersedia: {filename}")
        continue

    url = f"https://drive.google.com/uc?id={file_id}"

    print(f"Mengunduh {filename}...")

    downloaded = gdown.download(
        url,
        str(destination),
        quiet=False,
        fuzzy=True
    )

    if not downloaded or not destination.exists():
        raise RuntimeError(
            f"Gagal mengunduh model: {filename}"
        )

    if destination.stat().st_size == 0:
        raise RuntimeError(
            f"File model kosong: {filename}"
        )

    print(f"Berhasil mengunduh: {filename}")

print("Semua model berhasil disiapkan.")
