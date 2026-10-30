// Theme Switcher (Dual-Mode Parity)
(() => {
  const applyTheme = (theme) => {
    const mode = theme === 'dark' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', mode);
    document.documentElement.setAttribute('data-mode', mode);
  };

  const savedTheme = localStorage.getItem('nga_theme') || localStorage.getItem('nga_theme_mode');
  if (savedTheme === 'dark') {
    applyTheme('dark');
  } else {
    applyTheme('light');
  }

  const updateToggleButtons = () => {
    const isDark = document.documentElement.getAttribute('data-mode') === 'dark' || document.documentElement.getAttribute('data-theme') === 'dark';
    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
      btn.innerHTML = isDark
        ? '<span class="toggle-icon">☀</span> Mode Terang'
        : '<span class="toggle-icon">☾</span> Mode Gelap';
      btn.setAttribute('aria-label', isDark ? 'Beralih ke Tema Siang (Guguran Daun)' : 'Beralih ke Tema Malam (Hujan Galeri)');
    });
  };

  document.addEventListener('DOMContentLoaded', () => {
    updateToggleButtons();

    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
      btn.addEventListener('click', (event) => {
        const currentTheme = document.documentElement.getAttribute('data-mode') || document.documentElement.getAttribute('data-theme');
        const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
        const rect = btn.getBoundingClientRect();
        const wx = event.clientX || (rect.left + rect.width / 2);
        const wy = event.clientY || (rect.top + rect.height / 2);
        document.documentElement.style.setProperty('--wx', `${wx}px`);
        document.documentElement.style.setProperty('--wy', `${wy}px`);

        const commitTheme = () => {
          applyTheme(nextTheme);
          localStorage.setItem('nga_theme', nextTheme);
          localStorage.setItem('nga_theme_mode', nextTheme);
          updateToggleButtons();
          if (typeof window.requestViewerPaint === 'function') {
            window.requestViewerPaint();
          }
        };

        if (document.startViewTransition && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
          document.startViewTransition(commitTheme);
        } else {
          commitTheme();
        }
      });
    });
  });
})();

// ── Mobile Menu Toggle ──
const menuToggle = document.querySelector('.menu-toggle');
const globalNav = document.querySelector('.global-nav');

if (menuToggle && globalNav) {
  menuToggle.addEventListener('click', () => {
    const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
    menuToggle.setAttribute('aria-expanded', String(!isExpanded));
    menuToggle.setAttribute('aria-label', isExpanded ? 'Buka navigasi' : 'Tutup navigasi');
    globalNav.classList.toggle('is-open', !isExpanded);
  });

  globalNav.addEventListener('click', (event) => {
    if (event.target.closest('a') && window.matchMedia('(max-width: 768px)').matches) {
      menuToggle.setAttribute('aria-expanded', 'false');
      menuToggle.setAttribute('aria-label', 'Buka navigasi');
      globalNav.classList.remove('is-open');
    }
  });
}

// ── Catalog Search & Filter Controller ──
const catalogGrid = document.querySelector('[data-catalog-grid]');

