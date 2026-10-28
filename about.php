<?php
// Tentang Kami
$page_title       = "Tentang Kami | Nusantara Gallery Art";
$page_description = "Profil, visi, dan pendekatan GLAM Nusantara Gallery Art.";
$current_page     = "tentang";

include "layout/header.php";
?>

    <main id="main-content" class="page-width">
      <header class="page-title">
        <p class="eyebrow">Profil institusi · GLAM digital</p>
        <h2>Merawat warisan, memperluas akses.</h2>
        <p>Nusantara Gallery Art adalah proyek sistem informasi Galeri, Perpustakaan, Arsip, dan Museum yang mempertemukan koleksi budaya dengan publik dan peneliti.</p>
      </header>
      <section class="about-intro" aria-labelledby="vision-title">
        <h2 id="vision-title">Koleksi menjadi hidup ketika cerita dan konteksnya ikut dibuka.</h2>
        <section class="about-copy">
          <p>Platform ini dirancang sebagai ruang temu antara dokumentasi koleksi dan pengalaman penelusuran yang mudah diakses. Setiap objek dapat dikenali melalui identitas, konteks sejarah, dan catatan kuratorialnya.</p>
          <p>Struktur informasi berlapis membantu pengunjung bergerak dari gambaran umum, ke kategori koleksi, hingga detail rekod. Alur ini mendukung eksplorasi publik tanpa mengorbankan kedalaman yang dibutuhkan dalam kerja akademik.</p>
        </section>
      </section>
      <section class="about-values" aria-label="Pendekatan GLAM">
        <article><p class="eyebrow">01 · Galeri</p><h3>Pengalaman visual</h3><p>Karya disajikan sebagai pintu masuk untuk mengamati bentuk, seniman, dan cerita sejarah.</p></article>
        <article><p class="eyebrow">02 · Perpustakaan &amp; Arsip</p><h3>Informasi yang terstruktur</h3><p>Metadata dan provenance memberi landasan penelusuran serta rujukan bagi kebutuhan riset.</p></article>
        <article><p class="eyebrow">03 · Museum</p><h3>Konteks yang terjaga</h3><p>Catatan interpretatif menghubungkan objek dengan waktu, tempat, dan sudut pandang yang membentuknya.</p></article>
      </section>
      <section class="profile-line" aria-label="Identitas proyek">
        <p><strong>Proyek Sistem Informasi GLAM</strong> · Program Studi Sarjana Terapan Perpustakaan Digital, Universitas Negeri Malang</p>
        <p>Mohammad Yanuar Rizky · NIM 250911718423 · Off: O1HB</p>
      </section>
      <section class="manifesto" aria-labelledby="audiences-title">
        <p class="eyebrow">Untuk pembaca yang ingin melihat lebih dekat</p>
        <h2 id="audiences-title">Dari peneliti yang membutuhkan jejak sumber hingga pelajar yang baru menemukan sebuah karya.</h2>
        <a class="text-link" href="catalog.php">Masuk ke koleksi <span aria-hidden="true">→</span></a>
      </section>
    </main>

<?php
include "layout/footer.php";
?>
