<?php
// Header Navigasi
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

$is_kurator = !empty($_SESSION['kurator_logged_in']);

if (!isset($page_title)) {
    $page_title = "Nusantara Gallery Art — Warisan dalam Jangkauan";
}
if (!isset($page_description)) {
    $page_description = "Jelajahi koleksi seni dan warisan budaya Nusantara melalui arsip digital, metadata, dan pameran virtual.";
}
if (!isset($current_page)) {
    $current_page = "";
}
?>
<!DOCTYPE html>
<html lang="id" data-mode="light" data-theme="light">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= htmlspecialchars($page_description) ?>" />
    <title><?= htmlspecialchars($page_title) ?></title>
    <script>
      (function() {
        try {
          var saved = localStorage.getItem('nga_theme') || localStorage.getItem('nga_theme_mode');
          var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
          var mode = saved ? saved : (prefersDark ? 'dark' : 'light');
          document.documentElement.setAttribute('data-theme', mode);
          document.documentElement.setAttribute('data-mode', mode);
          document.documentElement.classList.add('js');
        } catch (e) {}
      })();
    </script>
    <link rel="preconnect" href="https://api.fontshare.com" crossorigin />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://api.fontshare.com/v2/css?f[]=sentient@300,400&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <script src="assets/js/atmosphere.js" defer></script>
    <script src="assets/js/main.js" defer></script>
  </head>
  <body>
    <a class="skip-link" href="#main-content">Lewati ke konten</a>
    <header class="site-header">
      <a class="brand" href="index.php" aria-label="Nusantara Gallery Art, beranda">
        <h1>Nusantara Gallery Art</h1>
      </a>
      <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="global-nav" aria-label="Buka navigasi">
        <span></span><span></span><span></span>
      </button>
      <nav class="global-nav" id="global-nav" aria-label="Navigasi utama">
        <div class="nav-links">
          <a href="index.php"<?= ($current_page === 'beranda') ? ' aria-current="page"' : '' ?>>Beranda</a>
          <a href="catalog.php"<?= ($current_page === 'koleksi') ? ' aria-current="page"' : '' ?>>Koleksi</a>
          <a href="index.php#pameran">Buku Sketsa</a>
          <a href="layanan.php"<?= ($current_page === 'layanan') ? ' aria-current="page"' : '' ?>>Tiket &amp; Layanan</a>
          <a href="about.php"<?= ($current_page === 'tentang') ? ' aria-current="page"' : '' ?>>Tentang</a>
          <?php if ($is_kurator): ?>
            <a href="formulir.php"<?= ($current_page === 'formulir') ? ' aria-current="page"' : '' ?>>Entri GLAM</a>
          <?php endif; ?>
        </div>
        <div class="nav-actions">
          <button class="theme-toggle-btn" type="button" data-theme-toggle aria-label="Ganti Tema">
            Mode Gelap
          </button>
          <?php if ($is_kurator): ?>
            <a href="login.php" class="btn-login">Kurator: <?= htmlspecialchars($_SESSION['kurator_user'] ?? 'Aktif') ?></a>
            <a href="login.php?action=logout" class="text-link" title="Keluar">Keluar</a>
          <?php else: ?>
            <a href="login.php"<?= ($current_page === 'login') ? ' aria-current="page"' : '' ?> class="btn-login">Portal Kurator</a>
          <?php endif; ?>
        </div>
      </nav>
    </header>
