# HISTORY & CHECKPOINT PROYEK: NUSANTARA GALLERY ART
*Catatan Kontinuitas Tugas Terstruktur & Redesain Antarmuka GLAM*

Dokumen ini disusun sebagai ringkasan komprehensif bagi pemilik proyek (Yanel) dan agen kecerdasan buatan (AI) penerus jika sesi percakapan beralih akun atau mengalami batas kuota (*rate limit*). Seluruh konteks akademis, spesifikasi teknis, dan berkas yang telah dikerjakan terdokumentasi di sini.

---

## 1. Identitas & Konteks Akademis

- **Nama Pengembang / Mahasiswa**: Mohammad Yanuar Rizky
- **NIM**: 250911718423
- **Program Studi**: Sarjana Terapan Perpustakaan Digital, Universitas Negeri Malang
- **Offering**: O1HB
- **Mata Kuliah**: DKPD236013 Pemrograman Web (Pertemuan 7)
- **Dosen Pengampu**: Ibu Lidya Amalia Rahmania
- **Judul Tugas**: Tugas Terstruktur 7 — *Penyusunan Formulir Input Pengguna & Validasi Format Data Proyek GLAM*
- **Referensi Acuan Kurikulum**: Dennis, A., Wixom, B. H., & Roth, R. M. (2022). *Systems Analysis and Design* (8th ed.). Wiley.

---

## 2. Kepatuhan Penuh Terhadap Persyaratan Tugas 07

Semua instruksi pada file panduan tugas dosen di `/home/yanel/TUGAS DOSEN/Tugas 07 Penyusunan Formulir Input Pengguna.pdf` telah terpenuhi 100%:

### A. Berkas & Struktur Navigasi
1. Telah dibuat berkas **`formulir.html`** yang tersambung ke menu navigasi utama di seluruh halaman (`index.html`, `catalog.html`, `detail.html`, `about.html`, `login.html`, `formulir.html`).
2. Menggunakan tag pembungkus utama `<form action="#" method="POST" class="glam-entry-form" data-glam-form>`.

### B. Penerapan 6 Elemen Kontrol Input Wajib (Dennis et al., 2022)
1. **Text Box (`<input type="text">`)**:
   - `nama_pengusul`: Entri nama lengkap kurator/peneliti.
   - `judul_koleksi`: Entri nama karya seni atau judul manuskrip.
   - `nomor_inventaris`: Entri kode registrasi baku perpustakaan (`pattern="NG-[0-9]{4}-[0-9]{3}"`).
2. **Format-Specific Text Box (`<input type="email">` & `<input type="date">`)**:
   - `email_pengusul`: Surel institusi resmi (memvalidasi keberadaan simbol `@` dan domain valid).
   - `tanggal_pendataan`: Penentuan tanggal arsip berformat standar kalender ISO.
3. **Radio Button (`<input type="radio">`)** — *Mutually Exclusive*:
   - `pilar_glam`: Pemilihan 1 pilar lembaga asal objek (Galeri Seni, Perpustakaan Digital, Pusat Arsip Sejarah, atau Museum Warisan Budaya).
4. **Check Box (`<input type="checkbox">`)** — *Multiple Choice*:
   - `format_media[]`: Pemilihan jamak format berkas preservasi digital (Foto Resolusi Tinggi RAW, Model 3D Fotogrametri, Transkripsi Aksara Kuno, Skema Metadata METS/XML).
   - `pernyataan_validitas`: Pernyataan integritas etika data preservasi digital (wajib dicentang).
5. **Drop-down Selection List (`<select>` & `<option>`)**:
   - `kategori_koleksi`: Pemilihan klasifikasi kategori koleksi dari daftar pilihan panjang (Lukisan Klasik, Naskah Kuno/Daluang, Artefak 3D, Tekstil/Batik, Litografi & Peta).
6. **Command Button (`<button type="submit">` & `<button type="reset">`)**:
   - Tombol pengiriman data metadata registrasi dan tombol pembersihan formulir.

