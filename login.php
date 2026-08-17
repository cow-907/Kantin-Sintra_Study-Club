<?php
require 'includes/init.php';

if (sudahLogin()) {
    if (isAdmin()) {
        header('Location: admin/menu.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

// Guest login flow (Lanjutkan sebagai Pembeli)
if (isset($_GET['guest'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = 2;
    $_SESSION['nama']    = 'Pembeli';
    $_SESSION['email']   = 'pembeli@local';
    $_SESSION['role']    = 'user';
    header('Location: index.php');
    exit;
}

$error = '';

// Admin login flow
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email dan kata sandi wajib diisi.';
    } else {
        session_regenerate_id(true);
        $_SESSION['user_id'] = 1;
        $_SESSION['nama']    = 'Admin';
        $_SESSION['email']   = $email;
        $_SESSION['role']    = 'admin';

        header('Location: admin/menu.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Kantin Sintra</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container min-vh-100 d-flex align-items-center justify-content-center py-4">
    <div class="card product-card p-4" style="max-width:420px; width:100%;">
      <div class="text-center mb-3">
        <h3 class="fw-bold mb-0">Masuk ke Akun</h3>
        <div class="text-muted small">Selamat datang kembali di Kantin Sintra</div>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-danger py-2"><?= bersihkan($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="nama@email.com" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Kata Sandi</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan kata sandi" required>
        </div>

        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-custom-primary">Masuk ➜</button>
        </div>

        <div class="d-grid">
          <a href="login.php?guest=1" class="btn btn-outline-custom">Lanjutkan sebagai Pembeli</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
