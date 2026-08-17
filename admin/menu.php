<?php
require '../includes/init.php';
wajibAdmin();

$judulHalaman = 'Kelola Menu';
$halamanAktif = 'menu';

// ---- Hapus produk ----
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $pdo->prepare("DELETE FROM produk WHERE id = ?")->execute([$id]);
    setFlash('success', 'Produk dihapus.');
    header('Location: menu.php');
    exit;
}

// ---- Toggle status aktif/nonaktif ----
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    $pdo->prepare("UPDATE produk SET status = IF(status='aktif','nonaktif','aktif') WHERE id = ?")->execute([$id]);
    header('Location: menu.php');
    exit;
}

$kataKunci = trim($_GET['cari'] ?? '');
if ($kataKunci !== '') {
    $stmt = $pdo->prepare("SELECT produk.*, kategori.nama AS kategori_nama
                            FROM produk JOIN kategori ON kategori.id = produk.kategori_id
                            WHERE produk.nama LIKE ?
                            ORDER BY produk.id DESC");
    $stmt->execute(['%' . $kataKunci . '%']);
    $produkList = $stmt->fetchAll();
} else {
    $produkList = $pdo->query("SELECT produk.*, kategori.nama AS kategori_nama
                                FROM produk JOIN kategori ON kategori.id = produk.kategori_id
                                ORDER BY produk.id DESC")->fetchAll();
}

require 'includes/header.php';
?>

<!-- Toolbar: search + tambah menu -->
<div class="app-header d-flex align-items-center justify-content-between flex-wrap gap-3" style="padding:16px 20px;">
  <form method="get" class="input-group header-search mb-0" style="max-width:320px;">
    <span class="input-group-text"><i class="bi bi-search"></i></span>
    <input type="text" name="cari" class="form-control" placeholder="Cari menu..." value="<?= bersihkan($kataKunci) ?>">
  </form>
  <a href="menu_form.php" class="btn-pesan d-inline-flex align-items-center justify-content-center gap-2 text-decoration-none"
     style="width:auto; padding:10px 18px;">
    <i class="bi bi-plus-lg"></i> Tambah Menu
  </a>
</div>

<!-- Tabel menu -->
<div class="food-card mt-3">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr style="color:var(--text-soft); font-size:12px; text-transform:uppercase;">
          <th style="width:48px;">No</th>
          <th style="width:80px;">Foto</th>
          <th>Nama Menu</th>
          <th>Kategori</th>
          <th>Harga</th>
          <th>Stok</th>
          <th>Status</th>
          <th style="width:100px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($produkList)): ?>
        <tr><td colspan="8" class="text-center text-muted py-3">Belum ada produk.</td></tr>
        <?php endif; ?>
        <?php $no = 1; foreach ($produkList as $p): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td>
            <img src="<?= bersihkan($p['gambar']) ?>" alt="<?= bersihkan($p['nama']) ?>"
                 style="width:56px; height:56px; margin:0; object-fit:cover; border-radius:8px;">
          </td>
          <td>
            <div class="food-name" style="font-size:14.5px; margin-bottom:2px;"><?= bersihkan($p['nama']) ?></div>
            <div style="color:var(--text-soft); font-size:12.5px;"><?= bersihkan($p['deskripsi']) ?></div>
          </td>
          <td><span class="category-pill" style="padding:5px 14px; font-size:12.5px;"><?= bersihkan($p['kategori_nama']) ?></span></td>
          <td><span class="food-price" style="margin-bottom:0; font-size:14.5px; color:var(--text-dark);"><?= rupiah($p['harga']) ?></span></td>
          <td><span class="fw-semibold text-secondary" style="font-size:14.5px;"><?= (int) $p['stok'] ?></span></td>
          <td>
            <a href="menu.php?toggle=<?= $p['id'] ?>" class="status-pill text-decoration-none <?= $p['status'] === 'aktif' ? 'status-selesai' : 'status-dibatalkan' ?>">
              <?= ucfirst($p['status']) ?>
            </a>
          </td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <a href="menu_form.php?id=<?= $p['id'] ?>" class="icon-btn text-decoration-none" style="width:32px; height:32px; background:var(--blue-btn); color:#fff; border:none;" title="Edit"><i class="bi bi-pencil-fill"></i></a>
              <a href="menu.php?hapus=<?= $p['id'] ?>" class="icon-btn text-decoration-none" style="width:32px; height:32px; background:#E8432C; color:#fff; border:none;" onclick="return confirm('Hapus produk ini?')" title="Hapus"><i class="bi bi-trash-fill"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require 'includes/footer.php'; ?>
