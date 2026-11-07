<?php
// Registrasi Akun Kurator
$page_title       = "Daftar Akun Kurator Baru — Nusantara Gallery Art";
$page_description = "Registrasi akun pengelola kuratorial dan arsiparis Nusantara Gallery Art.";
$current_page     = "login";

require_once __DIR__ . '/config/auth.php';
$pesan_reg = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $pesan_reg = auth_register(trim($_POST['reg_nama'] ?? ''), trim($_POST['reg_nip'] ?? ''), trim($_POST['reg_email'] ?? ''),
        $_POST['reg_divisi'] ?? '', $_POST['reg_password'] ?? '', $_POST['reg_confirm'] ?? '');
    if ($pesan_reg === null) {
        header('Location: login.php?pesan=terdaftar');
        exit;
    }
}

include "layout/header.php";
?>

    <main id="main-content" class="auth-main page-width">
      <section class="login-section" aria-labelledby="register-title">
        <article class="login-card">
          <div class="auth-header-badge">
            <span class="auth-badge-tag">Sistem Informasi GLAM</span>
            <span class="auth-badge-id">Registrasi Kurator</span>
          </div>

          <h3 id="register-title">Buat Akun Kurator Baru</h3>
          <p class="auth-intro">Daftarkan akun kurator baru untuk mendapatkan akses ke lembar preservasi digital dan sistem entri metadata koleksi GLAM Nusantara Gallery Art.</p>

          <!-- Formulir Pendaftaran Kurator Baru -->
          <form class="login-form" id="register-form" method="POST" action="register.php">
            <div class="form-group">
              <label for="reg-nama">Nama Lengkap Kurator <span class="required-indicator">*</span></label>
              <input
                id="reg-nama"
                type="text"
                name="reg_nama"
                value="<?= htmlspecialchars($_POST['reg_nama'] ?? '') ?>"
                placeholder="Contoh: Mohammad Yanuar Rizky"
                required
                minlength="3"
                autocomplete="name"
              />
            </div>

            <div class="form-group">
              <label for="reg-nip">NIP / Nomor Identitas Pegawai <span class="required-indicator">*</span></label>
              <input
                id="reg-nip"
                type="text"
                name="reg_nip"
                value="<?= htmlspecialchars($_POST['reg_nip'] ?? '') ?>"
                placeholder="Contoh: 250911718423"
                required
                pattern="[0-9]{6,20}"
                title="Masukkan NIP berupa angka (6–20 digit)"
                autocomplete="off"
              />
            </div>

            <div class="form-group">
              <label for="reg-email">Alamat Surel Institusi (Email) <span class="required-indicator">*</span></label>
              <input
                id="reg-email"
                type="email"
                name="reg_email"
                value="<?= htmlspecialchars($_POST['reg_email'] ?? '') ?>"
                placeholder="kurator@institusi.ac.id"
                required
                autocomplete="email"
              />
            </div>

            <div class="form-group">
              <label for="reg-divisi">Divisi Preservasi GLAM <span class="required-indicator">*</span></label>
              <select id="reg-divisi" name="reg_divisi" required>
                <option value="" disabled selected>-- Pilih Divisi Lembaga --</option>
                <option value="Galeri Seni">Galeri Seni (Lukisan &amp; Seni Rupa)</option>
                <option value="Perpustakaan Digital">Perpustakaan Digital (Naskah Kuno &amp; Manuskrip)</option>
                <option value="Pusat Arsip Sejarah">Pusat Arsip Sejarah (Kartografi &amp; Dokumen)</option>
                <option value="Museum Warisan Budaya">Museum Warisan Budaya (Artefak &amp; Pahat Andesit)</option>
              </select>
            </div>

            <div class="form-group">
              <label for="reg-password">Buat Kata Sandi <span class="required-indicator">*</span></label>
              <input
                id="reg-password"
                type="password"
                name="reg_password"
                placeholder="Minimal 6 karakter"
                required
                minlength="6"
                autocomplete="new-password"
              />
            </div>

            <div class="form-group">
              <label for="reg-confirm">Konfirmasi Kata Sandi <span class="required-indicator">*</span></label>
              <input
                id="reg-confirm"
                type="password"
                name="reg_confirm"
                placeholder="Ulangi kata sandi"
                required
                minlength="6"
                autocomplete="new-password"
              />
            </div>

            <button class="button button-full" type="submit">
              Daftarkan Akun &amp; Buka Formulir
            </button>
            <p class="form-message" aria-live="polite" style="color: var(--invalid-border)"><?= htmlspecialchars($pesan_reg) ?></p>
          </form>

          <!-- Tautan kembali ke Login -->
          <p class="auth-switch-text">
            Sudah memiliki akun? <a href="login.php" class="text-link">Masuk ke Portal Kurator</a>
          </p>

          <div class="auth-card-footer">
            <a class="text-link return-link" href="index.php">Kembali ke Beranda Galeri</a>
            <span class="auth-security-note">Terenkripsi Standar Sistem GLAM</span>
          </div>
        </article>
      </section>
    </main>

<?php
include "layout/footer.php";
?>
