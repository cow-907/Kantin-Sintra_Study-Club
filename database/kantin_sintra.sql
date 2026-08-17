-- =========================================================
-- Database: kantin_sintra
-- Sistem Kantin Sintra - Backend PHP + MySQL
-- Cara pakai: buka phpMyAdmin > Import > pilih file ini
-- =========================================================

CREATE DATABASE IF NOT EXISTS kantin_sintra CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kantin_sintra;

-- =========================================================
-- Tabel users (pembeli & admin)
-- =========================================================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- Tabel kategori produk
-- =========================================================
CREATE TABLE kategori (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(50) NOT NULL,
  slug VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- =========================================================
-- Tabel produk (menu)
-- =========================================================
CREATE TABLE produk (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kategori_id INT NOT NULL,
  nama VARCHAR(100) NOT NULL,
  deskripsi TEXT,
  harga INT NOT NULL,
  gambar VARCHAR(255),
  stok INT NOT NULL DEFAULT 0,
  status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- Tabel pesanan (order header)
-- =========================================================
CREATE TABLE pesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total INT NOT NULL,
  status ENUM('menunggu','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
  catatan VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- Tabel detail pesanan (order items, snapshot harga saat order)
-- =========================================================
CREATE TABLE pesanan_detail (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pesanan_id INT NOT NULL,
  produk_id INT NULL,
  nama_produk VARCHAR(100) NOT NULL,
  harga INT NOT NULL,
  jumlah INT NOT NULL,
  subtotal INT NOT NULL,
  FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
  FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================================
-- Data awal: kategori
-- =========================================================
INSERT INTO kategori (nama, slug) VALUES
('Burger', 'burger'),
('Pizza', 'pizza'),
('Minuman', 'minuman');

-- =========================================================
-- Data awal: produk (dipindahkan dari Daftar Menu.html)
-- =========================================================
INSERT INTO produk (kategori_id, nama, deskripsi, harga, gambar, stok, status) VALUES
(1, 'Beef Burger', 'Burger daging sapi panggang dengan saus spesial.', 25000, 'https://i.pinimg.com/736x/b4/95/e7/b495e7922f7313e4cc00912740feeade.jpg', 20, 'aktif'),
(1, 'Cheese Burger', 'Burger dengan lelehan keju cheddar.', 30000, 'https://i.pinimg.com/736x/c7/05/23/c70523c237c9047733d3151ae04682a8.jpg', 20, 'aktif'),
(1, 'Chicken Burger', 'Burger ayam crispy dengan selada segar.', 29000, 'https://i.pinimg.com/736x/97/99/cc/9799cc9759a92aa73ba4b4a0b5177790.jpg', 20, 'aktif'),
(2, 'Vegan Pizza', 'Pizza sayuran segar tanpa produk hewani.', 29000, 'https://i.pinimg.com/1200x/27/ab/5e/27ab5edd0885b823023a2b5ba47a1f04.jpg', 15, 'aktif'),
(2, 'Pepperoni Pizza', 'Pizza dengan topping pepperoni melimpah.', 30000, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?auto=format&fit=crop&w=600&q=80', 15, 'aktif'),
(3, 'Es Teh', 'Minuman dingin dengan teh murni dari Kerinci.', 5000, 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=600&q=80', 50, 'aktif'),
(3, 'Matcha', 'Minuman matcha creamy dan segar.', 20000, 'https://i.pinimg.com/736x/68/1b/09/681b0999bafb3a7cf775f127e837a568.jpg', 30, 'aktif'),
(3, 'Chocolate Milkshake', 'Milkshake cokelat kental dan manis.', 15000, 'https://i.pinimg.com/1200x/dc/83/37/dc833736a16a5d16416c76348d860b15.jpg', 30, 'aktif'),
(3, 'Strawberry Milkshake', 'Milkshake stroberi segar.', 16000, 'https://i.pinimg.com/1200x/ba/c1/79/bac179ec87b3bfcb3ded5a10cf09fb5a.jpg', 30, 'aktif');

-- Catatan: akun admin TIDAK dibuat lewat SQL ini karena password harus
-- di-hash oleh PHP (password_hash). Jalankan setup/buat_admin.php sekali
-- lewat browser untuk membuat akun admin pertama, lalu hapus file itu.
