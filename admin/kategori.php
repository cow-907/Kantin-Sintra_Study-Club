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
    <div class="food-card">
      <h5 class="fw-bold mb-3">Tambah Kategori</h5>
      <form method="post">
        <input type="hidden" name="aksi" value="tambah">
        <div class="mb-3">
          <label class="form-label small" for="nama-kat">Nama Kategori</label>
          <input type="text" id="nama-kat" name="nama" class="form-control" placeholder="Nama Kategori Baru" required>
        </div>
        <button type="submit" class="btn btn-checkout text-decoration-none text-center">Simpan</button>
      </form>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="food-card">
      <h5 class="fw-bold mb-3">Daftar Kategori</h5>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr style="color:var(--text-soft); font-size:12px; text-transform:uppercase;">
              <th>Nama Kategori</th>
              <th>Jumlah Produk</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($kategoriList as $kat): $formId = 'form-kat-' . $kat['id']; ?>
            <tr>
              <td style="min-width:200px;">
                <input type="text" name="nama" form="<?= $formId ?>" value="<?= bersihkan($kat['nama']) ?>" class="form-control">
              </td>
              <td><span class="category-pill" style="padding:5px 14px; font-size:12.5px;"><?= $kat['jumlah_produk'] ?> produk</span></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <form id="<?= $formId ?>" method="post">
                    <input type="hidden" name="aksi" value="edit">
                    <input type="hidden" name="id" value="<?= $kat['id'] ?>">
                  </form>
                  <button type="submit" form="<?= $formId ?>" class="icon-btn text-decoration-none" style="width:32px; height:32px; background:var(--blue-btn); color:#fff; border:none;" title="Simpan"><i class="bi bi-save-fill"></i></button>
                  <?php if ($kat['jumlah_produk'] == 0): ?>
                  <a href="kategori.php?hapus=<?= $kat['id'] ?>" class="icon-btn text-decoration-none" style="width:32px; height:32px; background:#E8432C; color:#fff; border:none;" onclick="return confirm('Hapus kategori ini?')" title="Hapus"><i class="bi bi-trash-fill"></i></a>
                  <?php endif; ?>
                </div>
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