if (catalogGrid) {
  const searchInput     = document.querySelector('#catalog-search');
  const filterButtons   = [...document.querySelectorAll('[data-filter]')];
  const cards           = [...catalogGrid.querySelectorAll('.catalog-card')];
  const resultCount     = document.querySelector('[data-result-count]');
  const emptyState      = document.querySelector('[data-empty-state]');
  
  // Elemen Filter Range Tahun
  const yearSlider      = document.querySelector('#filter-year-range');
  const yearMinInput    = document.querySelector('#year-min-input');
  const yearMaxInput    = document.querySelector('#year-max-input');
  const yearDisplay     = document.querySelector('#year-range-display');
  const eraTags         = [...document.querySelectorAll('.era-tag')];
  const resetBtn        = document.querySelector('#btn-reset-filters');

  // Sidebar Mobile Toggle
  const sidebar         = document.querySelector('#catalog-sidebar');
  const btnOpenSidebar  = document.querySelector('#btn-open-sidebar');
  const btnCloseSidebar = document.querySelector('#btn-sidebar-close');

  let activeFilter = 'all';
  let minYear = 300;
  let maxYear = 1950;

  // Update Tampilan Display Range Tahun
  const updateYearDisplay = () => {
    if (yearDisplay) {
      yearDisplay.textContent = `${minYear} – ${maxYear} M`;
    }
  };

  // Fungsi Utama Pemfilteran Koleksi
  const updateResults = () => {
    const query = (searchInput ? searchInput.value.trim().toLocaleLowerCase('id') : '');
    let visibleCount = 0;

    cards.forEach((card) => {
      const category = card.dataset.category || '';
      const year = parseInt(card.dataset.year || '0', 10);
      const searchData = (card.dataset.search || '').toLocaleLowerCase('id');

      const matchesCategory = activeFilter === 'all' || category === activeFilter;
      const matchesQuery    = query === '' || searchData.includes(query);
      const matchesYear     = year >= minYear && year <= maxYear;

      const isVisible = matchesCategory && matchesQuery && matchesYear;
      card.hidden = !isVisible;
      visibleCount += Number(isVisible);
    });

    if (resultCount) {
      resultCount.textContent = `${visibleCount} koleksi ditemukan`;
    }
    if (emptyState) {
      emptyState.hidden = visibleCount !== 0;
    }
  };

  // Event Listener: Filter Kategori
  filterButtons.forEach((button) => {
    button.addEventListener('click', () => {
      activeFilter = button.dataset.filter;
      filterButtons.forEach((item) => {
        const isActive = item === button;
        item.setAttribute('aria-pressed', String(isActive));
        item.classList.toggle('is-active', isActive);
      });
      updateResults();
    });
  });

  // Event Listener: Input Pencarian
  if (searchInput) {
    searchInput.addEventListener('input', updateResults);
  }

  // Event Listener: Slider Rentang Tahun
  if (yearSlider) {
    yearSlider.addEventListener('input', (e) => {
      maxYear = parseInt(e.target.value, 10);
      if (yearMaxInput) yearMaxInput.value = maxYear;
      updateYearDisplay();
      updateResults();
    });
  }

  // Event Listener: Input Angka Tahun
  if (yearMinInput) {
    yearMinInput.addEventListener('change', (e) => {
      minYear = Math.max(300, parseInt(e.target.value || '300', 10));
      if (minYear > maxYear) minYear = maxYear;
      yearMinInput.value = minYear;
      updateYearDisplay();
      updateResults();
    });
  }

  if (yearMaxInput) {
    yearMaxInput.addEventListener('change', (e) => {
      maxYear = Math.min(1950, parseInt(e.target.value || '1950', 10));
      if (maxYear < minYear) maxYear = minYear;
      yearMaxInput.value = maxYear;
      if (yearSlider) yearSlider.value = maxYear;
      updateYearDisplay();
      updateResults();
    });
  }

  // Event Listener: Tag Era Cepat
  eraTags.forEach((tag) => {
    tag.addEventListener('click', () => {
      eraTags.forEach((t) => t.classList.remove('is-active'));
      tag.classList.add('is-active');

      minYear = parseInt(tag.dataset.eraMin, 10);
      maxYear = parseInt(tag.dataset.eraMax, 10);

      if (yearMinInput) yearMinInput.value = minYear;
      if (yearMaxInput) yearMaxInput.value = maxYear;
      if (yearSlider) yearSlider.value = maxYear;

      updateYearDisplay();
      updateResults();
    });
  });

  // Event Listener: Reset Semua Filter
  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      activeFilter = 'all';
      minYear = 300;
      maxYear = 1950;

      if (searchInput) searchInput.value = '';
      if (yearMinInput) yearMinInput.value = 300;
      if (yearMaxInput) yearMaxInput.value = 1950;
      if (yearSlider) yearSlider.value = 1950;

      filterButtons.forEach((b) => {
        const isAll = b.dataset.filter === 'all';
        b.setAttribute('aria-pressed', String(isAll));
        b.classList.toggle('is-active', isAll);
      });

      eraTags.forEach((t) => {
        t.classList.toggle('is-active', t.dataset.eraMin === '300' && t.dataset.eraMax === '1950');
      });

      updateYearDisplay();
      updateResults();
    });
  }

  // Event Listener: Sidebar Mobile Drawer
  if (btnOpenSidebar && sidebar) {
    btnOpenSidebar.addEventListener('click', () => {
      sidebar.classList.add('is-open');
    });
  }

  if (btnCloseSidebar && sidebar) {
    btnCloseSidebar.addEventListener('click', () => {
      sidebar.classList.remove('is-open');
    });
  }
}

// ── Print Metadata Button ──
const printButton = document.querySelector('[data-print-metadata]');
if (printButton) {
  printButton.addEventListener('click', () => window.print());
}

// 3D Canvas Viewer Controller
const viewer = document.querySelector('#artwork-viewer');

