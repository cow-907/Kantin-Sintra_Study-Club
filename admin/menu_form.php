<?php
require '../includes/init.php';
wajibAdmin();

$id = (int) ($_GET['id'] ?? 0);
$editMode = $id > 0;

$produk = [
    'nama' => '', 'deskripsi' => '', 'harga' => '', 'gambar' => '',
    'stok' => 0, 'kategori_id' => '', 'status' => 'aktif',
];

if ($editMode) {
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        setFlash('error', 'Produk tidak ditemukan.');
        header('Location: menu.php');
        exit;
    }
    $produk = $found;
}

$judulHalaman = $editMode ? 'Edit Produk' : 'Tambah Produk';
$halamanAktif = 'menu';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga = (int) ($_POST['harga'] ?? 0);
    $gambar = trim($_POST['gambar'] ?? '');
    $stok = (int) ($_POST['stok'] ?? 0);
    $kategoriId = (int) ($_POST['kategori_id'] ?? 0);
    $status = $_POST['status'] === 'nonaktif' ? 'nonaktif' : 'aktif';

    if ($nama === '' || $harga <= 0 || $kategoriId <= 0 || $gambar === '') {
        $error = 'Nama, kategori, harga, dan URL gambar wajib diisi dengan benar.';
        $produk = $_POST; // supaya form tidak kosong lagi
    } else {
        if ($editMode) {
            $stmt = $pdo->prepare("UPDATE produk SET nama=?, deskripsi=?, harga=?, gambar=?, stok=?, kategori_id=?, status=? WHERE id=?");
            $stmt->execute([$nama, $deskripsi, $harga, $gambar, $stok, $kategoriId, $status, $id]);
            setFlash('success', 'Produk diperbarui.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO produk (nama, deskripsi, harga, gambar, stok, kategori_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $deskripsi, $harga, $gambar, $stok, $kategoriId, $status]);
            setFlash('success', 'Produk ditambahkan.');
        }
        header('Location: menu.php');
        exit;
    }
}

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY nama")->fetchAll();

require 'includes/header.php';
?>

<div class="card p-4" style="max-width:700px;">
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= bersihkan($error) ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label small">Nama Produk</label>
      <input type="text" name="nama" class="form-control" value="<?= bersihkan($produk['nama']) ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label small">Kategori</label>
      <select name="kategori_id" class="form-select" required>
        <option value="">-- Pilih Kategori --</option>
        <?php foreach ($kategoriList as $kat): ?>
        <option value="<?= $kat['id'] ?>" <?= (int) ($produk['kategori_id'] ?? 0) === (int) $kat['id'] ? 'selected' : '' ?>><?= bersihkan($kat['nama']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label small">Harga (Rp)</label>
        <input type="number" name="harga" class="form-control" value="<?= bersihkan((string) $produk['harga']) ?>" min="1" required>
      </div>
      <div class="col-md-6">
        <label class="form-label small">Stok</label>
        <input type="number" name="stok" class="form-control" value="<?= bersihkan((string) $produk['stok']) ?>" min="0" required>
      </div>
    </div>

    <div class="mb-3 mt-3">
      <label class="form-label small">URL Gambar</label>
      <input type="url" name="gambar" class="form-control" value="<?= bersihkan($produk['gambar']) ?>" placeholder="https://..." required>
      <div class="form-text">Tempel URL gambar (mis. dari Unsplash/Pinterest), atau upload manual ke folder assets/img lalu isi path-nya di sini.</div>
    </div>

    <div class="mb-3">
      <label class="form-label small">Deskripsi</label>
      <textarea name="deskripsi" class="form-control" rows="3"><?= bersihkan($produk['deskripsi']) ?></textarea>
    </div>

    <div class="mb-4">
      <label class="form-label small">Status</label>
      <select name="status" class="form-select">
        <option value="aktif" <?= $produk['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
        <option value="nonaktif" <?= $produk['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
      </select>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-custom-primary"><?= $editMode ? 'Simpan Perubahan' : 'Tambah Produk' ?></button>
      <a href="menu.php" class="btn btn-outline-custom">Batal</a>
    </div>
  </form>
</div>

<?php require 'includes/footer.php'; ?>
