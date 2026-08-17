<?php
require '../includes/init.php';
wajibAdmin();

$judulHalaman = 'Dashboard';
$halamanAktif = 'dashboard';

$totalProduk   = (int) $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
$totalPesanan  = (int) $pdo->query("SELECT COUNT(*) FROM pesanan")->fetchColumn();
$pesananBaru   = (int) $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status = 'menunggu'")->fetchColumn();
$totalPendapatan = (int) $pdo->query("SELECT COALESCE(SUM(total),0) FROM pesanan WHERE status = 'selesai'")->fetchColumn();

$pesananTerbaru = $pdo->query("SELECT pesanan.*, users.nama AS nama_user
                                FROM pesanan JOIN users ON users.id = pesanan.user_id
                                ORDER BY pesanan.created_at DESC LIMIT 5")->fetchAll();

require 'includes/header.php';
?>

<div class="row g-3 mb-4">
  <div class="col-md-3 col-6">
    <div class="stat-card">
      <div class="stat-value"><?= $totalProduk ?></div>
      <div class="stat-label">Total Produk</div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="stat-card">
      <div class="stat-value"><?= $totalPesanan ?></div>
      <div class="stat-label">Total Pesanan</div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="stat-card">
      <div class="stat-value text-warning"><?= $pesananBaru ?></div>
      <div class="stat-label">Menunggu Konfirmasi</div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="stat-card">
      <div class="stat-value" style="font-size:20px;"><?= rupiah($totalPendapatan) ?></div>
      <div class="stat-label">Pendapatan (Selesai)</div>
    </div>
  </div>
</div>

<div class="card p-3">
  <h5 class="fw-bold mb-3">Pesanan Terbaru</h5>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>Pemesan</th>
          <th>Total</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($pesananTerbaru)): ?>
        <tr><td colspan="6" class="text-center text-muted py-3">Belum ada pesanan.</td></tr>
        <?php endif; ?>
        <?php foreach ($pesananTerbaru as $p): ?>
        <tr>
          <td>#<?= $p['id'] ?></td>
          <td><?= bersihkan($p['nama_user']) ?></td>
          <td><?= rupiah($p['total']) ?></td>
          <td><span class="status-pill status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
          <td><?= date('d M Y, H:i', strtotime($p['created_at'])) ?></td>
          <td><a href="pesanan.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-custom">Detail</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require 'includes/footer.php'; ?>
