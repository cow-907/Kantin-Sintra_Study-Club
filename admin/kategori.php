<?php
require '../includes/init.php';
wajibAdmin();

$judulHalaman = 'Kelola Kategori';
$halamanAktif = 'kategori';

// ---- Proses tambah ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'tambah') {
    $nama = trim($_POST['nama'] ?? '');
    if ($nama !== '') {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $nama));
        $stmt = $pdo->prepare("INSERT INTO kategori (nama, slug) VALUES (?, ?)");
        $stmt->execute([$nama, $slug]);
        setFlash('success', 'Kategori ditambahkan.');
    }
    header('Location: kategori.php');
    exit;
}

// ---- Proses edit ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'edit') {
    $id = (int) $_POST['id'];
    $nama = trim($_POST['nama'] ?? '');
    if ($nama !== '') {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $nama));
        $stmt = $pdo->prepare("UPDATE kategori SET nama = ?, slug = ? WHERE id = ?");
        $stmt->execute([$nama, $slug, $id]);
        setFlash('success', 'Kategori diperbarui.');
    }
    header('Location: kategori.php');
    exit;
}

// ---- Proses hapus ----
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE kategori_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        setFlash('error', 'Kategori tidak bisa dihapus karena masih punya produk.');
    } else {
        $pdo->prepare("DELETE FROM kategori WHERE id = ?")->execute([$id]);
        setFlash('success', 'Kategori dihapus.');
    }
    header('Location: kategori.php');
    exit;
}

$kategoriList = $pdo->query("SELECT kategori.*, (SELECT COUNT(*) FROM produk WHERE produk.kategori_id = kategori.id) AS jumlah_produk
                              FROM kategori ORDER BY id")->fetchAll();

require 'includes/header.php';
?>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card p-3">
      <h5 class="fw-bold mb-3">Tambah Kategori</h5>
      <form method="post">
        <input type="hidden" name="aksi" value="tambah">
        <div class="mb-3">
          <label class="form-label small">Nama Kategori</label>
          <input type="text" name="nama" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-custom-primary w-100">Simpan</button>
      </form>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card p-3">
      <h5 class="fw-bold mb-3">Daftar Kategori</h5>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead><tr><th>Nama</th><th>Jumlah Produk</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($kategoriList as $kat): $formId = 'form-kat-' . $kat['id']; ?>
            <tr>
              <td style="min-width:200px;">
                <input type="text" name="nama" form="<?= $formId ?>" value="<?= bersihkan($kat['nama']) ?>" class="form-control form-control-sm">
              </td>
              <td><?= $kat['jumlah_produk'] ?></td>
              <td class="d-flex gap-2">
                <form id="<?= $formId ?>" method="post">
                  <input type="hidden" name="aksi" value="edit">
                  <input type="hidden" name="id" value="<?= $kat['id'] ?>">
                </form>
                <button type="submit" form="<?= $formId ?>" class="btn btn-sm btn-outline-custom">Simpan</button>
                <?php if ($kat['jumlah_produk'] == 0): ?>
                <a href="kategori.php?hapus=<?= $kat['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kategori ini?')">Hapus</a>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require 'includes/footer.php'; ?>
