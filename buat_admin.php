<?php
/**
 * SCRIPT SEKALI PAKAI - Membuat akun admin pertama.
 *
 * Cara pakai:
 * 1. Pastikan database & tabel sudah dibuat (import database/kantin_sintra.sql)
 * 2. Buka file ini di browser: http://localhost/kantin-sintra-php/buat_admin.php
 * 3. Isi form, submit
 * 4. SETELAH BERHASIL, HAPUS FILE INI dari server (penting untuk keamanan!)
 */

require 'includes/init.php';

// Jika sudah ada admin, jangan izinkan buat lagi lewat halaman ini
$sudahAdaAdmin = (bool) $pdo->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1")->fetchColumn();

$pesan = '';
$sukses = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$sudahAdaAdmin) {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nama === '' || $email === '' || strlen($password) < 6) {
        $pesan = 'Lengkapi semua field. Kata sandi minimal 6 karakter.';
    } else {
        $cek = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $cek->execute([$email]);
        if ($cek->fetch()) {
            $pesan = 'Email sudah dipakai.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$nama, $email, $hash]);
            $sukses = true;
            $pesan = 'Akun admin berhasil dibuat! Silakan login, lalu HAPUS file buat_admin.php ini.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Setup Admin - Kantin Sintra</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5" style="max-width:480px;">
    <div class="card p-4">
      <h4 class="fw-bold mb-3">Setup Akun Admin</h4>

      <?php if ($sudahAdaAdmin && !$sukses): ?>
        <div class="alert alert-warning">Akun admin sudah ada. Untuk keamanan, hapus file <code>buat_admin.php</code> ini dari server.</div>
      <?php else: ?>

        <?php if ($pesan): ?>
          <div class="alert alert-<?= $sukses ? 'success' : 'danger' ?>"><?= bersihkan($pesan) ?></div>
        <?php endif; ?>

        <?php if (!$sukses): ?>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Kata Sandi</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Buat Akun Admin</button>
        </form>
        <?php else: ?>
          <a href="login.php" class="btn btn-success w-100">Ke Halaman Login</a>
        <?php endif; ?>

      <?php endif; ?>
    </div>
  </div>
</body>
</html>
