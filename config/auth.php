<?php
/**
 * Autentikasi kurator: MySQL (tabel `kurator`, sandi bcrypt) dengan fallback akun demo bila DB offline.
 * Sesi dipulihkan dari cookie bertanda tangan HMAC karena Vercel serverless tidak menyimpan sesi PHP.
 */
require_once __DIR__ . '/database.php';

const AUTH_COOKIE = 'nga_kurator_token';
// Hash bcrypt dari 'glam2026' (akun demo kurator.yanel, sama dengan seed di SQL)
const AUTH_DEMO_HASH = '$2y$12$655R2imniR9waWQ5zhlH7ewQ2yZ01/lPHVyzPAqIcYvABNSfQLiFy';

function auth_secret(): string {
    // shortcut: default publik hanya untuk demo, wajib set env SESSION_SECRET di produksi
    return getenv('SESSION_SECRET') ?: 'nga_glam_secret_2026';
}

function auth_b64(string $s): string { return rtrim(strtr(base64_encode($s), '+/', '-_'), '='); }

/** Cari akun berdasarkan email / NIP / username demo, verifikasi sandi. Null bila gagal. */
function auth_verify_login(string $id, string $password): ?array {
    if ($db = get_db_connection()) {
        try {
            $st = $db->prepare('SELECT nip, nama, email, divisi, password_hash FROM kurator WHERE email = ? OR nip = ? LIMIT 1');
            $st->execute([$id, $id]);
            if ($u = $st->fetch()) {
                return password_verify($password, $u['password_hash'])
                    ? ['user' => $u['email'], 'nama' => $u['nama'], 'nip' => $u['nip'], 'divisi' => $u['divisi']] : null;
            }
        } catch (PDOException $e) {
            error_log('auth: ' . $e->getMessage());
        }
    }
    if ($id === 'kurator.yanel' && password_verify($password, AUTH_DEMO_HASH)) {
        return ['user' => 'kurator.yanel', 'nama' => 'Mohammad Yanuar Rizky', 'nip' => '250911718423', 'divisi' => 'Galeri Seni'];
    }
    return null;
}

/** Mulai sesi + cookie bertanda tangan untuk akun $u. */
function auth_login(array $u, string $peran): void {
    session_regenerate_id(true);
    $u['peran'] = $peran;
    $_SESSION['kurator_logged_in'] = true;
    foreach (['user', 'nama', 'nip', 'peran'] as $k) { $_SESSION['kurator_' . $k] = $u[$k]; }
    $payload = auth_b64(json_encode($u + ['exp' => time() + 86400]));
    $secure = !empty($_SERVER['HTTPS']) || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    setcookie(AUTH_COOKIE, $payload . '.' . hash_hmac('sha256', $payload, auth_secret()), [
        'expires' => time() + 86400, 'path' => '/', 'secure' => $secure, 'httponly' => true, 'samesite' => 'Lax',
    ]);
}

/** Pulihkan sesi dari cookie bila perlu. Mengembalikan true bila kurator login. */
function auth_check(): bool {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (empty($_SESSION['kurator_logged_in']) && !empty($_COOKIE[AUTH_COOKIE])) {
        [$payload, $sig] = array_pad(explode('.', $_COOKIE[AUTH_COOKIE], 2), 2, '');
        $u = json_decode((string) base64_decode(strtr($payload, '-_', '+/')), true);
        if (hash_equals(hash_hmac('sha256', $payload, auth_secret()), $sig) && is_array($u) && ($u['exp'] ?? 0) > time()) {
            $_SESSION['kurator_logged_in'] = true;
            foreach (['user', 'nama', 'nip', 'peran'] as $k) { $_SESSION['kurator_' . $k] = $u[$k] ?? ''; }
        }
    }
    return !empty($_SESSION['kurator_logged_in']);
}

function auth_logout(): void {
    $_SESSION = [];
    setcookie(AUTH_COOKIE, '', time() - 42000, '/');
    if (session_status() === PHP_SESSION_ACTIVE) { session_destroy(); }
}

/** Daftarkan kurator baru. Mengembalikan pesan error, atau null bila sukses. */
function auth_register(string $nama, string $nip, string $email, string $divisi, string $pass, string $confirm): ?string {
    if (mb_strlen($nama) < 3 || !preg_match('/^[0-9]{6,20}$/', $nip) || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || $divisi === '' || strlen($pass) < 6) {
        return 'Data tidak valid: nama min. 3 huruf, NIP 6–20 digit, email benar, divisi dipilih, sandi min. 6 karakter.';
    }
    if ($pass !== $confirm) { return 'Konfirmasi kata sandi tidak cocok.'; }
    if (!($db = get_db_connection())) { return 'Pendaftaran memerlukan basis data, yang belum aktif di server ini.'; }
    try {
        $db->prepare('INSERT INTO kurator (nip, nama, email, divisi, password_hash) VALUES (?, ?, ?, ?, ?)')
           ->execute([$nip, $nama, $email, $divisi, password_hash($pass, PASSWORD_DEFAULT)]);
    } catch (PDOException $e) {
        return $e->getCode() === '23000' ? 'NIP atau email sudah terdaftar.' : 'Gagal menyimpan akun, coba lagi.';
    }
    return null;
}
