<?php
// Layanan Reservasi Tiket & Tur Pameran Seni Nusantara (GLAM)

// Konfigurasi Halaman & Metadata
$page_title       = "Layanan Tiket & Reservasi Pameran — Nusantara Gallery Art";
$page_description = "Simulasi pemesanan karcis kunjungan, tur kuratorial, dan pameran seni budaya digital GLAM.";
$current_page     = "layanan";

// Memanggil Komponen Modular Layout Header
include "layout/header.php";

// Master Kategori Pengunjung (Tarif Bersubsidi & Komersial)
$kategori_pengunjung_master = [
    'pelajar'     => ['tarif' => 15000, 'label' => 'Pelajar / Mahasiswa',    'desc' => 'Tarif edukasi bersubsidi'],
    'umum'        => ['tarif' => 35000, 'label' => 'Pengunjung Umum (WNI)',  'desc' => 'Tarif reguler domestik'],
    'mancanegara' => ['tarif' => 75000, 'label' => 'Wisatawan Mancanegara',  'desc' => 'Pass turis internasional'],
    'peneliti'    => ['tarif' => 20000, 'label' => 'Peneliti / Sivitas Seni', 'desc' => 'Akses riset & kurasi'],
];

// Master Paket Sayap Pameran / Galeri Seni
$paket_pameran_master = [
    'tetap'     => ['biaya' => 0,     'label' => 'Galeri Tetap Seni Nusantara'],
    'maestro'   => ['biaya' => 15000, 'label' => 'Pameran Retrospeksi Maestro Raden Saleh'],
    'digital'   => ['biaya' => 25000, 'label' => 'Paviliun Imersif Digital & 3D Interaktif'],
    'manuskrip' => ['biaya' => 20000, 'label' => 'Ruang Preservasi Naskah & Seni Kuno'],
];

// Master Layanan Panduan Kuratorial & Fasilitas Edukasi
$layanan_tambahan_master = [
    'mandiri'   => ['biaya' => 0,     'label' => 'Eksplorasi Mandiri (Audio Guide Standar)'],
    'kurator'   => ['biaya' => 50000, 'label' => 'Pemandu Tur Kurator Galeri (Sesi Kelompok)'],
    'katalog'   => ['biaya' => 45000, 'label' => 'Paket Buku Katalog Pameran Cetak Eksklusif'],
    'kombinasi' => ['biaya' => 85000, 'label' => 'Paket Lengkap: Tur Kurator + Buku Katalog'],
];

$hasil_kalkulasi = null;
$pesan_error     = '';

// Data default formulir input
$form_input = [
    'nama_pemesan'        => '',
    'kategori_pengunjung' => 'umum',
    'paket_pameran'       => 'tetap',
    'tanggal_kunjungan'   => '',
    'jumlah_tiket'        => 1,
    'layanan_tambahan'    => 'mandiri',
];