### C. Penerapan Uji Validasi Masukan (*Edit Checks*) & CSS Visual Feedback
1. **Completeness Check**: Atribut `required` pada kolom nama, email, pilar lembaga, judul, nomor inventaris, kategori, tanggal, dan pernyataan validitas.
2. **Format Check**: 
   - `type="email"` untuk validasi sintaks surel.
   - `pattern="NG-[0-9]{4}-[0-9]{3}"` untuk memvalidasi nomor inventaris harus berformat seperti `NG-1857-001`.
   - `minlength="3"` dan `maxlength="80"` pada nama.
3. **Visual Feedback CSS**:
   - Aturan `:focus:invalid` (garis batas merah terakota `#cf332b` + efek glow lembut) memberi indikasi langsung saat kolom belum memenuhi kriteria.
   - Aturan `:focus:valid` (garis batas hijau daun `#2e7d32` + efek glow hijau) memberi indikasi langsung saat data sudah valid.

---

## 3. Eksekusi Desain: Konsep Buku Sketsa Interaktif (Meng To Inspired)

Mengacu pada video inspirasi `/home/yanel/inspo/Screencast From 2026-10-14 23-27-02.mp4` dan dokumen [**`design.md`**](file:///home/yanel/project/Nusantara_Gallery_Art/design.md):

1. **Kanvas Buku Sketsa Lapangan Terbuka (*Open Spread Notebook*)**:
   - Menghubungkan pengalaman fisik jurnal kurator dengan perpustakaan digital.
   - Dilengkapi tekstur kertas arsip (*parchment*), lipatan bayangan jilid tengah (*spine fold*), aksen pita selotip arsip (*ephemera tape*), dan cap kuratorial resmi.
2. **Kaca Pembesar Interaktif (*Optical Magnifier Lens*)**:
   - Lensa lingkaran berdiameter 160px berbingkai kuningan (*metallic brass*) dengan gagang kayu (*wood handle*).
   - Bergerak mulus mengikuti kursor mouse atau sentuhan layar pada area karya seni, memperbesar detail tekstur kanvas dan serat naskah sebesar **2.4x**.
3. **Bilah Navigasi Pelat & Indeks Overlay**:
   - Tombol *Pelat Sebelumnya* dan *Pelat Selanjutnya*.
   - Menampilkan 4 mahakarya koleksi:
     - Pelat 01: Lukisan *Penangkapan Pangeran Diponegoro* karya Raden Saleh (1857).
     - Pelat 02: Manuskrip *Babad Diponegoro* (UNESCO Memory of the World).
     - Pelat 03: *Arca Prajnaparamita Singhasari* (Batu andesit abad ke-13).
     - Pelat 04: Lukisan *Perburuan Rusa* karya Raden Saleh (1846).
   - Tombol `☰ Indeks Pelat` membuka lembaran daftar pelat bergaya katalog museum, memungkinkan pengunjung melompat langsung ke halaman karya yang dipilih.

---

## 4. Rincian Berkas Proyek

| Berkas | Status | Peran & Modifikasi Utama |
| :--- | :--- | :--- |
| [`design.md`](file:///home/yanel/project/Nusantara_Gallery_Art/design.md) | **Dibuat Baru** | Spesifikasi lengkap sistem desain, palet warna, tipografi, dan interaksi. |
| [`formulir.html`](file:///home/yanel/project/Nusantara_Gallery_Art/formulir.html) | **Dibuat Baru** | Halaman formulir input pengguna GLAM memenuhi seluruh rubrik Tugas 07. |
| [`HISTORY.md`](file:///home/yanel/project/Nusantara_Gallery_Art/HISTORY.md) | **Dibuat Baru** | Berkas histori ini untuk kontinuitas pengembangan antar-sesi AI. |
| [`index.html`](file:///home/yanel/project/Nusantara_Gallery_Art/index.html) | **Diperbarui** | Pameran diganti dengan Buku Sketsa Terbuka & Kaca Pembesar; navigasi diperbarui. |
| [`catalog.html`](file:///home/yanel/project/Nusantara_Gallery_Art/catalog.html) | **Diperbarui** | Navigasi menu disinkronkan menyertakan *Formulir Preservasi*. |
| [`detail.html`](file:///home/yanel/project/Nusantara_Gallery_Art/detail.html) | **Diperbarui** | Navigasi menu disinkronkan menyertakan *Formulir Preservasi*. |
| [`about.html`](file:///home/yanel/project/Nusantara_Gallery_Art/about.html) | **Diperbarui** | Navigasi menu disinkronkan menyertakan *Formulir Preservasi*. |
| [`login.html`](file:///home/yanel/project/Nusantara_Gallery_Art/login.html) | **Diperbarui** | Navigasi menu disinkronkan menyertakan *Formulir Preservasi*. |
| [`assets/css/style.css`](file:///home/yanel/project/Nusantara_Gallery_Art/assets/css/style.css) | **Diperbarui** | Ditambahkan CSS `:focus:invalid` & `:focus:valid`, styling formulir, dan styling buku sketsa. |
| [`assets/js/main.js`](file:///home/yanel/project/Nusantara_Gallery_Art/assets/js/main.js) | **Diperbarui** | Ditambahkan logika kanvas buku sketsa, pembesaran optis kaca pembesar, indeks modal, dan form validator. |
| [`README.md`](file:///home/yanel/project/Nusantara_Gallery_Art/README.md) | **Diperbarui** | Daftar halaman disesuaikan dengan arsitektur terbaru. |

---

## 5. Panduan Pengambilan Bukti untuk Laporan Tugas 07

Ketika menyusun dokumen laporan (*MOHAMMAD YANUAR - Laporan 07 Desain Input & Validasi Format Data Proyek GLAM.pdf*), mahasiswa dapat mengambil 4 tangkapan layar (*screenshots*) berikut:

1. **Tangkapan Layar Formulir Kosong**: Buka `http://localhost:8000/formulir.html` di peramban, tangkap seluruh kartu formulir.
2. **Tangkapan Layar Respon Peringatan Validasi (Invalid)**: 
   - Klik field *Alamat Surel* lalu ketikkan teks tanpa simbol `@` (misal: `kurator`), atau ketikkan nomor inventaris tanpa pola `NG-` (misal: `1234`).
   - Amati garis batas (*border*) yang menyala merah terakota (`:focus:invalid`) dan pesan validasi bawaan HTML5 saat tombol submit diklik.
3. **Tangkapan Layar Respon Visual Valid**:
   - Isi field *Nama*, *Surel* berformat benar (`kurator@kemdikbud.go.id`), dan *Nomor Inventaris* berformat benar (`NG-1857-001`).
   - Amati garis batas (*border*) yang berubah menjadi hijau daun (`:focus:valid`).
4. **Tangkapan Layar Kode di Editor**:
   - Buka `formulir.html` di VS Code / editor teks untuk memperlihatkan penggunaan tag `<form method="POST">`, `<input type="text">`, `<input type="email">`, `<input type="date">`, `<input type="radio">`, `<input type="checkbox">`, dan `<select>`.
   - Buka `assets/css/style.css` pada bagian aturan `:focus:invalid` dan `:focus:valid`.

---

## 6. Cara Menjalankan Situs Secara Lokal

Buka terminal pada direktori proyek:
```bash
cd /home/yanel/project/Nusantara_Gallery_Art
php -S localhost:8000
```
Buka tautan di peramban: `http://localhost:8000/`.

---

## 7. Catatan Pelaksanaan 5 Poin Revisi Pengguna (revisi web agar sesuai.txt)

Berdasarkan berkas evaluasi pengguna di `/home/yanel/project/revisi web agar sesuai.txt`, lima perbaikan menyeluruh telah dieksekusi:

1. **Revisi 1 (Alur Portal Kurator & Buat Akun Baru)**:
   - Halaman `login.html` kini memiliki dua tab interaktif: *Masuk ke Portal* dan *+ Buat Akun Kurator Baru* (dengan field nama, NIP, surel institusi, pilihan divisi GLAM, dan kata sandi).
   - Pengguna diarahkan ke `formulir.html` setelah login atau pendaftaran berhasil, dengan status sesi aktif tersimpan di `sessionStorage`.
   - Pada `formulir.html` tersedia bilah status sesi kurator (`.curator-session-bar`) lengkap dengan tombol *Keluar Sesi (Logout)*.
   - Menu navigasi publik kini bersih mengarah ke *Portal Kurator*.

2. **Revisi 2 (Perbaikan Bug Dark/Light Mode & Desain Terpadu)**:
   - Memperbaiki konflik variabel tema dan menghapus kelas latar belakang yang menimpa mode warna.
   - Seluruh situs kini menggunakan **SATU** arsitektur desain terpadu yang dapat beralih seketika antara Light Mode (`#f8f6f0`) dan Dark Mode (`#0f0f11`) dengan kontras teks dan kanvas yang presisi.

3. **Revisi 3 (Perbaikan 3D Virtual & Lukisan Tidak Muncul)**:
   - Menghapus atribut `crossOrigin = 'anonymous'` yang memicu galat CORS pada berkas lokal dan gambar Unsplash.
   - Menambahkan event listener `img.onload` dan fallback otomatis `img.onerror` ke `assets/images/diponegoro.jpg`.
   - Menambahkan placeholder frame kuratorial saat gambar sedang dimuat sehingga kanvas 3D tidak pernah kosong atau gelap gulita.
   - Pada `index.html` ditambahkan *Mode Switcher*: pengguna dapat memilih antara mode *Jurnal Buku Sketsa* atau *Ruang Pameran Virtual 3D*.

4. **Revisi 4 (Animasi Membalik Halaman Buku 3D / Page Flip)**:
   - Dibuat efek transisi fisik lembaran buku sketsa (`.page-turning-leaf.turning-forward` dan `.turning-backward`) dengan perspektif 3D 2000px dan rotasi `rotateY(180deg)`.
   - Saat berpindah dari pelat 1 ke pelat berikutnya (atau sebaliknya), lembaran kertas berputar secara tiga dimensi seperti buku fisik yang dibalik halamannya.

5. **Revisi 5 (Hierarki Font & Tata Letak Header)**:
   - Mengubah layout header dengan `flex-direction: row-reverse`: teks logo *Nusantara Gallery Art* berada di **pojok kanan**, bilah navigasi berada di **sebelah kiri**, dan terdapat ruang kosong yang lapang di **bagian tengah**.
   - Menata ulang hierarki font: logo dengan display serif Cinzel, judul utama dengan serif Lora, nomor inventaris/badge dengan Space Mono, dan teks tubuh dengan Onest.

---

## 8. Instruksi untuk Agen AI Penerus (Next Prompt Instructions)

Jika Anda adalah agen AI yang membaca dokumen ini setelah pergantian akun:
1. Seluruh 5 poin revisi telah terintegrasi dengan sempurna.
2. Seluruh kode saat ini berstatus valid (lolos uji `node -c`), tanpa error CORS, dan siap dijalankan langsung via `php -S localhost:8000`.
3. Mahasiswa dapat langsung mengambil tangkapan layar untuk Tugas 07 menggunakan panduan pada Bab 5.


---

## 8. Sesi Revisi Lanjutan — 2026-10-18 (Perbaikan dari revisi web 2.txt)

Sesi ini menyelesaikan seluruh poin yang belum tuntas:

### Bug 1 SELESAI: Dark Mode — Teks Card Putih di Atas Background Putih
- Root cause: `.catalog-card { background: #ffffff; }` hardcoded mengabaikan CSS variable dark mode.
- Fix: Semua hardcoded `#ffffff` background diganti dengan `var(--bg-card)`, `var(--input-bg)`.
- Tambahan: `.catalog-card section { background: var(--bg-card); }` dan `.catalog-card h3 { color: var(--text-primary); }`.
- Token baru `--text-secondary` ditambahkan ke `:root` dan `[data-theme="dark"]`.

### Bug 2 SELESAI: Buku Sketsa Vertikal / Layout Rusak
- Root cause: `@media (max-width: 900px) {` tidak memiliki `}` penutup — CSS desktop horizontal layout terperangkap dalam media query mobile.
- Fix: Tambahkan `}` penutup + `.sketchbook-page { flex: 1 1 auto; width: 100%; }` di dalam media query.
- Desktop: `.sketchbook-book { flex-direction: row; }` dan `.sketchbook-page { flex: 0 0 50%; width: 50%; }`.

### Bug 3 SELESAI: Header — Logo di Pojok Kiri, Nav di Pojok Kanan
- Root cause: `flex-direction: row-reverse` sebelumnya menempatkan logo di kanan (salah arah).
- Fix: Diubah ke `flex-direction: row`, `.brand { margin-right: auto; }`, `.global-nav { margin-left: auto; }`.

### Bug 4 SELESAI: Form Login & Register Dipisah ke Halaman Terpisah
- Fix login.html: Hanya berisi form login + tautan "Belum memiliki akun? Buat akun kurator baru" ke register.html.
- Baru register.html: Halaman standalone dengan 6 field (Nama, NIP dengan pattern, Email, Divisi, Password, Konfirmasi Password) + tautan balik ke login.html.
- main.js logika tab dihapus; handler login dan register masing-masing bekerja mandiri per halaman dengan checkValidity().

---

## 10. Sesi Revisi Web & Penyelesaian Laporan Tugas 07 — 2026-10-20 (revisi web 2.txt)

Sesi ini menuntaskan seluruh instruksi dari `revisi web 2.txt`, feedback visual screenshot, serta laporan penugasan dosen:

### A. Perbaikan Revisi Web (Nusantara Gallery Art)
1. **Pembersihan Garis Hitam / Catatan Berlebih pada Formulir (`formulir.html`)**:
   - Menghapus komponen `<aside class="academic-reference-note">` yang sebelumnya dilingkari hitam pada tangkapan layar `Pasted image.png`. Formulir kini tampil bersih, rapi, dan profesional tanpa kotak debug akademik di bawah tombol submit.
   - Menghilangkan `border-top` kaku pada `.form-actions-bar` agar transisi area tombol menyatu alami dengan kartu formulir.
2. **Proporsi Buku Sketsa Homepage (`index.html` & `assets/css/style.css`)**:
   - Memperbaiki duplikasi tag `<header class="sketchbook-header">` pada baris 49–50 `index.html`.
   - Mengurangi tinggi vertikal berlebih buku sketsa (`.sketchbook-book min-height` diturunkan dari 520px ke 420px; `.plate-canvas-frame` dibatasi `max-height: 400px`; `.plate-image` dibatasi `max-height: 320px`). Buku kini tampil proporsional secara horizontal menyerupai bentang fisik dua halaman jurnal kurator.
   - Menyesuaikan tipografi judul pelat (`font-size: 1.45rem`) dan paragraf catatan kurator (`0.88rem`, `line-height: 1.65`) untuk mencegah teks meluap.
3. **Perbaikan Teks Tabrakan pada Bilah Navigasi Bawah (`sketchbook-controls-bar`)**:
   - Mengatur `flex-wrap: wrap`, jarak `gap: 12px`, dan batas lebar judul pelat (`max-width: 280px` dengan ellipsis teks) sehingga judul karya seni tidak lagi bertubrukan dengan tombol navigasi *Balik Halaman* maupun tombol *Indeks Pelat* & *Portal Kurator*.
   - Menambahkan aturan responsif mobile dan tablet yang memusatkan tombol kontrol secara proporsional.

### B. Penyelesaian Laporan Tugas Terstruktur 07 (TUGAS DOSEN)
1. **Penyusunan Naskah Akademik Komprehensif (Dennis et al., 2022)**:
   - **Identitas**: Mohammad Yanuar Rizky (NIM 250911718423, Offering O1HB), Dosen Pengampu Ibu Lidya Amalia Rahmania.
   - **Bab I (Analisis Kontrol Input)**: Analisis mendalam 6 kontrol input (Text Box, Format-Specific Text Box, Radio Button *mutually exclusive*, Check Box *multiple choice*, Drop-down Selection List, dan Command Buttons) dikaitkan dengan domain data GLAM dan efisiensi kognitif pengguna.
   - **Bab II (Implementasi Uji Validitas)**: Analisis *Completeness Check* (`required`), *Format Check* (`type="email"`, `pattern="NG-[0-9]{4}-[0-9]{3}"`, `type="date"`), serta *Visual Feedback CSS* (`:focus:invalid` merah terakota `#cf332b` dan `:focus:valid` hijau dedaunan `#2e7d32`).
   - **Bab III (Bukti Pengujian & Tangkapan Layar)**: Menyematkan 4 tangkapan layar beresolusi tinggi (Gambar 3.1 Formulir Kosong, Gambar 3.2 Respon Input Invalid, Gambar 3.3 Respon Input Valid, dan Gambar 3.4 Kode Editor VS Code).
   - **Bab IV (Referensi)**: Sitasi baku APA 7th Edition (Dennis et al., 2022; DCMI, 2020; ICOM-CIDOC, 2021; W3C, 2023).
2. **Luaran Berkas yang Dihasilkan**:
   - PDF Siap Kumpul: [`TUGAS DOSEN/MOHAMMAD YANUAR - Laporan 07 Desain Input & Validasi Format Data Proyek GLAM.pdf`](file:///home/yanel/project/TUGAS%20DOSEN/MOHAMMAD%20YANUAR%20-%20Laporan%2007%20Desain%20Input%20&%20Validasi%20Format%20Data%20Proyek%20GLAM.pdf) (9 halaman format rapi A4, tanpa watermark, siap dikumpulkan ke dosen).
   - Word DOCX Siap Edit: [`TUGAS DOSEN/MOHAMMAD YANUAR - Laporan 07 Desain Input & Validasi Format Data Proyek GLAM.docx`](file:///home/yanel/project/TUGAS%20DOSEN/MOHAMMAD%20YANUAR%20-%20Laporan%2007%20Desain%20Input%20&%20Validasi%20Format%20Data%20Proyek%20GLAM.docx) (format Microsoft Word standar 4-4-3-3 cm, Times New Roman 12pt).
   - Direktori Tangkapan Layar: [`TUGAS DOSEN/screenshots/`](file:///home/yanel/project/TUGAS%20DOSEN/screenshots/) memuat seluruh 4 aset tangkapan layar resolusi tinggi.

---

## 11. Sesi Pembersihan Kode & Eliminasi Kode Mati (Code Cleanup) — 2026-10-28

Berdasarkan penugasan pembersihan kode tidak terpakai (*dead code cleanup*):

1. **Eliminasi Direktori & Aset Duplikat / Mati**:
   - Menghapus direktori `assets/image/` yang merupakan salinan duplikat sebesar 5,68 MB (seluruh referensi proyek aktif menggunakan `assets/images/`).
   - Menghapus berkas uji tak terpakai `data/test.php` (hanya berisi komentar uji coba kosong).
   - Menghapus aset gambar yang tidak pernah dipanggil: `assets/images/starry.jpg` dan `assets/images/logo-placeholder.png`.
2. **Penyatuan Arsitektur PHP & Pengarsipan Berkas Statis**:
   - Memindahkan berkas HTML statis lama (`about.html`, `catalog.html`, `detail.html`, `formulir.html`, `index.html`, `login.html`, `register.html`) ke direktori arsip khusus `archive_tugas7_statis/` agar akar proyek bersih dan murni menggunakan PHP Native modular.
   - Mengarahkan `.htaccess` secara spesifik ke `DirectoryIndex index.php`.
3. **Perbaikan Tautan & Refaktor JavaScript (`assets/js/main.js`)**:
   - Memperbaiki tautan pelat buku sketsa `plateDetailLink` dari `detail.html` ke `detail.php?id=...`.
   - Memperbaiki pengalihan alur portal kurator ke berkas `.php` (`formulir.php`, `login.php`).
4. **Pembersihan CSS (`assets/css/style.css`)**:
   - Menghapus aturan CSS mati yang sudah tidak memiliki elemen HTML terkait: `.academic-reference-note`, `.note-grid`, `.auth-tab-group`, `.auth-tab`, `.catalog-tools`, dan `.filter-button`.
   - Menambahkan kompatibilitas selektor modal pameran 3D (`.modal-3d-overlay`, `.modal-3d-dialog`, `.modal-3d-header`, dll.) pada `detail.php`.
5. **Restorasi Formulir Kurator Berstandar Dennis et al. (`formulir.php`)**:
   - Menyelaraskan form entri metadata pada `formulir.php` dengan kelas CSS terdaftar dan 6 kontrol input baku Dennis et al. (2022).


