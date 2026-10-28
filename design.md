# Dokumen Desain Sistem: Nusantara Gallery Art
*Preservasi Digital & Pameran Interaktif Warisan Budaya Nusantara*

Dokumen ini mendefinisikan arah visual, arsitektur informasi, sistem desain, dan spesifikasi interaksi antarmuka untuk platform perpustakaan digital **Nusantara Gallery Art**. Konsep ini mengadopsi metafora *Buku Sketsa Lapangan & Jurnal Kuratorial* (*Archival Field Sketchbook*), terinspirasi dari antarmuka interaktif jurnal visual Meng To dengan adaptasi penuh untuk kebutuhan dokumentasi warisan budaya (GLAM).

---

## 1. Visi & Filosofi Desain

### 1.1 Metafora Utama: *Jurnal Kurator Warisan Nusantara*
Koleksi seni dan artefak sejarah disajikan bukan sekadar deretan kartu grid biasa, melainkan lembaran fisik jurnal preservasi terbuka (*open spread sketchbook*). Pengunjung berperan sebagai kurator atau peneliti yang membuka lembar demi lembar catatan arsip, mengamati detail goresan kuas Raden Saleh, serat kertas daluang manuskrip kuno, hingga tekstur arca batu andesit menggunakan lensa pembesar (*magnifying glass*).

### 1.2 Pilar Pengalaman Pengguna
1. **Keaslian Material (*Tactile Authenticity*)**: Permukaan kertas lawas (*parchment*), lipatan jilid buku, ornamen flora tropis Nusantara di sudut kanvas, dan aksen cap arsip resmi.
2. **Eksplorasi Mendalam (*Active Investigation*)**: Lensa pembesar interaktif yang memperbesar tekstur karya secara mulus (*optical lens zoom*).
3. **Kekayaan Data Perpustakaan (*Archival Precision*)**: Format metadata standar perpustakaan (Dublin Core & MODS: nomor inventaris, provenans, medium, dimensi, dan catatan sejarah).
4. **Performa & Aksesibilitas**: Tanpa pustaka pihak ketiga berbobot berat; menggunakan Vanilla JavaScript, HTML5 Canvas, dan CSS modern yang ringan, responsif, serta mematuhi WCAG 2.1 AA.

---

## 2. Analisis Inspirasi Visual & Interaksi

