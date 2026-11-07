<?php
/**
 * Konfigurasi Koneksi Basis Data Relasional MySQL / MariaDB (XAMPP)
 * Proyek Sistem Informasi GLAM - Nusantara Gallery Art
 * Mahasiswa: Mohammad Yanuar Rizky (NIM: 250911718423)
 */

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'db_glam_nusantaragallery');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Mendapatkan koneksi PDO ke basis data
 * Mengembalikan instance PDO atau null jika server database sedang non-aktif
 */
function get_db_connection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 2, // Timeout cepat untuk fallback
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Fallback anggun (graceful fallback) jika MySQL sedang offline
        error_log("Database connection notice: " . $e->getMessage());
        return null;
    }
}
