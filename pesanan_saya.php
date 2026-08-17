<?php
require 'includes/init.php';

$judulHalaman = 'Pesanan Saya';
$halamanAktif = 'pesanan';

$stmt = $pdo->prepare("SELECT * FROM pesanan WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$daftarPesanan = $stmt->fetchAll();

// Ambil detail semua pesanan sekaligus (dikelompokkan per pesanan_id)
$detailPerPesanan = [];
if ($daftarPesanan) {
    $ids = array_column($daftarPesanan, 'id');
    $placeholder = implode(',', array_fill(0, count($ids), '?'));
    $stmtDetail = $pdo->prepare("SELECT * FROM pesanan_detail WHERE pesanan_id IN ($placeholder)");
    $stmtDetail->execute($ids);
    foreach ($stmtDetail->fetchAll() as $d) {
        $detailPerPesanan[$d['pesanan_id']][] = $d;
    }
}

require 'includes/header.php';
?>

<div class="mb-4">
  <h3 class="section-title">Pesanan Saya</h3>
  <div class="text-muted small">Riwayat pesanan kamu di Kantin Sintra</div>
</div>

<?php if (empty($daftarPesanan)): ?>
  <div class="text-center py-5">
    <i class="bi bi-receipt" style="font-size:48px; color:var(--text-soft);"></i>
    <p class="text-muted mt-3">Belum ada pesanan.</p>
    <a href="menu.php" class="btn btn-custom-primary">Mulai Pesan</a>
  </div>
<?php else: ?>

  <?php foreach ($daftarPesanan as $pesanan): ?>
  <div class="summary-card mb-3">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
      <div>
        <div class="fw-bold">Pesanan #<?= $pesanan['id'] ?></div>
        <div class="text-muted small"><?= date('d M Y, H:i', strtotime($pesanan['created_at'])) ?></div>
      </div>
      <span class="status-pill status-<?= $pesanan['status'] ?>"><?= ucfirst($pesanan['status']) ?></span>
    </div>

    <?php foreach ($detailPerPesanan[$pesanan['id']] ?? [] as $d): ?>
    <div class="d-flex justify-content-between mb-1">
      <span class="small"><?= bersihkan($d['nama_produk']) ?> × <?= $d['jumlah'] ?></span>
      <span class="small"><?= rupiah($d['subtotal']) ?></span>
    </div>
    <?php endforeach; ?>

    <?php if (!empty($pesanan['catatan'])): ?>
    <div class="text-muted small mt-2"><strong>Catatan:</strong> <?= bersihkan($pesanan['catatan']) ?></div>
    <?php endif; ?>

    <hr class="my-3" style="border-color:#eee;">
    <div class="d-flex justify-content-between align-items-center">
      <span class="fw-bold small">Total</span>
      <span class="price-green"><?= rupiah($pesanan['total']) ?></span>
    </div>
  </div>
  <?php endforeach; ?>

<?php endif; ?>

<?php require 'includes/footer.php'; ?>