// Pemrosesan formulir pemesanan tiket pameran di sisi server (Back-End)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $form_input['nama_pemesan']        = trim($_POST['nama_pemesan'] ?? '');
    $form_input['kategori_pengunjung'] = $_POST['kategori_pengunjung'] ?? 'umum';
    $form_input['paket_pameran']       = $_POST['paket_pameran'] ?? 'tetap';
    $form_input['tanggal_kunjungan']   = $_POST['tanggal_kunjungan'] ?? '';
    $form_input['jumlah_tiket']        = (int) ($_POST['jumlah_tiket'] ?? 1);
    $form_input['layanan_tambahan']    = $_POST['layanan_tambahan'] ?? 'mandiri';

    // Validasi Kelengkapan dan Integritas Data
    if (empty($form_input['nama_pemesan']) || empty($form_input['tanggal_kunjungan'])) {
        $pesan_error = 'Nama pemesan dan tanggal kunjungan wajib diisi dengan benar.';
    } elseif ($form_input['jumlah_tiket'] < 1) {
        $pesan_error = 'Jumlah tiket pemesanan minimal adalah 1 orang.';
    } elseif (!strtotime($form_input['tanggal_kunjungan'])) {
        $pesan_error = 'Format tanggal kunjungan tidak valid.';
    } elseif (!isset($kategori_pengunjung_master[$form_input['kategori_pengunjung']])) {
        $pesan_error = 'Kategori pengunjung yang dipilih tidak valid.';
    } elseif (!isset($paket_pameran_master[$form_input['paket_pameran']])) {
        $pesan_error = 'Pilihan sayap pameran seni tidak valid.';
    } elseif (!isset($layanan_tambahan_master[$form_input['layanan_tambahan']])) {
        $pesan_error = 'Pilihan opsi layanan kuratorial tidak valid.';
    } else {
        // 1. SEQUENCE & SELECTION (Kondisi 1): Menentukan tarif dasar tiket per orang
        $info_kategori = $kategori_pengunjung_master[$form_input['kategori_pengunjung']];
        $info_pameran  = $paket_pameran_master[$form_input['paket_pameran']];
        $info_layanan  = $layanan_tambahan_master[$form_input['layanan_tambahan']];

        $tarif_dasar_kategori = $info_kategori['tarif'];
        $biaya_akses_pameran  = $info_pameran['biaya'];
        $tarif_satuan_murni   = $tarif_dasar_kategori + $biaya_akses_pameran;

        // 2. SELECTION (Kondisi 2): Evaluasi Hari Kunjungan (Weekend Surcharge Rp 5.000/tiket)
        $tgl_obj           = new DateTime($form_input['tanggal_kunjungan']);
        $nama_hari_inggris = $tgl_obj->format('l');
        $peta_hari = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];
        $nama_hari_id = $peta_hari[$nama_hari_inggris] ?? $nama_hari_inggris;

        $is_weekend = ($nama_hari_inggris === 'Saturday' || $nama_hari_inggris === 'Sunday');
        $surcharge_weekend_satuan = $is_weekend ? 5000 : 0;
        $total_surcharge_weekend  = $surcharge_weekend_satuan * $form_input['jumlah_tiket'];

        $tarif_satuan_efektif = $tarif_satuan_murni + $surcharge_weekend_satuan;
        $subtotal_tiket       = $tarif_satuan_efektif * $form_input['jumlah_tiket'];

        // 3. SELECTION (Kondisi 3): Evaluasi Diskon Rombongan (Group Discount 15% jika tiket >= 5)
        if ($form_input['jumlah_tiket'] >= 5) {
            $is_rombongan     = true;
            $persen_diskon    = 15;
            $nominal_diskon   = (int) round($subtotal_tiket * 0.15);
            $status_reservasi = 'ROMBONGAN';
        } else {
            $is_rombongan     = false;
            $persen_diskon    = 0;
            $nominal_diskon   = 0;
            $status_reservasi = 'REGULER';
        }

        // 4. SEQUENCE: Penambahan Biaya Layanan Kurator/Katalog & Total Pembayaran
        $biaya_layanan_tambahan = $info_layanan['biaya'];
        $total_pembayaran       = ($subtotal_tiket - $nominal_diskon) + $biaya_layanan_tambahan;

        // Kode Registrasi Tiket Unik
        $kode_tiket = 'NGA-PASS-2026-' . strtoupper(substr(md5($form_input['nama_pemesan'] . $form_input['tanggal_kunjungan']), 0, 8));

        $hasil_kalkulasi = [
            'nama_pemesan'             => htmlspecialchars($form_input['nama_pemesan']),
            'kategori_label'           => $info_kategori['label'],
            'pameran_label'            => $info_pameran['label'],
            'layanan_label'            => $info_layanan['label'],
            'tanggal_kunjungan'        => $tgl_obj->format('d F Y'),
            'nama_hari'                => $nama_hari_id,
            'is_weekend'               => $is_weekend,
            'surcharge_weekend_satuan' => $surcharge_weekend_satuan,
            'total_surcharge_weekend'  => $total_surcharge_weekend,
            'jumlah_tiket'             => $form_input['jumlah_tiket'],
            'tarif_satuan_murni'       => $tarif_satuan_murni,
            'tarif_satuan_efektif'     => $tarif_satuan_efektif,
            'subtotal_tiket'           => $subtotal_tiket,
            'is_rombongan'             => $is_rombongan,
            'persen_diskon'            => $persen_diskon,
            'nominal_diskon'           => $nominal_diskon,
            'biaya_layanan_tambahan'   => $biaya_layanan_tambahan,
            'total_pembayaran'         => $total_pembayaran,
            'status_reservasi'         => $status_reservasi,
            'kode_tiket'               => $kode_tiket,
        ];
    }
}

