<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? clean($pageTitle) . ' — ' : '' ?><?= SITE_TITLE ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="index.php" class="nav-brand">
        <span class="brand-icon">♻</span>
        <span class="brand-name"><?= SITE_NAME ?></span>
        <span class="brand-sub">Pelaporan Sampah Liar</span>
    </a>
    <div class="nav-links">
        <a href="index.php" class="<?= ($activePage ?? '') === 'home' ? 'active' : '' ?>">Beranda</a>
        <a href="index.php?page=laporan" class="<?= ($activePage ?? '') === 'laporan' ? 'active' : '' ?>">Semua Laporan</a>
        <?php if (isAdmin()): ?>
            <a href="index.php?page=dashboard" class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">Dashboard Admin</a>
            <a href="index.php?page=grafik"    class="<?= ($activePage ?? '') === 'grafik'    ? 'active' : '' ?>">Grafik</a>
            <a href="index.php?page=logout" class="nav-logout">Keluar</a>
        <?php else: ?>
            <a href="index.php?page=login" class="<?= ($activePage ?? '') === 'login' ? 'active' : '' ?>">Admin</a>
            <a href="index.php?page=lapor" class="btn-nav-lapor">+ Buat Laporan</a>
        <?php endif; ?>
    </div>
</nav>

<main class="container">

<?php
$flash = getFlash();
if ($flash):
?>
<div class="alert alert-<?= $flash['type'] ?>">
    <?= clean($flash['msg']) ?>
</div>
<?php endif; ?>
