<?php
/**
 * Master Data Loader Koleksi Nusantara Gallery Art
 * Mendukung basis data relasional MySQL (db_glam_nusantaragallery)
 * dengan fallback otomatis ke JSON lokal jika server offline.
 * 
 * Pertemuan 10 - Konfigurasi Basis Data Relasional GLAM
 * Mahasiswa: Mohammad Yanuar Rizky (NIM: 250911718423)
 */

require_once __DIR__ . '/../config/database.php';

$file1 = __DIR__ . '/koleksi_1.json';
$file2 = __DIR__ . '/koleksi_2.json';

$part1 = file_exists($file1) ? (json_decode(file_get_contents($file1), true) ?: []) : [];
$part2 = file_exists($file2) ? (json_decode(file_get_contents($file2), true) ?: []) : [];

$KOLEKSI_DATA = array_merge($part1, $part2);

// Indeks data JSON berdasarkan nomor inventaris untuk mempertahankan slug & metadata ekstra
$JSON_BY_INV = [];
foreach ($KOLEKSI_DATA as $k => $v) {
    if (!empty($v['inventaris'])) {
        $JSON_BY_INV[$v['inventaris']] = $k;
    }
}

// Jika koneksi basis data MySQL aktif, jadikan tabel koleksi_seni sebagai sumber utama
$db = get_db_connection();
if ($db !== null) {
    try {
        $stmt = $db->query("SELECT * FROM koleksi_seni ORDER BY id_koleksi ASC");
        $db_rows = $stmt->fetchAll();
        if (!empty($db_rows)) {
            $synced = [];
            foreach ($db_rows as $row) {
                $inv = $row['nomor_inventaris'];
                $slug = $JSON_BY_INV[$inv] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $row['judul_koleksi']), '-'));
                $base = $KOLEKSI_DATA[$slug] ?? [];
                $kategori_code = $row['kategori_koleksi'];
                $synced[$slug] = array_merge($base, [
                    'id'            => $slug,
                    'id_koleksi'    => (int)$row['id_koleksi'],
                    'inventaris'    => $inv,
                    'judul'         => $row['judul_koleksi'],
                    'kategori'      => ucfirst($kategori_code),
                    'kategori_code' => $kategori_code,
                    'pencipta'      => $row['seniman_pencipta'],
                    'tahun'         => $row['tahun_perolehan'] ? ($row['tahun_perolehan'] . ' M') : '-',
                    'tahun_num'     => (int)$row['tahun_perolehan'],
                    'asal'          => $row['asal_daerah'] ?: '-',
                    'medium'        => $row['medium_teknik'] ?: '-',
                    'dimensi'       => $row['dimensi'] ?: '-',
                    'ruang3D'       => $row['lokasi_ruang'] ?: 'RUANG 01 — LUKISAN SEJARAH',
                    'catatan'       => $row['deskripsi'] ?: '',
                    'gambar'        => $row['gambar'] ?: 'assets/images/diponegoro.jpg',
                    'tanggal_masuk' => $row['tanggal_masuk'],
                ]);
            }
            $KOLEKSI_DATA = $synced;
        }
    } catch (Exception $e) {
        // Gunakan fallback data lokal jika terjadi kendala kueri
    }
}

function normalize_kategori_koleksi(string $kat): string {
    $map = ['tekstil' => 'wastra', 'litografi' => 'artefak'];
    $kat = strtolower(trim($kat));
    $kat = $map[$kat] ?? $kat;
    return in_array($kat, ['lukisan', 'naskah', 'artefak', 'wastra'], true) ? $kat : 'lukisan';
}

function get_koleksi_row_by_id(int $id_koleksi): ?array {
    if ($db = get_db_connection()) {
        $st = $db->prepare("SELECT * FROM koleksi_seni WHERE id_koleksi = ?");
        $st->execute([$id_koleksi]);
        if ($r = $st->fetch()) return $r;
    }
    global $KOLEKSI_DATA;
    $idx = 1;
    foreach ($KOLEKSI_DATA as $item) {
        $cid = $item['id_koleksi'] ?? $idx;
        if ($cid === $id_koleksi) {
            return [
                'id_koleksi'       => $cid,
                'nomor_inventaris' => $item['inventaris'] ?? '',
                'judul_koleksi'    => $item['judul'] ?? '',
                'kategori_koleksi' => $item['kategori_code'] ?? 'lukisan',
                'seniman_pencipta' => $item['pencipta'] ?? '',
                'tahun_perolehan'  => $item['tahun_num'] ?? 1900,
                'asal_daerah'      => $item['asal'] ?? '',
                'medium_teknik'    => $item['medium'] ?? '',
                'dimensi'          => $item['dimensi'] ?? '',
                'lokasi_ruang'     => $item['ruang3D'] ?? '',
                'deskripsi'        => $item['catatan'] ?? '',
                'gambar'           => $item['gambar'] ?? 'assets/images/diponegoro.jpg',
            ];
        }
        $idx++;
    }
    return null;
}

