<?php
// Katalog Koleksi
require_once __DIR__ . '/data/koleksi.php';

$page_title       = "Koleksi | Nusantara Gallery Art";
$page_description = "Telusuri 50 koleksi lukisan, naskah, artefak, dan wastra Nusantara dengan filter kategori dan rentang era.";
$current_page     = "koleksi";

$counts = get_koleksi_counts();

include "layout/header.php";
?>

    <main id="main-content" class="page-width">
      <header class="page-title">
        <p class="eyebrow">Arsip Terbuka GLAM</p>
        <h2>Koleksi yang Merawat Ingatan</h2>
        <p>Telusuri <?= (int)$counts['all'] ?> karya pilihan warisan budaya Nusantara melalui dokumentasi metadata visual dan kuratorial terpercaya. Gunakan bilah samping untuk memfilter berdasarkan kategori, rentang era sejarah, atau melakukan pemesanan tiket pameran.</p>
      </header>

      <!-- Tata Letak Dua Kolom: Sidebar Filter & Grid Konten -->
      <div class="catalog-layout">
        
        <!-- ═══ SIDEBAR FILTER & LAYANAN ═══ -->
        <aside class="catalog-sidebar" id="catalog-sidebar" aria-label="Bilah Samping Filter Koleksi">
          <div class="sidebar-header">
            <h3>Filter &amp; Layanan</h3>
            <button class="btn-sidebar-close" id="btn-sidebar-close" type="button" aria-label="Tutup filter">×</button>
          </div>

          <!-- 1. Pencarian Kata Kunci -->
          <div class="sidebar-widget">
            <label class="sidebar-label" for="catalog-search">Cari Koleksi</label>
            <div class="search-field">
              <input
                id="catalog-search"
                type="search"
                aria-label="Cari judul, seniman, asal wilayah, atau era"
                placeholder="Cari judul, seniman, era…"
                autocomplete="off"
              />
            </div>
          </div>

          <!-- 2. Filter Jenis Koleksi -->
          <div class="sidebar-widget">
            <span class="sidebar-label">Jenis Koleksi</span>
            <div class="sidebar-filter-list" role="tablist" aria-label="Pilihan Jenis Koleksi">
              <button class="sidebar-filter-btn is-active" type="button" data-filter="all" aria-pressed="true">
                <span>Semua Koleksi</span>
                <span class="filter-count" data-count="all"><?= $counts['all'] ?></span>
              </button>
              <button class="sidebar-filter-btn" type="button" data-filter="lukisan" aria-pressed="false">
                <span>Lukisan Klasik</span>
                <span class="filter-count" data-count="lukisan"><?= $counts['lukisan'] ?></span>
              </button>
              <button class="sidebar-filter-btn" type="button" data-filter="naskah" aria-pressed="false">
                <span>Naskah Kuno</span>
                <span class="filter-count" data-count="naskah"><?= $counts['naskah'] ?></span>
              </button>
              <button class="sidebar-filter-btn" type="button" data-filter="artefak" aria-pressed="false">
                <span>Artefak 3D</span>
                <span class="filter-count" data-count="artefak"><?= $counts['artefak'] ?></span>
              </button>
              <button class="sidebar-filter-btn" type="button" data-filter="wastra" aria-pressed="false">
                <span>Wastra &amp; Kriya</span>
                <span class="filter-count" data-count="wastra"><?= $counts['wastra'] ?></span>
              </button>
            </div>
          </div>

          <!-- 3. Filter Range Tahun (Rentang Era) -->
          <div class="sidebar-widget">
            <div class="sidebar-label-row">
              <span class="sidebar-label">Rentang Tahun</span>
              <span class="year-range-display" id="year-range-display">300 – 1950 M</span>
            </div>
            
            <div class="year-slider-container">
              <input
                type="range"
                id="filter-year-range"
                min="300"
                max="1950"
                value="1950"
                step="10"
                aria-label="Filter tahun maksimum"
              />
              <div class="slider-marks">
                <span>300 M</span>
                <span>1200 M</span>
                <span>1950 M</span>
              </div>
            </div>

            <div class="year-input-grid">
              <div>
                <label for="year-min-input" class="sub-label">Tahun Awal</label>
                <input type="number" id="year-min-input" value="300" min="300" max="1950" step="10" />
              </div>
              <div>
                <label for="year-max-input" class="sub-label">Tahun Akhir</label>
                <input type="number" id="year-max-input" value="1950" min="300" max="1950" step="10" />
              </div>
            </div>

            <div class="era-quick-tags">
              <button type="button" class="era-tag" data-era-min="300" data-era-max="1400">Klasik &amp; Kuno (300–1400 M)</button>
              <button type="button" class="era-tag" data-era-min="1400" data-era-max="1800">Pertengahan (1400–1800 M)</button>
              <button type="button" class="era-tag" data-era-min="1800" data-era-max="1950">Modern Awal (1800–1950 M)</button>
              <button type="button" class="era-tag is-active" data-era-min="300" data-era-max="1950">Semua Era</button>
            </div>
          </div>

          <!-- 4. Layanan Reservasi Tiket Pameran (Sidebar Widget) -->
          <div class="sidebar-widget sidebar-service-card">
            <div class="service-card-header">
              <span class="service-card-icon" style="font-family: var(--font-mono); font-size: 0.75rem; font-weight: 700; border: 1px solid var(--border-color); padding: 3px 6px; border-radius: 4px;">PASS</span>
              <div>
                <h4>Reservasi Tiket</h4>
                <p>Karcis &amp; Tur Pameran</p>
              </div>
            </div>
            <p class="service-card-desc">
              Pesan karcis kunjungan pameran, tur kurator ahli, dan nikmati diskon rombongan pameran seni.
            </p>
            <a href="layanan.php" class="button button-primary btn-vermilion button-full service-btn">
              Pesan Tiket Pameran <span aria-hidden="true">→</span>
            </a>
          </div>

          <!-- 5. Tombol Reset Filter -->
          <div class="sidebar-widget" style="margin-bottom: 0;">
            <button type="button" class="btn-reset-filters" id="btn-reset-filters">
              Reset Semua Filter
            </button>
          </div>
        </aside>

        <!-- ═══ AREA KONTEN GRID KOLEKSI ═══ -->
        <section class="catalog-content-area" aria-label="Daftar Koleksi Budaya">
          
          <div class="catalog-top-bar">
            <button type="button" class="btn-open-sidebar" id="btn-open-sidebar" aria-expanded="false" aria-controls="catalog-sidebar">
              Filter &amp; Layanan
            </button>
            <p class="result-count" data-result-count aria-live="polite"><?= $counts['all'] ?> koleksi</p>
          </div>

          <section class="catalog-grid" data-catalog-grid aria-label="Daftar koleksi">
            <?php foreach ($KOLEKSI_DATA as $item): ?>
              <?php
                // Tentukan gambar fallback lokal sesuai kategori jika akses gambar eksternal luring
                $fallback = 'assets/images/diponegoro.jpg';
                if (($item['kategori_code'] ?? '') === 'naskah') {
                    $fallback = 'assets/images/babad-diponegoro.jpg';
                } elseif (($item['kategori_code'] ?? '') === 'artefak') {
                    $fallback = 'assets/images/prajnaparamita.jpg';
                } elseif (($item['kategori_code'] ?? '') === 'wastra') {
                    $fallback = 'assets/images/perburuan-rusa.jpg';
                }
              ?>
              <article class="catalog-card"
                       data-category="<?= htmlspecialchars($item['kategori_code'] ?? '') ?>"
                       data-year="<?= (int)($item['tahun_num'] ?? 0) ?>"
                       data-search="<?= htmlspecialchars($item['search_text'] ?? '') ?>">
                <figure>
                  <img src="<?= htmlspecialchars($item['gambar'] ?? '') ?>"
                       alt="<?= htmlspecialchars($item['altGambar'] ?? $item['judul']) ?>"
                       loading="lazy"
                       onerror="this.onerror=null;this.src='<?= $fallback ?>';" />
                  <span><?= htmlspecialchars($item['kategori'] ?? '') ?></span>
                </figure>
                <section>
                  <h3><?= htmlspecialchars($item['judul'] ?? '') ?></h3>
                  <p class="meta-artist"><?= htmlspecialchars($item['pencipta'] ?? '') ?></p>
                  <span class="era"><?= htmlspecialchars($item['asal'] ?? '') ?> · <?= htmlspecialchars($item['tahun'] ?? '') ?></span>
                  <a class="text-link" href="detail.php?id=<?= urlencode($item['id']) ?>">Lihat rekod <span aria-hidden="true">→</span></a>
                </section>
              </article>
            <?php endforeach; ?>

            <p class="empty-state" data-empty-state hidden>
              Tidak ada koleksi yang sesuai dengan kriteria filter Anda. Coba perluas rentang tahun atau pilih kategori lain.
            </p>
          </section>
        </section>
      </div>
    </main>

<?php
include "layout/footer.php";
?>
