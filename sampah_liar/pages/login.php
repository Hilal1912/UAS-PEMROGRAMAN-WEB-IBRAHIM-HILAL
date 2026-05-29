<?php
// pages/login.php — Login Admin

if (isAdmin()) redirect('index.php?page=dashboard');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $db->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && md5($password) === $admin['password']) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama'];
            setFlash('success', 'Selamat datang, ' . $admin['nama'] . '!');
            redirect('index.php?page=dashboard');
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>

<div class="login-wrap">
    <div class="card">
        <div style="text-align:center;margin-bottom:1.5rem">
            <div style="font-size:2.8rem;margin-bottom:8px">🔐</div>
            <h2 style="font-size:1.3rem;font-weight:700;color:var(--green)">Login Admin</h2>
            <p style="font-size:0.85rem;color:var(--text-muted);margin-top:4px"><?= SITE_TITLE ?></p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error"><?= clean($error) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       value="<?= clean($_POST['username'] ?? '') ?>"
                       placeholder="Masukkan username" autofocus required>
            </div>
            <div class="form-group" style="margin-bottom:1.25rem">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-green btn-block btn-lg">Masuk →</button>
        </form>

        <div class="demo-hint">
            Demo login: <strong>admin</strong> / <strong>admin123</strong>
        </div>
    </div>

    <p style="text-align:center;margin-top:1rem;font-size:0.85rem">
        <a href="index.php" style="color:var(--text-muted)">← Kembali ke Beranda</a>
    </p>
</div>
