# Nusantara Gallery Art

Digital cultural heritage gallery prototype for the GLAM information-systems project.

## Pages

- `index.php` is the home page with the interactive open sketchbook spread and optical magnifying glass lens.
- `catalog.php` provides keyword search, era slider, and category filtering across 50 cultural collections.
- `detail.php` presents the featured object's metadata and interactive 3D virtual exhibition modal.
- `formulir.php` provides the protected curator input form for GLAM collection metadata entry with input validation (Dennis et al., 2022).
- `layanan.php` provides exhibition ticket reservation, weekend surcharge calculation, and group discount logic.
- `about.php` describes the institutional profile and GLAM approach.
- `login.php` & `register.php` are the curator authentication portal interfaces.
- `archive_tugas7_statis/` contains archived static HTML files from Pertemuan 07.
- `design.md` defines the complete UI design system, palette, and sketchbook interaction specifications.
- `HISTORY.md` documents project chronology, academic compliance, and continuity checkpoints.

## Run locally

Serve the project root with PHP built-in server or XAMPP:

```sh
/opt/lampp/bin/php -S localhost:8000
```

Then open `http://localhost:8000`.

## Notes

The curator login is a front-end demonstration and does not authenticate users. Production use requires a server-side identity service. The catalog's additional sample artwork images are loaded from Unsplash; the featured Diponegoro image is stored locally in `assets/images/`.
## Pembaruan Pertemuan 09: Logika Pemrosesan Sisi Server (PHP Native)

Pada penugasan **Tugas Terstruktur 9: Pemrograman Web (DKPD236013)**, arsitektur situs telah ditingkatkan ke sisi server (*back-end*) menggunakan skrip PHP Native:

1. **Modularisasi Layout (`include`)**:
   - `layout/header.php` : Modul `<head>`, branding GLAM, toggle mode gelap, dan navigasi aktif.
   - `layout/footer.php` : Modul hak cipta akademik digital (`date('Y')`) dan penutup dokumen.
2. **Implementasi Logika Bisnis Layanan Galeri (`layanan.php`)**:
   - Menghitung estimasi reservasi karcis kunjungan pameran dan layanan kuratorial seni budaya secara dinamis di sisi server.
   - Mengakomodasi diferensiasi tarif kategori pengunjung (Pelajar Rp 15.000, Umum Rp 35.000, Mancanegara Rp 75.000, Peneliti Rp 20.000), sayap pameran (Koleksi Tetap, Pameran Maestro, Paviliun Digital 3D, Galeri Naskah), penyesuaian akhir pekan (Weekend surcharge Rp 5.000/tiket), diskon rombongan (15% untuk &ge; 5 orang), dan opsi pemandu tur kurator ahli.
   - Memenuhi 3 kontrol algoritma pemrograman terstruktur Arthur M. Langer (2008): *Sequence*, *Selection*, dan *Iteration*.
3. **Autentikasi Sisi Server (`login.php`)**:
   - Validasi kredensial pengurus/kurator GLAM secara dinamis (*Username:* `kurator.yanel`, *Password:* `glam2026`).
4. **Halaman Beranda Dinamis (`index.php`)**, **Katalog Dinamis (`catalog.php`)**, dan **Profil Lembaga (`about.php`)**.

### Menjalankan di XAMPP:
Proyek telah disinkronkan ke direktori server lokal XAMPP:
- `http://localhost/Nusantara_Gallery_Art/`
- `http://localhost/250911718423_MohammadYanuarRizky_Tugas9/`
