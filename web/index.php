<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Rekomendasi Laptop - Klasifikasi Kebutuhan NLP</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- Style CSS Murni (Tanpa Framework) -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Header / Navbar -->
    <header class="site-header">
        <div class="header-container">
            <a href="index.php" class="brand-link">
                <div class="brand-logo">L</div>
                <div>
                    <div class="brand-title">LaptopMatch NLP</div>
                    <div class="brand-subtitle">Sistem Rekomendasi Berbasis Kebutuhan Pengguna</div>
                </div>
            </a>
            <div class="header-nav">
                <a href="index.php" class="nav-btn active">Form Prediksi</a>
            </div>
        </div>
    </header>

    <!-- Konten Utama -->
    <main class="main-wrapper">
        <section class="hero-section">
            <span class="academic-badge">Proyek Skripsi · Text Mining & Machine Learning</span>
            <h1 class="hero-title">Temukan Laptop yang Tepat Sesuai Kebutuhanmu</h1>
            <p class="hero-desc">
                Tuliskan deskripsi aktivitas kerja, software yang akan digunakan, atau spesifikasi yang kamu inginkan. Sistem NLP akan menganalisis kebutuhanmu dan mengelompokkannya ke kategori laptop yang paling sesuai.
            </p>
        </section>

        <!-- Form Card -->
        <div class="card-form">
            <form method="POST" action="hasil.php" id="formPrediksi">
                <div class="form-group">
                    <label for="deskripsi" class="form-label">Deskripsi Kebutuhan Laptop</label>
                    <div class="form-hint">Jelaskan kegiatanmu (misal: coding, render 3D, gaming berat, ngetik skripsi, atau software seperti Adobe, VS Code, Blender).</div>
                    
                    <div class="textarea-wrapper">
                        <textarea 
                            name="deskripsi" 
                            id="deskripsi" 
                            class="custom-textarea" 
                            placeholder="Contoh: Saya butuh laptop untuk skripsi data science, coding Python dan Docker, butuh RAM 32GB dan baterai awet..." 
                            required></textarea>
                    </div>

                    <div class="textarea-footer">
                        <span id="charCount">0 karakter · 0 kata</span>
                        <span style="color: var(--text-muted);">Tekan Ctrl + Enter untuk kirim</span>
                    </div>
                </div>

                <!-- Contoh Preset Prompt Kebutuhan -->
                <div class="presets-container">
                    <span class="presets-label">Pilih Contoh Skenario Kebutuhan:</span>
                    <div class="presets-grid">
                        <button type="button" class="preset-chip" onclick="isiContoh('gaming')">
                            🎮 Gaming Berat & Streaming
                        </button>
                        <button type="button" class="preset-chip" onclick="isiContoh('programming')">
                            💻 Programming & Docker
                        </button>
                        <button type="button" class="preset-chip" onclick="isiContoh('workstation')">
                            🏢 Workstation & CAD 3D
                        </button>
                        <button type="button" class="preset-chip" onclick="isiContoh('desain')">
                            🎨 Desain Grafis & Render 4K
                        </button>
                        <button type="button" class="preset-chip" onclick="isiContoh('office')">
                            📝 Mahasiswa & Ketik Skripsi
                        </button>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <span>Mulai Prediksi Kategori</span>
                    <span aria-hidden="true">&rarr;</span>
                </button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        &copy; <?php echo date("Y"); ?> Sistem Rekomendasi Laptop Berbasis NLP · Tugas Akhir / Skripsi
    </footer>

    <!-- Script Javascript Murni -->
    <script src="script.js"></script>
</body>
</html>
