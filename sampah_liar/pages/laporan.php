<?php
// pages/laporan.php — Daftar Semua Laporan

// Filter
$filterStatus = $_GET['status'] ?? '';
$filterJenis  = $_GET['jenis'] ?? '';
$search       = trim($_GET['q'] ?? '');

$where  = [];
$params = [];

if ($filterStatus) { $where[] = 'status = ?'; $params[] = $filterStatus; }
if ($filterJenis)  { $where[] = 'jenis = ?';  $params[] = $filterJenis; }
if ($search) {
    $where[] = '(nama_pelapor LIKE ? OR alamat LIKE ? OR deskripsi LIKE ?)';
    $like = "%$search%";
    $params = array_merge($params, [$like, $like, $like]);
}

$sql  = 'SELECT * FROM laporan';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY created_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <div class="card-title">📋 Daftar Semua Laporan</div>
        <a href="index.php?page=lapor" class="btn btn-green btn-sm">+ Buat Laporan</a>
    </div>

    <!-- Filter -->
    <form method="GET" action="index.php"
          style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:1.25rem;align-items:flex-end">
        <input type="hidden" name="page" value="laporan">
        <div style="flex:1;min-width:180px">
            <label style="display:block;font-size:0.8rem;margin-bottom:4px;color:var(--text-muted)">Cari</label>
            <input type="text" name="q" value="<?= clean($search) ?>" placeholder="Nama, alamat, deskripsi...">
        </div>
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
            <?php if ($filterStatus || $filterJenis || $search): ?>
            <a href="index.php?page=laporan" class="btn btn-outline btn-sm">Reset</a>
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
                <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:2.5rem">
                    Tidak ada laporan ditemukan.
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
                    <td><?= clean(mb_substr($l['alamat'], 0, 45)) ?>...</td>
                    <td><?= statusBadge($l['status']) ?></td>
                    <td><?= tglIndo($l['created_at']) ?></td>
                    <td>
                        <a href="index.php?page=detail&id=<?= $l['id'] ?>"
                           class="btn btn-blue btn-sm">Detail</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
