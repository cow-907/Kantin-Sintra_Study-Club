<?php
require 'includes/init.php';

wajibLogin('checkout.php');

$judulHalaman = 'Checkout';

$data = detailKeranjang($pdo);
$items = $data['items'];
$subtotal = $data['total'];

if (empty($items)) {
    setFlash('error', 'Keranjang kosong, tidak bisa checkout.');
    header('Location: menu.php');
    exit;
}

$pajak = (int) round($subtotal * 0.10);
$total = $subtotal + $pajak;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catatan = bersihkan($_POST['catatan'] ?? '');

    try {
        $pdo->beginTransaction();

        // Kunci & validasi stok setiap item sebelum membuat pesanan
        foreach ($items as $item) {
            $stmt = $pdo->prepare("SELECT stok FROM produk WHERE id = ? FOR UPDATE");
            $stmt->execute([$item['produk']['id']]);
            $stokSaatIni = (int) $stmt->fetchColumn();
            if ($stokSaatIni < $item['jumlah']) {
                throw new Exception('Stok "' . $item['produk']['nama'] . '" tidak mencukupi.');
            }
        }

        // Simpan header pesanan
        $stmt = $pdo->prepare("INSERT INTO pesanan (user_id, total, status, catatan) VALUES (?, ?, 'menunggu', ?)");
        $stmt->execute([$_SESSION['user_id'], $total, $catatan]);
        $pesananId = $pdo->lastInsertId();

        // Simpan detail pesanan & kurangi stok
        $stmtDetail = $pdo->prepare("INSERT INTO pesanan_detail (pesanan_id, produk_id, nama_produk, harga, jumlah, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
        $stmtStok = $pdo->prepare("UPDATE produk SET stok = stok - ? WHERE id = ?");

        foreach ($items as $item) {
            $p = $item['produk'];
            $stmtDetail->execute([$pesananId, $p['id'], $p['nama'], $p['harga'], $item['jumlah'], $item['subtotal']]);
            $stmtStok->execute([$item['jumlah'], $p['id']]);
        }

        $pdo->commit();
        kosongkanKeranjang();

        setFlash('success', 'Pesanan berhasil dibuat! Silakan tunggu konfirmasi dari kantin.');
        header('Location: pesanan_saya.php');
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

require 'includes/header.php';
?>

<div class="container py-5" style="max-width: 900px;">
  <div class="d-flex align-items-center mb-4">
    <a href="keranjang.php" class="back-link text-dark me-3 d-inline-flex align-items-center text-decoration-none">
      <i class="bi bi-arrow-left fs-4"></i>
      <span class="ms-2 d-none d-sm-inline">Kembali</span>
    </a>
    <h3 class="header-title mb-0">Checkout</h3>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= bersihkan($error) ?></div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-lg-7">
      <div class="summary-card mb-3">
        <h5 class="fw-bold mb-3">Ringkasan Pesanan</h5>
        <?php foreach ($items as $item): $p = $item['produk']; ?>
        <div class="d-flex justify-content-between mb-2">
          <span class="small"><?= bersihkan($p['nama']) ?> × <?= $item['jumlah'] ?></span>
          <span class="small fw-semibold"><?= rupiah($item['subtotal']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="summary-card">
        <h5 class="fw-bold mb-3">Informasi Pemesan</h5>
        <p class="mb-1"><strong>Nama:</strong> <?= bersihkan($_SESSION['nama']) ?></p>
        <p class="mb-0"><strong>Email:</strong> <?= bersihkan($_SESSION['email']) ?></p>
      </div>
    </div>

    <div class="col-lg-5">
      <form method="post" class="summary-card">
        <h5 class="fw-bold mb-4">Pembayaran</h5>

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

        <div class="mb-3">
          <label class="form-label small">Catatan (opsional)</label>
          <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: tanpa cabai, kirim ke kelas TI-2A"></textarea>
        </div>

        <button type="submit" class="btn btn-checkout">Buat Pesanan</button>
      </form>
    </div>
  </div>
</div>

<?php require 'includes/footer.php'; ?>
