<?php
require 'includes/init.php';

$judulHalaman = 'Daftar Menu';
$halamanAktif = 'menu';

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY id")->fetchAll();

// ---- Ambil parameter filter dari URL ----
$kategoriId = isset($_GET['kategori']) ? (int) $_GET['kategori'] : 0;
$kataKunci  = trim($_GET['cari'] ?? '');
$urutan     = $_GET['urutan'] ?? 'nama_az';

// ---- Bangun query dinamis dengan prepared statement ----
$where  = ["produk.status = 'aktif'"];
$params = [];

if ($kategoriId > 0) {
    $where[] = 'produk.kategori_id = ?';
    $params[] = $kategoriId;
}
if ($kataKunci !== '') {
    $where[] = 'produk.nama LIKE ?';
    $params[] = '%' . $kataKunci . '%';
}

$orderBy = match ($urutan) {
    'nama_za'        => 'produk.nama DESC',
    'harga_rendah'   => 'produk.harga ASC',
    'harga_tinggi'   => 'produk.harga DESC',
    default          => 'produk.nama ASC',
};

$sql = "SELECT produk.*, kategori.nama AS kategori_nama FROM produk
        JOIN kategori ON kategori.id = produk.kategori_id
        WHERE " . implode(' AND ', $where) . " ORDER BY $orderBy";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produkList = $stmt->fetchAll();

require 'includes/header.php';
?>

    <div class="mb-3">
      <h3 class="section-title">Semua Produk</h3>
      <div class="text-muted small">Menampilkan <?= count($produkList) ?> produk</div>
    </div>

    <!-- Filter bar -->
    <form class="card mb-4 p-3" method="get" action="menu.php">
      <div class="row g-3 align-items-center">
        <div class="col-md-6">
          <label class="form-label small mb-1">Kategori</label>
          <select class="form-select" name="kategori" onchange="this.form.submit()">
            <option value="0">Semua Produk</option>
            <?php foreach ($kategoriList as $kat): ?>
            <option value="<?= $kat['id'] ?>" <?= $kategoriId === (int) $kat['id'] ? 'selected' : '' ?>><?= bersihkan($kat['nama']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label small mb-1">Urutkan</label>
          <select class="form-select" name="urutan" onchange="this.form.submit()">
            <option value="nama_az" <?= $urutan === 'nama_az' ? 'selected' : '' ?>>Nama A-Z</option>
            <option value="nama_za" <?= $urutan === 'nama_za' ? 'selected' : '' ?>>Nama Z-A</option>
            <option value="harga_rendah" <?= $urutan === 'harga_rendah' ? 'selected' : '' ?>>Harga Rendah ke Tinggi</option>
            <option value="harga_tinggi" <?= $urutan === 'harga_tinggi' ? 'selected' : '' ?>>Harga Tinggi ke Rendah</option>
          </select>
        </div>
        <div class="col-md-3 text-md-end">
          <label class="form-label small mb-1 d-block invisible">Search</label>
          <div class="input-group">
            <input class="form-control" type="search" name="cari" placeholder="Cari produk..." value="<?= bersihkan($kataKunci) ?>">
            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
          </div>
        </div>
      </div>
    </form>

    <!-- Grid Produk -->
    <div class="row g-4">
      <?php if (empty($produkList)): ?>
        <p class="text-muted">Produk tidak ditemukan.</p>
      <?php endif; ?>

      <?php foreach ($produkList as $produk): ?>
      <div class="col-6 col-md-3">
        <div class="food-card">
          <div class="badge-row"><span class="badge bg-light text-success small"><?= bersihkan($produk['kategori_nama']) ?></span></div>
          <a href="produk.php?id=<?= $produk['id'] ?>" class="text-decoration-none text-reset">
            <img src="<?= bersihkan($produk['gambar']) ?>" alt="<?= bersihkan($produk['nama']) ?>">
            <div class="food-name"><?= bersihkan($produk['nama']) ?></div>
            <div class="food-price"><?= rupiah($produk['harga']) ?></div>
          </a>
          <form action="keranjang_proses.php" method="post">
            <input type="hidden" name="aksi" value="tambah">
            <input type="hidden" name="produk_id" value="<?= $produk['id'] ?>">
            <input type="hidden" name="kembali" value="keranjang.php">
            <button type="submit" class="btn btn-custom-primary mt-3 w-100"><i class="bi bi-cart3"></i> Tambah ke Keranjang</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

<?php require 'includes/footer.php'; ?>
