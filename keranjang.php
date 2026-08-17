<?php
require 'includes/init.php';

$judulHalaman = 'Keranjang Belanja';

$data = detailKeranjang($pdo);
$items = $data['items'];
$subtotal = $data['total'];
$pajak = (int) round($subtotal * 0.10);
$total = $subtotal + $pajak;

require 'includes/header.php';
?>

<div class="container py-5" style="max-width: 1100px;">

  <div class="d-flex align-items-center mb-4">
    <a href="index.php" class="back-link text-dark me-3 d-inline-flex align-items-center text-decoration-none">
      <i class="bi bi-arrow-left fs-4" aria-hidden="true"></i>
      <span class="ms-2 d-none d-sm-inline">Kembali</span>
    </a>
    <h3 class="header-title mb-0">Keranjang Belanja</h3>
  </div>

  <?php if (empty($items)): ?>

    <div class="text-center py-5">
      <i class="bi bi-cart-x" style="font-size:48px; color:var(--text-soft);"></i>
      <p class="text-muted mt-3">Keranjang kamu masih kosong.</p>
      <a href="menu.php" class="btn btn-custom-primary">Mulai Belanja</a>
    </div>

  <?php else: ?>

  <div class="row g-4">

    <!-- Kolom Daftar Produk -->
    <div class="col-lg-8">
      <?php foreach ($items as $item): $p = $item['produk']; ?>
      <div class="cart-item-card">
        <div class="d-flex align-items-center justify-content-between">

          <div class="d-flex align-items-center gap-3">
            <img src="<?= bersihkan($p['gambar']) ?>" alt="<?= bersihkan($p['nama']) ?>" class="item-img">
            <div>
              <h5 class="fw-bold mb-1 fs-6"><?= bersihkan($p['nama']) ?></h5>
              <p class="text-muted small mb-2"><?= bersihkan($p['kategori_nama'] ?? '') ?></p>

              <form action="keranjang_proses.php" method="post" class="d-flex align-items-center gap-1">
                <input type="hidden" name="aksi" value="ubah">
                <input type="hidden" name="produk_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="kembali" value="keranjang.php">
                <button type="submit" name="jumlah" value="<?= $item['jumlah'] - 1 ?>" class="btn btn-qty">-</button>
                <input type="text" class="form-control qty-input p-0" value="<?= $item['jumlah'] ?>" readonly>
                <button type="submit" name="jumlah" value="<?= $item['jumlah'] + 1 ?>" class="btn btn-qty">+</button>
              </form>
            </div>
          </div>

          <div class="text-end d-flex flex-column justify-content-between align-items-end" style="height: 90px;">
            <form action="keranjang_proses.php" method="post">
              <input type="hidden" name="aksi" value="hapus">
              <input type="hidden" name="produk_id" value="<?= $p['id'] ?>">
              <input type="hidden" name="kembali" value="keranjang.php">
              <button type="submit" class="trash-icon btn btn-link p-0" aria-label="Hapus"><i class="bi bi-trash"></i></button>
            </form>
            <div>
              <div class="unit-calc"><?= rupiah($p['harga']) ?> × <?= $item['jumlah'] ?></div>
              <div class="price-green"><?= rupiah($item['subtotal']) ?></div>
            </div>
          </div>

        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Ringkasan Belanja -->
    <div class="col-lg-4">
      <div class="summary-card">
        <h5 class="fw-bold mb-4">Ringkasan Belanja</h5>

        <div class="d-flex justify-content-between mb-2">
          <span class="text-secondary small">Subtotal</span>
          <span class="fw-semibold small"><?= rupiah($subtotal) ?></span>
        </div>

        <div class="d-flex justify-content-between mb-3">
          <span class="text-secondary small">PPN 10%</span>
          <span class="small" style="color:#61c29e;"><?= rupiah($pajak) ?></span>
        </div>

        <hr class="my-3" style="border-color:#eee;">

        <div class="d-flex justify-content-between align-items-center mb-4">
          <span class="fw-bold fs-6">Total</span>
          <span class="price-green fs-5"><?= rupiah($total) ?></span>
        </div>

        <a href="checkout.php" class="btn btn-checkout mb-3 d-block text-center text-decoration-none">Lanjut ke Pembayaran</a>
        <a href="menu.php" class="btn-continue">Lanjut Belanja</a>
      </div>
    </div>

  </div>

  <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
