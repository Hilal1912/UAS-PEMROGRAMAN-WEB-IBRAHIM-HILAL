<?php
// pages/dashboard.php — Dashboard Admin
requireAdmin();

$stat  = getStatistik($db);
$total = max(1, (int)$stat['total']);

// Filter
$filterStatus = $_GET['status'] ?? '';
$filterJenis  = $_GET['jenis']  ?? '';

$where  = [];
$params = [];
if ($filterStatus) { $where[] = 'status = ?'; $params[] = $filterStatus; }
if ($filterJenis)  { $where[] = 'jenis = ?';  $params[] = $filterJenis; }

$sql = 'SELECT * FROM laporan';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY created_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:10px">
    <h2 style="font-size:1.3rem;font-weight:700;color:var(--green)">📊 Dashboard Admin</h2>
    <span style="font-size:0.85rem;color:var(--text-muted)">
        Login sebagai <strong><?= clean($_SESSION['admin_nama'] ?? 'Admin') ?></strong>
    </span>
</div>

<!-- STATISTIK -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="num"><?= $stat['total'] ?></div>
        <div class="lbl">Total Laporan</div>
    </div>
    <div class="stat-card s-orange">
        <div class="num"><?= $stat['menunggu'] ?></div>
        <div class="lbl">Menunggu</div>
    </div>
    <div class="stat-card s-blue">
        <div class="num"><?= $stat['diproses'] ?></div>
        <div class="lbl">Diproses</div>
    </div>
    <div class="stat-card">
        <div class="num"><?= $stat['selesai'] ?></div>
        <div class="lbl">Selesai</div>
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

<!-- MANAJEMEN LAPORAN -->
<div class="card">
    <div class="card-header">
        <div class="card-title">📋 Manajemen Laporan</div>
    </div>

    <!-- Filter -->
    <form method="GET" action="index.php"
          style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:1.25rem">
        <input type="hidden" name="page" value="dashboard">
        <div>
            <label style="display:block;font-size:0.8rem;margin-bottom:4px;color:var(--text-muted)">Status</label>
            <select name="status">
                <option value="">Semua Status</option>
                <option value="Menunggu" <?= $filterStatus==='Menunggu'?'selected':'' ?>>Menunggu</option>
                <option value="Diproses" <?= $filterStatus==='Diproses'?'selected':'' ?>>Diproses</option>
                <option value="Selesai"  <?= $filterStatus==='Selesai'?'selected':'' ?>>Selesai</option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:0.8rem;margin-bottom:4px;color:var(--text-muted)">Jenis</label>
            <select name="jenis">
                <option value="">Semua Jenis</option>
                <option value="Tumpukan Sampah" <?= $filterJenis==='Tumpukan Sampah'?'selected':'' ?>>Tumpukan Sampah</option>
                <option value="TPS Ilegal"      <?= $filterJenis==='TPS Ilegal'?'selected':'' ?>>TPS Ilegal</option>
            </select>
        </div>
        <div style="display:flex;gap:6px;align-items:flex-end">
            <button type="submit" class="btn btn-green btn-sm">🔍 Filter</button>
            <?php if ($filterStatus || $filterJenis): ?>
            <a href="index.php?page=dashboard" class="btn btn-outline btn-sm">Reset</a>
            <?php endif; ?>
        </div>
    </form>

    <p style="font-size:0.83rem;color:var(--text-muted);margin-bottom:1rem">
        Menampilkan <strong><?= count($rows) ?></strong> laporan
    </p>

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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:2rem">
                    Tidak ada laporan.
                </td></tr>
                <?php else: ?>
                <?php foreach ($rows as $l): ?>
                <tr>
                    <td><?= $l['id'] ?></td>
                    <td>
                        <strong><?= clean($l['nama_pelapor']) ?></strong><br>
                        <small style="color:var(--text-muted)"><?= clean($l['telepon']) ?></small>
                    </td>
                    <td><?= jenisBadge($l['jenis']) ?></td>
                    <td style="max-width:180px"><?= clean(mb_substr($l['alamat'],0,45)) ?>...</td>
                    <td><?= statusBadge($l['status']) ?></td>
                    <td><?= tglIndo($l['created_at']) ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="index.php?page=detail&id=<?= $l['id'] ?>"
                               class="btn btn-blue btn-sm">Detail</a>
                            <a href="index.php?page=hapus&id=<?= $l['id'] ?>"
                               class="btn btn-red btn-sm btn-hapus">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
