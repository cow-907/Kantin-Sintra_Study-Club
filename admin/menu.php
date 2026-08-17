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

$produkList = $pdo->query("SELECT produk.*, kategori.nama AS kategori_nama
                            FROM produk JOIN kategori ON kategori.id = produk.kategori_id
                            ORDER BY produk.id DESC")->fetchAll();

require 'includes/header.php';
?>

<div class="d-flex justify-content-end mb-3">
  <a href="menu_form.php" class="btn btn-custom-primary"><i class="bi bi-plus-lg"></i> Tambah Produk</a>
</div>

<div class="card p-3">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($produkList)): ?>
        <tr><td colspan="7" class="text-center text-muted py-3">Belum ada produk.</td></tr>
        <?php endif; ?>
        <?php foreach ($produkList as $p): ?>
        <tr>
          <td><img src="<?= bersihkan($p['gambar']) ?>" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:8px;"></td>
          <td><?= bersihkan($p['nama']) ?></td>
          <td><?= bersihkan($p['kategori_nama']) ?></td>
          <td><?= rupiah($p['harga']) ?></td>
          <td><?= (int) $p['stok'] ?></td>
          <td>
            <a href="menu.php?toggle=<?= $p['id'] ?>" class="status-pill text-decoration-none <?= $p['status'] === 'aktif' ? 'status-selesai' : 'status-dibatalkan' ?>">
              <?= ucfirst($p['status']) ?>
            </a>
          </td>
          <td class="d-flex gap-2">
            <a href="menu_form.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-custom">Edit</a>
            <a href="menu.php?hapus=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus produk ini?')">Hapus</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require 'includes/footer.php'; ?>