function save_koleksi_db(array $data, ?int $id_koleksi = null): array {
    $inv     = strtoupper(trim($data['nomor_inventaris'] ?? ''));
    $judul   = trim($data['judul_koleksi'] ?? '');
    $kat     = normalize_kategori_koleksi($data['kategori_koleksi'] ?? 'lukisan');
    $seniman = trim($data['seniman_pencipta'] ?? '') ?: 'Anonim Nusantara';
    $tahun   = (int)($data['tahun_perolehan'] ?? 0) ?: (preg_match('/NG-([0-9]{4})-/', $inv, $m) ? (int)$m[1] : (int)date('Y'));
    $asal    = trim($data['asal_daerah'] ?? '') ?: 'Nusantara';
    $medium  = trim($data['medium_teknik'] ?? '') ?: 'Media campuran';
    $dimensi = trim($data['dimensi'] ?? '') ?: '-';
    $ruang   = trim($data['lokasi_ruang'] ?? '') ?: 'RUANG 01 — LUKISAN SEJARAH';
    $desk    = trim($data['deskripsi'] ?? '');
    $gambar  = trim($data['gambar'] ?? '') ?: 'assets/images/diponegoro.jpg';

    if ($judul === '' || !preg_match('/^NG-[0-9]{4}-[0-9]{3}$/', $inv)) {
        return ['ok' => false, 'msg' => 'Judul wajib diisi dan Nomor Inventaris wajib berformat NG-YYYY-XXX.'];
    }
    if (!($db = get_db_connection())) {
        return ['ok' => false, 'msg' => 'Basis data MySQL belum terhubung. Perubahan memerlukan koneksi MySQL aktif.'];
    }

    try {
        // Cek apakah skema 3NF Tugas 11 (kolom id_kategori, id_seniman, id_ruang, id_kurator) tersedia
        $has_3nf = (bool)$db->query("SHOW COLUMNS FROM koleksi_seni LIKE 'id_kategori'")->fetch();
        $fk_vals = [];
        if ($has_3nf) {
            $db->prepare("INSERT IGNORE INTO kategori_koleksi (nama_kategori) VALUES (?)")->execute([$kat]);
            $st = $db->prepare("SELECT id_kategori FROM kategori_koleksi WHERE nama_kategori = ?");
            $st->execute([$kat]);
            $fk_vals['id_kategori'] = (int)$st->fetchColumn();

            $db->prepare("INSERT IGNORE INTO seniman (nama_seniman) VALUES (?)")->execute([$seniman]);
            $st = $db->prepare("SELECT id_seniman FROM seniman WHERE nama_seniman = ?");
            $st->execute([$seniman]);
            $fk_vals['id_seniman'] = (int)$st->fetchColumn();

            $db->prepare("INSERT IGNORE INTO ruang (nama_ruang) VALUES (?)")->execute([$ruang]);
            $st = $db->prepare("SELECT id_ruang FROM ruang WHERE nama_ruang = ?");
            $st->execute([$ruang]);
            $fk_vals['id_ruang'] = (int)$st->fetchColumn();

            $fk_vals['id_kurator'] = (int)$db->query("SELECT MIN(id_kurator) FROM kurator")->fetchColumn() ?: 1;
        }

        if ($id_koleksi) {
            if ($has_3nf) {
                $sql = "UPDATE koleksi_seni SET nomor_inventaris=?, judul_koleksi=?, kategori_koleksi=?, id_kategori=?,
                        seniman_pencipta=?, id_seniman=?, tahun_perolehan=?, asal_daerah=?, medium_teknik=?, dimensi=?,
                        lokasi_ruang=?, id_ruang=?, deskripsi=?, gambar=? WHERE id_koleksi=?";
                $db->prepare($sql)->execute([$inv, $judul, $kat, $fk_vals['id_kategori'], $seniman, $fk_vals['id_seniman'],
                    $tahun, $asal, $medium, $dimensi, $ruang, $fk_vals['id_ruang'], $desk, $gambar, $id_koleksi]);
            } else {
                $sql = "UPDATE koleksi_seni SET nomor_inventaris=?, judul_koleksi=?, kategori_koleksi=?, seniman_pencipta=?,
                        tahun_perolehan=?, asal_daerah=?, medium_teknik=?, dimensi=?, lokasi_ruang=?, deskripsi=?, gambar=?
                        WHERE id_koleksi=?";
                $db->prepare($sql)->execute([$inv, $judul, $kat, $seniman, $tahun, $asal, $medium, $dimensi, $ruang, $desk, $gambar, $id_koleksi]);
            }
            return ['ok' => true, 'id' => $id_koleksi, 'msg' => "Koleksi \"$judul\" ($inv) berhasil diperbarui."];
        } else {
            if ($has_3nf) {
                $sql = "INSERT INTO koleksi_seni (nomor_inventaris, judul_koleksi, kategori_koleksi, id_kategori,
                        seniman_pencipta, id_seniman, tahun_perolehan, asal_daerah, medium_teknik, dimensi,
                        lokasi_ruang, id_ruang, deskripsi, gambar, id_kurator)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $db->prepare($sql)->execute([$inv, $judul, $kat, $fk_vals['id_kategori'], $seniman, $fk_vals['id_seniman'],
                    $tahun, $asal, $medium, $dimensi, $ruang, $fk_vals['id_ruang'], $desk, $gambar, $fk_vals['id_kurator']]);
            } else {
                $sql = "INSERT INTO koleksi_seni (nomor_inventaris, judul_koleksi, kategori_koleksi, seniman_pencipta,
                        tahun_perolehan, asal_daerah, medium_teknik, dimensi, lokasi_ruang, deskripsi, gambar)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $db->prepare($sql)->execute([$inv, $judul, $kat, $seniman, $tahun, $asal, $medium, $dimensi, $ruang, $desk, $gambar]);
            }
            return ['ok' => true, 'id' => (int)$db->lastInsertId(), 'msg' => "Koleksi baru \"$judul\" ($inv) berhasil disimpan ke basis data."];
        }
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            return ['ok' => false, 'msg' => "Nomor inventaris $inv sudah digunakan oleh koleksi lain."];
        }
        return ['ok' => false, 'msg' => 'Gagal menyimpan koleksi: ' . $e->getMessage()];
    }
}

