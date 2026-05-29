<?php
// pages/home.php — Halaman Beranda
$stat = getStatistik($db);

// Ambil 5 laporan terbaru
$stmt = $db->query("SELECT * FROM laporan ORDER BY created_at DESC LIMIT 5");
$recent = $stmt->fetchAll();
?>

<!-- HERO -->
<div class="hero">
    <h1>♻ Sistem Pelaporan Sampah Liar</h1>
    <p>Laporkan tumpukan sampah atau TPS ilegal di sekitar Anda. Setiap laporan akan segera ditindaklanjuti oleh petugas kebersihan.</p>
    <a href="index.php?page=lapor" class="btn btn-white btn-lg">📋 Buat Laporan Sekarang</a>
</div>

<!-- STATISTIK -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="num"><?= $stat['total'] ?></div>
        <div class="lbl">Total Laporan</div>
    </div>
    <div class="stat-card s-orange">
        <div class="num"><?= $stat['menunggu'] ?></div>
        <div class="lbl">Menunggu Tindak</div>
    </div>
    <div class="stat-card s-blue">
        <div class="num"><?= $stat['diproses'] ?></div>
        <div class="lbl">Sedang Diproses</div>
    </div>
    <div class="stat-card">
        <div class="num"><?= $stat['selesai'] ?></div>
        <div class="lbl">Selesai Ditangani</div>
    </div>
    <div class="stat-card s-red">
        <div class="num"><?= $stat['tps_ilegal'] ?></div>
        <div class="lbl">TPS Ilegal</div>
    </div>
    <div class="stat-card s-purple">
        <div class="num"><?= $stat['tumpukan'] ?></div>
        <div class="lbl">Tumpukan Sampah</div>
    </div>
</div>

<!-- GRAFIK -->
<div class="card">
    <div class="card-header">
        <div class="card-title">📊 Grafik Pelaporan</div>
    </div>
    <?php
    $total = max(1, (int)$stat['total']);
    $bars = [
        ['label'=>'Menunggu',        'val'=>(int)$stat['menunggu'],   'color'=>'#e65100'],
        ['label'=>'Diproses',         'val'=>(int)$stat['diproses'],   'color'=>'#1565c0'],
        ['label'=>'Selesai',          'val'=>(int)$stat['selesai'],    'color'=>'#2e7d32'],
        ['label'=>'TPS Ilegal',       'val'=>(int)$stat['tps_ilegal'], 'color'=>'#c62828'],
        ['label'=>'Tumpukan Sampah',  'val'=>(int)$stat['tumpukan'],   'color'=>'#6a1b9a'],
    ];
    ?>
    <div class="chart-bar">
        <?php foreach ($bars as $b):
            $pct = round($b['val'] / $total * 100);
        ?>
        <div class="bar-item">
            <span class="bar-label"><?= $b['label'] ?></span>
            <div class="bar-track">
                <div class="bar-fill"
                     data-width="<?= $pct ?>"
                     style="background:<?= $b['color'] ?>">
                    <?= $pct ?>%
                </div>
            </div>
            <span class="bar-num"><?= $b['val'] ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- LAPORAN TERBARU -->
<div class="card">
    <div class="card-header">
        <div class="card-title">📋 Laporan Terbaru</div>
        <a href="index.php?page=laporan" class="btn btn-outline btn-sm">Lihat Semua →</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pelapor</th>
                    <th>Jenis</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent)): ?>
                <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:2rem">Belum ada laporan.</td></tr>
                <?php else: ?>
                <?php foreach ($recent as $l): ?>
                <tr>
                    <td><?= $l['id'] ?></td>
                    <td><strong><?= clean($l['nama_pelapor']) ?></strong></td>
                    <td><?= jenisBadge($l['jenis']) ?></td>
                    <td><?= clean(mb_substr($l['alamat'], 0, 40)) ?>...</td>
                    <td><?= statusBadge($l['status']) ?></td>
                    <td><?= tglIndo($l['created_at']) ?></td>
                    <td><a href="index.php?page=detail&id=<?= $l['id'] ?>" class="btn btn-blue btn-sm">Detail</a></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
