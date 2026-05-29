<?php
// pages/detail.php — Detail Laporan

$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM laporan WHERE id = ?");
$stmt->execute([$id]);
$l = $stmt->fetch();

if (!$l) {
    echo '<div class="alert alert-error">Laporan tidak ditemukan. <a href="index.php?page=laporan">Kembali ke daftar</a></div>';
    return;
}

// Proses update status (admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isAdmin()) {
    $status  = $_POST['status'] ?? '';
    $catatan = trim($_POST['catatan_admin'] ?? '');
    $allowed = ['Menunggu', 'Diproses', 'Selesai'];
    if (in_array($status, $allowed)) {
        $upd = $db->prepare("UPDATE laporan SET status = ?, catatan_admin = ? WHERE id = ?");
        $upd->execute([$status, $catatan, $id]);
        setFlash('success', 'Status laporan #' . $id . ' berhasil diperbarui.');
        redirect('index.php?page=detail&id=' . $id);
    }
}
?>

<div class="card">
    <div class="card-header">
        <div class="card-title">📄 Detail Laporan #<?= $l['id'] ?></div>
        <div style="display:flex;gap:8px">
            <a href="index.php?page=laporan" class="btn btn-outline btn-sm">← Kembali</a>
            <?php if (isAdmin()): ?>
            <a href="index.php?page=hapus&id=<?= $l['id'] ?>"
               class="btn btn-red btn-sm btn-hapus">🗑 Hapus</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="detail-grid">

        <!-- Kolom kiri: informasi -->
        <div>
            <div class="detail-box">
                <div class="detail-row"><span class="dk">ID Laporan</span> <strong>#<?= $l['id'] ?></strong></div>
                <div class="detail-row"><span class="dk">Nama Pelapor</span> <?= clean($l['nama_pelapor']) ?></div>
                <div class="detail-row"><span class="dk">Telepon</span> <?= clean($l['telepon']) ?></div>
                <div class="detail-row"><span class="dk">Jenis</span> <?= jenisBadge($l['jenis']) ?></div>
                <div class="detail-row"><span class="dk">Status</span> <?= statusBadge($l['status']) ?></div>
                <div class="detail-row"><span class="dk">Tanggal Lapor</span> <?= tglIndo($l['created_at'], true) ?></div>
                <div class="detail-row"><span class="dk">Update Terakhir</span> <?= tglIndo($l['updated_at'], true) ?></div>
            </div>

            <div class="detail-box">
                <p style="font-weight:600;margin-bottom:6px">📍 Lokasi</p>
                <p style="font-size:0.9rem"><?= clean($l['alamat']) ?></p>
                <?php if ($l['latitude']): ?>
                <p style="font-size:0.8rem;color:var(--text-muted);margin-top:4px">
                    GPS: <?= clean($l['latitude']) ?>, <?= clean($l['longitude']) ?>
                </p>
                <?php endif; ?>
            </div>

            <div class="detail-box">
                <p style="font-weight:600;margin-bottom:6px">📝 Deskripsi</p>
                <p style="font-size:0.9rem;line-height:1.65"><?= nl2br(clean($l['deskripsi'])) ?></p>
            </div>

            <?php if ($l['catatan_admin']): ?>
            <div class="detail-box blue">
                <p style="font-weight:600;color:var(--blue);margin-bottom:6px">💬 Catatan Admin</p>
                <p style="font-size:0.9rem;color:var(--blue)"><?= nl2br(clean($l['catatan_admin'])) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Kolom kanan: foto + tracking -->
        <div>
            <?php if ($l['foto'] && file_exists(UPLOAD_DIR . $l['foto'])): ?>
            <div class="card" style="padding:0;overflow:hidden;box-shadow:none;border:1.5px solid var(--border);margin-bottom:1rem">
                <img src="<?= UPLOAD_URL . clean($l['foto']) ?>" alt="Foto kondisi sampah" class="foto-full">
                <p style="padding:8px 14px;font-size:0.8rem;color:var(--text-muted)">📷 Foto kondisi sampah</p>
            </div>
            <?php else: ?>
            <div class="no-foto" style="margin-bottom:1rem">Tidak ada foto</div>
            <?php endif; ?>

            <!-- Tracking Status -->
            <div class="card" style="box-shadow:none;border:1.5px solid var(--border)">
                <div class="card-title" style="margin-bottom:0">🔄 Tracking Penanganan</div>
                <ul class="timeline">
                    <li class="done">
                        <strong>Laporan Diterima</strong>
                        <small><?= tglIndo($l['created_at'], true) ?></small>
                    </li>
                    <li class="<?= in_array($l['status'], ['Diproses','Selesai']) ? 'done' : '' ?>">
                        <strong>Sedang Diproses</strong>
                        <small>
                            <?php if ($l['status'] === 'Diproses'): ?>
                                Petugas menuju lokasi
                            <?php elseif ($l['status'] === 'Selesai'): ?>
                                Sudah selesai
                            <?php else: ?>
                                Menunggu penugasan petugas...
                            <?php endif; ?>
                        </small>
                    </li>
                    <li class="<?= $l['status'] === 'Selesai' ? 'done' : '' ?>">
                        <strong>Selesai Ditangani</strong>
                        <?php if ($l['status'] === 'Selesai'): ?>
                        <small><?= tglIndo($l['updated_at'], true) ?></small>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- UPDATE STATUS (admin only) -->
    <?php if (isAdmin()): ?>
    <div class="detail-box" style="background:var(--green-pale);border:1.5px solid #c8e6c9;margin-top:1rem">
        <p style="font-weight:700;color:var(--green);margin-bottom:12px">⚙️ Update Status (Admin)</p>
        <form method="POST" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
            <div class="form-group" style="margin:0;min-width:160px">
                <label>Status Baru</label>
                <select name="status">
                    <option value="Menunggu" <?= $l['status']==='Menunggu'?'selected':'' ?>>Menunggu</option>
                    <option value="Diproses" <?= $l['status']==='Diproses'?'selected':'' ?>>Diproses</option>
                    <option value="Selesai"  <?= $l['status']==='Selesai'?'selected':'' ?>>Selesai</option>
                </select>
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:220px">
                <label>Catatan Admin</label>
                <input type="text" name="catatan_admin"
                       value="<?= clean($l['catatan_admin'] ?? '') ?>"
                       placeholder="Tulis catatan tindakan...">
            </div>
            <button type="submit" class="btn btn-green">💾 Simpan Perubahan</button>
        </form>
    </div>
    <?php endif; ?>
</div>
