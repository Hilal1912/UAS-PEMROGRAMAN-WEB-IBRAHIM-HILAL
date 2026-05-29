<?php
// ============================================================
// KONFIGURASI DATABASE
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // Sesuaikan password MySQL Anda
define('DB_NAME', 'db_sampah_liar');

define('SITE_NAME', 'SiLiar');
define('SITE_TITLE', 'Sistem Pelaporan Sampah Liar');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:2rem;background:#ffebee;color:#c62828;border-radius:8px;margin:2rem">
                <strong>Koneksi Database Gagal!</strong><br>
                Pastikan MySQL aktif dan database <em>' . DB_NAME . '</em> sudah dibuat.<br>
                Jalankan file <code>database.sql</code> terlebih dahulu.<br><br>
                Error: ' . htmlspecialchars($e->getMessage()) . '
            </div>');
        }
    }
    return $pdo;
}
