<?php
require 'includes/init.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT produk.*, kategori.nama AS kategori_nama
                        FROM produk JOIN kategori ON kategori.id = produk.kategori_id
                        WHERE produk.id = ? AND produk.status = 'aktif'");
$stmt->execute([$id]);
$produk = $stmt->fetch();

if (!$produk) {
    setFlash('error', 'Produk tidak ditemukan.');
    header('Location: menu.php');
    exit;
}

$judulHalaman = $produk['nama'];
$halamanAktif = 'menu';

require 'includes/header.php';
?>

<div class="container min-vh-100 d-flex justify-content-center align-items-center py-5">
  <div class="card product-card p-4 w-100" style="max-width: 1400px; padding: 3rem;">

    <a href="menu.php" class="btn-close position-absolute top-0 end-0 m-4" aria-label="Kembali"></a>

    <div class="row g-4 align-items-center">
      <!-- Gambar Produk -->
      <div class="col-md-5">
        <img src="<?= bersihkan($produk['gambar']) ?>" alt="<?= bersihkan($produk['nama']) ?>"
             class="img-fluid product-img" style="width:100%; height:520px; object-fit:cover; border-radius:18px;">
      </div>

      <!-- Informasi Produk -->
      <div class="col-md-7">
        <div class="ps-md-2">

          <div class="mb-2">
            <span class="badge-category"><?= bersihkan($produk['kategori_nama']) ?></span>
          </div>

          <h2 class="product-title mb-1"><?= bersihkan($produk['nama']) ?></h2>
          <div class="product-price mb-3"><?= rupiah($produk['harga']) ?></div>

          <div class="stock-box mb-4">
            <?= $produk['stok'] > 0 ? 'Stok: Tersedia (' . (int) $produk['stok'] . ')' : 'Stok: Habis' ?>
          </div>

          <h6 class="fw-bold mb-2">Deskripsi Produk</h6>
          <p class="text-secondary small mb-4"><?= nl2br(bersihkan($produk['deskripsi'])) ?></p>

          <?php if ($produk['stok'] > 0): ?>
          <form action="keranjang_proses.php" method="post">
            <input type="hidden" name="aksi" value="tambah">
            <input type="hidden" name="produk_id" value="<?= $produk['id'] ?>">
            <input type="hidden" name="kembali" value="keranjang.php">

            <div class="mb-4">
              <label class="form-label small fw-bold mb-2 d-block">Jumlah</label>
              <div class="d-flex align-items-center gap-3">
                <div class="input-group input-group-sm" style="width:auto;">
                  <button type="button" class="btn btn-outline-secondary px-2" onclick="ubahJumlah(-1)">-</button>
                  <input id="jumlah-input" type="number" name="jumlah" class="form-control qty-input fw-semibold" value="1" min="1" max="<?= (int) $produk['stok'] ?>" readonly>
                  <button type="button" class="btn btn-outline-secondary px-2" onclick="ubahJumlah(1)">+</button>
                </div>
                <span class="text-muted small">Total: <strong id="total-price" class="text-dark"><?= rupiah($produk['harga']) ?></strong></span>
              </div>
            </div>

            <div class="row g-2">
              <div class="col-12">
                <button type="submit" class="btn btn-custom-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                  <i class="bi bi-cart"></i> Tambah ke Keranjang
                </button>
              </div>
            </div>
          </form>
          <?php else: ?>
            <button class="btn btn-secondary w-100 py-2" disabled>Stok Habis</button>
          <?php endif; ?>

        </div>
      </div>
    </div>

  </div>
</div>

<script>
const hargaSatuan = <?= (int) $produk['harga'] ?>;
function formatRupiah(angka) {
  return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
}
function ubahJumlah(delta) {
  const input = document.getElementById('jumlah-input');
  let nilai = parseInt(input.value || '1', 10) + delta;
  const min = parseInt(input.min || '1', 10);
  const max = parseInt(input.max || '999', 10);
  if (nilai < min) nilai = min;
  if (nilai > max) nilai = max;
  input.value = nilai;
  document.getElementById('total-price').innerText = formatRupiah(nilai * hargaSatuan);
}
</script>

<?php require 'includes/footer.php'; ?>
