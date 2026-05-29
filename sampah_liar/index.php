<?php
// ============================================================
// SISTEM PELAPORAN SAMPAH LIAR
// index.php — Router Utama
// ============================================================
// Universitas Islam Negeri Siber Syekh Nurjati Cirebon
// Jurusan Informatika | Matakuliah: Pemrograman Web
// Dosen: Dr. Saluky, M.Kom | Kelas C | Proyek 2
// ============================================================

session_start();

require_once 'includes/config.php';
require_once 'includes/functions.php';

$db = getDB();

// ---- ROUTING ----
$page = $_GET['page'] ?? 'home';

// Logout
if ($page === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Hapus laporan (admin)
if ($page === 'hapus' && isAdmin()) {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        // Hapus foto jika ada
        $stmt = $db->prepare("SELECT foto FROM laporan WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['foto'] && file_exists(UPLOAD_DIR . $row['foto'])) {
            unlink(UPLOAD_DIR . $row['foto']);
        }
        $del = $db->prepare("DELETE FROM laporan WHERE id = ?");
        $del->execute([$id]);
        setFlash('success', "Laporan #$id berhasil dihapus.");
    }
    redirect('index.php?page=dashboard');
}

// Halaman yang valid
$validPages = ['home', 'lapor', 'laporan', 'detail', 'login', 'dashboard', 'grafik'];
if (!in_array($page, $validPages)) {
    $page = 'home';
}

// Judul halaman
$titles = [
    'home'      => 'Beranda',
    'lapor'     => 'Buat Laporan',
    'laporan'   => 'Daftar Laporan',
    'detail'    => 'Detail Laporan',
    'login'     => 'Login Admin',
    'dashboard' => 'Dashboard Admin',
    'grafik'    => 'Grafik Pelaporan',
];
$pageTitle  = $titles[$page] ?? 'Beranda';
$activePage = $page;

// ---- RENDER ----
require_once 'includes/header.php';
require_once 'pages/' . $page . '.php';
require_once 'includes/footer.php';
