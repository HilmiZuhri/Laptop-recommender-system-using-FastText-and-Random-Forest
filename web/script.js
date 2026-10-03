/**
 * Sistem Rekomendasi Laptop Berbasis NLP - Skripsi Script
 * Vanilla JavaScript (Murni Tanpa Framework)
 */

// Bank Contoh Prompt Kebutuhan untuk Pengujian Skripsi
const CONTOH_DESKRIPSI = {
    gaming: "Saya membutuhkan laptop gaming performa tinggi dengan kartu grafis dedicated NVIDIA RTX untuk memainkan game AAA seperti Cyberpunk 2077 dan Black Myth Wukong. Layar harus 165Hz+, RAM minimal 16GB atau 32GB, dan sistem pendingin optimal.",
    programming: "Laptop untuk software engineering, coding mobile app dengan Flutter dan Android Studio, menjalankan multi-container Docker, serta kompilasi kode cepat. Butuh RAM minimal 32 GB, prosesor multi-core efisien, dan baterai tahan lama.",
    workstation: "Kebutuhan engineer untuk simulasi CAD 3D Autodesk Revit, SolidWorks, komputasi analisis numerik, dan data science machine learning dengan dataset besar. Butuh sertifikasi ISV, bodi kokoh kelas militer, dan RAM 32GB atau 64GB.",
    desain: "Saya seorang desainer grafis dan editor video konten kreator. Butuh laptop dengan layar akurasi warna tinggi 100% sRGB / DCI-P3 Pantone Validated, kuat untuk Adobe Premiere Pro export video 4K dan 3D rendering Blender tanpa patah.",
    office: "Saya mahasiswa butuh laptop ringan dan hemat daya untuk pengerjaan skripsi di Microsoft Word, olah data statistik di Excel / SPSS, browsing jurnal ilmiah, meeting Zoom, dan baterai awet dipakai seharian di kampus."
};

// 1. Mengisi form dengan contoh kebutuhan
function isiContoh(kategori) {
    const textarea = document.getElementById('deskripsi');
    if (!textarea) return;
    
    if (CONTOH_DESKRIPSI[kategori]) {
        textarea.value = CONTOH_DESKRIPSI[kategori];
        textarea.focus();
        updateCharCount();
        
        // Animasi feedback kecil
        textarea.style.borderColor = 'var(--primary)';
        setTimeout(() => {
            textarea.style.borderColor = '';
        }, 400);
    }
}

// 2. Hitung jumlah karakter & kata
function updateCharCount() {
    const textarea = document.getElementById('deskripsi');
    const counter = document.getElementById('charCount');
    if (!textarea || !counter) return;

    const text = textarea.value.trim();
    const chars = textarea.value.length;
    const words = text.length === 0 ? 0 : text.split(/\s+/).length;

    counter.textContent = `${chars} karakter · ${words} kata`;
}

// 3. Fallback jika gambar eksternal gagal dimuat (Zero broken images)
function handleImageError(imgElement, namaLaptop) {
    if (!imgElement || !imgElement.parentNode) return;
    
    const container = imgElement.parentNode;
    const placeholder = document.createElement('div');
    placeholder.className = 'img-fallback-placeholder';
    placeholder.innerHTML = `
        <div class="img-fallback-icon">💻</div>
        <div class="img-fallback-text">${escapeHtml(namaLaptop || 'Laptop')}</div>
    `;
    
    imgElement.style.display = 'none';
    container.appendChild(placeholder);
}

// Helper escape HTML
function escapeHtml(str) {
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// 4. Pencarian / Filter instan di halaman hasil.php
function filterLaptops() {
    const input = document.getElementById('searchInput');
    const grid = document.getElementById('laptopGrid');
    const counter = document.getElementById('visibleCount');
    if (!input || !grid) return;

    const query = input.value.toLowerCase().trim();
    const cards = grid.querySelectorAll('.laptop-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const text = card.getAttribute('data-name') || '';
        if (text.includes(query)) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    if (counter) {
        counter.textContent = visibleCount;
    }
}

// 5. Pengurutan harga (Sorting) di halaman hasil.php
function sortLaptops() {
    const select = document.getElementById('sortSelect');
    const grid = document.getElementById('laptopGrid');
    if (!select || !grid) return;

    const value = select.value;
    const cards = Array.from(grid.querySelectorAll('.laptop-card'));

    if (value === 'price-asc') {
        cards.sort((a, b) => {
            const pA = parseInt(a.getAttribute('data-price') || '0', 10);
            const pB = parseInt(b.getAttribute('data-price') || '0', 10);
            return pA - pB;
        });
    } else if (value === 'price-desc') {
        cards.sort((a, b) => {
            const pA = parseInt(a.getAttribute('data-price') || '0', 10);
            const pB = parseInt(b.getAttribute('data-price') || '0', 10);
            return pB - pA;
        });
    }

    // Render ulang kartu sesuai urutan
    cards.forEach(card => grid.appendChild(card));
}

// Inisialisasi event listener saat DOM siap
document.addEventListener('DOMContentLoaded', () => {
    const textarea = document.getElementById('deskripsi');
    if (textarea) {
        textarea.addEventListener('input', updateCharCount);
        updateCharCount();

        // Shortcut Ctrl + Enter untuk submit
        textarea.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                const form = document.getElementById('formPrediksi');
                if (form && textarea.value.trim().length > 0) {
                    form.submit();
                }
            }
        });
    }
});
