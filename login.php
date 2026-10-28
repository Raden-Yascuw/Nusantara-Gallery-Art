<?php
// Portal Masuk Kurator
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Aksi Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    setcookie('nga_kurator_token', '', time() - 42000, '/');
    session_destroy();
    header("Location: login.php?pesan=logout");
    exit;
}

$page_title       = "Masuk Portal Kurator | Nusantara Gallery Art";
$page_description = "Portal autentikasi pengelola kuratorial dan arsiparis Nusantara Gallery Art.";
$current_page     = "login";

// Kredensial Pengurus GLAM (Data Master Simulasi)
$master_kurator = [
    'username'  => 'kurator.yanel',
    'password'  => 'glam2026',
    'nama'      => 'Mohammad Yanuar Rizky',
    'nip'       => '250911718423',
    'institusi' => 'Universitas Negeri Malang',
];

$status_login   = null;
$pesan_login    = '';
$input_username = '';

// Proses Autentikasi via POST
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $input_username = trim($_POST['username'] ?? '');
    $input_password = $_POST['password'] ?? '';
    $input_role     = $_POST['role'] ?? 'kurator-utama';

    $daftar_peran = [
        'kurator-utama' => 'Kurator Koleksi & Preservasi Digital',
        'pustakawan'    => 'Pustakawan Digital & Pengkatalog GLAM',
        'konservator'   => 'Konservator Arsip & Benda Budaya',
        'admin'         => 'Administrator Sistem Informasi GLAM',
    ];

    if (empty($input_username) || empty($input_password)) {
        $status_login = 'error';
        $pesan_login  = 'Username dan kata sandi wajib diisi lengkap.';
    } elseif ($input_username === $master_kurator['username'] && $input_password === $master_kurator['password']) {
        $status_login = 'success';
        $nama_aktif   = $master_kurator['nama'];
        $peran_aktif  = $daftar_peran[$input_role] ?? 'Kurator Galeri';
        
        // Simpan sesi login kurator
        $_SESSION['kurator_logged_in'] = true;
        $_SESSION['kurator_user']      = $master_kurator['username'];
        $_SESSION['kurator_nama']      = $master_kurator['nama'];
        $_SESSION['kurator_nip']       = $master_kurator['nip'];
        $_SESSION['kurator_peran']     = $peran_aktif;

        // Token autentikasi untuk kehandalan lingkungan serverless (Vercel)
        $token = hash_hmac('sha256', $master_kurator['username'], 'nga_glam_secret_2026');
        setcookie('nga_kurator_token', $token, time() + 86400, '/', '', false, true);

        $pesan_login  = "Autentikasi berhasil! Selamat bertugas di Ruang Kuratorial, {$nama_aktif}.";
    } else {
        $status_login = 'error';
        $pesan_login  = 'Username atau kata sandi tidak cocok dengan basis data kurator.';
    }
}

// Cek jika sudah dalam keadaan login sebelumnya
$sudah_login = !empty($_SESSION['kurator_logged_in']);

