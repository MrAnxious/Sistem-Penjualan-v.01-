<?php
// File: config/database.php
// Struktur database untuk website toko jenang

/*
Berikut adalah struktur database untuk sistem penjualan toko jenang:

1. Tabel: kategori
   - id_kategori (int, primary key, auto increment)
   - nama_kategori (varchar)
   - deskripsi (text)
   - icon (varchar)
   - slug (varchar)
   - tanggal_input (datetime)

2. Tabel: produk
   - id_produk (int, primary key, auto increment)
   - id_kategori (int, foreign key)
   - nama_produk (varchar)
   - deskripsi (text)
   - harga (decimal)
   - stok (int)
   - berat (int) // dalam gram
   - gambar (varchar) // nama file gambar utama
   - kadaluarsa (int) // jumlah hari
   - komposisi (text)
   - penyimpanan (text)
   - unggulan (tinyint) // produk unggulan (1 = ya, 0 = tidak)
   - terjual (int) // jumlah produk yang sudah terjual
   - tanggal_input (datetime)

3. Tabel: produk_gambar
   - id_gambar (int, primary key, auto increment)
   - id_produk (int, foreign key)
   - gambar (varchar) // nama file gambar tambahan
   - urutan (int)

4. Tabel: customer
   - id_customer (int, primary key, auto increment)
   - nama (varchar)
   - email (varchar)
   - telepon (varchar)
   - alamat (text)
   - kota (varchar)
   - kode_pos (varchar)
   - tanggal_daftar (datetime)

5. Tabel: pesanan
   - id_pesanan (varchar, primary key) // format: INV-YmdHis-randomnumber
   - id_customer (int, foreign key)
   - total_harga (decimal)
   - metode_pembayaran (varchar) // transfer_bank, e_wallet, cod
   - status (varchar) // pending, dibayar, diproses, dikirim, selesai, dibatalkan
   - catatan (text)
   - bukti_pembayaran (varchar) // nama file bukti pembayaran
   - tanggal_pesanan (datetime)
   - tanggal_pembayaran (datetime)
   - tanggal_pengiriman (datetime)
   - tanggal_selesai (datetime)

6. Tabel: pesanan_item
   - id_item (int, primary key, auto increment)
   - id_pesanan (varchar, foreign key)
   - id_produk (int, foreign key)
   - jumlah (int)
   - harga (decimal)
   - subtotal (decimal)

7. Tabel: review
   - id_review (int, primary key, auto increment)
   - id_produk (int, foreign key)
   - id_customer (int, foreign key)
   - rating (int) // 1-5
   - ulasan (text)
   - tanggal (datetime)

8. Tabel: testimonial
   - id_testimonial (int, primary key, auto increment)
   - id_customer (int, foreign key)
   - testimonial (text)
   - rating (int) // 1-5
   - status (varchar) // active, inactive
   - tanggal (datetime)

9. Tabel: admin
   - id_admin (int, primary key, auto increment)
   - nama (varchar)
   - username (varchar)
   - password (varchar) // hashed password
   - email (varchar)
   - level (varchar) // admin, staff
   - last_login (datetime)
   - status (tinyint) // 1 = aktif, 0 = nonaktif
*/

// SQL untuk membuat database dan tabel-tabel
$sql = "
-- Membuat database
CREATE DATABASE IF NOT EXISTS toko_jenang;
USE toko_jenang;

-- Tabel kategori
CREATE TABLE IF NOT EXISTS kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    icon VARCHAR(255),
    slug VARCHAR(100),
    tanggal_input DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabel produk
CREATE TABLE IF NOT EXISTS produk (
    id_produk INT AUTO_INCREMENT PRIMARY KEY,
    id_kategori INT,
    nama_produk VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10,2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    berat INT NOT NULL COMMENT 'dalam gram',
    gambar VARCHAR(255),
    kadaluarsa INT COMMENT 'jumlah hari',
    komposisi TEXT,
    penyimpanan TEXT,
    unggulan TINYINT DEFAULT 0 COMMENT '1 = ya, 0 = tidak',
    terjual INT DEFAULT 0,
    tanggal_input DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE SET NULL
);

-- Tabel produk_gambar
CREATE TABLE IF NOT EXISTS produk_gambar (
    id_gambar INT AUTO_INCREMENT PRIMARY KEY,
    id_produk INT,
    gambar VARCHAR(255),
    urutan INT DEFAULT 0,
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE CASCADE
);

-- Tabel customer
CREATE TABLE IF NOT EXISTS customer (
    id_customer INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telepon VARCHAR(20),
    alamat TEXT,
    kota VARCHAR(100),
    kode_pos VARCHAR(10),
    tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabel pesanan
CREATE TABLE IF NOT EXISTS pesanan (
    id_pesanan VARCHAR(50) PRIMARY KEY,
    id_customer INT,
    total_harga DECIMAL(10,2) NOT NULL,
    metode_pembayaran VARCHAR(20) NOT NULL COMMENT 'transfer_bank, e_wallet, cod',
    status VARCHAR(20) NOT NULL DEFAULT 'pending' COMMENT 'pending, dibayar, diproses, dikirim, selesai, dibatalkan',
    catatan TEXT,
    bukti_pembayaran VARCHAR(255),
    tanggal_pesanan DATETIME DEFAULT CURRENT_TIMESTAMP,
    tanggal_pembayaran DATETIME,
    tanggal_pengiriman DATETIME,
    tanggal_selesai DATETIME,
    FOREIGN KEY (id_customer) REFERENCES customer(id_customer) ON DELETE SET NULL
);

-- Tabel pesanan_item
CREATE TABLE IF NOT EXISTS pesanan_item (
    id_item INT AUTO_INCREMENT PRIMARY KEY,
    id_pesanan VARCHAR(50),
    id_produk INT,
    jumlah INT NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE SET NULL
);

-- Tabel review
CREATE TABLE IF NOT EXISTS review (
    id_review INT AUTO_INCREMENT PRIMARY KEY,
    id_produk INT,
    id_customer INT,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    ulasan TEXT,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE CASCADE,
    FOREIGN KEY (id_customer) REFERENCES customer(id_customer) ON DELETE SET NULL
);

-- Tabel testimonial
CREATE TABLE IF NOT EXISTS testimonial (
    id_testimonial INT AUTO_INCREMENT PRIMARY KEY,
    id_customer INT,
    testimonial TEXT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    status VARCHAR(10) DEFAULT 'active' COMMENT 'active, inactive',
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_customer) REFERENCES customer(id_customer) ON DELETE SET NULL
);

-- Tabel admin
CREATE TABLE IF NOT EXISTS admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    level VARCHAR(10) NOT NULL DEFAULT 'staff' COMMENT 'admin, staff',
    last_login DATETIME,
    status TINYINT DEFAULT 1 COMMENT '1 = aktif, 0 = nonaktif'
);

-- Memasukkan data admin default
INSERT INTO admin (nama, username, password, email, level) VALUES 
('Administrator', 'admin', '$2y$10$GxpR.n6JWJ9EsOkz1GKoDOb7NARNv9OIzxZ1BFVUh.ymhkJyxgOZi', 'admin@example.com', 'admin');
-- password: admin123
";

// Catatan: Script ini hanya menunjukkan struktur SQL dan tidak dieksekusi secara langsung.
// Gunakan tools seperti phpMyAdmin untuk menjalankan SQL ini.