<?php
/**
 * Kumpulan fungsi bantu yang dipakai di banyak halaman.
 * File ini WAJIB di-include setelah session_start() dan koneksi.php.
 */

// ---------- Format & keamanan ----------

function rupiah($angka) {
    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}

function bersihkan($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// ---------- Flash message (notifikasi sekali tampil) ----------

function setFlash($tipe, $pesan) {
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function tampilkanFlash() {
    if (empty($_SESSION['flash'])) {
        return;
    }
    $tipe = $_SESSION['flash']['tipe'] === 'error' ? 'danger' : $_SESSION['flash']['tipe'];
    $pesan = bersihkan($_SESSION['flash']['pesan']);
    echo "<div class=\"alert alert-{$tipe} alert-dismissible fade show\" role=\"alert\">{$pesan}
        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button></div>";
    unset($_SESSION['flash']);
}

// ---------- Auth helper ----------

function sudahLogin() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return sudahLogin() && ($_SESSION['role'] ?? '') === 'admin';
}

// Panggil di atas halaman yang WAJIB login (mis. checkout, pesanan_saya)
function wajibLogin($redirectSetelahLogin = null) {
    return true;
}

// Panggil di atas setiap halaman admin/*.php
function wajibAdmin() {
    if (!sudahLogin()) {
        header('Location: ../login.php');
        exit;
    }
    if (!isAdmin()) {
        header('Location: ../index.php');
        exit;
    }
}

// ---------- Keranjang (disimpan di session, format: [produk_id => jumlah]) ----------

function isiKeranjang() {
    if (!isset($_SESSION['keranjang']) || !is_array($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }
    return $_SESSION['keranjang'];
}

function jumlahItemKeranjang() {
    $total = 0;
    foreach (isiKeranjang() as $qty) {
        $total += (int) $qty;
    }
    return $total;
}

function tambahKeKeranjang($produkId, $jumlah = 1) {
    $keranjang = isiKeranjang();
    $produkId = (int) $produkId;
    $jumlah = max(1, (int) $jumlah);

    if (isset($keranjang[$produkId])) {
        $keranjang[$produkId] += $jumlah;
    } else {
        $keranjang[$produkId] = $jumlah;
    }
    $_SESSION['keranjang'] = $keranjang;
}

function ubahJumlahKeranjang($produkId, $jumlah) {
    $keranjang = isiKeranjang();
    $produkId = (int) $produkId;
    $jumlah = (int) $jumlah;

    if ($jumlah <= 0) {
        unset($keranjang[$produkId]);
    } else {
        $keranjang[$produkId] = $jumlah;
    }
    $_SESSION['keranjang'] = $keranjang;
}

function hapusDariKeranjang($produkId) {
    $keranjang = isiKeranjang();
    unset($keranjang[(int) $produkId]);
    $_SESSION['keranjang'] = $keranjang;
}

function kosongkanKeranjang() {
    $_SESSION['keranjang'] = [];
}

/**
 * Ambil detail produk yang ada di keranjang lengkap dengan subtotalnya.
 * Mengembalikan array baris + grand total.
 */
function detailKeranjang(PDO $pdo) {
    $keranjang = isiKeranjang();
    $items = [];
    $grandTotal = 0;

    if (empty($keranjang)) {
        return ['items' => $items, 'total' => 0];
    }

    $ids = array_map('intval', array_keys($keranjang));
    $placeholder = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT produk.*, kategori.nama AS kategori_nama FROM produk JOIN kategori ON kategori.id = produk.kategori_id WHERE produk.id IN ($placeholder)");
    $stmt->execute($ids);
    $produkList = $stmt->fetchAll();

    foreach ($produkList as $produk) {
        $qty = (int) $keranjang[$produk['id']];
        $subtotal = $qty * (int) $produk['harga'];
        $grandTotal += $subtotal;
        $items[] = [
            'produk'   => $produk,
            'jumlah'   => $qty,
            'subtotal' => $subtotal,
        ];
    }

    return ['items' => $items, 'total' => $grandTotal];
}