include "layout/header.php";
?>

    <main id="main-content" class="auth-main">
      <section class="login-section" aria-labelledby="login-title">
        <article class="login-card">
          <div class="auth-header-badge">
            <span class="auth-badge-tag">Sistem Informasi GLAM</span>
            <span class="auth-badge-id">AUTH</span>
          </div>

          <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'wajib_login'): ?>
            <div style="background: var(--invalid-bg); border: 1px solid var(--invalid-border); padding: 12px 16px; border-radius: 8px; margin-bottom: 1.2rem; color: var(--invalid-border); font-size: 0.9rem; line-height: 1.5;">
              <strong>Akses Terproteksi:</strong> Halaman Entri Metadata GLAM bersifat tertutup dan hanya dapat diakses setelah kurator berhasil masuk.
            </div>
          <?php elseif (isset($_GET['pesan']) && $_GET['pesan'] === 'logout'): ?>
            <div style="background: var(--valid-bg); border: 1px solid var(--valid-border); padding: 12px 16px; border-radius: 8px; margin-bottom: 1.2rem; color: var(--valid-border); font-size: 0.9rem;">
              <strong>Sesi Selesai:</strong> Anda telah keluar dari sesi kuratorial.
            </div>
          <?php endif; ?>

          <?php if ($status_login === 'success' || $sudah_login): ?>
            <?php
            $nama_user  = $_SESSION['kurator_nama'] ?? $master_kurator['nama'];
            $nip_user   = $_SESSION['kurator_nip'] ?? $master_kurator['nip'];
            $peran_user = $_SESSION['kurator_peran'] ?? 'Kurator Koleksi & Preservasi';
            ?>
            <div style="text-align: center; padding: 1rem 0;">
              <div style="font-family: var(--font-mono); font-size: 0.8rem; letter-spacing: 0.15em; color: var(--accent-gold); margin-bottom: 0.6rem;">PORTAL KURATOR &bull; GLAM</div>
              <h2 id="login-title" style="margin-bottom: 0.4rem;">Sesi Kuratorial Aktif</h2>
              <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.95rem;">
                <?= !empty($pesan_login) ? htmlspecialchars($pesan_login) : "Selamat datang kembali, " . htmlspecialchars($nama_user) . "." ?>
              </p>

              <!-- Kartu Kredensial Pengenal Kurator -->
              <div class="curator-pass-container">
                <div class="curator-pass-header">
                  <span class="curator-pass-chip">TERAUTENTIKASI RESMI</span>
                  <span class="curator-pass-id">ID: KUR-<?= strtoupper(substr(md5($nip_user), 0, 6)) ?></span>
                </div>

                <div class="curator-pass-grid">
                  <div class="curator-pass-row">
                    <span class="curator-pass-label">Nama Pengurus</span>
                    <span class="curator-pass-value"><?= htmlspecialchars($nama_user) ?></span>
                  </div>
                  <div class="curator-pass-row">
                    <span class="curator-pass-label">NIM / NIP</span>
                    <span class="curator-pass-value font-mono"><?= htmlspecialchars($nip_user) ?></span>
                  </div>
                  <div class="curator-pass-row">
                    <span class="curator-pass-label">Peran Kuratorial</span>
                    <span class="curator-pass-value"><?= htmlspecialchars($peran_user) ?></span>
                  </div>
                  <div class="curator-pass-row">
                    <span class="curator-pass-label">Waktu Sesi Masuk</span>
                    <span class="curator-pass-value font-mono"><?= date('d F Y, H:i:s') ?> WIB</span>
                  </div>
                  <div class="curator-pass-row">
                    <span class="curator-pass-label">Tingkat Izin Sistem</span>
                    <span class="curator-pass-value" style="color: var(--valid-border);">&#9679; Akses Penuh Entri Metadata</span>
                  </div>
                </div>
              </div>

              <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a class="button button-primary btn-vermilion" href="formulir.php">
                  Buka Formulir Entri GLAM <span aria-hidden="true">→</span>
                </a>
                <a class="button button-secondary" href="layanan.php">
                  Reservasi Tiket &amp; Layanan
                </a>
              </div>

              <div style="margin-top: 1.5rem;">
                <a class="text-link" href="login.php?action=logout">← Keluar Sesi (Logout)</a>
              </div>
            </div>

          <?php else: ?>
            <h2 id="login-title">Masuk Portal Kurator</h2>
            <p class="auth-intro">Autentikasi akun pengurus GLAM untuk mengakses panel preservasi dan formulir entri metadata tertutup.</p>

            <?php if ($status_login === 'error'): ?>
              <div style="background: var(--invalid-bg); border: 1px solid var(--invalid-border); padding: 12px 16px; border-radius: 8px; margin-bottom: 1.2rem; color: var(--invalid-border); font-size: 0.9rem;">
                <strong>Autentikasi Gagal:</strong> <?= htmlspecialchars($pesan_login) ?>
              </div>
            <?php endif; ?>

            <form class="login-form" method="POST" action="login.php" novalidate>
              <div class="form-group">
                <label for="username">Username / Identitas Kurator <span class="required-indicator">*</span></label>
                <input
                  id="username"
                  type="text"
                  name="username"
                  autocomplete="username"
                  placeholder="Contoh: kurator.yanel"
                  required
                  value="<?= htmlspecialchars($input_username) ?>"
                />
              </div>

              <div class="form-group">
                <label for="password">Kata Sandi Akses <span class="required-indicator">*</span></label>
                <input
                  id="password"
                  type="password"
                  name="password"
                  autocomplete="current-password"
                  placeholder="Masukkan kata sandi pengurus"
                  required
                />
              </div>

              <div class="form-group">
                <label for="role">Hak Akses Peran Kuratorial</label>
                <select id="role" name="role">
                  <option value="kurator-utama" selected>Kurator Koleksi &amp; Preservasi Digital</option>
                  <option value="pustakawan">Pustakawan Digital &amp; Pengkatalog GLAM</option>
                  <option value="konservator">Konservator Arsip &amp; Benda Budaya</option>
                  <option value="admin">Administrator Sistem Informasi GLAM</option>
                </select>
              </div>

              <button class="button button-primary btn-vermilion button-full" type="submit">
                Masuk ke Portal Pengurus <span aria-hidden="true">→</span>
              </button>
            </form>

            <div style="margin-top: 1.2rem; padding: 12px 14px; background: var(--bg-surface); border: 1px dashed var(--border-color); border-radius: 8px; font-size: 0.83rem; color: var(--text-muted); line-height: 1.5;">
              <strong>Kredensial Pengurus:</strong><br />
              Username: <code style="font-family: var(--font-mono); color: var(--accent-vermilion);">kurator.yanel</code> &nbsp;|&nbsp; Password: <code style="font-family: var(--font-mono); color: var(--accent-vermilion);">glam2026</code>
            </div>

            <p class="auth-switch-text">
              Memerlukan pendaftaran kurator baru? <a href="register.php" class="text-link">Ajukan registrasi pengurus →</a>
            </p>
          <?php endif; ?>

          <div class="auth-card-footer">
            <a class="text-link return-link" href="index.php">← Kembali ke Beranda Galeri</a>
            <span class="auth-security-note">Terenkripsi Standar Sistem GLAM</span>
          </div>
        </article>
      </section>
    </main>

<?php
include "layout/footer.php";
?>
