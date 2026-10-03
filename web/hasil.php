<?php
// Pastikan ada input deskripsi
$deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';

if (empty($deskripsi)) {
    header("Location: index.php");
    exit;
}

$api_url = "http://localhost:5000/predict";
$data = array("deskripsi" => $deskripsi);
$kategori = "";
$status_api = "online";
$status_pesan = "Berhasil terhubung ke model machine learning (Flask API).";

// Setup HTTP POST Request ke Flask API dengan timeout 3 detik
$options = array(
    "http" => array(
        "header"  => "Content-Type: application/json\r\n",
        "method"  => "POST",
        "content" => json_encode($data),
        "timeout" => 3.0,
        "ignore_errors" => true
    ),
);

$context = stream_context_create($options);
$result = @file_get_contents($api_url, false, $context);

if ($result !== FALSE) {
    $response = json_decode($result, true);
    if (isset($response["kategori"])) {
        $kategori = trim($response["kategori"]);
    }
}

// Fallback cerdas: Jika Flask API lokal (port 5000) belum menyala/offline saat demo skripsi,
// gunakan pencocokan kata kunci (rule-based NLP) agar aplikasi tidak error/crash.
if (empty($kategori)) {
    $status_api = "offline";
    $status_pesan = "Flask API offline, menggunakan sistem pencocokan NLP cadangan.";
    
    $d_lower = strtolower($deskripsi);
    
    if (preg_match('/(rtx|game|gaming|cyberpunk|stream|fps|rog|predator|legion|144hz|240hz)/i', $d_lower)) {
        $kategori = "Gaming";
    } elseif (preg_match('/(cad|solidworks|revit|ansys|simulasi|workstation|ecc|isv|precision|zbook|thinkpad p)/i', $d_lower)) {
        $kategori = "Workstation";
    } elseif (preg_match('/(code|coding|programmer|programming|docker|flutter|vscode|developer|kompilasi|react|python)/i', $d_lower)) {
        $kategori = "Programming";
    } elseif (preg_match('/(photoshop|illustrator|desain|design|premiere|video|render|srgb|color grading|creator|oled)/i', $d_lower)) {
        $kategori = "Desain Grafis";
    } else {
        $kategori = "Office";
    }
}

// Load dataset laptop dari laptops.json
$laptops = array();
if (file_exists('laptops.json')) {
    $json_content = file_get_contents('laptops.json');
    $laptops = json_decode($json_content, true);
    if (!is_array($laptops)) {
        $laptops = array();
    }
}

// Filter laptop berdasarkan kategori hasil prediksi (case-insensitive)
$filtered = array_filter($laptops, function($laptop) use ($kategori) {
    if (!isset($laptop['kategori'])) return false;
    return strtolower(trim($laptop['kategori'])) === strtolower(trim($kategori));
});

