<?php
// File: produk.php
// Halaman daftar produk

// Include koneksi database
require_once 'config/koneksi.php';
require_once 'config/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Jenang - Jenang Kudus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Include navbar -->
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <!-- Sidebar Filter -->
            <div class="col-md-3">
                <!-- Kategori -->
                <div class="card mb-4">
                    <div class="card-header">
                        Kategori
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php
                        $query = "SELECT * FROM kategori";
                        $result = mysqli_query($koneksi, $query);
                        
                        while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <li class="list-group-item">
                            <a href="produk.php?kategori=<?php echo $row['id_kategori']; ?>" class="text-decoration-none">
                                <?php echo $row['nama_kategori']; ?>
                            </a>
                        </li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>

                <!-- Filter Harga -->
                <div class="card">
                    <div class="card-header">
                        Filter Harga
                    </div>
                    <div class="card-body">
                        <form action="produk.php" method="GET">
                            <?php if(isset($_GET['kategori'])): ?>
                            <input type="hidden" name="kategori" value="<?php echo $_GET['kategori']; ?>">
                            <?php endif; ?>
                            <div class="mb-3">
                                <label for="min_price" class="form-label">Harga Minimum</label>
                                <input type="number" class="form-control" id="min_price" name="min_price" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="max_price" class="form-label">Harga Maksimum</label>
                                <input type="number" class="form-control" id="max_price" name="max_price" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Daftar Produk -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Produk Jenang</h2>
                    <!-- Dropdown Urutan -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Urutkan
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                            <li><a class="dropdown-item" href="?<?php echo isset($_GET['kategori']) ? 'kategori=' . $_GET['kategori'] . '&' : ''; ?>sort=latest">Terbaru</a></li>
                            <li><a class="dropdown-item" href="?<?php echo isset($_GET['kategori']) ? 'kategori=' . $_GET['kategori'] . '&' : ''; ?>sort=price_low">Harga: Rendah ke Tinggi</a></li>
                            <li><a class="dropdown-item" href="?<?php echo isset($_GET['kategori']) ? 'kategori=' . $_GET['kategori'] . '&' : ''; ?>sort=price_high">Harga: Tinggi ke Rendah</a></li>
                            <li><a class="dropdown-item" href="?<?php echo isset($_GET['kategori']) ? 'kategori=' . $_GET['kategori'] . '&' : ''; ?>sort=popular">Terpopuler</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Grid Produk -->
                <div class="row">
                    <?php
                    // Build query based on filters
                    $query = "SELECT * FROM produk WHERE 1=1";
                    
                    if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
                        $kategori = mysqli_real_escape_string($koneksi, $_GET['kategori']);
                        $query .= " AND id_kategori = '$kategori'";
                    }
                    
                    if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
                        $min_price = mysqli_real_escape_string($koneksi, $_GET['min_price']);
                        $query .= " AND harga >= '$min_price'";
                    }
                    
                    if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
                        $max_price = mysqli_real_escape_string($koneksi, $_GET['max_price']);
                        $query .= " AND harga <= '$max_price'";
                    }
                    
                    // Sorting
                    if (isset($_GET['sort'])) {
                        switch ($_GET['sort']) {
                            case 'latest':
                                $query .= " ORDER BY tanggal_input DESC";
                                break;
                            case 'price_low':
                                $query .= " ORDER BY harga ASC";
                                break;
                            case 'price_high':
                                $query .= " ORDER BY harga DESC";
                                break;
                            case 'popular':
                                $query .= " ORDER BY terjual DESC";
                                break;
                            default:
                                $query .= " ORDER BY tanggal_input DESC";
                        }
                    } else {
                        $query .= " ORDER BY tanggal_input DESC";
                    }
                    
                    $result = mysqli_query($koneksi, $query);
                    
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="assets/img/products/<?php echo $row['gambar']; ?>" class="card-img-top" alt="<?php echo $row['nama_produk']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['nama_produk']; ?></h5>
                                <p class="card-text">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                                <div class="d-flex justify-content-between">
                                    <a href="detail_produk.php?id=<?php echo $row['id_produk']; ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                    <button class="btn btn-sm btn-primary add-to-cart" data-id="<?php echo $row['id_produk']; ?>">+ Keranjang</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo '<div class="col-12"><div class="alert alert-info">Tidak ada produk yang ditemukan.</div></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Include footer -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>