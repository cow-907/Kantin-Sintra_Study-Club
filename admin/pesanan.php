<?php
require '../includes/init.php';
wajibAdmin();

$judulHalaman = 'Kelola Pesanan';
$halamanAktif = 'pesanan';

// ---- Update status ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = (int) $_POST['id'];
    $statusBaru = $_POST['status'];
    $statusValid = ['menunggu', 'diproses', 'selesai', 'dibatalkan'];

    if (in_array($statusBaru, $statusValid, true)) {
        $pdo->prepare("UPDATE pesanan SET status = ? WHERE id = ?")->execute([$statusBaru, $id]);
        setFlash('success', "Status pesanan #$id diperbarui menjadi $statusBaru.");
    }
    header('Location: pesanan.php' . (isset($_GET['id']) ? '?id=' . (int) $_GET['id'] : ''));
    exit;
}

$filterStatus = $_GET['status'] ?? '';
$where = [];
$params = [];
if ($filterStatus !== '') {
    $where[] = 'pesanan.status = ?';
    $params[] = $filterStatus;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT pesanan.*, users.nama AS nama_user, users.email AS email_user
                        FROM pesanan JOIN users ON users.id = pesanan.user_id
                        $whereSql ORDER BY pesanan.created_at DESC");
$stmt->execute($params);
$daftarPesanan = $stmt->fetchAll();

// Detail pesanan tertentu (jika diklik)
$detailId = (int) ($_GET['id'] ?? 0);
$itemDetail = [];
if ($detailId) {
    $stmt = $pdo->prepare("SELECT * FROM pesanan_detail WHERE pesanan_id = ?");
    $stmt->execute([$detailId]);
    $itemDetail = $stmt->fetchAll();
}

require 'includes/header.php';
?>

<!-- Filter Tombol Status -->
<div class="d-flex gap-2 mb-3 flex-wrap">
  <a href="pesanan.php" class="category-pill text-decoration-none <?= $filterStatus === '' ? 'active' : '' ?>">Semua</a>
  <a href="pesanan.php?status=menunggu" class="category-pill text-decoration-none <?= $filterStatus === 'menunggu' ? 'active' : '' ?>">Menunggu</a>
  <a href="pesanan.php?status=diproses" class="category-pill text-decoration-none <?= $filterStatus === 'diproses' ? 'active' : '' ?>">Diproses</a>
  <a href="pesanan.php?status=selesai" class="category-pill text-decoration-none <?= $filterStatus === 'selesai' ? 'active' : '' ?>">Selesai</a>
  <a href="pesanan.php?status=dibatalkan" class="category-pill text-decoration-none <?= $filterStatus === 'dibatalkan' ? 'active' : '' ?>">Dibatalkan</a>
</div>

<!-- Tabel Pesanan -->
<div class="food-card">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr style="color:var(--text-soft); font-size:12px; text-transform:uppercase;">
          <th>#</th><th>Pemesan</th><th>Total</th><th>Status</th><th>Tanggal</th><th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($daftarPesanan)): ?>
        <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada pesanan.</td></tr>
        <?php endif; ?>
        <?php foreach ($daftarPesanan as $p): ?>
        <tr class="<?= $p['id'] === $detailId ? 'table-active' : '' ?>">
          <td><span class="fw-bold">#<?= $p['id'] ?></span></td>
          <td>
            <div class="food-name" style="font-size:14px; margin-bottom:2px;"><?= bersihkan($p['nama_user']) ?></div>
            <div style="color:var(--text-soft); font-size:12px;"><?= bersihkan($p['email_user']) ?></div>
          </td>
          <td><span class="food-price" style="margin-bottom:0; font-size:14px;"><?= rupiah($p['total']) ?></span></td>
          <td><span class="status-pill status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
          <td style="font-size:13px; color:var(--text-soft);"><?= date('d M Y, H:i', strtotime($p['created_at'])) ?></td>
          <td>
            <a href="pesanan.php?id=<?= $p['id'] ?><?= $filterStatus ? '&status=' . $filterStatus : '' ?>" class="icon-btn text-decoration-none" style="width:32px; height:32px; background:var(--blue-btn); color:#fff; border:none;" title="Detail"><i class="bi bi-eye-fill"></i></a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php if ($detailId && $itemDetail):
      $pesananSaatIni = array_values(array_filter($daftarPesanan, fn($p) => $p['id'] === $detailId))[0] ?? null; ?>
<div class="food-card mt-4">
  <h5 class="fw-bold mb-3">Detail Pesanan #<?= $detailId ?></h5>

  <table class="table table-sm mb-4">
    <thead>
      <tr style="color:var(--text-soft); font-size:12px; text-transform:uppercase;">
        <th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($itemDetail as $d): ?>
      <tr>
        <td><?= bersihkan($d['nama_produk']) ?></td>
        <td><?= rupiah($d['harga']) ?></td>
        <td><?= $d['jumlah'] ?></td>
        <td><?= rupiah($d['subtotal']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php if ($pesananSaatIni): ?>
  <form method="post" class="d-flex align-items-end gap-2" style="max-width:400px;">
    <input type="hidden" name="id" value="<?= $detailId ?>">
    <div class="flex-grow-1">
      <label class="form-label small">Ubah Status</label>
      <select name="status" class="form-select">
        <?php foreach (['menunggu', 'diproses', 'selesai', 'dibatalkan'] as $s): ?>
        <option value="<?= $s ?>" <?= $pesananSaatIni['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" name="update_status" value="1" class="btn btn-checkout" style="width:auto; padding:10px 18px;">Simpan</button>
  </form>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>
