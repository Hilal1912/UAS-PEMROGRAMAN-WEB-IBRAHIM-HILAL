<?php
// pages/lapor.php — Form Buat Laporan

// Proses submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama'] ?? '');
    $telepon   = trim($_POST['telepon'] ?? '');
    $alamat    = trim($_POST['alamat'] ?? '');
    $jenis     = trim($_POST['jenis'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $latitude  = trim($_POST['latitude'] ?? '');
    $longitude = trim($_POST['longitude'] ?? '');

    $errors = [];
    if (!$nama)      $errors[] = 'Nama pelapor wajib diisi.';
    if (!$telepon)   $errors[] = 'Nomor telepon wajib diisi.';
    if (!$alamat)    $errors[] = 'Alamat/lokasi wajib diisi.';
    if (!$jenis)     $errors[] = 'Jenis laporan wajib dipilih.';
    if (!$deskripsi) $errors[] = 'Deskripsi wajib diisi.';

    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
        $foto = uploadFoto($_FILES['foto']);
        if ($foto === null) $errors[] = 'Foto gagal diunggah. Pastikan format JPG/PNG/WEBP dan ukuran maksimal 5 MB.';
    }

    if (empty($errors)) {
        $stmt = $db->prepare("
            INSERT INTO laporan
                (nama_pelapor, telepon, alamat, jenis, deskripsi, foto, latitude, longitude)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nama, $telepon, $alamat, $jenis, $deskripsi, $foto, $latitude ?: null, $longitude ?: null]);
        $newId = $db->lastInsertId();
        setFlash('success', "Laporan berhasil dikirim! Nomor laporan Anda: #$newId. Kami akan segera menindaklanjuti.");
        redirect('index.php?page=laporan');
    }
}
?>

<div class="card">
    <div class="card-header">
        <div class="card-title">📋 Form Laporan Sampah Liar</div>
        <a href="index.php" class="btn btn-outline btn-sm">← Kembali</a>
    </div>

    <p style="font-size:0.88rem;color:var(--text-muted);margin-bottom:1.5rem">
        Isi formulir di bawah ini dengan lengkap dan benar.
        Tanda <span style="color:var(--red)">*</span> wajib diisi.
    </p>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <strong>Terdapat kesalahan:</strong>
        <ul style="margin-top:6px;padding-left:1.2rem">
            <?php foreach ($errors as $err): ?>
            <li><?= clean($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>

        <!-- Identitas Pelapor -->
        <div class="section-title">👤 Identitas Pelapor</div>
        <div class="form-row">
            <div class="form-group">
                <label for="nama">Nama Lengkap <span class="req">*</span></label>
                <input type="text" id="nama" name="nama"
                       value="<?= clean($_POST['nama'] ?? '') ?>"
                       placeholder="Nama lengkap Anda" required>
            </div>
            <div class="form-group">
                <label for="telepon">Nomor Telepon <span class="req">*</span></label>
                <input type="tel" id="telepon" name="telepon"
                       value="<?= clean($_POST['telepon'] ?? '') ?>"
                       placeholder="08xxxxxxxxxx" required>
            </div>
        </div>

        <!-- Data Laporan -->
        <div class="section-title">🗑️ Data Laporan</div>

        <div class="form-group">
            <label for="jenis">Jenis Laporan <span class="req">*</span></label>
            <select id="jenis" name="jenis" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="Tumpukan Sampah" <?= ($_POST['jenis']??'')==='Tumpukan Sampah'?'selected':'' ?>>Tumpukan Sampah</option>
                <option value="TPS Ilegal"      <?= ($_POST['jenis']??'')==='TPS Ilegal'?'selected':'' ?>>TPS Ilegal</option>
            </select>
        </div>

        <div class="form-group">
            <label for="alamat">Alamat / Lokasi Sampah <span class="req">*</span></label>
            <input type="text" id="alamat" name="alamat"
                   value="<?= clean($_POST['alamat'] ?? '') ?>"
                   placeholder="Jl. ..., Kelurahan, Kecamatan, Kota" required>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi / Keterangan <span class="req">*</span></label>
            <textarea id="deskripsi" name="deskripsi" required
                      placeholder="Jelaskan kondisi sampah, perkiraan volume, dampak yang dirasakan warga..."><?= clean($_POST['deskripsi'] ?? '') ?></textarea>
        </div>

        <!-- Foto -->
        <div class="form-group">
            <label for="input-foto">📷 Foto Kondisi <span style="font-weight:400;color:var(--text-muted)">(opsional)</span></label>
            <input type="file" id="input-foto" name="foto" accept="image/jpeg,image/png,image/webp,image/gif">
            <p class="form-hint">Format: JPG, PNG, WEBP. Ukuran maksimal 5 MB.</p>
            <img id="foto-preview" src="" alt="Preview foto">
        </div>

        <!-- GPS -->
        <div class="gps-box">
            <div class="gps-title">📍 Koordinat GPS (Otomatis)</div>
            <div class="form-row" style="margin-bottom:10px">
                <div class="form-group" style="margin-bottom:0">
                    <label for="input-lat">Latitude</label>
                    <input type="text" id="input-lat" name="latitude"
                           value="<?= clean($_POST['latitude'] ?? '') ?>"
                           placeholder="Klik tombol deteksi...">
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label for="input-lng">Longitude</label>
                    <input type="text" id="input-lng" name="longitude"
                           value="<?= clean($_POST['longitude'] ?? '') ?>"
                           placeholder="Klik tombol deteksi...">
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                <button type="button" id="btn-get-loc" class="btn btn-outline btn-sm">
                    📡 Deteksi Lokasi Otomatis
                </button>
                <small id="loc-status" style="font-size:0.82rem"></small>
            </div>
            <div id="map-preview"></div>
        </div>

        <!-- Submit -->
        <div style="display:flex;gap:10px;margin-top:0.5rem">
            <button type="submit" class="btn btn-green btn-lg">📤 Kirim Laporan</button>
            <a href="index.php" class="btn btn-outline btn-lg">Batal</a>
        </div>

    </form>
</div>
