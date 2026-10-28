<?php
// Beranda Utama - Nusantara Gallery Art
$page_title       = "Nusantara Gallery Art | Warisan dalam Jangkauan";
$page_description = "Jelajahi koleksi seni dan warisan budaya Nusantara melalui arsip digital, metadata, dan pameran virtual.";
$current_page     = "beranda";

include "layout/header.php";
?>

    <main id="main-content">
      <section class="hero page-width" aria-labelledby="hero-title">
        <p class="badge">Digital Cultural Heritage</p>
        <h2 id="hero-title">Preservasi Digital<br />Mahakarya Warisan Nusantara</h2>
        <p class="hero-copy">Menjaga cerita di balik setiap karya. Jelajahi koleksi budaya melalui dokumentasi visual dan metadata yang terbuka untuk riset maupun publik.</p>
        <nav class="hero-actions" aria-label="Jelajahi galeri">
          <a class="button button-primary" href="catalog.php">Jelajahi koleksi <span aria-hidden="true">↗</span></a>
          <a class="text-link" href="layanan.php">Tiket &amp; Layanan Tur <span aria-hidden="true">→</span></a>
          <a class="text-link" href="about.php">Mengenal galeri <span aria-hidden="true">→</span></a>
        </nav>
      </section>

      <section class="sketchbook-section page-width" id="pameran" aria-labelledby="exhibition-title" data-sketchbook-desk>
        <header class="sketchbook-header">
          <p class="eyebrow">Jurnal Lapangan Kurator &amp; Pameran Digital</p>
          <h2 id="exhibition-title">Eksplorasi Mahakarya Warisan Nusantara</h2>
          <p>Pilih mode tampilan di bawah: amati detail goresan fisik lewat <strong>Buku Sketsa &amp; Kaca Pembesar</strong>, atau nikmati perspektif ruang di <strong>Pameran Virtual 3D</strong>.</p>
        </header>
        <div class="viewer-mode-selector" role="tablist" aria-label="Pilihan mode tampilan pameran">
          <button type="button" class="viewer-mode-btn is-active" data-view-mode="sketchbook" role="tab" aria-selected="true">
            Jurnal Buku Sketsa &amp; Lup
          </button>
          <button type="button" class="viewer-mode-btn" data-view-mode="virtual3d" role="tab" aria-selected="false">
            Ruang Pameran Virtual 3D
          </button>
        </div>
        <div class="sketchbook-spread-container" data-view-pane="sketchbook">
          <article class="sketchbook-book" data-sketchbook-book aria-label="Buku sketsa pameran terbuka dua halaman">
            
            <!-- Halaman Kiri: Kanvas Karya & Kaca Pembesar -->
            <div class="sketchbook-page page-left" data-page-left>
              <div class="plate-canvas-frame" data-plate-canvas tabindex="0" role="region" aria-label="Kanvas karya seni, arahkan kursor untuk memperbesar dengan kaca pembesar">
                <img class="plate-image" data-plate-image src="assets/images/diponegoro.jpg" alt="Lukisan Penangkapan Pangeran Diponegoro karya Raden Saleh" />
                <div class="magnifier-lens" data-magnifier aria-hidden="true"></div>
              </div>
              <div class="plate-tag-bottom" data-plate-tag>PELAT 01 — LUKISAN KLASIK · 1857</div>
            </div>

            <!-- Halaman Kanan: Catatan Kurator & Spesifikasi -->
            <div class="sketchbook-page page-right" data-page-right>
              <div class="curator-journal-entry">
                <span class="ephemera-tape">ARSIP RESMI</span>
                <p class="plate-number-badge" data-plate-badge>PELAT 01</p>
                <h3 class="plate-artwork-title" data-plate-title>Penangkapan Pangeran Diponegoro</h3>
                <p class="plate-meta-line" data-plate-meta>Raden Saleh (c. 1811–1880) · 1857 · Jawa Tengah / Batavia</p>
                <p class="curator-notes-paragraph" data-plate-notes>
                  Sebuah pembacaan kembali atas peristiwa penangkapan Pangeran Diponegoro pada 1830 di Magelang. Raden Saleh menghadirkan peristiwa sejarah melalui perspektif yang menempatkan martabat sang pangeran di pusat narasi, berbeda kontras dengan lukisan versi Nicolaas Pieneman.
                </p>

                <div class="plate-spec-list">
                  <div class="plate-spec-item">
                    <strong>Medium</strong>
                    <span data-spec-medium>Cat minyak pada kanvas</span>
                  </div>
                  <div class="plate-spec-item">
                    <strong>Dimensi</strong>
                    <span data-spec-dimension>112 × 179 cm</span>
                  </div>
                  <div class="plate-spec-item">
                    <strong>Provenans</strong>
                    <span data-spec-provenance>Koleksi Istana Kepresidenan RI</span>
                  </div>
                  <div class="plate-spec-item">
                    <strong>Klasifikasi</strong>
                    <span data-spec-category>Lukisan Klasik</span>
                  </div>
                </div>
              </div>

              <div class="page-right-footer">
                <span class="archival-seal">TERVERIFIKASI SISTEM GLAM</span>
                <a class="text-link btn-detail" data-plate-detail-link href="detail.php?id=diponegoro-lukisan">
                  Rekod Metadata Lengkap <span aria-hidden="true">→</span>
                </a>
              </div>
            </div>
          </article>

          <!-- Bilah Navigasi Bawah Buku Sketsa (Archival Controls Bar) -->
          <div class="sketchbook-controls-bar" aria-label="Kontrol navigasi pelat buku sketsa">
            <div class="controls-left">
              <button class="btn-plate-nav" data-plate-prev type="button" aria-label="Pelat sebelumnya (balik halaman ke belakang)">
                <span aria-hidden="true">←</span> Balik Sebelumnya
              </button>
              <button class="btn-plate-nav" data-plate-next type="button" aria-label="Pelat selanjutnya (balik halaman ke depan)">
                Balik Selanjutnya <span aria-hidden="true">→</span>
              </button>
            </div>

            <div class="controls-center">
              <div class="plate-tracker-label" data-plate-tracker>PELAT 01 / 04</div>
              <div class="plate-tracker-title" data-tracker-title>Penangkapan Pangeran Diponegoro</div>
            </div>

            <div class="controls-right">
              <button class="btn-index-toggle" data-index-toggle type="button" aria-expanded="false" aria-controls="plates-modal">
                Indeks Pelat
              </button>
              <a class="button button-primary" href="login.php" style="font-size:0.85rem; padding: 8px 14px;">
                Portal Kurator <span aria-hidden="true">↗</span>
              </a>
            </div>
          </div>
        </div>
        <figure class="viewer-frame" data-view-pane="virtual3d" hidden>
          <canvas id="artwork-viewer" aria-label="Tampilan interaktif 3D karya Penangkapan Pangeran Diponegoro"></canvas>
          <img class="viewer-fallback" src="assets/images/diponegoro.jpg" alt="Lukisan Penangkapan Pangeran Diponegoro karya Raden Saleh" />
          <figcaption><span>RUANG 01 — LUKISAN KLASIK SEJARAH</span><span>JAWA, 1857 · GESER MOUSE UNTUK EFEK 3D PERSPEKTIF</span></figcaption>
        </figure>

        <!-- Modal Indeks Pelat Koleksi -->
        <div class="plates-index-modal" id="plates-modal" data-plates-modal role="dialog" aria-modal="true" aria-labelledby="index-modal-title">
          <div class="plates-index-sheet">
            <header>
              <div>
                <p class="eyebrow">Katalog Terstruktur GLAM</p>
                <h3 id="index-modal-title">Indeks Pelat Warisan Nusantara</h3>
              </div>
              <button class="btn-close-sheet" data-close-index type="button" aria-label="Tutup indeks">×</button>
            </header>
            <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 16px;">
              Pilih salah satu pelat di bawah untuk langsung membuka lembaran buku sketsa:
            </p>
            <ul class="plates-table-list" data-plates-list></ul>
          </div>
        </div>
      </section>

      <section class="featured page-width" aria-labelledby="featured-title">
        <header class="section-heading section-heading-row">
          <section>
            <p class="eyebrow">Sorotan koleksi</p>
            <h2 id="featured-title">Cerita yang layak dijaga.</h2>
          </section>
          <a class="text-link" href="catalog.php">Semua koleksi <span aria-hidden="true">→</span></a>
        </header>
        <article class="featured-artwork" id="card-artwork">
          <figure class="featured-image">
            <img src="assets/images/diponegoro.jpg" alt="Raden Saleh menggambarkan penangkapan Pangeran Diponegoro dalam lukisan tahun 1857" />
            <figcaption>KOLEKSI PILIHAN <span>NG-1857-001</span></figcaption>
          </figure>
          <section class="artwork-info">
            <p class="badge">Lukisan Klasik</p>
            <h3>Penangkapan Pangeran Diponegoro</h3>
            <p class="meta-artist">Raden Saleh · 1857</p>
            <p class="description">Sebuah pembacaan kembali atas penangkapan Pangeran Diponegoro pada 1830. Raden Saleh menghadirkan peristiwa sejarah melalui sudut pandang yang menempatkan martabat sang pangeran di pusat narasi.</p>
            <a class="text-link btn-detail" href="detail.php?id=diponegoro-lukisan">Jelajahi metadata &amp; visual 3D <span aria-hidden="true">→</span></a>
          </section>
        </article>
      </section>

      <section class="manifesto page-width" aria-labelledby="manifesto-title">
        <p class="eyebrow">Akses. Konteks. Keberlanjutan.</p>
        <h2 id="manifesto-title">Warisan budaya bukan sekadar untuk dikenang. Ia perlu terus ditemukan.</h2>
        <a class="text-link" href="about.php">Tentang Nusantara Gallery Art <span aria-hidden="true">→</span></a>
      </section>
    </main>

<?php
include "layout/footer.php";
?>
