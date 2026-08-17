<?php
// Variabel opsional yang bisa di-set sebelum include file ini:
// $halamanAktif = 'beranda' | 'menu' | 'tentang'
$halamanAktif = $halamanAktif ?? '';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($judulHalaman) ? bersihkan($judulHalaman) . ' - Kantin Sintra' : 'Kantin Sintra' ?></title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/kantin-sintra-php/assets/css/style.css">
</head>
<body>

<div class="app-shell">
  <div class="app-frame">

    <header class="app-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
      <a href="/kantin-sintra-php/index.php" class="d-flex align-items-center gap-2 text-decoration-none">
        <div class="brand-icon"><i class="bi bi-shop"></i></div>
        <div>
          <div class="brand-name">Kantin Sintra</div>
          <div class="brand-sub">Kantin Sintra</div>
        </div>
      </a>

      <nav class="main-nav">
        <ul class="nav">
          <li class="nav-item"><a class="nav-link <?= $halamanAktif === 'beranda' ? 'active' : '' ?>" href="/kantin-sintra-php/index.php">Beranda</a></li>
          <li class="nav-item"><a class="nav-link <?= $halamanAktif === 'menu' ? 'active' : '' ?>" href="/kantin-sintra-php/menu.php">Produk</a></li>
          <li class="nav-item"><a class="nav-link <?= $halamanAktif === 'tentang' ? 'active' : '' ?>" href="/kantin-sintra-php/tentang.php">Tentang Kami</a></li>
          <?php if (sudahLogin()): ?>
          <li class="nav-item"><a class="nav-link <?= $halamanAktif === 'pesanan' ? 'active' : '' ?>" href="/kantin-sintra-php/pesanan_saya.php">Pesanan Saya</a></li>
          <?php endif; ?>
        </ul>
      </nav>

      <div class="d-flex align-items-center gap-2">
        <form action="/kantin-sintra-php/menu.php" method="get" class="d-none d-md-flex" style="width:220px;">
          <div class="input-group header-search">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input name="cari" type="search" class="form-control" placeholder="Search" aria-label="Search" value="<?= bersihkan($_GET['cari'] ?? '') ?>">
          </div>
        </form>
        <a href="/kantin-sintra-php/keranjang.php" class="icon-btn position-relative" aria-label="Keranjang">
          <i class="bi bi-cart3"></i>
          <?php $jmlKeranjang = jumlahItemKeranjang(); if ($jmlKeranjang > 0): ?>
          <span class="badge bg-danger rounded-pill position-absolute" style="top:-6px;right:-6px;min-width:20px;"><?= $jmlKeranjang ?></span>
          <?php endif; ?>
        </a>

        <?php if (sudahLogin()): ?>
          <div class="dropdown">
            <a class="login-link text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle me-1"></i><?= bersihkan(explode(' ', $_SESSION['nama'])[0]) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <?php if (isAdmin()): ?>
              <li><a class="dropdown-item" href="/kantin-sintra-php/admin/index.php"><i class="bi bi-speedometer2 me-2"></i>Panel Admin</a></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="/kantin-sintra-php/pesanan_saya.php"><i class="bi bi-receipt me-2"></i>Pesanan Saya</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="/kantin-sintra-php/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a class="login-link text-decoration-none" href="/kantin-sintra-php/login.php">Login</a>
        <?php endif; ?>
      </div>
    </header>

    <?php tampilkanFlash(); ?>
