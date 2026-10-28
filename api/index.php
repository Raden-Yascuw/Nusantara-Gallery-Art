<?php
// Vercel Serverless Gateway Router untuk Nusantara Gallery Art
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');

// Mapping URL ke file PHP
if ($path === '' || $path === 'index' || $path === 'index.php') {
    require __DIR__ . '/../index.php';
    exit;
}

// Normalisasi ekstensi .php
$page = preg_replace('/\.php$/', '', $path);
$target_file = __DIR__ . '/../' . $page . '.php';

if (file_exists($target_file)) {
    require $target_file;
    exit;
}

// 404 jika file tidak ditemukan
http_response_code(404);
echo "404 — Halaman tidak ditemukan";