if (viewer) {
  const image = document.querySelector('.viewer-fallback');
  const context = viewer.getContext('2d');
  const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
  let pointerX = 0;
  let pointerY = 0;
  let currentX = 0;
  let currentY = 0;
  let frameRequested = false;

  const paint = () => {
    frameRequested = false;
    const bounds = viewer.getBoundingClientRect();
    const pixelRatio = Math.min(window.devicePixelRatio || 1, 2);
    const width = Math.max(1, Math.round(bounds.width * pixelRatio));
    const height = Math.max(1, Math.round(bounds.height * pixelRatio));

    if (viewer.width !== width || viewer.height !== height) {
      viewer.width = width;
      viewer.height = height;
    }

    context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
    context.clearRect(0, 0, bounds.width, bounds.height);

    // Studio Gallery Wall Background
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    context.fillStyle = isDark ? '#141316' : '#231e1c';
    context.fillRect(0, 0, bounds.width, bounds.height);

    // Floor perspective line
    context.strokeStyle = 'rgba(255, 255, 255, 0.05)';
    context.lineWidth = 1;
    context.beginPath();
    context.moveTo(0, bounds.height * 0.85);
    context.lineTo(bounds.width, bounds.height * 0.85);
    context.stroke();

    const naturalW = image.naturalWidth || 900;
    const naturalH = image.naturalHeight || 560;

    // Ambient background glow
    if (image.complete && image.naturalWidth > 0) {
      const scale = Math.max(bounds.width / naturalW, bounds.height / naturalH);
      const bgW = naturalW * scale;
      const bgH = naturalH * scale;
      context.save();
      context.filter = 'blur(18px) brightness(0.35)';
      context.drawImage(image, (bounds.width - bgW) / 2, (bounds.height - bgH) / 2, bgW, bgH);
      context.restore();
    }

    context.fillStyle = 'rgba(10, 10, 12, 0.4)';
    context.fillRect(0, 0, bounds.width, bounds.height);

    // 3D Perspective Artwork Rendering
    const artWidth = Math.min(bounds.width * 0.82, bounds.height * 1.35);
    const artHeight = artWidth * naturalH / naturalW;
    const tiltX = motionPreference.matches ? 0 : currentX * 0.04;
    const tiltY = motionPreference.matches ? 0 : currentY * 0.03;

    context.save();
    context.translate(bounds.width / 2, bounds.height / 2);
    context.transform(1, tiltY, -tiltX, 1, 0, 0);

    // Drop shadow
    context.shadowColor = 'rgba(0, 0, 0, 0.75)';
    context.shadowBlur = 36;
    context.shadowOffsetY = 18;
    context.shadowOffsetX = -currentX * 15;

    // Bingkai Emas / Kayu Jati 3D
    const borderPad = 14;
    context.fillStyle = '#4d3a2b';
    context.fillRect(-artWidth / 2 - borderPad, -artHeight / 2 - borderPad, artWidth + borderPad * 2, artHeight + borderPad * 2);

    // Garis list kuningan
    context.strokeStyle = 'rgba(238, 216, 161, 0.45)';
    context.lineWidth = 2;
    context.strokeRect(-artWidth / 2 - borderPad, -artHeight / 2 - borderPad, artWidth + borderPad * 2, artHeight + borderPad * 2);

    context.shadowColor = 'transparent';

    if (image.complete && image.naturalWidth > 0) {
      context.drawImage(image, -artWidth / 2, -artHeight / 2, artWidth, artHeight);
    } else {
      context.fillStyle = '#222';
      context.fillRect(-artWidth / 2, -artHeight / 2, artWidth, artHeight);
      context.fillStyle = '#fff';
      context.font = '14px sans-serif';
      context.textAlign = 'center';
      context.fillText('Memuat Karya Seni...', 0, 0);
    }

    // Sorotan Lampu Galeri (Gallery Spotlight)
    const sheen = context.createLinearGradient(-artWidth / 2, -artHeight / 2, artWidth / 2, artHeight / 2);
    sheen.addColorStop(0, `rgba(255, 255, 255, ${Math.max(0, 0.15 + currentX * 0.1)})`);
    sheen.addColorStop(0.5, 'rgba(255, 255, 255, 0)');
    sheen.addColorStop(1, 'rgba(0, 0, 0, 0.35)');
    context.fillStyle = sheen;
    context.fillRect(-artWidth / 2, -artHeight / 2, artWidth, artHeight);

    context.restore();

    if (!motionPreference.matches && (Math.abs(pointerX - currentX) > 0.001 || Math.abs(pointerY - currentY) > 0.001)) {
      currentX += (pointerX - currentX) * 0.1;
      currentY += (pointerY - currentY) * 0.1;
      frameRequested = true;
      window.requestAnimationFrame(paint);
    }

    viewer.dataset.ready = 'true';
  };

  const requestPaint = () => {
    if (!frameRequested) {
      frameRequested = true;
      window.requestAnimationFrame(paint);
    }
  };

  window.requestViewerPaint = requestPaint;

  viewer.addEventListener('pointermove', (event) => {
    const bounds = viewer.getBoundingClientRect();
    pointerX = ((event.clientX - bounds.left) / bounds.width - 0.5) * 2;
    pointerY = ((event.clientY - bounds.top) / bounds.height - 0.5) * 2;
    requestPaint();
  });

  viewer.addEventListener('pointerleave', () => {
    pointerX = 0;
    pointerY = 0;
    requestPaint();
  });

  image.addEventListener('load', requestPaint);
  image.addEventListener('error', () => {
    image.src = 'assets/images/diponegoro.jpg';
  });
  window.addEventListener('resize', requestPaint);
  requestPaint();
}

