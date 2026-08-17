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

<div class="d-flex gap-2 mb-3 flex-wrap">
  <a href="pesanan.php" class="btn btn-sm <?= $filterStatus === '' ? 'btn-custom-primary' : 'btn-outline-custom' ?>">Semua</a>
  <a href="pesanan.php?status=menunggu" class="btn btn-sm <?= $filterStatus === 'menunggu' ? 'btn-custom-primary' : 'btn-outline-custom' ?>">Menunggu</a>
  <a href="pesanan.php?status=diproses" class="btn btn-sm <?= $filterStatus === 'diproses' ? 'btn-custom-primary' : 'btn-outline-custom' ?>">Diproses</a>
  <a href="pesanan.php?status=selesai" class="btn btn-sm <?= $filterStatus === 'selesai' ? 'btn-custom-primary' : 'btn-outline-custom' ?>">Selesai</a>
  <a href="pesanan.php?status=dibatalkan" class="btn btn-sm <?= $filterStatus === 'dibatalkan' ? 'btn-custom-primary' : 'btn-outline-custom' ?>">Dibatalkan</a>
</div>

<div class="card p-3">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>#</th><th>Pemesan</th><th>Total</th><th>Status</th><th>Tanggal</th><th></th></tr></thead>
      <tbody>
        <?php if (empty($daftarPesanan)): ?>
        <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada pesanan.</td></tr>
        <?php endif; ?>
        <?php foreach ($daftarPesanan as $p): ?>
        <tr class="<?= $p['id'] === $detailId ? 'table-active' : '' ?>">
          <td>#<?= $p['id'] ?></td>
          <td><?= bersihkan($p['nama_user']) ?><div class="text-muted small"><?= bersihkan($p['email_user']) ?></div></td>
          <td><?= rupiah($p['total']) ?></td>
          <td><span class="status-pill status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
          <td><?= date('d M Y, H:i', strtotime($p['created_at'])) ?></td>
          <td><a href="pesanan.php?id=<?= $p['id'] ?><?= $filterStatus ? '&status=' . $filterStatus : '' ?>" class="btn btn-sm btn-outline-custom">Detail</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php if ($detailId && $itemDetail): $pesananSaatIni = array_values(array_filter($daftarPesanan, fn($p) => $p['id'] === $detailId))[0] ?? null; ?>
<div class="card p-3 mt-4">
  <h5 class="fw-bold mb-3">Detail Pesanan #<?= $detailId ?></h5>

  <table class="table table-sm mb-4">
    <thead><tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th></tr></thead>
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
    <button type="submit" name="update_status" value="1" class="btn btn-custom-primary">Simpan</button>
  </form>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>