$total_cocok = count($filtered);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Rekomendasi - <?php echo htmlspecialchars($kategori); ?> | LaptopMatch NLP</title>
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
                <a href="index.php" class="nav-btn">← Input Baru</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-wrapper">
        
        <!-- Tombol Kembali -->
        <div class="back-nav">
            <a href="index.php" class="btn-back">
                &larr; Ubah Deskripsi Kebutuhan
            </a>
        </div>

        <!-- Banner Hasil Prediksi -->
        <section class="result-banner">
            <div class="result-banner-header">
                <div>
                    <div class="kategori-label">Hasil Klasifikasi Kategori:</div>
                    <div class="kategori-value"><?php echo htmlspecialchars($kategori); ?></div>
                </div>
                <div>
                    <?php if ($status_api === "online"): ?>
                        <span class="badge-status">
                            &bull; Model NLP API Terhubung
                        </span>
                    <?php else: ?>
                        <span class="badge-status warning">
                            &bull; Mode Cadangan (Rule-based NLP)
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="result-summary-text">
                Berdasarkan analisis teks kebutuhan pengguna, sistem merekomendasikan kategori <strong><?php echo htmlspecialchars($kategori); ?></strong> dengan total <strong><?php echo $total_cocok; ?></strong> unit laptop yang relevan.
            </div>

            <div class="result-query-box">
                &ldquo;<?php echo htmlspecialchars($deskripsi); ?>&rdquo;
            </div>
        </section>

        <!-- Toolbar Pencarian & Urutan -->
        <div class="toolbar-results">
            <div class="count-text">
                Menampilkan <span class="count-number" id="visibleCount"><?php echo $total_cocok; ?></span> laptop kategori <strong><?php echo htmlspecialchars($kategori); ?></strong>
            </div>
            
            <div class="filter-controls">
                <input type="text" id="searchInput" class="input-search" placeholder="Cari nama laptop/spek..." onkeyup="filterLaptops()">
                <select id="sortSelect" class="select-sort" onchange="sortLaptops()">
                    <option value="default">Urutkan: Rekomendasi</option>
                    <option value="price-asc">Harga: Termurah</option>
                    <option value="price-desc">Harga: Tertinggi</option>
                </select>
            </div>
        </div>

        <!-- Daftar Kartu Laptop -->
        <div class="laptop-grid" id="laptopGrid">
            <?php if (!empty($filtered)): ?>
                <?php foreach ($filtered as $lap): ?>
                    <?php 
                        $nama = htmlspecialchars($lap['nama_laptop'] ?? 'Laptop Tanpa Nama');
                        $gambar = htmlspecialchars($lap['gambar'] ?? '');
                        $kat = htmlspecialchars($lap['kategori'] ?? $kategori);
                        $cpu = htmlspecialchars($lap['cpu'] ?? '-');
                        $gpu = htmlspecialchars($lap['gpu'] ?? '-');
                        $ram = htmlspecialchars($lap['ram'] ?? '-');
                        $storage = htmlspecialchars($lap['storage'] ?? '-');
                        $layar = htmlspecialchars($lap['layar'] ?? '-');
                        $harga_raw = (int)($lap['harga'] ?? 0);
                        $harga_format = number_format($harga_raw, 0, ',', '.');
                    ?>
                    <article class="laptop-card" data-price="<?php echo $harga_raw; ?>" data-name="<?php echo strtolower($nama . ' ' . $cpu . ' ' . $gpu); ?>">
                        <div class="laptop-image-wrapper">
                            <span class="laptop-category-tag"><?php echo $kat; ?></span>
                            <?php if (!empty($gambar)): ?>
                                <img 
                                    src="<?php echo $gambar; ?>" 
                                    alt="<?php echo $nama; ?>" 
                                    class="laptop-img" 
                                    loading="lazy"
                                    referrerpolicy="no-referrer"
                                    onerror="handleImageError(this, '<?php echo addslashes($nama); ?>')">
                            <?php else: ?>
                                <div class="img-fallback-placeholder">
                                    <div class="img-fallback-icon">💻</div>
                                    <div class="img-fallback-text"><?php echo $nama; ?></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="laptop-content">
                            <h2 class="laptop-title"><?php echo $nama; ?></h2>

                            <div class="specs-list">
                                <div class="spec-item">
                                    <span class="spec-label">CPU</span>
                                    <span class="spec-value"><?php echo $cpu; ?></span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">GPU</span>
                                    <span class="spec-value"><?php echo $gpu; ?></span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">RAM</span>
                                    <span class="spec-value"><?php echo $ram; ?></span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">Storage</span>
                                    <span class="spec-value"><?php echo $storage; ?></span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">Layar</span>
                                    <span class="spec-value"><?php echo $layar; ?></span>
                                </div>
                            </div>

                            <div class="laptop-card-footer">
                                <div class="price-container">
                                    <span class="price-label">Estimasi Harga</span>
                                    <span class="price-value">Rp <?php echo $harga_format; ?></span>
                                </div>
                                <a href="https://www.tokopedia.com/search?q=<?php echo urlencode($nama); ?>" target="_blank" rel="noopener noreferrer" class="btn-detail">
                                    Cek Pasar &nearr;
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state" style="grid-column: 1 / -1;">
                    <div class="empty-icon">🔍</div>
                    <h3 class="empty-title">Tidak Ada Laptop Ditemukan</h3>
                    <p class="empty-desc">
                        Belum ada unit laptop yang terdaftar dalam dataset untuk kategori <strong><?php echo htmlspecialchars($kategori); ?></strong>. Coba periksa file <code>laptops.json</code> atau masukkan variasi deskripsi lainnya.
                    </p>
                    <a href="index.php" class="btn-back">Coba Deskripsi Baru</a>
                </div>
            <?php endif; ?>
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
