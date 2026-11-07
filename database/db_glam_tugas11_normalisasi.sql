-- Tugas 11: Pemodelan Data Logis (ERD 3NF) - Nusantara Gallery Art
-- Mahasiswa: Mohammad Yanuar Rizky (NIM: 250911718423)
-- Jalankan SETELAH db_glam_250911718423.sql. Bersifat aditif: kolom lama koleksi_seni
-- (kategori_koleksi, seniman_pencipta, lokasi_ruang) dipertahankan agar kode PHP yang ada tetap jalan.

USE `db_glam_nusantaragallery`;
SET NAMES utf8mb4;

-- ---------- Tabel referensi (hasil pemisahan 3NF) ----------
CREATE TABLE IF NOT EXISTS `kurator` (
  `id_kurator` int(11) NOT NULL AUTO_INCREMENT,
  `nip` varchar(30) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `divisi` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  PRIMARY KEY (`id_kurator`),
  UNIQUE KEY `uq_kurator_nip` (`nip`),
  UNIQUE KEY `uq_kurator_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kategori_koleksi` (
  `id_kategori` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(30) NOT NULL,
  PRIMARY KEY (`id_kategori`),
  UNIQUE KEY `uq_nama_kategori` (`nama_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `seniman` (
  `id_seniman` int(11) NOT NULL AUTO_INCREMENT,
  `nama_seniman` varchar(100) NOT NULL,
  PRIMARY KEY (`id_seniman`),
  UNIQUE KEY `uq_nama_seniman` (`nama_seniman`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ruang` (
  `id_ruang` int(11) NOT NULL AUTO_INCREMENT,
  `nama_ruang` varchar(50) NOT NULL,
  PRIMARY KEY (`id_ruang`),
  UNIQUE KEY `uq_nama_ruang` (`nama_ruang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pameran` (
  `id_pameran` int(11) NOT NULL AUTO_INCREMENT,
  `kode_pameran` varchar(20) NOT NULL COMMENT 'Kunci paket_pameran di layanan.php',
  `nama_pameran` varchar(100) NOT NULL,
  `biaya_akses` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_pameran`),
  UNIQUE KEY `uq_kode_pameran` (`kode_pameran`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kategori_pengunjung` (
  `id_kat_pengunjung` int(11) NOT NULL AUTO_INCREMENT,
  `kode_kategori` varchar(20) NOT NULL COMMENT 'Kunci kategori_pengunjung di layanan.php',
  `nama_kategori` varchar(60) NOT NULL,
  `tarif_dasar` int(11) NOT NULL,
  PRIMARY KEY (`id_kat_pengunjung`),
  UNIQUE KEY `uq_kode_kategori` (`kode_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `layanan_tambahan` (
  `id_layanan` int(11) NOT NULL AUTO_INCREMENT,
  `kode_layanan` varchar(20) NOT NULL COMMENT 'Kunci layanan_tambahan di layanan.php',
  `nama_layanan` varchar(100) NOT NULL,
  `biaya_layanan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_layanan`),
  UNIQUE KEY `uq_kode_layanan` (`kode_layanan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Data referensi dari layanan.php, register.php, dan koleksi_seni ----------
INSERT IGNORE INTO `kategori_pengunjung` (`kode_kategori`, `nama_kategori`, `tarif_dasar`) VALUES
('pelajar', 'Pelajar / Mahasiswa', 15000),
('umum', 'Pengunjung Umum (WNI)', 35000),
('mancanegara', 'Wisatawan Mancanegara', 75000),
('peneliti', 'Peneliti / Sivitas Seni', 20000);

INSERT IGNORE INTO `pameran` (`kode_pameran`, `nama_pameran`, `biaya_akses`) VALUES
('tetap', 'Galeri Tetap Seni Nusantara', 0),
('maestro', 'Pameran Retrospeksi Maestro Raden Saleh', 15000),
('digital', 'Paviliun Imersif Digital & 3D Interaktif', 25000),
('manuskrip', 'Ruang Preservasi Naskah & Seni Kuno', 20000);

INSERT IGNORE INTO `layanan_tambahan` (`kode_layanan`, `nama_layanan`, `biaya_layanan`) VALUES
('mandiri', 'Eksplorasi Mandiri (Audio Guide Standar)', 0),
('kurator', 'Pemandu Tur Kurator Galeri (Sesi Kelompok)', 50000),
('katalog', 'Paket Buku Katalog Pameran Cetak Eksklusif', 45000),
('kombinasi', 'Paket Lengkap: Tur Kurator + Buku Katalog', 85000);

INSERT IGNORE INTO `kategori_koleksi` (`nama_kategori`) VALUES ('lukisan'), ('naskah'), ('artefak'), ('wastra');
INSERT IGNORE INTO `seniman` (`nama_seniman`) SELECT DISTINCT `seniman_pencipta` FROM `koleksi_seni`;
INSERT IGNORE INTO `ruang` (`nama_ruang`) SELECT DISTINCT `lokasi_ruang` FROM `koleksi_seni` WHERE `lokasi_ruang` IS NOT NULL;

-- Kurator demo (kredensial sama dengan login.php: kurator.yanel / glam2026, disimpan sebagai hash bcrypt)
INSERT IGNORE INTO `kurator` (`nip`, `nama`, `email`, `divisi`, `password_hash`) VALUES
('250911718423', 'Mohammad Yanuar Rizky', 'kurator.yanel@nusantaragallery.id', 'Galeri Seni',
 '$2y$12$655R2imniR9waWQ5zhlH7ewQ2yZ01/lPHVyzPAqIcYvABNSfQLiFy');

-- ---------- Kunci asing pada koleksi_seni ----------
ALTER TABLE `koleksi_seni`
  ADD COLUMN `id_kategori` int(11) NULL AFTER `kategori_koleksi`,
  ADD COLUMN `id_seniman` int(11) NULL AFTER `seniman_pencipta`,
  ADD COLUMN `id_ruang` int(11) NULL AFTER `lokasi_ruang`,
  ADD COLUMN `id_kurator` int(11) NULL;

UPDATE `koleksi_seni` k
  JOIN `kategori_koleksi` c ON c.nama_kategori = k.kategori_koleksi
  JOIN `seniman` s ON s.nama_seniman = k.seniman_pencipta
  LEFT JOIN `ruang` r ON r.nama_ruang = k.lokasi_ruang
  SET k.id_kategori = c.id_kategori, k.id_seniman = s.id_seniman, k.id_ruang = r.id_ruang,
      k.id_kurator = (SELECT MIN(id_kurator) FROM `kurator`);

ALTER TABLE `koleksi_seni`
  MODIFY `id_kategori` int(11) NOT NULL,
  MODIFY `id_seniman` int(11) NOT NULL,
  MODIFY `id_kurator` int(11) NOT NULL,
  ADD KEY `idx_fk_kategori` (`id_kategori`),
  ADD KEY `idx_fk_seniman` (`id_seniman`),
  ADD KEY `idx_fk_ruang` (`id_ruang`),
  ADD KEY `idx_fk_kurator` (`id_kurator`),
  ADD CONSTRAINT `fk_koleksi_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_koleksi` (`id_kategori`),
  ADD CONSTRAINT `fk_koleksi_seniman` FOREIGN KEY (`id_seniman`) REFERENCES `seniman` (`id_seniman`),
  ADD CONSTRAINT `fk_koleksi_ruang` FOREIGN KEY (`id_ruang`) REFERENCES `ruang` (`id_ruang`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_koleksi_kurator` FOREIGN KEY (`id_kurator`) REFERENCES `kurator` (`id_kurator`);

-- ---------- Entitas asosiatif PAMERAN_KOLEKSI (resolusi M:N) ----------
CREATE TABLE IF NOT EXISTS `pameran_koleksi` (
  `id_pameran` int(11) NOT NULL,
  `id_koleksi` int(11) NOT NULL,
  PRIMARY KEY (`id_pameran`, `id_koleksi`),
  KEY `idx_pk_koleksi` (`id_koleksi`),
  CONSTRAINT `fk_pk_pameran` FOREIGN KEY (`id_pameran`) REFERENCES `pameran` (`id_pameran`) ON DELETE CASCADE,
  CONSTRAINT `fk_pk_koleksi` FOREIGN KEY (`id_koleksi`) REFERENCES `koleksi_seni` (`id_koleksi`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Galeri Tetap = semua koleksi; Maestro = karya Raden Saleh; Digital 3D = artefak; Manuskrip = naskah
INSERT IGNORE INTO `pameran_koleksi` (`id_pameran`, `id_koleksi`)
SELECT p.id_pameran, k.id_koleksi FROM `pameran` p JOIN `koleksi_seni` k ON
  p.kode_pameran = 'tetap'
  OR (p.kode_pameran = 'maestro' AND k.seniman_pencipta LIKE 'Raden Saleh%')
  OR (p.kode_pameran = 'digital' AND k.kategori_koleksi = 'artefak')
  OR (p.kode_pameran = 'manuskrip' AND k.kategori_koleksi = 'naskah');

-- ---------- Transaksi reservasi (layanan.php) ----------
-- Atribut kalkulasi (hari, status, subtotal, diskon, total) tidak disimpan: dihitung dari tarif, jumlah, dan tanggal.
CREATE TABLE IF NOT EXISTS `reservasi` (
  `kode_tiket` varchar(30) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `tanggal_kunjungan` date NOT NULL,
  `jumlah_tiket` int(11) NOT NULL,
  `id_kat_pengunjung` int(11) NOT NULL,
  `id_pameran` int(11) NOT NULL,
  `id_layanan` int(11) NOT NULL,
  PRIMARY KEY (`kode_tiket`),
  KEY `idx_res_kat` (`id_kat_pengunjung`),
  KEY `idx_res_pameran` (`id_pameran`),
  KEY `idx_res_layanan` (`id_layanan`),
  CONSTRAINT `chk_res_jumlah` CHECK (`jumlah_tiket` >= 1),
  CONSTRAINT `fk_res_kat` FOREIGN KEY (`id_kat_pengunjung`) REFERENCES `kategori_pengunjung` (`id_kat_pengunjung`),
  CONSTRAINT `fk_res_pameran` FOREIGN KEY (`id_pameran`) REFERENCES `pameran` (`id_pameran`),
  CONSTRAINT `fk_res_layanan` FOREIGN KEY (`id_layanan`) REFERENCES `layanan_tambahan` (`id_layanan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Riwayat awal dari $riwayat_reservasi; kode tiket memakai rumus yang sama dengan layanan.php
INSERT IGNORE INTO `reservasi` (`kode_tiket`, `nama_pemesan`, `tanggal_kunjungan`, `jumlah_tiket`, `id_kat_pengunjung`, `id_pameran`, `id_layanan`)
SELECT CONCAT('NGA-PASS-2026-', UPPER(SUBSTR(MD5(CONCAT(v.nama, v.tgl)), 1, 8))), v.nama, v.tgl, v.jumlah,
       kp.id_kat_pengunjung, pm.id_pameran, lt.id_layanan
FROM (
  SELECT 'Dewi Sartika Putri' nama, '2026-10-28' tgl, 2 jumlah, 'pelajar' kat, 'tetap' pam, 'mandiri' lay UNION ALL
  SELECT 'SMA Negeri 3 Malang (Rombongan Seni)', '2026-10-31', 12, 'pelajar', 'maestro', 'kurator' UNION ALL
  SELECT 'Dr. Arthur M. Langer', '2026-10-29', 1, 'peneliti', 'manuskrip', 'katalog' UNION ALL
  SELECT 'Alexander van Houten', '2026-11-01', 2, 'mancanegara', 'digital', 'mandiri' UNION ALL
  SELECT 'Komunitas Perupa Nuansa Jawa Timur', '2026-11-07', 8, 'umum', 'maestro', 'kombinasi'
) v
JOIN `kategori_pengunjung` kp ON kp.kode_kategori = v.kat
JOIN `pameran` pm ON pm.kode_pameran = v.pam
JOIN `layanan_tambahan` lt ON lt.kode_layanan = v.lay;