Berdasarkan referensi video (*Meng To's Sketchbook Landing Page*), terdapat lima elemen inti yang diadopsi ke Nusantara Gallery Art:

| Elemen Inspirasi | Implementasi Nusantara Gallery Art |
| :--- | :--- |
| **Buku Sketsa Terbuka (2 Halaman)** | Buku sketsa horizontal di tengah layar menampilkan karya budaya: halaman kiri untuk visual karya/sketsa, halaman kanan untuk catatan kurator & metadata (atau karya membentang lebar di kedua halaman). |
| **Kaca Pembesar Interaktif (Lup)** | Lensa pembesar dengan bingkai logam kuningan dan gagang kayu yang mengikuti kursor mouse / sentuhan jari, memperlihatkan resolusi tinggi detail goresan kuas, stempel, atau aksara kuno. |
| **Daftar Pelat / Indeks Arsip (*Plate Index*)** | Tampilan indeks editorial bergaya katalog museum: nomor pelat (`01`, `02`, ...), judul karya, daerah asal, dan tahun pembuatan dalam tipografi serif elegan. |
| **Aksen Flora & Kertas Arsip** | Latar belakang krem kertas bertekstur lembut dengan ilustrasi flora Nusantara (pakis, dedaunan rempah, motif ukir tipis) di sudut-sudut bidang pandang. |
| **Catatan Kurator Tempelan (*Ephemera*)** | Catatan tambahan berwujud carik kertas terselip, selotip arsip transparan, atau stempel kurasi resmi program studi perpustakaan. |

---

## 3. Sistem Desain (*Design System*)

### 3.1 Palet Warna

Palet warna menggunakan nuansa kertas alami, pigmen bumi (*earthy pigments*), dan tinta lawas yang ramah di mata:

| Token Warna | Nilai Hex | Peran Penggunaan |
| :--- | :--- | :--- |
| `--bg-desk` | `#F4F0E8` | Warna latar alas kerja / meja arsip |
| `--paper-base` | `#FDFBF7` | Permukaan kertas buku sketsa |
| `--paper-shadow` | `rgba(60, 47, 35, 0.12)` | Bayangan tepi halaman dan lipatan tengah buku |
| `--ink-primary` | `#231F1C` | Teks utama, judul, dan garis batas tegas |
| `--ink-secondary` | `#665E55` | Teks sekunder, deskripsi, dan metadata umum |
| `--ink-muted` | `#968D82` | Label nomor inventaris, placeholder, dan garis pemisah |
| `--accent-terracotta` | `#A85437` | Aksen stempel kuratorial, tombol aksi primer, dan status aktif |
| `--brass-rim` | `#B89358` | Bingkai kuningan pada kaca pembesar |
| `--wood-handle` | `#5C3A21` | Gagang kayu kaca pembesar |

### 3.2 Tipografi

Menggunakan hierarki editorial berkontras tinggi antara serif klasik dan sans-serif fungsional:

1. **Display & Headings**: Serif klasik bernuansa editorial museum (contoh: *Lora*, *Playfair Display*, atau *Cinzel*).
   - Ukuran: Judul Koleksi `2.25rem` (36px), Subjudul `1.25rem` (20px).
2. **Metadata & Kode Arsip**: Monospace proporsional (contoh: *JetBrains Mono* atau *Space Mono*).
   - Ukuran: `0.8rem` (12.8px), `letter-spacing: 0.08em`, huruf kapital untuk nomor registrasi inventaris.
3. **Isi Bacaan (*Body*)**: Sans-serif netral dengan legibilitas tinggi (contoh: *Onest* atau *Inter*).
   - Ukuran: `0.95rem` (15.2px), `line-height: 1.6`.

---

## 4. Arsitektur Informasi & Alur Halaman

```
[ Beranda (Landing Page) ]
   ├── Hero: Buku Sketsa Terbuka Interaktif + Lup Pembesar
   ├── Kontrol Halaman: Navigasi Pelat (Prev / Next, Nomor Halaman)
   ├── Mode Indeks Cepat: Toggle Daftar Koleksi (Table of Plates)
   └── Sorotan Kuratorial & Pengantar GLAM
        │
        ├──> [ Katalog Koleksi (catalog.html) ]
        │      ├── Filter Kategori: Lukisan Klasik, Naskah Kuno, Artefak 3D
        │      ├── Bilah Pencarian Kata Kunci & Metadata
        │      └── Tampilan Kartu Arsip / Tampilan Pelat Buku
        │
        ├──> [ Detail Koleksi (detail.html) ]
        │      ├── Lembaran Arsip Komprehensif
        │      ├── Visualizer Zoom Resolusi Tinggi / Kanvas 3D
        │      ├── Lembar Rekod Metadata Lengkap (Standar Dublin Core)
        │      └── Fitur Cetak Rekod Arsip Resmi (Printable Record)
        │
        ├──> [ Profil Galeri & Metodologi (about.html) ]
        │      ├── Visi Preservasi Digital Sarjana Terapan Perpustakaan Digital UM
        │      └── Metodologi Digitalisasi Warisan Budaya (GLAM)
        │
        └──> [ Portal Kurator (login.html) ]
               └── Autentikasi Pengelola Arsip
```

---

## 5. Spesifikasi Komponen Inti Interaktif

### 5.1 Komponen 1: Kanvas Buku Sketsa (*Sketchbook Spread Viewer*)
- **Rasio Dimensi**: Rentang aspek rasio 16:10 hingga 3:2 dengan sudut melengkung halus (`border-radius: 8px`).
- **Efek Lipatan Tengah (*Spine Binding*)**: Gradien bayangan lembut di garis pemisah tengah buku (`linear-gradient(to right, transparent, rgba(0,0,0,0.1) 48%, rgba(0,0,0,0.2) 50%, rgba(0,0,0,0.1) 52%, transparent)`).
- **Halaman Kiri**: Visual karya seni utama (berbingkai kanvas atau sketsa air) dengan label pelat dan nomor halaman.
- **Halaman Kanan**: Narasi kurator yang diketik ala mesin tik/serif, potongan stempel provenans, dan metadata ringkas.
- **Transisi**: Animasi geser halus (*crossfade spread*) saat berpindah halaman karya.

### 5.2 Komponen 2: Kaca Pembesar Interaktif (*Interactive Magnifier Lens*)
- **Fisik Lensa**: Lingkaran berdiameter 140px – 180px dengan bingkai refleksi kuningan (*metallic brass gradient*) dan gagang miring ke kanan bawah.
- **Optik**: Menampilkan potongan gambar karya dalam resolusi asli (2.0x – 2.5x perbesaran).
- **Interaksi**:
  - Desktop: Posisi lup mengikuti kursor kursor saat berada di atas area buku sketsa.
  - Opsi Tombol Kunci/Lepas: Pengguna dapat mengklik untuk mengunci posisi lup atau menyembunyikannya.
  - Mobile/Touch: Dapat digeser melalui gestur sentuh (*touch drag*) dengan pegangan yang tidak tertutup jari.

### 5.3 Komponen 3: Bilah Kontrol Bawah (*Bottom Archival Controls*)
- **Elemen Kontrol**:
  - Tombol Navigasi: Tanda panah kiri (`←`) dan kanan (`→`).
  - Indikator Pelat: `PELAT 01 DARI 08 — PENANGKAPAN PANGERAN DIPONEGORO`.
  - Tombol Aksi: Ikon daftar indeks pelat (`☰ INDEKS`), tombol zoom layar penuh, dan tautan langsung ke lembar metadata detail.

### 5.4 Komponen 4: Laci Indeks Pelat (*Plates Index Overlay*)
- Saat ikon `INDEKS` ditekan, daftar seluruh koleksi muncul dalam lembaran transparan/kertas:
  - Format tabel nomor urut: `01`, `02`, `03`...
  - Judul karya, pembuat, tahun, dan kategori.
  - Mengklik salah satu baris langsung membuka karya tersebut pada buku sketsa.

---

## 6. Standar Teknis & Aksesibilitas

1. **Struktur Semantik**:
   - Penggunaan tag semantik HTML5 (`<main>`, `<article>`, `<section>`, `<figure>`, `<figcaption>`).
   - Atribut ARIA lengkap (`aria-label`, `aria-expanded`, `aria-controls`, `role="region"`).
2. **Kesesuaian Tampilan (*Responsive Breakpoints*)**:
   - **Desktop (> 1024px)**: Bentang penuh 2 halaman (*spread view*) dengan lup aktif.
   - **Tablet (768px – 1023px)**: Bentang 2 halaman yang diskalakan proporsional.
   - **Ponsel (< 768px)**: Tampilan buku halaman tunggal vertikal bertahap dengan kontrol zoom sentuh.
3. **Preferensi Pengguna (*User Preferences*)**:
   - `prefers-reduced-motion`: Menonaktifkan animasi gerak lup berlebih, menggantinya dengan zoom statis saat diklik.
   - Mode Kontras & Cetak: Format cetak metadata bersih untuk keperluan akademis tanpa elemen antarmuka navigasi.

---

## 7. Rencana Kerja Implementasi Bertahap (*Phased Roadmap*)

| Tahapan | Sasaran Kerja | Luaran Utama |
| :--- | :--- | :--- |
| **Tahap 1** | Penataan Fondasi & Spesifikasi | Dokumen `design.md` disetujui, penyiapan aset tekstur kertas & vektor ornamen. |
| **Tahap 2** | Pembuatan Komponen Buku & Kaca Pembesar | Implementasi visual buku terbuka 2 halaman dan logika JavaScript lensa pembesar di kanvas. |
| **Tahap 3** | Redesain Beranda (`index.html`) | Mengganti hero konvensional dengan *Sketchbook Viewer* interaktif dan kontrol navigasi pelat. |
| **Tahap 4** | Refinement Katalog & Indeks Pelat (`catalog.html`) | Memperbarui daftar koleksi dengan tipografi editorial, filter kategori terpadu, dan pencarian instan. |
| **Tahap 5** | Standarisasi Lembar Rekod Detail (`detail.html`) | Merapikan metadata Dublin Core, catatan sejarah, dan layout cetak resmi dokumentasi perpustakaan. |
| **Tahap 6** | Uji Coba Responsif & Aksesibilitas | Pengujian di berbagai ukuran layar dan validasi performa tanpa pustaka berat. |
