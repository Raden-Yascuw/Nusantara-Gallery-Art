<?php
/**
 * Master Data Loader Koleksi Nusantara Gallery Art
 * Memuat 50 koleksi autentik (Lukisan, Naskah Kuno, Artefak 3D, Wastra & Kriya)
 */

$file1 = __DIR__ . '/koleksi_1.json';
$file2 = __DIR__ . '/koleksi_2.json';

$part1 = file_exists($file1) ? (json_decode(file_get_contents($file1), true) ?: []) : [];
$part2 = file_exists($file2) ? (json_decode(file_get_contents($file2), true) ?: []) : [];

$KOLEKSI_DATA = array_merge($part1, $part2);

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
