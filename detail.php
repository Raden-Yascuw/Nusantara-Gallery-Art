<?php
// Detail Koleksi
require_once __DIR__ . '/data/koleksi.php';

$id = trim($_GET['id'] ?? '');
$item = get_koleksi_by_id($id);

if (!$item) {
    header("Location: catalog.php");
    exit;
}

$page_title       = "{$item['judul']} — Nusantara Gallery Art";
$page_description = "Rekod metadata dan catatan kuratorial {$item['judul']} — {$item['kategori']}.";
$current_page     = "koleksi";

include "layout/header.php";
?>

    <script>
      /* ── Rekod koleksi aktif dari sisi server ────────────── */
      const data = <?= json_encode($item, JSON_UNESCAPED_UNICODE) ?>;
      let activeItemData = data;

      /* ── Render halaman setelah DOM siap ──────────────────────────── */
      document.addEventListener('DOMContentLoaded', () => {

        /* Update <title> dan <meta description> */
        document.title = `${data.judul} — Nusantara Gallery Art`;
        const metaDesc = document.querySelector('meta[name="description"]');
        if (metaDesc) {
          metaDesc.setAttribute(
            'content',
            `Metadata dan catatan kuratorial ${data.judul} — ${data.kategori}.`
          );
        }

        /* Render header halaman */
        document.querySelector('[data-detail-eyebrow]').textContent =
          `Rekod Koleksi — ${data.inventaris}`;
        document.querySelector('[data-detail-judul]').textContent = data.judul;

        /* Render gambar dengan fallback aman */
        const img = document.querySelector('[data-detail-img]');
        img.onerror = () => {
          img.onerror = null;
          let fb = 'assets/images/diponegoro.jpg';
          if (data.kategori_code === 'naskah') fb = 'assets/images/babad-diponegoro.jpg';
          else if (data.kategori_code === 'artefak') fb = 'assets/images/prajnaparamita.jpg';
          else if (data.kategori_code === 'wastra') fb = 'assets/images/perburuan-rusa.jpg';
          img.src = fb;
        };
        img.src = data.gambar;
        img.alt = data.altGambar || data.judul;
        document.querySelector('[data-detail-caption]').textContent = data.caption || `${data.pencipta}, ${data.tahun}`;

        /* Render badge kategori */
        document.querySelector('[data-detail-kategori]').textContent = data.kategori;

        /* Render tabel metadata */
        document.querySelector('[data-td-inventaris]').textContent = data.inventaris || '-';
        document.querySelector('[data-td-judul]').textContent      = data.judul || '-';
        document.querySelector('[data-td-kategori]').textContent   = data.kategori || '-';
        document.querySelector('[data-td-pencipta]').textContent   = data.pencipta || '-';
        document.querySelector('[data-td-tahun]').textContent      = data.tahun || '-';
        document.querySelector('[data-td-asal]').textContent       = data.asal || '-';
        document.querySelector('[data-td-medium]').textContent     = data.medium || '-';
        document.querySelector('[data-td-dimensi]').textContent    = data.dimensi || '-';
        document.querySelector('[data-td-provenans]').textContent  = data.provenans || '-';
        document.querySelector('[data-td-sumber]').textContent     = data.sumberGambar || data.provenans || '-';

        /* Render catatan kuratorial */
        document.querySelector('[data-detail-catatan]').textContent = data.catatan;

        /* Hubungkan tombol Virtual Exhibition 3D */
        const btnExhibit = document.querySelector('[data-btn-exhibit]');
        if (btnExhibit) {
          btnExhibit.addEventListener('click', () => {
            openVirtualModal(data);
          });
        }
      });

      /* ── Virtual 3D Modal Controller ──────────────────────────────── */
      let modalViewerActive = false;
      let modalAnimId = null;

      function openVirtualModal(item) {
        const overlay = document.querySelector('#modal-3d-overlay');
        document.querySelector('#modal-artwork-title').textContent = item.judul;
        document.querySelector('#modal-room-tag').textContent = `${item.ruang3D || 'Ruang Utama'} — ${item.tahun}`;
        document.querySelector('#modal-info-footer').textContent = item.infoVisual || 'Visualisasi kuratorial 3D ruang pameran museum.';

        overlay.classList.add('is-active');
        document.body.style.overflow = 'hidden';
        initModal3DCanvas(item);
      }

      function closeVirtualModal() {
        const overlay = document.querySelector('#modal-3d-overlay');
        overlay.classList.remove('is-active');
        document.body.style.overflow = '';
        modalViewerActive = false;
        if (modalAnimId) {
          cancelAnimationFrame(modalAnimId);
          modalAnimId = null;
        }
      }

      function initModal3DCanvas(item) {
        const canvas = document.querySelector('#modal-canvas-3d');
        const ctx = canvas.getContext('2d');
        modalViewerActive = true;

        const img = new Image();
        img.onload = () => {
          if (modalViewerActive) render();
        };
        img.onerror = () => {
          img.onerror = null;
          let fb = 'assets/images/diponegoro.jpg';
          if (item.kategori_code === 'naskah') fb = 'assets/images/babad-diponegoro.jpg';
          else if (item.kategori_code === 'artefak') fb = 'assets/images/prajnaparamita.jpg';
          else if (item.kategori_code === 'wastra') fb = 'assets/images/perburuan-rusa.jpg';
          img.src = fb;
        };
        img.src = item.gambar;

        let rotX = 0, rotY = 0;
        let targetRotX = 0, targetRotY = 0;
        let zoom = 1.0;
        let targetZoom = 1.0;
        let isDragging = false;
        let lastMouseX = 0, lastMouseY = 0;

        function resize() {
          const rect = canvas.getBoundingClientRect();
          const dpr = Math.min(window.devicePixelRatio || 1, 2);
          canvas.width = Math.round(rect.width * dpr);
          canvas.height = Math.round(rect.height * dpr);
        }
        resize();

        function render() {
          if (!modalViewerActive) return;

          rotX += (targetRotX - rotX) * 0.08;
          rotY += (targetRotY - rotY) * 0.08;
          zoom += (targetZoom - zoom) * 0.1;

          const w = canvas.width;
          const h = canvas.height;
          const dpr = Math.min(window.devicePixelRatio || 1, 2);

          ctx.clearRect(0, 0, w, h);

          // Background Gallery Studio
          const bgGrad = ctx.createRadialGradient(w / 2, h / 2, 40 * dpr, w / 2, h / 2, Math.max(w, h) * 0.7);
          bgGrad.addColorStop(0, '#242023');
          bgGrad.addColorStop(0.65, '#131114');
          bgGrad.addColorStop(1, '#09080a');
          ctx.fillStyle = bgGrad;
          ctx.fillRect(0, 0, w, h);

          // Floor grid perspective
          ctx.save();
          ctx.strokeStyle = 'rgba(255, 255, 255, 0.04)';
          ctx.lineWidth = 1 * dpr;
          const floorY = h * 0.78;
          for (let gx = -w; gx <= w * 2; gx += 80 * dpr) {
            ctx.beginPath();
            ctx.moveTo(w / 2, floorY * 0.5);
            ctx.lineTo(gx, h);
            ctx.stroke();
          }
          ctx.restore();

          // Render Objek 3D
          ctx.save();
          ctx.translate(w / 2, h / 2 - 15 * dpr);

          const cosY = Math.cos(rotY);
          const sinY = Math.sin(rotY);
          const cosX = Math.cos(rotX);
          const sinX = Math.sin(rotX);

          ctx.transform(cosY * zoom, sinX * 0.3 * zoom, -sinY * 0.2 * zoom, cosX * zoom, 0, 0);

          // Dimensi display objek
          const baseDim = Math.min(w * 0.62, h * 0.68);
          const aspect = (img.naturalWidth && img.naturalHeight) ? (img.naturalWidth / img.naturalHeight) : 1.5;
          let objW, objH;

          if (item.tipeVisual === 'artefak') {
            objH = baseDim * 0.95;
            objW = objH * Math.min(aspect, 1.1);
          } else if (item.tipeVisual === 'naskah') {
            objH = baseDim * 0.88;
            objW = objH * aspect;
          } else {
            objW = baseDim;
            objH = objW / aspect;
          }

          // Shadow objek 3D
          ctx.shadowColor = 'rgba(0, 0, 0, 0.75)';
          ctx.shadowBlur = 45 * dpr;
          ctx.shadowOffsetY = 24 * dpr;
          ctx.shadowOffsetX = -rotY * 40 * dpr;

          // Frame / Dudukan 3D
          const borderPad = (item.tipeVisual === 'artefak' ? 14 : 18) * dpr;
          ctx.fillStyle = item.frameColor || '#4d3a2b';
          ctx.fillRect(-objW / 2 - borderPad, -objH / 2 - borderPad, objW + borderPad * 2, objH + borderPad * 2);

          // Highlight tepi emas/perunggu
          ctx.strokeStyle = 'rgba(255, 230, 180, 0.35)';
          ctx.lineWidth = 2 * dpr;
          ctx.strokeRect(-objW / 2 - borderPad, -objH / 2 - borderPad, objW + borderPad * 2, objH + borderPad * 2);

          ctx.shadowColor = 'transparent';

          if (img.complete && img.naturalWidth > 0) {
            ctx.drawImage(img, -objW / 2, -objH / 2, objW, objH);
          } else {
            ctx.fillStyle = '#1c1a1e';
            ctx.fillRect(-objW / 2, -objH / 2, objW, objH);
            ctx.fillStyle = 'rgba(255, 255, 255, 0.7)';
            ctx.font = `${13 * dpr}px sans-serif`;
            ctx.textAlign = 'center';
            ctx.fillText('Memuat Visual Objek 3D...', 0, 0);
          }

          // Pencahayaan sorot (sheen gradient)
          const sheenGrad = ctx.createLinearGradient(-objW / 2, -objH / 2, objW / 2, objH / 2);
          sheenGrad.addColorStop(0, `rgba(255, 255, 255, ${Math.max(0, 0.18 + rotY * 0.15)})`);
          sheenGrad.addColorStop(0.5, 'rgba(255, 255, 255, 0)');
          sheenGrad.addColorStop(1, 'rgba(0, 0, 0, 0.35)');
          ctx.fillStyle = sheenGrad;
          ctx.fillRect(-objW / 2, -objH / 2, objW, objH);

          ctx.restore();

          modalAnimId = requestAnimationFrame(render);
        }

        modalAnimId = requestAnimationFrame(render);

        // Event interaksi drag / putar objek
        canvas.onpointerdown = (e) => {
          isDragging = true;
          lastMouseX = e.clientX;
          lastMouseY = e.clientY;
          canvas.setPointerCapture(e.pointerId);
        };

        canvas.onpointermove = (e) => {
          if (!isDragging) return;
          const deltaX = e.clientX - lastMouseX;
          const deltaY = e.clientY - lastMouseY;
          lastMouseX = e.clientX;
          lastMouseY = e.clientY;

          targetRotY = Math.max(-0.65, Math.min(0.65, targetRotY + deltaX * 0.005));
          targetRotX = Math.max(-0.4, Math.min(0.4, targetRotX + deltaY * 0.004));
        };

        canvas.onpointerup = canvas.onpointercancel = () => {
          isDragging = false;
        };

        canvas.onwheel = (e) => {
          e.preventDefault();
          targetZoom = Math.max(0.75, Math.min(1.5, targetZoom - e.deltaY * 0.0012));
        };
      }
    </script>

    <main id="main-content" class="page-width">
      <header class="page-title">
        <p data-detail-eyebrow>Rekod Koleksi</p>
        <h2 data-detail-judul>Memuat koleksi…</h2>
        <p>Rekod metadata dan konteks kuratorial untuk mendukung penelusuran, sitasi, dan riset koleksi.</p>
      </header>
      <section class="detail-layout" aria-label="Detail karya dan metadata">
        <figure class="detail-artwork metadata-artwork">
          <img data-detail-img src="" alt="" />
          <figcaption data-detail-caption></figcaption>
        </figure>
        <section class="metadata-panel" aria-labelledby="metadata-title">
          <p class="badge" data-detail-kategori></p>
          <h2 id="metadata-title">Metadata Objek</h2>
          <table class="metadata-table">
            <tbody>
              <tr><th scope="row">Nomor inventaris</th><td data-td-inventaris></td></tr>
              <tr><th scope="row">Judul</th><td data-td-judul></td></tr>
              <tr><th scope="row">Kategori</th><td data-td-kategori></td></tr>
              <tr><th scope="row">Pencipta</th><td data-td-pencipta></td></tr>
              <tr><th scope="row">Tahun era</th><td data-td-tahun></td></tr>
              <tr><th scope="row">Asal wilayah</th><td data-td-asal></td></tr>
              <tr><th scope="row">Medium / bahan</th><td data-td-medium></td></tr>
              <tr><th scope="row">Dimensi</th><td data-td-dimensi></td></tr>
              <tr><th scope="row">Provenans &amp; Koleksi</th><td data-td-provenans></td></tr>
              <tr><th scope="row">Sumber dokumentasi</th><td data-td-sumber></td></tr>
            </tbody>
          </table>
          <section class="curatorial-notes" aria-labelledby="notes-title">
            <h3 id="notes-title">Catatan Kuratorial</h3>
            <p data-detail-catatan></p>
          </section>
          <nav class="metadata-actions" aria-label="Tindakan metadata">
            <a class="button button-secondary" href="catalog.php">Kembali ke Katalog</a>
            <button class="button button-secondary" type="button" data-print-metadata>Unduh Metadata PDF</button>
            <button class="button" type="button" data-btn-exhibit>Buka Virtual Exhibition 3D</button>
          </nav>
        </section>
      </section>
    </main>

    <!-- ═══ MODAL VIRTUAL 3D EXHIBITION ═══ -->
    <div id="modal-3d-overlay" class="modal-3d-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-artwork-title">
      <div class="modal-3d-dialog">
        <header class="modal-3d-header">
          <div>
            <span class="modal-room-tag" id="modal-room-tag">Ruang 01 — Lukisan Sejarah</span>
            <h3 id="modal-artwork-title" class="modal-3d-title">Judul Koleksi</h3>
          </div>
          <button type="button" class="btn-modal-close" onclick="closeVirtualModal()" aria-label="Tutup pameran 3D">×</button>
        </header>
        <div class="modal-3d-canvas-wrap">
          <canvas id="modal-canvas-3d"></canvas>
          <div class="modal-3d-hint">
            <span>Seret kursor untuk memutar sudut pandang, gulir roda mouse untuk memperbesar</span>
          </div>
        </div>
        <footer class="modal-3d-footer">
          <p id="modal-info-footer">Tampilan format interaktif ruang pamer seni rupa.</p>
          <button type="button" class="button btn-sm" onclick="closeVirtualModal()">Tutup Tampilan 3D</button>
        </footer>
      </div>
    </div>

<?php
include "layout/footer.php";
?>