// ─── Mode Switcher Pameran (Buku Sketsa vs Ruang 3D) ───
(() => {
  const modeButtons = document.querySelectorAll('[data-view-mode]');
  const viewPanes = document.querySelectorAll('[data-view-pane]');

  modeButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      const targetMode = btn.dataset.viewMode;
      modeButtons.forEach((b) => {
        const isSelected = b === btn;
        b.classList.toggle('is-active', isSelected);
        b.setAttribute('aria-selected', String(isSelected));
      });

      viewPanes.forEach((pane) => {
        const isMatch = pane.dataset.viewPane === targetMode;
        pane.hidden = !isMatch;
        if (isMatch && targetMode === 'virtual3d' && typeof window.requestViewerPaint === 'function') {
          setTimeout(window.requestViewerPaint, 50);
        }
      });
    });
  });
})();

// Buku Sketsa & Kaca Pembesar Interaktif
(() => {
  const sketchbookDesk = document.querySelector('[data-sketchbook-desk]');
  if (!sketchbookDesk) return;

  const PLATES_DATA = [
    {
      id: 'diponegoro-lukisan',
      number: 'Pelat 01',
      title: 'Penangkapan Pangeran Diponegoro',
      creator: 'Raden Saleh (c. 1811–1880)',
      year: '1857',
      origin: 'Jawa Tengah / Batavia',
      medium: 'Cat minyak pada kanvas',
      dimension: '112 × 179 cm',
      provenance: 'Koleksi Istana Kepresidenan Republik Indonesia',
      category: 'Lukisan Klasik',
      image: 'assets/images/diponegoro.jpg',
      alt: 'Lukisan Penangkapan Pangeran Diponegoro karya Raden Saleh',
      notes: 'Sebuah pembacaan kembali atas peristiwa penangkapan Pangeran Diponegoro pada 1830 di Magelang. Raden Saleh menghadirkan peristiwa sejarah melalui sudut pandang yang menempatkan martabat sang pangeran di pusat narasi, berbeda kontras dengan lukisan versi Nicolaas Pieneman.'
    },
    {
      id: 'babad-diponegoro',
      number: 'Pelat 02',
      title: 'Babad Diponegoro',
      creator: 'Pangeran Diponegoro',
      year: 'Abad ke-19 (1831–1832)',
      origin: 'Manado & Makassar',
      medium: 'Tinta pada daluang (kertas keprabon)',
      dimension: 'Manuskrip Multi-Jilid',
      provenance: 'Perpustakaan Nasional RI (UNESCO Memory of the World)',
      category: 'Naskah Kuno',
      image: 'assets/images/babad-diponegoro.jpg',
      alt: 'Lembaran manuskrip kuno Babad Diponegoro',
      notes: 'Autobiografi bertembang Macapat yang ditulis selama masa pengasingan. Merupakan saksi primer terpenting Perang Jawa dari sudut pandang pribumi yang telah ditetapkan sebagai warisan dokumenter dunia UNESCO pada tahun 2013.'
    },
    {
      id: 'arca-prajnaparamita',
      number: 'Pelat 03',
      title: 'Arca Prajnaparamita',
      creator: 'Pemahat Tak Dikenal (Era Singhasari)',
      year: 'Abad ke-13 Masehi',
      origin: 'Candi Singhasari, Malang, Jawa Timur',
      medium: 'Pahatan Batu Andesit Gunung',
      dimension: 'Tinggi ± 126 cm',
      provenance: 'Museum Nasional Indonesia, Jakarta',
      category: 'Artefak 3D',
      image: 'assets/images/prajnaparamita.jpg',
      alt: 'Arca Prajnaparamita Singhasari',
      notes: 'Puncak estetika seni pahat klasik Jawa Timur yang memvisualisasikan dewi kebijaksanaan transendental. Diyakini sebagai perwujudan deifikasi Ken Dedes, ibu dari raja-raja tanah Singhasari dan Majapahit.'
    },
    {
      id: 'perburuan-rusa',
      number: 'Pelat 04',
      title: 'Perburuan Rusa (Deer Hunt)',
      creator: 'Raden Saleh (c. 1811–1880)',
      year: '1846',
      origin: 'Dresden, Jerman / Jawa',
      medium: 'Cat minyak pada kanvas besar',
      dimension: '178 × 262 cm',
      provenance: 'Museum Negeri Sonobudoyo, Yogyakarta',
      category: 'Lukisan Klasik',
      image: 'assets/images/perburuan-rusa.jpg',
      alt: 'Lukisan Perburuan Rusa karya Raden Saleh',
      notes: 'Mahakarya beraliran romantisisme dramatis yang menggambarkan ketegangan pergulatan manusia, anjing pemburu, dan satwa liar di lanskap eksotis Nusantara.'
    }
  ];

  let currentPlateIndex = 0;
  let isPageFlipping = false;

  // DOM Elements
  const bookEl = document.querySelector('[data-sketchbook-book]');
  const pageLeft = document.querySelector('[data-page-left]');
  const pageRight = document.querySelector('[data-page-right]');
  const plateImg = document.querySelector('[data-plate-image]');
  const plateCanvas = document.querySelector('[data-plate-canvas]');
  const magnifier = document.querySelector('[data-magnifier]');
  const plateBadge = document.querySelector('[data-plate-badge]');
  const plateTitle = document.querySelector('[data-plate-title]');
  const plateMeta = document.querySelector('[data-plate-meta]');
  const plateNotes = document.querySelector('[data-plate-notes]');
  const specMedium = document.querySelector('[data-spec-medium]');
  const specDimension = document.querySelector('[data-spec-dimension]');
  const specProvenance = document.querySelector('[data-spec-provenance]');
  const specCategory = document.querySelector('[data-spec-category]');
  const plateDetailLink = document.querySelector('[data-plate-detail-link]');
  const plateTracker = document.querySelector('[data-plate-tracker]');
  const trackerTitle = document.querySelector('[data-tracker-title]');
  const plateTag = document.querySelector('[data-plate-tag]');

  const btnPrev = document.querySelector('[data-plate-prev]');
  const btnNext = document.querySelector('[data-plate-next]');
  const btnIndexToggle = document.querySelector('[data-index-toggle]');
  const platesModal = document.querySelector('[data-plates-modal]');
  const btnCloseIndex = document.querySelector('[data-close-index]');
  const platesListContainer = document.querySelector('[data-plates-list]');

  const applyPlateData = (targetIdx) => {
    currentPlateIndex = targetIdx;
    const plate = PLATES_DATA[currentPlateIndex];

    if (plateImg) {
      plateImg.src = plate.image;
      plateImg.alt = plate.alt;
    }

    if (plateBadge) plateBadge.textContent = plate.number;
    if (plateTitle) plateTitle.textContent = plate.title;
    if (plateMeta) plateMeta.textContent = `${plate.creator}, ${plate.year} — ${plate.origin}`;
    if (plateNotes) plateNotes.textContent = plate.notes;
    if (specMedium) specMedium.textContent = plate.medium;
    if (specDimension) specDimension.textContent = plate.dimension;
    if (specProvenance) specProvenance.textContent = plate.provenance;
    if (specCategory) specCategory.textContent = plate.category;
    if (plateTag) plateTag.textContent = `${plate.number} — ${plate.category}, ${plate.year}`;

    if (plateDetailLink) {
      plateDetailLink.href = `detail.php?id=${plate.id}`;
    }

    if (plateTracker) {
      plateTracker.textContent = `Pelat ${String(currentPlateIndex + 1).padStart(2, '0')} / ${String(PLATES_DATA.length).padStart(2, '0')}`;
    }
    if (trackerTitle) {
      trackerTitle.textContent = plate.title;
    }

    if (platesListContainer) {
      const rows = platesListContainer.querySelectorAll('.plates-table-item');
      rows.forEach((row, rIdx) => {
        row.classList.toggle('is-current', rIdx === currentPlateIndex);
      });
    }

    if (magnifier) {
      magnifier.classList.remove('is-visible');
    }
  };

  // Synthesizer Suara Fisik Membalik Kertas Buku Sketsa (Web Audio API)
  let audioCtx = null;
  const playPageTurnSound = (direction = 'forward') => {
    try {
      const AudioContextClass = window.AudioContext || window.webkitAudioContext;
      if (!AudioContextClass) return;
      if (!audioCtx) audioCtx = new AudioContextClass();
      if (audioCtx.state === 'suspended') audioCtx.resume();

      const now = audioCtx.currentTime;
      const duration = 0.62;
      const sampleRate = audioCtx.sampleRate;
      const frameCount = Math.floor(sampleRate * duration);
      const noiseBuffer = audioCtx.createBuffer(1, frameCount, sampleRate);
      const output = noiseBuffer.getChannelData(0);

      // Pink-brownish noise supaya tekstur serat kertas terasa hangat (bukan desis tajam)
      let b0 = 0, b1 = 0, b2 = 0;
      for (let i = 0; i < frameCount; i++) {
        const white = Math.random() * 2 - 1;
        b0 = 0.997 * b0 + white * 0.029;
        b1 = 0.985 * b1 + white * 0.032;
        b2 = 0.950 * b2 + white * 0.048;
        output[i] = (b0 + b1 + b2 + white * 0.08) * 1.6;
      }

      const noise = audioCtx.createBufferSource();
      noise.buffer = noiseBuffer;

      // Filter gesekan lembaran kertas (bandpass menyapu sesuai lengkungan halaman)
      const bandpass = audioCtx.createBiquadFilter();
      bandpass.type = 'bandpass';
      bandpass.Q.setValueAtTime(1.35, now);
      const startFreq = direction === 'forward' ? 680 : 580;
      const peakFreq = direction === 'forward' ? 1650 : 1420;
      const endFreq = direction === 'forward' ? 520 : 620;
      bandpass.frequency.setValueAtTime(startFreq, now);
      bandpass.frequency.exponentialRampToValueAtTime(peakFreq, now + 0.24);
      bandpass.frequency.exponentialRampToValueAtTime(endFreq, now + 0.56);

      // Pelembut frekuensi tinggi agar natural seperti kertas katun tebal
      const lowpass = audioCtx.createBiquadFilter();
      lowpass.type = 'lowpass';
      lowpass.frequency.setValueAtTime(2800, now);

      // Amplop volume gesekan kertas (awal angkat -> puncak ayunan -> mendarat)
      const rustleGain = audioCtx.createGain();
      rustleGain.gain.setValueAtTime(0.0001, now);
      rustleGain.gain.linearRampToValueAtTime(0.18, now + 0.045);
      rustleGain.gain.exponentialRampToValueAtTime(0.32, now + 0.22);
      rustleGain.gain.exponentialRampToValueAtTime(0.0001, now + 0.58);

      // Efek spasial kiri-kanan mengikuti arah balik lembaran buku
      const panner = audioCtx.createStereoPanner ? audioCtx.createStereoPanner() : null;
      if (panner) {
        const panStart = direction === 'forward' ? 0.35 : -0.35;
        const panEnd = direction === 'forward' ? -0.35 : 0.35;
        panner.pan.setValueAtTime(panStart, now);
        panner.pan.linearRampToValueAtTime(panEnd, now + 0.52);
      }

      noise.connect(bandpass);
      bandpass.connect(lowpass);
      if (panner) {
        lowpass.connect(panner);
        panner.connect(rustleGain);
      } else {
        lowpass.connect(rustleGain);
      }
      rustleGain.connect(audioCtx.destination);

      noise.start(now);
      noise.stop(now + duration);

      // Bunyi lembut halaman mendarat di atas tumpukan kertas (soft paper settle thump)
      const settleTime = now + 0.46;
      const osc = audioCtx.createOscillator();
      const settleGain = audioCtx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(115, settleTime);
      osc.frequency.exponentialRampToValueAtTime(46, settleTime + 0.11);

      settleGain.gain.setValueAtTime(0.0001, settleTime);
      settleGain.gain.linearRampToValueAtTime(0.09, settleTime + 0.012);
      settleGain.gain.exponentialRampToValueAtTime(0.0001, settleTime + 0.12);

      osc.connect(settleGain);
      settleGain.connect(audioCtx.destination);
      osc.start(settleTime);
      osc.stop(settleTime + 0.13);
    } catch (e) {
      // Abaikan jika browser memblokir audio otomatis
    }
  };

  // Render Pelat Buku Sketsa
  const renderPlate = (index, direction = 'instant') => {
    if (isPageFlipping) return;

    if (index < 0) index = PLATES_DATA.length - 1;
    if (index >= PLATES_DATA.length) index = 0;

    if (direction === 'instant' || !bookEl) {
      applyPlateData(index);
      return;
    }

    isPageFlipping = true;
    playPageTurnSound(direction);

    // Tambahkan lembaran kertas membalik 3D (Leaf flip)
    const leaf = document.createElement('div');
    leaf.className = `page-turning-leaf ${direction === 'forward' ? 'turning-forward' : 'turning-backward'}`;
    bookEl.appendChild(leaf);

    if (pageLeft) pageLeft.classList.add('page-flip-fade');
    if (pageRight) pageRight.classList.add('page-flip-fade');

    // Pada titik tengah putaran 90 derajat (320ms), ubah konten pelat
    setTimeout(() => {
      applyPlateData(index);
    }, 320);

    // Bersihkan efek setelah putaran 180 derajat selesai
    setTimeout(() => {
      if (leaf.parentNode) leaf.parentNode.removeChild(leaf);
      if (pageLeft) pageLeft.classList.remove('page-flip-fade');
      if (pageRight) pageRight.classList.remove('page-flip-fade');
      isPageFlipping = false;
    }, 660);
  };

  if (btnPrev) btnPrev.addEventListener('click', () => renderPlate(currentPlateIndex - 1, 'backward'));
  if (btnNext) btnNext.addEventListener('click', () => renderPlate(currentPlateIndex + 1, 'forward'));

  // Kaca Pembesar Interaktif (Magnifying Glass Lens)
  if (plateCanvas && magnifier && plateImg) {
    const ZOOM_FACTOR = 2.4;

    const moveMagnifier = (clientX, clientY) => {
      if (isPageFlipping) {
        magnifier.classList.remove('is-visible');
        return;
      }

      const rect = plateImg.getBoundingClientRect();
      const canvasRect = plateCanvas.getBoundingClientRect();

      if (
        clientX < rect.left ||
        clientX > rect.right ||
        clientY < rect.top ||
        clientY > rect.bottom
      ) {
        magnifier.classList.remove('is-visible');
        return;
      }

      magnifier.classList.add('is-visible');

      const lensX = clientX - canvasRect.left;
      const lensY = clientY - canvasRect.top;

      magnifier.style.setProperty('--lx', `${lensX}px`);
      magnifier.style.setProperty('--ly', `${lensY}px`);

      const x = clientX - rect.left;
      const y = clientY - rect.top;

      const bgWidth = rect.width * ZOOM_FACTOR;
      const bgHeight = rect.height * ZOOM_FACTOR;

      const bgX = -(x * ZOOM_FACTOR - magnifier.offsetWidth / 2);
      const bgY = -(y * ZOOM_FACTOR - magnifier.offsetHeight / 2);

      magnifier.style.backgroundImage = `url("${plateImg.src}")`;
      magnifier.style.backgroundSize = `${bgWidth}px ${bgHeight}px`;
      magnifier.style.backgroundPosition = `${bgX}px ${bgY}px`;
    };

    plateCanvas.addEventListener('pointermove', (e) => {
      moveMagnifier(e.clientX, e.clientY);
    });

    plateCanvas.addEventListener('pointerleave', () => {
      magnifier.classList.remove('is-visible');
    });

    plateCanvas.addEventListener('touchmove', (e) => {
      if (e.touches && e.touches[0]) {
        moveMagnifier(e.touches[0].clientX, e.touches[0].clientY);
      }
    }, { passive: true });

    plateCanvas.addEventListener('touchend', () => {
      magnifier.classList.remove('is-visible');
    });
  }

  // Populate Index Modal & Handle Toggles
  if (platesListContainer) {
    platesListContainer.innerHTML = PLATES_DATA.map((p, idx) => `
      <li class="plates-table-item ${idx === 0 ? 'is-current' : ''}" data-plate-idx="${idx}">
        <div class="item-left-block">
          <span class="item-plate-num">${String(idx + 1).padStart(2, '0')}</span>
          <div>
            <div class="item-plate-title">${p.title}</div>
            <div class="item-plate-creator">${p.creator} (${p.year})</div>
          </div>
        </div>
        <span class="item-plate-category">${p.category}</span>
      </li>
    `).join('');

    platesListContainer.addEventListener('click', (e) => {
      const item = e.target.closest('[data-plate-idx]');
      if (item) {
        const idx = parseInt(item.dataset.plateIdx, 10);
        const direction = idx > currentPlateIndex ? 'forward' : 'backward';
        renderPlate(idx, direction);
        if (platesModal) platesModal.classList.remove('is-active');
        if (btnIndexToggle) btnIndexToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  if (btnIndexToggle && platesModal) {
    btnIndexToggle.addEventListener('click', () => {
      const isActive = platesModal.classList.toggle('is-active');
      btnIndexToggle.setAttribute('aria-expanded', String(isActive));
    });
  }

  if (btnCloseIndex && platesModal) {
    btnCloseIndex.addEventListener('click', () => {
      platesModal.classList.remove('is-active');
      if (btnIndexToggle) btnIndexToggle.setAttribute('aria-expanded', 'false');
    });
  }

  if (platesModal) {
    platesModal.addEventListener('click', (e) => {
      if (e.target === platesModal) {
        platesModal.classList.remove('is-active');
        if (btnIndexToggle) btnIndexToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && platesModal && platesModal.classList.contains('is-active')) {
      platesModal.classList.remove('is-active');
      if (btnIndexToggle) btnIndexToggle.setAttribute('aria-expanded', 'false');
    }
  });

  renderPlate(0, 'instant');
})();

// Autentikasi Kurator
(() => {
  // ── Handle Form Login (login.html) ──
  const loginForm = document.querySelector('[data-login-form]');
  if (loginForm) {
    loginForm.addEventListener('submit', (e) => {
      e.preventDefault();

      if (!loginForm.checkValidity()) {
        loginForm.reportValidity();
        return;
      }

      const usernameInput = document.querySelector('#username');
      const username = usernameInput ? usernameInput.value.trim() : 'Kurator';
      const roleSelect = document.querySelector('#role');
      const roleText = roleSelect ? roleSelect.options[roleSelect.selectedIndex].text : 'Kurator Koleksi & Preservasi';

      const curatorSession = {
        name: username,
        role: roleText,
        loggedInAt: new Date().toISOString()
      };
      sessionStorage.setItem('nga_curator_session', JSON.stringify(curatorSession));

      const msgEl = loginForm.querySelector('[data-form-message]');
      if (msgEl) {
        msgEl.style.color = 'var(--valid-border)';
        msgEl.textContent = 'Autentikasi Berhasil! Mengalihkan ke Formulir Preservasi...';
      }

      setTimeout(() => {
        window.location.href = 'formulir.php';
      }, 700);
    });
  }

  // ── Handle Form Pendaftaran (register.php) ──
  const registerForm = document.querySelector('[data-register-form]');
  if (registerForm) {
    registerForm.addEventListener('submit', (e) => {
      e.preventDefault();

      if (!registerForm.checkValidity()) {
        registerForm.reportValidity();
        return;
      }

      const pass = document.querySelector('#reg-password').value;
      const confirm = document.querySelector('#reg-confirm').value;
      const msgEl = registerForm.querySelector('[data-reg-message]');

      if (pass !== confirm) {
        if (msgEl) {
          msgEl.style.color = 'var(--invalid-border)';
          msgEl.textContent = 'Perhatian: Konfirmasi kata sandi tidak cocok dengan kata sandi yang dibuat!';
        }
        document.querySelector('#reg-confirm').focus();
        return;
      }

      const regNama = document.querySelector('#reg-nama').value.trim() || 'Kurator Baru';
      const regNip = document.querySelector('#reg-nip').value.trim() || '250911718423';
      const regDivisi = document.querySelector('#reg-divisi').value || 'Divisi Preservasi';

      const curatorSession = {
        name: `${regNama} (NIP: ${regNip})`,
        role: `Kurator ${regDivisi}`,
        loggedInAt: new Date().toISOString()
      };
      sessionStorage.setItem('nga_curator_session', JSON.stringify(curatorSession));

      if (msgEl) {
        msgEl.style.color = 'var(--valid-border)';
        msgEl.textContent = 'Pendaftaran Akun Berhasil! Mengalihkan ke Halaman Masuk...';
      }

      setTimeout(() => {
        window.location.href = 'login.php';
      }, 700);
    });
  }

  // ── Bilah Sesi Kurator pada formulir.php ──
  const sessionBar = document.querySelector('[data-curator-session-bar]');
  if (sessionBar) {
    const rawSession = sessionStorage.getItem('nga_curator_session');
    const userNameEl = document.querySelector('[data-session-user-name]');
    const userRoleEl = document.querySelector('[data-session-role]');

    if (rawSession) {
      try {
        const session = JSON.parse(rawSession);
        if (userNameEl) userNameEl.textContent = session.name;
        if (userRoleEl) userRoleEl.textContent = session.role;
      } catch (err) {
        console.warn('Gagal membaca sesi kurator:', err);
      }
    }

    const btnLogout = document.querySelector('[data-btn-logout]');
    if (btnLogout) {
      btnLogout.addEventListener('click', () => {
        sessionStorage.removeItem('nga_curator_session');
        window.location.href = 'login.php?action=logout';
      });
    }
  }
})();

// Validasi Formulir Koleksi GLAM
(() => {
  const glamForm = document.querySelector('[data-glam-form]');
  if (!glamForm) return;

  const statusBanner = document.querySelector('[data-form-status]');

  glamForm.addEventListener('submit', (e) => {
    e.preventDefault();

    if (!glamForm.checkValidity()) {
      glamForm.reportValidity();
      if (statusBanner) {
        statusBanner.hidden = false;
        statusBanner.className = 'form-status-banner is-error';
        statusBanner.textContent = 'Perhatian: Harap lengkapi semua field bertanda bintang (*) sesuai format yang diminta (Completeness & Format Check).';
      }
      return;
    }

    const formData = new FormData(glamForm);
    const judul = formData.get('judul_koleksi') || 'Objek Warisan Budaya';
    const noInv = formData.get('nomor_inventaris') || 'NG-2026-XXX';
    const pengusul = formData.get('nama_pengusul') || 'Kurator';

    if (statusBanner) {
      statusBanner.hidden = false;
      statusBanner.className = 'form-status-banner is-success';
      statusBanner.innerHTML = `
        <strong>Registrasi Metadata Berhasil!</strong><br />
        Koleksi <em>"${judul}"</em> dengan nomor inventaris <code>${noInv}</code> telah divalidasi oleh ${pengusul} dan tercatat dalam sistem informasi GLAM.
      `;
      statusBanner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  });

  glamForm.addEventListener('reset', () => {
    if (statusBanner) {
      statusBanner.hidden = true;
      statusBanner.textContent = '';
    }
  });
})();

// Hallmark Scroll Choreography & Entrance Animations
(() => {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  document.addEventListener('DOMContentLoaded', () => {
    const revealTargets = document.querySelectorAll(
      '.sketchbook-section, .featured, .manifesto, .catalog-card, .about-intro, .about-values article, .login-card, .form-wrapper-card, .archival-slip'
    );

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-revealed');
            observer.unobserve(entry.target);
          }
        });
      },
      {
        threshold: 0.08,
        rootMargin: '0px 0px -40px 0px'
      }
    );

    revealTargets.forEach((el, index) => {
      el.classList.add('reveal-on-scroll');
      el.style.setProperty('--reveal-delay', `${(index % 4) * 0.08}s`);
      observer.observe(el);
    });
  });
})();
