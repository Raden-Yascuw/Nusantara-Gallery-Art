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
    $page_title = "Nusantara Gallery Art | Warisan dalam Jangkauan";
}
if (!isset($page_description)) {
    $page_description = "Jelajahi koleksi seni dan warisan budaya Nusantara melalui arsip digital, metadata, dan pameran virtual.";
}
if (!isset($current_page)) {
    $current_page = "";
}
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= htmlspecialchars($page_description) ?>" />
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Lora:ital,wght@0,400;0,600;1,400&family=Onest:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <script src="assets/js/main.js" defer></script>
  </head>
  <body>
    <a class="skip-link" href="#main-content">Lewati ke konten</a>
    <header class="site-header">
      <a class="brand" href="index.php" aria-label="Nusantara Gallery Art, beranda">
        <h1>Nusantara <span>Gallery Art</span></h1>
      </a>
      <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="global-nav" aria-label="Buka navigasi">
        <span></span><span></span><span></span>
      </button>
      <nav class="global-nav" id="global-nav" aria-label="Navigasi utama">
        <a href="index.php"<?= ($current_page === 'beranda') ? ' aria-current="page"' : '' ?>>Beranda</a>
        <a href="catalog.php"<?= ($current_page === 'koleksi') ? ' aria-current="page"' : '' ?>>Koleksi</a>
        <a href="index.php#pameran">Buku Sketsa &amp; 3D</a>
        <a href="layanan.php"<?= ($current_page === 'layanan') ? ' aria-current="page"' : '' ?>>Tiket &amp; Layanan</a>
        <a href="about.php"<?= ($current_page === 'tentang') ? ' aria-current="page"' : '' ?>>Tentang Kami</a>
        
        <?php if ($is_kurator): ?>
          <!-- Menu khusus kurator yang sudah login -->
          <a href="formulir.php"<?= ($current_page === 'formulir') ? ' aria-current="page"' : '' ?> style="color: var(--accent-vermilion); font-weight: 600;">Entri GLAM</a>
        <?php endif; ?>

        <button class="theme-toggle-btn" type="button" data-theme-toggle aria-label="Ganti Tema">
          Mode Gelap
        </button>

        <?php if ($is_kurator): ?>
          <a href="login.php" class="btn-login" style="background: var(--valid-border); border-color: var(--valid-border);">Kurator: <?= htmlspecialchars($_SESSION['kurator_user'] ?? 'Aktif') ?></a>
          <a href="login.php?action=logout" style="font-size: 0.82rem; color: var(--text-muted); margin-left: 4px;" title="Keluar">Keluar</a>
        <?php else: ?>
          <a href="login.php"<?= ($current_page === 'login') ? ' aria-current="page"' : '' ?> class="btn-login">Portal Kurator</a>
        <?php endif; ?>
      </nav>
    </header>
