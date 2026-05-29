<?php
// ============================================================
// FUNGSI PEMBANTU (HELPER)
// ============================================================

/**
 * Redirect ke URL lain
 */
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

/**
 * Sanitasi input string
 */
function clean(string $str): string {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

/**
 * Cek apakah admin sudah login
 */
function isAdmin(): bool {
    return isset($_SESSION['admin_id']);
}

/**
 * Wajib login admin, redirect jika belum
 */
function requireAdmin(): void {
    if (!isAdmin()) {
        redirect('index.php?page=login');
    }
}

/**
 * Set flash message ke session
 */
function setFlash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

/**
 * Ambil & hapus flash message
 */
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

/**
 * Badge HTML untuk status laporan
 */
function statusBadge(string $status): string {
    $map = [
        'Menunggu' => 'badge-warning',
        'Diproses'  => 'badge-info',
        'Selesai'   => 'badge-success',
    ];
    $cls = $map[$status] ?? 'badge-secondary';
    return "<span class='badge {$cls}'>{$status}</span>";
}

/**
 * Badge HTML untuk jenis laporan
 */
function jenisBadge(string $jenis): string {
    $cls = $jenis === 'TPS Ilegal' ? 'badge-danger' : 'badge-primary';
    return "<span class='badge {$cls}'>{$jenis}</span>";
}

/**
 * Format tanggal ke format Indonesia
 */
function tglIndo(string $datetime, bool $withTime = false): string {
    $bulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $ts = strtotime($datetime);
    $d  = date('d', $ts);
    $m  = $bulan[(int)date('n', $ts)];
    $y  = date('Y', $ts);
    $t  = date('H:i', $ts);
    return $withTime ? "{$d} {$m} {$y}, {$t}" : "{$d} {$m} {$y}";
}

/**
 * Upload foto, kembalikan nama file atau null
 */
function uploadFoto(array $file): ?string {
    if (empty($file['name'])) return null;

    $allowed = ['jpg','jpeg','png','webp','gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed))      return null;
    if ($file['size'] > MAX_FILE_SIZE)  return null;
    if ($file['error'] !== UPLOAD_ERR_OK) return null;

    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

    $filename = 'foto_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $filename)) {
        return $filename;
    }
    return null;
}

/**
 * Hitung statistik laporan
 */
function getStatistik(PDO $db): array {
    $sql = "SELECT
                COUNT(*) AS total,
                SUM(status = 'Menunggu') AS menunggu,
                SUM(status = 'Diproses') AS diproses,
                SUM(status = 'Selesai')  AS selesai,
                SUM(jenis  = 'TPS Ilegal') AS tps_ilegal,
                SUM(jenis  = 'Tumpukan Sampah') AS tumpukan
            FROM laporan";
    return $db->query($sql)->fetch();
}
