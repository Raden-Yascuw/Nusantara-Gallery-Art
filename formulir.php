<?php
// Formulir Entri Koleksi GLAM (Khusus Kurator Terautentikasi)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Restorasi sesi kurator untuk lingkungan serverless (Vercel)
if (empty($_SESSION['kurator_logged_in']) && !empty($_COOKIE['nga_kurator_token'])) {
    $expected_token = hash_hmac('sha256', 'kurator.yanel', 'nga_glam_secret_2026');
    if (hash_equals($expected_token, $_COOKIE['nga_kurator_token'])) {
        $_SESSION['kurator_logged_in'] = true;
        $_SESSION['kurator_user']      = 'kurator.yanel';
        $_SESSION['kurator_nama']      = 'Mohammad Yanuar Rizky';
        $_SESSION['kurator_nip']       = '250911718423';
        $_SESSION['kurator_peran']     = 'Kurator Koleksi & Preservasi';
    }
}

// Proteksi Akses: Hanya bisa dibuka jika sudah login
if (empty($_SESSION['kurator_logged_in'])) {
    header("Location: login.php?pesan=wajib_login");
    exit;
}

$page_title       = "Formulir Entri Metadata Koleksi | Nusantara Gallery Art";
$page_description = "Formulir entri metadata dan pengajuan preservasi digital koleksi warisan budaya Nusantara (GLAM).";
$current_page     = "formulir";

$kurator_nama = $_SESSION['kurator_nama'] ?? 'Mohammad Yanuar Rizky';
$kurator_user = $_SESSION['kurator_user'] ?? 'kurator.yanel';
$kurator_peran = $_SESSION['kurator_peran'] ?? 'Kurator Koleksi & Preservasi';

