<?php
require 'includes/init.php';

if (sudahLogin()) {
    header('Location: index.php');
    exit;
}

$error = '';
$nama = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    if ($nama === '' || $email === '' || $password === '') {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Kata sandi minimal 6 karakter.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi kata sandi tidak cocok.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email sudah terdaftar. Silakan login.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$nama, $email, $hash]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['nama']    = $nama;
            $_SESSION['email']   = $email;
            $_SESSION['role']    = 'user';

            header('Location: index.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar Akun - Kantin Sintra</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/kantin-sintra-php/assets/css/style.css">
</head>
<body>
  <div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="card product-card p-4 auth-card">
      <div class="text-center mb-3">
        <h3 class="fw-bold mb-0">Buat Akun Baru</h3>
        <div class="text-muted small">Daftar untuk mulai memesan di Kantin Sintra</div>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-danger py-2"><?= bersihkan($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="mb-3">
          <label for="nama" class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control" id="nama" name="nama" value="<?= bersihkan($nama) ?>" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="nama@email.com" value="<?= bersihkan($email) ?>" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Kata Sandi</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 6 karakter" required>
        </div>

        <div class="mb-3">
          <label for="konfirmasi" class="form-label">Konfirmasi Kata Sandi</label>
          <input type="password" class="form-control" id="konfirmasi" name="konfirmasi" required>
        </div>

        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-custom-primary">Daftar ➜</button>
        </div>

        <div class="text-center small">
          Sudah punya akun? <a href="login.php">Masuk di sini</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
