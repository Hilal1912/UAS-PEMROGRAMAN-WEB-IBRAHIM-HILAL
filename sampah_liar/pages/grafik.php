<?php
// pages/grafik.php — Grafik & Statistik Pelaporan
requireAdmin();

$stat  = getStatistik($db);
$total = max(1, (int)$stat['total']);

// Laporan per bulan (6 bulan terakhir)
$bulan_res = $db->query("
    SELECT DATE_FORMAT(created_at,'%Y-%m') AS bln, COUNT(*) AS jml
    FROM laporan
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY bln ORDER BY bln ASC
");
$bulan_labels = [];
$bulan_data   = [];
$bulan_names  = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun',
                 '07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'];
foreach ($bulan_res->fetchAll() as $r) {
    [,$m] = explode('-', $r['bln']);
    $bulan_labels[] = ($bulan_names[$m] ?? $m) . ' ' . substr($r['bln'],0,4);
    $bulan_data[]   = (int)$r['jml'];
}

// Per status
$status_data = [
    (int)$stat['menunggu'],
    (int)$stat['diproses'],
    (int)$stat['selesai'],
];

// Per jenis
$jenis_data = [
    (int)$stat['tumpukan'],
    (int)$stat['tps_ilegal'],
];
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:10px">
    <h2 style="font-size:1.3rem;font-weight:700;color:var(--green)">📊 Grafik & Statistik Pelaporan</h2>
    <a href="index.php?page=dashboard" class="btn btn-outline btn-sm">← Dashboard</a>
</div>

<!-- STAT CARDS -->
<div class="stats-grid" style="margin-bottom:1.5rem">
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

<!-- CHART GRID -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem">
    <!-- Donut Status -->
    <div class="card">
        <div class="card-header"><div class="card-title">🍩 Distribusi Status</div></div>
        <div style="display:flex;justify-content:center;padding:1rem 0">
            <canvas id="chartStatus" style="max-height:220px"></canvas>
        </div>
    </div>
    <!-- Pie Jenis -->
    <div class="card">
        <div class="card-header"><div class="card-title">🥧 Distribusi Jenis Laporan</div></div>
        <div style="display:flex;justify-content:center;padding:1rem 0">
            <canvas id="chartJenis" style="max-height:220px"></canvas>
        </div>
    </div>
</div>

<!-- Tren Bulanan -->
<div class="card" style="margin-bottom:1.25rem">
    <div class="card-header"><div class="card-title">📈 Tren Laporan 6 Bulan Terakhir</div></div>
    <div style="padding:1rem">
        <canvas id="chartTren" style="max-height:220px"></canvas>
    </div>
</div>

<!-- Progress Bar Manual -->
<div class="card">
    <div class="card-header"><div class="card-title">📊 Perbandingan Status (Bar)</div></div>
    <div class="chart-bar" style="padding:1.25rem">
        <?php
        $bars = [
            ['label'=>'Menunggu',       'val'=>(int)$stat['menunggu'],   'color'=>'#e65100'],
            ['label'=>'Diproses',        'val'=>(int)$stat['diproses'],   'color'=>'#1565c0'],
            ['label'=>'Selesai',         'val'=>(int)$stat['selesai'],    'color'=>'#2e7d32'],
            ['label'=>'TPS Ilegal',      'val'=>(int)$stat['tps_ilegal'], 'color'=>'#c62828'],
            ['label'=>'Tumpukan Sampah', 'val'=>(int)$stat['tumpukan'],   'color'=>'#6a1b9a'],
        ];
        foreach ($bars as $b):
            $pct = round($b['val'] / $total * 100);
        ?>
        <div class="bar-item">
            <span class="bar-label"><?= $b['label'] ?></span>
            <div class="bar-track">
                <div class="bar-fill" data-width="<?= $pct ?>" style="background:<?= $b['color'] ?>">
                    <?= $pct ?>%
                </div>
            </div>
            <span class="bar-num"><?= $b['val'] ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const fontOpts = { plugins: { legend: { labels: { font: { family: 'Plus Jakarta Sans' } } } } };

// Donut Status
new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: {
        labels: ['Menunggu', 'Diproses', 'Selesai'],
        datasets: [{ data: <?= json_encode($status_data) ?>,
            backgroundColor: ['#e65100','#1565c0','#2e7d32'],
            borderWidth: 3, borderColor: '#fff' }]
    },
    options: { ...fontOpts, cutout: '60%' }
});

// Pie Jenis
new Chart(document.getElementById('chartJenis'), {
    type: 'pie',
    data: {
        labels: ['Tumpukan Sampah', 'TPS Ilegal'],
        datasets: [{ data: <?= json_encode($jenis_data) ?>,
            backgroundColor: ['#6a1b9a','#c62828'],
            borderWidth: 3, borderColor: '#fff' }]
    },
    options: fontOpts
});

// Line Tren Bulanan
new Chart(document.getElementById('chartTren'), {
    type: 'line',
    data: {
        labels: <?= json_encode($bulan_labels ?: ['Belum ada data']) ?>,
        datasets: [{ label: 'Laporan Masuk', data: <?= json_encode($bulan_data ?: [0]) ?>,
            borderColor: '#2e7d32', backgroundColor: 'rgba(46,125,50,0.1)',
            fill: true, tension: 0.4, pointRadius: 5, pointBackgroundColor: '#2e7d32' }]
    },
    options: { ...fontOpts, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
