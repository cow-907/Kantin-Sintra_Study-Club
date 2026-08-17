<?php
require 'includes/init.php';

$judulHalaman = 'Beranda';
$halamanAktif = 'beranda';

// Ambil kategori untuk pill navigasi
$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY id")->fetchAll();

// Menu populer: ambil 4 produk aktif secara acak/terbaru
$stmt = $pdo->query("SELECT * FROM produk WHERE status = 'aktif' ORDER BY id ASC LIMIT 4");
$produkPopuler = $stmt->fetchAll();

require 'includes/header.php';
?>

    <!--Kata Sambutan-->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
      <div>
        <div class="welcome-eyebrow">Selamat Datang</div>
        <h1 class="welcome-title mb-0">Ayo Pesan Makanan</h1>
      </div>
    </div>

    <!-- Banner Promo -->
    <div class="promo-banner mb-4">
      <div class="row align-items-center">
        <div class="col-lg-7">
          <h2>Pesan Makanan dan Dapatkan Diskon Hingga</h2>
          <div class="discount">Diskon 50%</div>
        </div>
      </div>
      <div class="promo-illustration d-none d-lg-flex">
        <i class="bi bi-people-fill"></i>
      </div>
    </div>

    <!-- Kategori -->
    <div class="mb-3">
      <div class="category-title mb-3">Kategori</div>
      <div class="d-flex flex-wrap gap-2">
        <a class="category-pill btn btn-primary btn-sm" href="menu.php">Semua</a>
        <?php foreach ($kategoriList as $kat): ?>
        <a class="category-pill btn btn-outline-secondary btn-sm" href="menu.php?kategori=<?= $kat['id'] ?>"><?= bersihkan($kat['nama']) ?></a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Menu Populer -->
    <div class="d-flex align-items-center justify-content-between mb-3 mt-4">
      <div class="section-title">Menu Populer</div>
      <a href="menu.php" class="see-all">Lihat Semua</a>
    </div>

    <div class="row g-3">
      <?php if (empty($produkPopuler)): ?>
        <p class="text-muted">Belum ada produk.</p>
      <?php endif; ?>

      <?php foreach ($produkPopuler as $produk): ?>
      <div class="col-6 col-md-3">
        <div class="food-card">
          <div class="badge-row"></div>
          <img src="<?= bersihkan($produk['gambar']) ?>" alt="<?= bersihkan($produk['nama']) ?>">
          <div class="food-name"><?= bersihkan($produk['nama']) ?></div>
          <div class="food-price"><?= rupiah($produk['harga']) ?></div>
          <a href="produk.php?id=<?= $produk['id'] ?>" class="btn-pesan d-block text-center">Pesan</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

<?php require 'includes/footer.php'; ?>
