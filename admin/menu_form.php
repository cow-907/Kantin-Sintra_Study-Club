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

$judulHalaman = $editMode ? 'Edit Menu' : 'Tambah Menu';
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

<div class="food-card mx-auto" style="max-width:550px;">

  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="category-title font-heading m-0"><?= $editMode ? 'Edit Menu' : 'Tambah Menu' ?></h2>
    <a href="menu.php" class="icon-btn" style="width:36px; height:36px;" aria-label="Tutup">
      <i class="bi bi-x-lg"></i>
    </a>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger py-2"><?= bersihkan($error) ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label" for="nama-menu">Nama Menu</label>
      <input type="text" id="nama-menu" name="nama" class="form-control" value="<?= bersihkan($produk['nama']) ?>" placeholder="Masukkan nama menu" required>
    </div>

    <div class="mb-3">
      <label class="form-label" for="kategori-menu">Kategori</label>
      <select id="kategori-menu" name="kategori_id" class="form-select" required>
        <option value="">Pilih kategori</option>
        <?php foreach ($kategoriList as $kat): ?>
        <option value="<?= $kat['id'] ?>" <?= (int) ($produk['kategori_id'] ?? 0) === (int) $kat['id'] ? 'selected' : '' ?>><?= bersihkan($kat['nama']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label" for="harga-menu">Harga</label>
        <div class="input-group">
          <span class="input-group-text">Rp</span>
          <input type="number" id="harga-menu" name="harga" class="form-control" value="<?= bersihkan((string) $produk['harga']) ?>" placeholder="Masukkan harga" min="1" required>
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label" for="stok-menu">Stok</label>
        <input type="number" id="stok-menu" name="stok" class="form-control" value="<?= bersihkan((string) $produk['stok']) ?>" placeholder="Masukkan stok" min="0" required>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label" for="foto-menu">URL Foto Menu</label>
      <input type="url" id="foto-menu" name="gambar" class="form-control" value="<?= bersihkan($produk['gambar']) ?>" placeholder="https://..." required>
      <div class="form-text">Tempel URL gambar (mis. dari Pinterest/Unsplash).</div>
    </div>

    <div class="mb-3">
      <label class="form-label" for="deskripsi-menu">Deskripsi</label>
      <textarea id="deskripsi-menu" name="deskripsi" class="form-control" rows="3" placeholder="Masukkan deskripsi menu" style="resize:none;" required><?= bersihkan($produk['deskripsi']) ?></textarea>
    </div>

    <div class="mb-0">
      <label class="form-label" for="status-menu">Status</label>
      <select id="status-menu" name="status" class="form-select" required>
        <option value="aktif" <?= $produk['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
        <option value="nonaktif" <?= $produk['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
      </select>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
      <a href="menu.php" class="btn btn-outline-secondary">Batal</a>
      <button type="submit" class="btn-pesan" style="width:auto; padding:10px 22px; border:none;">Simpan</button>
    </div>
  </form>

</div>

<?php require 'includes/footer.php'; ?>