function delete_koleksi_db(int $id_koleksi): array {
    if (!($db = get_db_connection())) {
        return ['ok' => false, 'msg' => 'Basis data MySQL belum terhubung. Penghapusan memerlukan koneksi MySQL aktif.'];
    }
    try {
        $row = get_koleksi_row_by_id($id_koleksi);
        if (!$row) {
            return ['ok' => false, 'msg' => 'Koleksi tidak ditemukan.'];
        }
        $st = $db->prepare("DELETE FROM koleksi_seni WHERE id_koleksi = ?");
        $st->execute([$id_koleksi]);
        return ['ok' => true, 'msg' => "Koleksi \"{$row['judul_koleksi']}\" ({$row['nomor_inventaris']}) berhasil dihapus."];
    } catch (PDOException $e) {
        return ['ok' => false, 'msg' => 'Gagal menghapus koleksi: ' . $e->getMessage()];
    }
}

/**
 * Mendapatkan koleksi berdasarkan ID
 */
function get_koleksi_by_id($id) {
    global $KOLEKSI_DATA;
    return $KOLEKSI_DATA[$id] ?? null;
}

/**
 * Menghitung total koleksi per kategori
 */
function get_koleksi_counts() {
    global $KOLEKSI_DATA;
    $counts = [
        'all'     => count($KOLEKSI_DATA),
        'lukisan' => 0,
        'naskah'  => 0,
        'artefak' => 0,
        'wastra'  => 0,
    ];
    foreach ($KOLEKSI_DATA as $item) {
        $cat = $item['kategori_code'] ?? '';
        if (isset($counts[$cat])) {
            $counts[$cat]++;
        }
    }
    return $counts;
}