// Data Riwayat Reservasi Kunjungan Terkini (ITERATION: Pengulangan Data Jamak)
$riwayat_reservasi = [
    [
        'nama'        => 'Dewi Sartika Putri',
        'kategori'    => 'pelajar',
        'pameran'     => 'tetap',
        'tgl'         => '28 Oktober 2026',
        'hari'        => 'Rabu (Hari Kerja)',
        'jumlah'      => 2,
        'layanan'     => 'mandiri',
        'status'      => 'REGULER',
        'total_bayar' => 30000,
    ],
    [
        'nama'        => 'SMA Negeri 3 Malang (Rombongan Seni)',
        'kategori'    => 'pelajar',
        'pameran'     => 'maestro',
        'tgl'         => '31 Oktober 2026',
        'hari'        => 'Sabtu (Akhir Pekan)',
        'jumlah'      => 12,
        'layanan'     => 'kurator',
        'status'      => 'ROMBONGAN',
        'total_bayar' => 407000,
    ],
    [
        'nama'        => 'Dr. Arthur M. Langer',
        'kategori'    => 'peneliti',
        'pameran'     => 'manuskrip',
        'tgl'         => '29 Oktober 2026',
        'hari'        => 'Kamis (Hari Kerja)',
        'jumlah'      => 1,
        'layanan'     => 'katalog',
        'status'      => 'REGULER',
        'total_bayar' => 85000,
    ],
    [
        'nama'        => 'Alexander van Houten',
        'kategori'    => 'mancanegara',
        'pameran'     => 'digital',
        'tgl'         => '01 November 2026',
        'hari'        => 'Minggu (Akhir Pekan)',
        'jumlah'      => 2,
        'layanan'     => 'mandiri',
        'status'      => 'REGULER',
        'total_bayar' => 210000,
    ],
    [
        'nama'        => 'Komunitas Perupa Nuansa Jawa Timur',
        'kategori'    => 'umum',
        'pameran'     => 'maestro',
        'tgl'         => '07 November 2026',
        'hari'        => 'Sabtu (Akhir Pekan)',
        'jumlah'      => 8,
        'layanan'     => 'kombinasi',
        'status'      => 'ROMBONGAN',
        'total_bayar' => 459000,
    ],
];
?>

    <main id="main-content">
      <section class="hero page-width no-print" aria-labelledby="layanan-title">
        <h2 id="layanan-title">Reservasi Karcis Kunjungan &amp;<br />Tur Kuratorial Seni GLAM</h2>
        <p class="hero-copy">Hitung estimasi tarif tiket pameran, penyesuaian akhir pekan, sesi pemandu kurator, dan diskon rombongan secara dinamis di sisi server.</p>
        <nav class="hero-actions" aria-label="Aksi Cepat">
          <?php if ($hasil_kalkulasi !== null): ?>
            <a class="button" href="#hasil-kalkulasi">Lihat Karcis Terbit</a>
          <?php endif; ?>
          <a class="button" href="#form-kalkulasi">Mulai Reservasi</a>
          <a class="text-link" href="#riwayat-reservasi">Lihat Riwayat Reservasi</a>
        </nav>
      </section>

      <?php if ($hasil_kalkulasi !== null): ?>
      <section class="page-width no-print">
        <div class="form-status-banner is-success">
          <strong>Karcis Masuk Berhasil Diterbitkan</strong> — Register: #<?= $hasil_kalkulasi['kode_tiket'] ?>, Total: Rp <?= number_format($hasil_kalkulasi['total_pembayaran'], 0, ',', '.') ?>.
          <a href="#hasil-kalkulasi" class="text-link">Lihat Karcis</a>
        </div>
      </section>
      <?php endif; ?>

      <section class="page-width no-print form-page-container" id="form-kalkulasi">
        <article class="form-wrapper-card">
          <header class="form-card-header">
            <div class="archive-stamp">
              <span class="stamp-code">GLAM-PASS</span>
              <span class="stamp-label">Layanan Galeri</span>
            </div>
            <h3>Formulir Reservasi Tiket Pameran</h3>
            <p class="form-intro">Masukkan data pengunjung, tanggal kunjungan, dan opsi layanan kuratorial untuk memproses penerbitan karcis masuk resmi galeri.</p>
          </header>

          <?php if (!empty($pesan_error)): ?>
            <div class="form-status-banner is-error">
              <strong>Galat:</strong> <?= htmlspecialchars($pesan_error) ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="layanan.php#hasil-kalkulasi" novalidate>
            <div class="form-group">
              <label for="nama_pemesan">Nama Pemesan / Peneliti / Rombongan <span class="required-indicator">*</span></label>
              <input
                id="nama_pemesan"
                type="text"
                name="nama_pemesan"
                placeholder="Contoh: Raden Mas Danang atau Komunitas Seni Budaya"
                required
                value="<?= htmlspecialchars($form_input['nama_pemesan']) ?>"
              />
            </div>

            <div class="form-row-grid">
              <div class="form-group">
                <label for="kategori_pengunjung">Kategori Pengunjung &amp; Tarif Dasar</label>
                <select id="kategori_pengunjung" name="kategori_pengunjung">
                  <?php foreach ($kategori_pengunjung_master as $key => $info): ?>
                    <option value="<?= $key ?>" <?= ($form_input['kategori_pengunjung'] === $key) ? 'selected' : '' ?>>
                      <?= $info['label'] ?> — Rp <?= number_format($info['tarif'], 0, ',', '.') ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label for="paket_pameran">Pilihan Galeri / Pameran Seni</label>
                <select id="paket_pameran" name="paket_pameran">
                  <?php foreach ($paket_pameran_master as $key => $info): ?>
                    <option value="<?= $key ?>" <?= ($form_input['paket_pameran'] === $key) ? 'selected' : '' ?>>
                      <?= $info['label'] ?> <?= ($info['biaya'] > 0) ? '(+Rp ' . number_format($info['biaya'], 0, ',', '.') . ')' : '(Termasuk)' ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="form-row-grid">
              <div class="form-group">
                <label for="tanggal_kunjungan">Tanggal Kunjungan <span class="required-indicator">*</span></label>
                <input
                  id="tanggal_kunjungan"
                  type="date"
                  name="tanggal_kunjungan"
                  required
                  value="<?= htmlspecialchars($form_input['tanggal_kunjungan']) ?>"
                />
              </div>

              <div class="form-group">
                <label for="jumlah_tiket">Jumlah Tiket / Pengunjung <span class="required-indicator">*</span></label>
                <input
                  id="jumlah_tiket"
                  type="number"
                  name="jumlah_tiket"
                  min="1"
                  max="100"
                  required
                  value="<?= (int) $form_input['jumlah_tiket'] ?>"
                />
              </div>
            </div>

            <div class="form-group">
              <label for="layanan_tambahan">Layanan Edukasi &amp; Pemandu Kurator</label>
              <select id="layanan_tambahan" name="layanan_tambahan">
                <?php foreach ($layanan_tambahan_master as $key => $info): ?>
                  <option value="<?= $key ?>" <?= ($form_input['layanan_tambahan'] === $key) ? 'selected' : '' ?>>
                    <?= $info['label'] ?> <?= ($info['biaya'] > 0) ? '(+Rp ' . number_format($info['biaya'], 0, ',', '.') . ')' : '(Gratis)' ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <small class="form-hint">Ketentuan: Akhir pekan (Sabtu &amp; Minggu) dikenakan penyesuaian operasional Rp 5.000 per tiket. Rombongan minimal 5 orang memperoleh diskon 15% dari subtotal tiket.</small>
            </div>

            <div class="form-actions-bar">
              <button class="button" type="submit">
                Proses Reservasi &amp; Terbitkan Karcis Masuk
              </button>
            </div>
          </form>
        </article>
      </section>

      <?php if ($hasil_kalkulasi !== null): ?>
      <section class="page-width form-page-container" id="hasil-kalkulasi">
        <article class="archival-slip <?= ($hasil_kalkulasi['is_rombongan']) ? 'slip-cleared' : 'slip-has-fine' ?>">
          
          <!-- Perforasi Fisik Karcis Arsip -->
          <div class="slip-perforation-top" aria-hidden="true">
            <span class="slip-perforation-line"></span>
          </div>

          <!-- Tajuk Dokumen Reservasi Tiket -->
          <header class="slip-header">
            <div class="slip-brand">
              <span class="slip-seal-badge" aria-hidden="true">NGA</span>
              <div>
                <span class="slip-institution">Buku Besar Arsip GLAM — Nusantara Gallery Art</span>
                <h3 class="slip-title">Karcis Kunjungan Resmi &amp; Pass Pameran</h3>
                <p class="slip-subtitle">Dokumen Bukti Reservasi Masuk &amp; Akses Kuratorial Pameran Seni</p>
              </div>
            </div>

            <div class="slip-serial-block">
              <span class="slip-serial-label">Register Reservasi</span>
              <span class="slip-serial-number">#<?= $hasil_kalkulasi['kode_tiket'] ?></span>
              <span class="slip-serial-time">Diterbitkan: <?= date('d/m/Y, H:i') ?> WIB</span>
            </div>
          </header>

          <!-- Stempel Otentikasi Tinta Fisik -->
          <?php if ($hasil_kalkulasi['is_rombongan']): ?>
            <div class="archival-stamp stamp-rombongan" role="status" aria-label="Status: Rombongan Diskon 15%">
              <div class="stamp-border">
                <span class="stamp-title">Rombongan</span>
                <span class="stamp-caption">Diskon 15% Diterapkan</span>
                <span class="stamp-detail"><?= $hasil_kalkulasi['jumlah_tiket'] ?> Tiket — Rp <?= number_format($hasil_kalkulasi['total_pembayaran'], 0, ',', '.') ?></span>
              </div>
            </div>
          <?php else: ?>
            <div class="archival-stamp stamp-tepat" role="status" aria-label="Status: Terverifikasi Tiket Resmi">
              <div class="stamp-border">
                <span class="stamp-title">Terverifikasi</span>
                <span class="stamp-caption">Tiket Resmi Masuk</span>
                <span class="stamp-detail"><?= $hasil_kalkulasi['jumlah_tiket'] ?> Tiket — Rp <?= number_format($hasil_kalkulasi['total_pembayaran'], 0, ',', '.') ?></span>
              </div>
            </div>
          <?php endif; ?>

          <!-- Tabel Rincian Tiket & Layanan (Ledger Style) -->
          <div class="slip-ledger">
            <table class="slip-table">
              <tbody>
                <tr>
                  <th scope="row">Nama Pemesan / Rombongan</th>
                  <td class="slip-val-lead"><?= $hasil_kalkulasi['nama_pemesan'] ?></td>
                </tr>
                <tr>
                  <th scope="row">Kategori Pengunjung</th>
                  <td>
                    <span class="slip-badge-category">
                      <?= $hasil_kalkulasi['kategori_label'] ?>
                    </span>
                    <span class="slip-tariff-note">(Tarif dasar: Rp <?= number_format($tarif_dasar_kategori, 0, ',', '.') ?> / orang)</span>
                  </td>
                </tr>
                <tr>
                  <th scope="row">Pameran Seni / Sayap Galeri</th>
                  <td>
                    <span class="slip-badge-category">
                      <?= $hasil_kalkulasi['pameran_label'] ?>
                    </span>
                    <?php if ($biaya_akses_pameran > 0): ?>
                      <span class="slip-tariff-note">(Tambahan akses pameran: +Rp <?= number_format($biaya_akses_pameran, 0, ',', '.') ?> / tiket)</span>
                    <?php else: ?>
                      <span class="slip-tariff-note">(Koleksi Tetap Galeri)</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <th scope="row">Jadwal &amp; Hari Kunjungan</th>
                  <td>
                    <strong><?= $hasil_kalkulasi['nama_hari'] ?>, <?= $hasil_kalkulasi['tanggal_kunjungan'] ?></strong>
                    <?php if ($hasil_kalkulasi['is_weekend']): ?>
                      <span class="slip-sub-note">(Akhir Pekan: Surcharge Rp 5.000 / tiket)</span>
                    <?php else: ?>
                      <span class="slip-sub-note">(Hari Kerja Reguler)</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <th scope="row">Jumlah Tiket Dipesan</th>
                  <td>
                    <strong><?= $hasil_kalkulasi['jumlah_tiket'] ?> Orang</strong>
                    <span class="slip-sub-note">(Tarif Efektif: Rp <?= number_format($hasil_kalkulasi['tarif_satuan_efektif'], 0, ',', '.') ?> / tiket)</span>
                  </td>
                </tr>
                <tr>
                  <th scope="row">Subtotal Biaya Tiket</th>
                  <td>
                    Rp <?= number_format($hasil_kalkulasi['subtotal_tiket'], 0, ',', '.') ?>
                  </td>
                </tr>
                <tr>
                  <th scope="row">Keringanan Diskon Rombongan</th>
                  <td>
                    <?php if ($hasil_kalkulasi['is_rombongan']): ?>
                      <strong>- Rp <?= number_format($hasil_kalkulasi['nominal_diskon'], 0, ',', '.') ?></strong>
                      <span class="slip-sub-note">(Apresiasi 15% untuk rombongan &ge; 5 orang)</span>
                    <?php else: ?>
                      <span>— 0% (Berlaku jika pemesanan &ge; 5 tiket)</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <th scope="row">Layanan Edukasi &amp; Kurator</th>
                  <td>
                    <span><?= $hasil_kalkulasi['layanan_label'] ?></span>
                    <?php if ($hasil_kalkulasi['biaya_layanan_tambahan'] > 0): ?>
                      <strong>(+Rp <?= number_format($hasil_kalkulasi['biaya_layanan_tambahan'], 0, ',', '.') ?>)</strong>
                    <?php else: ?>
                      <span class="slip-sub-note">(Termasuk paket mandiri)</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr class="slip-row-total">
                  <th scope="row">Total Pembayaran Karcis</th>
                  <td class="slip-total-amount amount-due">
                    Rp <?= number_format($hasil_kalkulasi['total_pembayaran'], 0, ',', '.') ?>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Catatan Kebijakan Akses Pameran -->
          <?php if ($hasil_kalkulasi['is_rombongan']): ?>
            <div class="slip-notice notice-clear">
              <div>
                <strong>Catatan Kurator:</strong> Pemesanan rombongan berjumlah <strong><?= $hasil_kalkulasi['jumlah_tiket'] ?> pengunjung</strong> mendapatkan potongan <strong>15% (Rp <?= number_format($hasil_kalkulasi['nominal_diskon'], 0, ',', '.') ?>)</strong>. Tunjukkan dokumen karcis ini ke meja resepsionis kurator saat kunjungan.
              </div>
            </div>
          <?php else: ?>
            <div class="slip-notice notice-fine">
              <div>
                <strong>Catatan Kunjungan:</strong> Tiket resmi diterbitkan untuk <strong><?= $hasil_kalkulasi['jumlah_tiket'] ?> pengunjung</strong> pada <strong><?= $hasil_kalkulasi['nama_hari'] ?>, <?= $hasil_kalkulasi['tanggal_kunjungan'] ?></strong>. Mohon hadir 15 menit sebelum sesi pameran dimulai demi ketertiban kurasi galeri.
              </div>
            </div>
          <?php endif; ?>

          <!-- Footer Karcis: Barcode & Tanda Pengesahan Kurator -->
          <footer class="slip-footer">
            <div class="slip-barcode-area">
              <div class="simulated-barcode" aria-hidden="true">
                <span class="bar b1"></span><span class="bar b3"></span><span class="bar b2"></span><span class="bar b4"></span><span class="bar b1"></span><span class="bar b2"></span><span class="bar b3"></span><span class="bar b1"></span><span class="bar b4"></span><span class="bar b2"></span><span class="bar b1"></span><span class="bar b3"></span><span class="bar b2"></span><span class="bar b4"></span><span class="bar b1"></span><span class="bar b3"></span><span class="bar b2"></span><span class="bar b1"></span><span class="bar b4"></span><span class="bar b3"></span><span class="bar b2"></span><span class="bar b1"></span><span class="bar b3"></span><span class="bar b4"></span><span class="bar b2"></span><span class="bar b1"></span><span class="bar b3"></span><span class="bar b2"></span><span class="bar b4"></span><span class="bar b1"></span>
              </div>
              <span class="barcode-number">2509 1171 8423 <?= strtoupper(substr(md5($hasil_kalkulasi['nama_pemesan']), 0, 6)) ?></span>
            </div>

            <div class="slip-signature-area">
              <span class="slip-sign-label">Petugas Kurator Galeri:</span>
              <div class="slip-sign-seal">
                <span class="seal-text">Terverifikasi — Kurator GLAM</span>
              </div>
              <span class="slip-sign-officer">Mohammad Yanuar Rizky (NIM 250911718423)</span>
            </div>
          </footer>

          <!-- Tombol Aksi Karcis (Cetak Dokumen & Unduh File) -->
          <div class="slip-actions no-print">
            <button type="button" class="button" onclick="window.print()">
              Cetak Karcis (PDF)
            </button>
            <button type="button" class="button button-secondary" onclick="unduhBerkasKarcis()">
              Unduh Berkas Karcis (.html)
            </button>
            <button type="button" class="button button-secondary" onclick="salinTautanKarcis()">
              Salin Tautan Karcis
            </button>
            <a href="#form-kalkulasi" class="button button-secondary">
              Pesan Karcis Baru
            </a>
          </div>

        </article>
      </section>
      <?php endif; ?>

      <section class="featured page-width no-print" id="riwayat-reservasi">
        <header class="section-heading section-heading-row">
          <section>
            <h2>Riwayat Reservasi Kunjungan Terkini</h2>
          </section>
        </header>

        <table class="metadata-table">
          <thead>
            <tr>
              <th scope="col">No</th>
              <th scope="col">Nama Pengunjung / Pemesan</th>
              <th scope="col">Kategori</th>
              <th scope="col">Pameran &amp; Jadwal</th>
              <th scope="col">Jumlah Tiket</th>
              <th scope="col">Status</th>
              <th scope="col">Total Bayar</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $nomor_urut = 1;
            foreach ($riwayat_reservasi as $item):
            ?>
            <tr>
              <td><?= $nomor_urut ?></td>
              <td><?= htmlspecialchars($item['nama']) ?></td>
              <td><?= htmlspecialchars($kategori_pengunjung_master[$item['kategori']]['label'] ?? $item['kategori']) ?></td>
              <td>
                <strong><?= $paket_pameran_master[$item['pameran']]['label'] ?? $item['pameran'] ?></strong><br />
                <span><?= $item['tgl'] ?> — <?= $item['hari'] ?></span>
              </td>
              <td><?= $item['jumlah'] ?> Tiket</td>
              <td><?= ($item['status'] === 'ROMBONGAN') ? 'Rombongan (Diskon 15%)' : 'Reguler' ?></td>
              <td>Rp <?= number_format($item['total_bayar'], 0, ',', '.') ?></td>
            </tr>
            <?php
            $nomor_urut++;
            endforeach;
            ?>
          </tbody>
        </table>
      </section>

      <section class="manifesto page-width no-print" aria-labelledby="manifesto-layanan">
        <h2 id="manifesto-layanan">Apresiasi seni dan pelestarian warisan budaya terbuka untuk seluruh lapisan masyarakat Nusantara.</h2>
        <a class="text-link" href="index.php">Kembali ke Beranda Galeri</a>
      </section>
    </main>

    <script>
    function unduhBerkasKarcis() {
      const slipElement = document.querySelector('.archival-slip');
      if (!slipElement) {
        alert('Karcis belum diterbitkan. Silakan lengkapi formulir dan klik "Proses Reservasi" terlebih dahulu.');
        return;
      }

      const clone = slipElement.cloneNode(true);
      const actions = clone.querySelector('.slip-actions');
      if (actions) actions.remove();

      const htmlContent = `<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Karcis_Resmi_<?= $hasil_kalkulasi['kode_tiket'] ?? 'NGA-PASS' ?></title>
  <link rel="preconnect" href="https://api.fontshare.com" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://api.fontshare.com/v2/css?f[]=sentient@300,400&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Hanken Grotesk', -apple-system, BlinkMacSystemFont, sans-serif;
      background: #f5f1e9;
      color: #26201a;
      margin: 0;
      padding: 30px 15px;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
      box-sizing: border-box;
    }
    .karcis-wrap {
      max-width: 740px;
      width: 100%;
      background: #faf7f0;
      border: 1px solid #26201a;
      border-radius: 2px;
      padding: 32px;
      box-sizing: border-box;
    }
    .slip-brand { display: flex; align-items: flex-start; gap: 14px; }
    .slip-institution { font-size: 0.78rem; color: #6e665c; display: block; }
    .slip-title { font-family: 'Sentient', Georgia, serif; font-weight: 300; font-size: 1.65rem; margin: 4px 0 2px; color: #26201a; }
    .slip-subtitle { font-size: 0.85rem; color: #6e665c; margin: 0; }
    .slip-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1.5px solid #26201a; padding-bottom: 18px; margin-bottom: 20px; gap: 16px; flex-wrap: wrap; }
    .slip-serial-block { text-align: right; }
    .slip-serial-label { font-size: 0.75rem; color: #6e665c; display: block; }
    .slip-serial-number { font-family: 'Sentient', Georgia, serif; font-size: 1.25rem; color: #26201a; display: block; }
    .slip-serial-time { font-size: 0.75rem; color: #6e665c; }
    .slip-table { width: 100%; border-collapse: collapse; margin: 18px 0; font-size: 0.92rem; }
    .slip-table th, .slip-table td { padding: 10px 8px; border-bottom: 1px solid #d8d1c5; text-align: left; }
    .slip-table th { color: #6e665c; width: 40%; font-weight: 400; }
    .slip-val-lead { font-family: 'Sentient', Georgia, serif; font-size: 1.15rem; color: #26201a; }
    .slip-row-total th, .slip-row-total td { border-top: 1.5px solid #26201a; border-bottom: 0; font-size: 1.25rem; padding: 14px 8px; color: #26201a; }
    .slip-notice { padding: 12px 14px; margin: 16px 0; font-size: 0.86rem; border: 1px solid #d8d1c5; }
    .slip-footer { display: flex; justify-content: space-between; align-items: flex-end; border-top: 1px solid #d8d1c5; padding-top: 16px; margin-top: 20px; flex-wrap: wrap; gap: 14px; }
    .simulated-barcode { display: flex; height: 36px; gap: 2px; align-items: stretch; margin-bottom: 4px; }
    .simulated-barcode .bar { background: #26201a; width: 2px; }
    .simulated-barcode .b2 { width: 3px; }
    .simulated-barcode .b3 { width: 1px; }
    .simulated-barcode .b4 { width: 4px; }
    .barcode-number { font-size: 0.75rem; color: #6e665c; }
    .slip-signature-area { text-align: right; font-size: 0.8rem; }
    .slip-sign-label { font-size: 0.75rem; color: #6e665c; display: block; }
    .slip-sign-officer { font-size: 0.85rem; color: #26201a; margin-top: 4px; display: block; }
    .archival-stamp { display: inline-block; margin-bottom: 14px; }
    .stamp-border { border: 1.5px solid #4c5b6b; padding: 8px 12px; color: #4c5b6b; }
    .actions-bar { margin-top: 20px; text-align: center; }
    .btn { padding: 12px 24px; border: 1px solid #26201a; border-radius: 999px; background: transparent; color: #26201a; font-size: 0.9rem; cursor: pointer; }
    @media print {
      body { background: #fff; padding: 0; }
      .actions-bar { display: none !important; }
      .karcis-wrap { border: 1px solid #000; max-width: 100%; padding: 20px; }
    }
  </style>
</head>
<body>
  <div class="karcis-wrap">
    ${clone.innerHTML}
  </div>
  <div class="actions-bar">
    <button class="btn" onclick="window.print()">Cetak Dokumen Ini</button>
  </div>
</body>
</html>`;

      const blob = new Blob([htmlContent], { type: 'text/html;charset=utf-8' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'Karcis-Resmi-<?= $hasil_kalkulasi['kode_tiket'] ?? 'NGA-PASS' ?>.html';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    }

    function salinTautanKarcis() {
      const link = window.location.origin + window.location.pathname + '#hasil-kalkulasi';
      navigator.clipboard.writeText(link).then(() => {
        alert('Tautan karcis berhasil disalin ke clipboard:\n' + link);
      }).catch(() => {
        prompt('Salin tautan karcis ini:', link);
      });
    }
    </script>

<?php
include "layout/footer.php";
?>