include "layout/header.php";
?>

    <!-- Bilah Status Sesi Kurator -->
    <div class="curator-session-bar" data-curator-session-bar>
      <div class="page-width session-bar-inner">
        <div class="session-info">
          <span class="session-indicator-dot"></span>
          <span class="session-label">Sesi Kurator Aktif:</span>
          <strong class="session-user"><?= htmlspecialchars($kurator_nama) ?> (<?= htmlspecialchars($kurator_user) ?>)</strong>
          <span class="session-badge">Hak Akses: <?= htmlspecialchars($kurator_peran) ?></span>
        </div>
        <div class="session-actions">
          <span class="session-timer" data-session-timer>Mode Akses Terproteksi</span>
          <a href="login.php?action=logout" class="btn-session-exit" data-session-exit>Keluar Sesi</a>
        </div>
      </div>
    </div>

    <main id="main-content" class="page-width form-page-container">
      <header class="page-title editorial-header">
        <p class="eyebrow">Dokumentasi &amp; Entri Sistem Informasi GLAM</p>
        <h2>Formulir Registrasi &amp; Metadata Koleksi Budaya</h2>
      </header>

      <section class="form-wrapper-card" aria-labelledby="form-heading">
        <header class="form-card-header">
          <div class="archive-stamp">
            <span class="stamp-code">GLAM-DKPD236013</span>
            <span class="stamp-label">FORMULIR INPUT RESMI</span>
          </div>
          <h3 id="form-heading">Lembar Pengajuan Objek Warisan Budaya</h3>
          <p class="form-intro">
            Tanda bintang (<span class="required-indicator" aria-hidden="true">*</span>) menunjukkan kolom wajib isi (*Completeness Check*). Perhatikan format data pada masing-masing field (*Format Check*).
          </p>
        </header>

        <!-- Formulir Input Pengguna dengan Method POST -->
        <form action="#" method="POST" class="glam-entry-form" data-glam-form novalidate>
          
          <!-- Bagian 1: Identitas Pengusul / Kurator -->
          <fieldset class="form-fieldset">
            <legend class="fieldset-legend">01. Identitas Kurator / Pengusul Data</legend>
            
            <div class="form-row-grid">
              <!-- Kontrol 1: Text Box (Nama Lengkap) -->
              <div class="form-group">
                <label for="nama_pengusul">
                  Nama Lengkap Kurator / Peneliti <span class="required-indicator" aria-label="wajib isi">*</span>
                </label>
                <input 
                  type="text" 
                  id="nama_pengusul" 
                  name="nama_pengusul" 
                  class="form-control" 
                  placeholder="Contoh: Mohammad Yanuar Rizky" 
                  value="<?= htmlspecialchars($kurator_nama) ?>"
                  minlength="3" 
                  maxlength="80" 
                  required 
                  aria-describedby="hint-nama"
                />
                <small id="hint-nama" class="form-hint">Entri teks bebas: minimal 3 karakter huruf.</small>
              </div>

              <!-- Kontrol 2: Format-Specific Text Box (Email) -->
              <div class="form-group">
                <label for="email_pengusul">
                  Alamat Surel Resmi (Email) <span class="required-indicator" aria-label="wajib isi">*</span>
                </label>
                <input 
                  type="email" 
                  id="email_pengusul" 
                  name="email_pengusul" 
                  class="form-control" 
                  placeholder="kurator@kemdikbud.go.id" 
                  required 
                  aria-describedby="hint-email"
                />
                <small id="hint-email" class="form-hint">Format Check: wajib menyertakan simbol @ dan domain valid.</small>
              </div>
            </div>

            <!-- Kontrol 3: Radio Button (Pilar Lembaga GLAM - Mutually Exclusive) -->
            <div class="form-group">
              <span class="group-label">
                Pilar Lembaga GLAM Asal Objek <span class="required-indicator" aria-label="wajib isi">*</span>
              </span>
              <small class="form-hint">Pilih salah satu dari 4 kategori yang saling pilah (*mutually exclusive*):</small>
              <div class="radio-button-grid" role="radiogroup" aria-required="true">
                <label class="radio-card">
                  <input type="radio" name="pilar_glam" value="galeri" required />
                  <span class="radio-custom"></span>
                  <span class="radio-content">
                    <strong>Galeri Seni</strong>
                    <small>Lukisan, sketsa, instalasi visual</small>
                  </span>
                </label>

                <label class="radio-card">
                  <input type="radio" name="pilar_glam" value="perpustakaan" />
                  <span class="radio-custom"></span>
                  <span class="radio-content">
                    <strong>Perpustakaan Digital</strong>
                    <small>Naskah kuno, manuskrip daluang, buku langka</small>
                  </span>
                </label>

                <label class="radio-card">
                  <input type="radio" name="pilar_glam" value="arsip" />
                  <span class="radio-custom"></span>
                  <span class="radio-content">
                    <strong>Pusat Arsip Sejarah</strong>
                    <small>Dokumen kolonial, korespondensi, peta tua</small>
                  </span>
                </label>

                <label class="radio-card">
                  <input type="radio" name="pilar_glam" value="museum" />
                  <span class="radio-custom"></span>
                  <span class="radio-content">
                    <strong>Museum Warisan Budaya</strong>
                    <small>Arca batu, senjata pusaka, artefak 3D</small>
                  </span>
                </label>
              </div>
            </div>
          </fieldset>

          <!-- Bagian 2: Metadata & Spesifikasi Objek Budaya -->
          <fieldset class="form-fieldset">
            <legend class="fieldset-legend">02. Metadata &amp; Deskripsi Koleksi</legend>

            <div class="form-row-grid">
              <!-- Kontrol 1: Text Box (Judul Koleksi) -->
              <div class="form-group">
                <label for="judul_koleksi">
                  Judul Koleksi / Nama Objek Warisan <span class="required-indicator" aria-label="wajib isi">*</span>
                </label>
                <input 
                  type="text" 
                  id="judul_koleksi" 
                  name="judul_koleksi" 
                  class="form-control" 
                  placeholder="Misal: Penangkapan Pangeran Diponegoro" 
                  required 
                  aria-describedby="hint-judul"
                />
                <small id="hint-judul" class="form-hint">Nama resmi karya seni atau naskah sesuai katalog primer.</small>
              </div>

              <!-- Kontrol 1 & Format Check: Nomor Registrasi Inventaris dengan regex pattern -->
              <div class="form-group">
                <label for="nomor_inventaris">
                  Nomor Inventaris Koleksi (Pola Baku) <span class="required-indicator" aria-label="wajib isi">*</span>
                </label>
                <input 
                  type="text" 
                  id="nomor_inventaris" 
                  name="nomor_inventaris" 
                  class="form-control font-mono" 
                  placeholder="NG-1857-001" 
                  pattern="NG-[0-9]{4}-[0-9]{3}" 
                  title="Format wajib: NG-YYYY-XXX (contoh: NG-1857-001)" 
                  required 
                  aria-describedby="hint-inventaris"
                />
                <small id="hint-inventaris" class="form-hint">Format Check: pola harus diawali `NG-` diikuti 4 digit tahun dan 3 digit seri.</small>
              </div>
            </div>

            <div class="form-row-grid">
              <!-- Kontrol 5: Drop-down Selection List (Kategori Koleksi Budaya) -->
              <div class="form-group">
                <label for="kategori_koleksi">
                  Klasifikasi Kategori Koleksi <span class="required-indicator" aria-label="wajib isi">*</span>
                </label>
                <select id="kategori_koleksi" name="kategori_koleksi" class="form-control" required aria-describedby="hint-kategori">
                  <option value="" disabled selected>-- Pilih Kategori Warisan Budaya --</option>
                  <option value="lukisan">Lukisan Klasik &amp; Romantisisme Hindia Timur</option>
                  <option value="naskah">Naskah Kuno &amp; Manuskrip Daluang / Lontar</option>
                  <option value="artefak">Artefak 3D &amp; Seni Pahat Batu Andesit</option>
                  <option value="tekstil">Tekstil Tradisional, Wastra &amp; Batik Kuno</option>
                  <option value="litografi">Litografi, Peta Kartografi &amp; Foto Bersejarah</option>
                </select>
                <small id="hint-kategori" class="form-hint">Drop-down list untuk memilih 1 klasifikasi baku dari daftar panjang.</small>
              </div>

              <!-- Kontrol 2: Format-Specific Text Box (Date Picker) -->
              <div class="form-group">
                <label for="tanggal_pendataan">
                  Tanggal Pencatatan / Registrasi <span class="required-indicator" aria-label="wajib isi">*</span>
                </label>
                <input 
                  type="date" 
                  id="tanggal_pendataan" 
                  name="tanggal_pendataan" 
                  class="form-control" 
                  required 
                  aria-describedby="hint-tanggal"
                />
                <small id="hint-tanggal" class="form-hint">Format-Specific Date: menentukan penanggalan arsip secara ISO (YYYY-MM-DD).</small>
              </div>
            </div>

            <!-- Kontrol 4: Check Box (Jenis Format Digitalisasi - Multiple Choice) -->
            <div class="form-group">
              <span class="group-label">
                Format Berkas Digital yang Tersedia (Boleh Lebih Dari Satu) <span class="required-indicator" aria-label="wajib isi">*</span>
              </span>
              <small class="form-hint">Check box untuk opsi non-eksklusif / pemilihan jamak (*multiple choice*):</small>
              <div class="checkbox-group-grid">
                <label class="checkbox-card">
                  <input type="checkbox" name="format_media[]" value="foto_raw" checked />
                  <span class="checkbox-custom"></span>
                  <span class="checkbox-text">
                    <strong>Fotografi Arsip Resolusi Tinggi</strong>
                    <small>Format TIFF / RAW uncompressed 300+ DPI</small>
                  </span>
                </label>

                <label class="checkbox-card">
                  <input type="checkbox" name="format_media[]" value="model_3d" />
                  <span class="checkbox-custom"></span>
                  <span class="checkbox-text">
                    <strong>Model 3D Fotogrametri / Mesh</strong>
                    <small>Format OBJ / GLTF interaktif 360 derajat</small>
                  </span>
                </label>

                <label class="checkbox-card">
                  <input type="checkbox" name="format_media[]" value="transkripsi" />
                  <span class="checkbox-custom"></span>
                  <span class="checkbox-text">
                    <strong>Transkripsi &amp; Translasi Aksara</strong>
                    <small>Alih aksara Jawa/Arab Melayu ke Latin dan terjemahan</small>
                  </span>
                </label>

                <label class="checkbox-card">
                  <input type="checkbox" name="format_media[]" value="metadata_mets" />
                  <span class="checkbox-custom"></span>
                  <span class="checkbox-text">
                    <strong>Skema Metadata XML (Dublin Core / METS)</strong>
                    <small>Interoperabilitas antar-perpustakaan digital nasional</small>
                  </span>
                </label>
              </div>
            </div>

            <!-- Kontrol Text Area untuk Catatan Kurator -->
            <div class="form-group">
              <label for="catatan_kurator">Catatan Konteks Sejarah &amp; Provenans (Opsional)</label>
              <textarea 
                id="catatan_kurator" 
                name="catatan_kurator" 
                rows="4" 
                class="form-control" 
                placeholder="Tuliskan latar belakang penciptaan, riwayat kepemilikan (provenance), atau temuan fisik objek..."
              ></textarea>
              <small class="form-hint">Penjelasan naratif untuk memperkaya nilai historis rekod di perpustakaan digital.</small>
            </div>

            <!-- Kontrol 4: Checkbox Pernyataan Integritas Data (Wajib) -->
            <div class="form-group term-box">
              <label class="checkbox-inline">
                <input type="checkbox" id="pernyataan_validitas" name="pernyataan_validitas" required />
                <span class="checkbox-custom"></span>
                <span class="checkbox-label-text">
                  Saya menyatakan bahwa data metadata yang dimasukkan telah diperiksa keabsahan sejarahnya dan mematuhi etika preservasi informasi GLAM. <span class="required-indicator">*</span>
                </span>
              </label>
            </div>
          </fieldset>

          <!-- Feedback Pesan Form -->
          <div class="form-status-banner" data-form-status hidden role="alert" aria-live="polite"></div>

          <!-- Kontrol 6: Command Buttons (Tombol Submit dan Reset) -->
          <div class="form-actions-bar">
            <button type="reset" class="button button-secondary">
              Bersihkan Formulir
            </button>
            <button type="submit" class="button button-primary btn-vermilion">
              Simpan &amp; Daftarkan Metadata Koleksi <span aria-hidden="true">↗</span>
            </button>
          </div>
        </form>
      </section>
    </main>

<?php
include "layout/footer.php";
?>
