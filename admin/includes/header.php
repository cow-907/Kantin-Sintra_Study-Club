<?php
$halamanAktif = $halamanAktif ?? 'menu';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($judulHalaman) ? htmlspecialchars($judulHalaman) . ' - Admin Kantin Sintra' : 'Admin Kantin Sintra' ?></title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/kantin-sintra-php/assets/css/style.css">
</head>
<body style="background:#F0F7FB;">

<div class="app-shell">
  <div class="app-frame">
    <div class="row g-3">

      <!-- Sidebar -->
      <div class="col-12 col-lg-3">
        <div class="app-header h-100 d-flex flex-column" style="padding:20px 16px;">
          <div class="d-flex align-items-center gap-2 mb-4">
            <div class="brand-icon"><i class="bi bi-shop"></i></div>
            <div class="brand-name">Kantin Sintra</div>
          </div>

          <nav class="d-flex flex-column gap-2">
            <a href="/kantin-sintra-php/admin/menu.php" class="category-pill active justify-content-start text-decoration-none">
              <i class="bi bi-file-earmark-text"></i> Menu
            </a>
          </nav>

          <a href="/kantin-sintra-php/logout.php" class="login-link mt-auto text-decoration-none text-danger" style="padding:10px 20px;">
            <i class="bi bi-box-arrow-left me-1"></i> Keluar
          </a>
        </div>
      </div>

      <!-- Main content -->
      <div class="col-12 col-lg-9">
        <div class="d-flex flex-column gap-3">

          <!-- Topbar -->
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2"
               style="background:linear-gradient(135deg,var(--navy) 0%,var(--navy-2) 100%); border-radius:16px; padding:16px 24px; color:#fff;">
            <h1 class="m-0" style="font-size:20px; font-family:'Poppins',sans-serif; font-weight:700;">Kelola Menu</h1>
            <div class="d-flex align-items-center gap-2"
                 style="background:rgba(255,255,255,0.15); border-radius:999px; padding:6px 14px; color:#fff; font-weight:500; font-size:14px;">
              <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?> <i class="bi bi-chevron-down" style="font-size:11px;"></i>
            </div>
          </div>

          <?php tampilkanFlash(); ?>
