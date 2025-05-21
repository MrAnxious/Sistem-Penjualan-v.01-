<?php
// File: index.php
// Halaman utama website toko jenang

// Include koneksi database
require_once 'config/koneksi.php';
require_once 'config/functions.php';

// Tetapkan judul halaman
$page_title = "Beranda";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Jenang Kudus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Include navbar -->
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <!-- Hero Section -->
        <div class="hero-section p-4 mb-4 bg-light rounded">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1>Selamat Datang di Toko Jenang Kudus</h1>
                    <p class="lead">Nikmati kelezatan asli jenang tradisional khas Kudus dengan cita rasa warisan leluhur yang terjaga kualitasnya.</p>
                    <a href="produk.php" class="btn btn-primary">Lihat Produk</a>
                </div>
                <div class="col-md-6">
                    <img src="assets/img/hero-jenang.jpg" alt="Jenang Kudus" class="img-fluid rounded">
                </div>
            </div>
        </div>

        <!-- Produk Unggulan -->
        <h2 class="mb-4">Produk Unggulan</h2>
        <div class="row">
            <?php
            // Get featured products (limit 4)
            $query = "SELECT * FROM produk WHERE unggulan = 1 LIMIT 4";
            $result = mysqli_query($koneksi, $query);
            
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <img src="assets/img/products/<?php echo $row['gambar']; ?>" class="card-img-top" alt="<?php echo $row['nama_produk']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['nama_produk']; ?></h5>
                        <p class="card-text">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                        <a href="detail_produk.php?id=<?php echo $row['id_produk']; ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                        <button class="btn btn-sm btn-primary add-to-cart" data-id="<?php echo $row['id_produk']; ?>">+ Keranjang</button>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>
        </div>

        <!-- Promo Section -->
        <div class="promo-section p-4 my-4 bg-light rounded">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2>Mengapa Memilih Jenang Kami?</h2>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success me-2" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                            Dibuat dengan bahan berkualitas tanpa pengawet
                        </li>
                        <li class="mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success me-2" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                            Resep tradisional turun-temurun
                        </li>
                        <li class="mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success me-2" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                            Proses pembuatan higienis
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success me-2" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                            Pengiriman cepat ke seluruh Indonesia
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 text-center">
                    <img src="assets/img/quality-badge.png" alt="Quality Badge" class="img-fluid" style="max-width: 150px;">
                </div>
            </div>
        </div>

        <!-- Kategori Jenang -->
        <h2 class="mb-4">Kategori Jenang</h2>
        <div class="row">
            <?php
            // Get categories
            $query = "SELECT * FROM kategori LIMIT 4";
            $result = mysqli_query($koneksi, $query);
            
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="col-md-3 mb-4">
                <div class="category-card p-3 bg-light rounded text-center">
                    <img src="assets/img/categories/<?php echo $row['icon']; ?>" alt="<?php echo $row['nama_kategori']; ?>" class="img-fluid mb-3" style="max-height: 100px;">
                    <h5><?php echo $row['nama_kategori']; ?></h5>
                    <a href="produk.php?kategori=<?php echo $row['id_kategori']; ?>" class="btn btn-sm btn-outline-primary">Lihat Produk</a>
                </div>
            </div>
            <?php
            }
            ?>
        </div>
    </div>

    <!-- Include footer -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>