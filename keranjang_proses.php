<?php
require 'includes/init.php';

$aksi = $_POST['aksi'] ?? '';
$produkId = (int) ($_POST['produk_id'] ?? 0);
$kembali = $_POST['kembali'] ?? 'keranjang.php';

// Cegah open-redirect: hanya izinkan kembali ke file lokal (.php)
if (!preg_match('/^[a-zA-Z0-9_\-.]+\.php(\?[a-zA-Z0-9_=&]*)?$/', $kembali)) {
    $kembali = 'keranjang.php';
}

switch ($aksi) {
    case 'tambah':
        $jumlah = max(1, (int) ($_POST['jumlah'] ?? 1));

        // Pastikan produk ada & aktif sebelum ditambahkan
        $stmt = $pdo->prepare("SELECT id, nama, stok FROM produk WHERE id = ? AND status = 'aktif'");
        $stmt->execute([$produkId]);
        $produk = $stmt->fetch();

        if (!$produk) {
            setFlash('error', 'Produk tidak ditemukan.');
            break;
        }
        if ($produk['stok'] < 1) {
            setFlash('error', 'Stok produk habis.');
            break;
        }

        tambahKeKeranjang($produkId, $jumlah);
        setFlash('success', $produk['nama'] . ' ditambahkan ke keranjang.');

        if (isset($_POST['langsung_checkout'])) {
            header('Location: keranjang.php');
            exit;
        }
        break;

    case 'ubah':
        $jumlah = (int) ($_POST['jumlah'] ?? 1);
        ubahJumlahKeranjang($produkId, $jumlah);
        break;

    case 'hapus':
        hapusDariKeranjang($produkId);
        setFlash('success', 'Item dihapus dari keranjang.');
        break;

    case 'kosongkan':
        kosongkanKeranjang();
        setFlash('success', 'Keranjang dikosongkan.');
        break;
}

header('Location: ' . $kembali);
exit;
