<?php
// File: admin/dashboard.php
// Halaman dashboard admin

// Start session
session_start();

// Cek login
if (!isset($_SESSION['admin_login'])) {
    header("Location: login.php");
    exit;
}

// Include koneksi database
require_once '../config/koneksi.php';

// Get admin info
$admin_id = $_SESSION['admin_id'];
$query_admin = "SELECT * FROM admin WHERE id_admin = '$admin_id'";
$result_admin = mysqli_query($koneksi, $query_admin);
$admin = mysqli_fetch_assoc($result_admin);

// Get dashboard statistics
// 1. Total Produk
$query_produk = "SELECT COUNT(*) as total FROM produk";
$result_produk = mysqli_query($koneksi, $query_produk);
$total_produk = mysqli_fetch_assoc($result_produk)['total'];

// 2. Total Pesanan
$query_pesanan = "SELECT COUNT(*) as total FROM pesanan";
$result_pesanan = mysqli_query($koneksi, $query_pesanan);
$total_pesanan = mysqli_fetch_assoc($result_pesanan)['total'];

// 3. Pesanan Pending
$query_pending = "SELECT COUNT(*) as total FROM pesanan WHERE status = 'pending'";
$result_pending = mysqli_query($koneksi, $query_pending);
$pesanan_pending = mysqli_fetch_assoc($result_pending)['total'];

// 4. Total Pendapatan
$query_pendapatan = "SELECT SUM(total_harga) as total FROM pesanan WHERE status != 'dibatalkan'";
$result_pendapatan = mysqli_query($koneksi, $query_pendapatan);
$total_pendapatan = mysqli_fetch_assoc($result_pendapatan)['total'] ?? 0;

// 5. Pesanan Terakhir
$query_last_orders = "SELECT p.*, c.nama FROM pesanan p 
                      JOIN customer c ON p.id_customer = c.id_customer 
                      ORDER BY p.tanggal_pesanan DESC LIMIT 5";
$result_last_orders = mysqli_query($koneksi, $query_last_orders);

// 6. Produk Terlaris
$query_popular = "SELECT p.id_produk, p.nama_produk, p.harga, p.gambar, p.terjual 
                 FROM produk p 
                 ORDER BY p.terjual DESC LIMIT 5";
$result_popular = mysqli_query($koneksi, $query_popular);

// 7. Stok Menipis
$query_low_stock = "SELECT * FROM produk WHERE stok <= 10 AND stok > 0 ORDER BY stok ASC LIMIT 5";
$result_low_stock = mysqli_query($koneksi, $query_low_stock);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Jenang Kudus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'assets/includes/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="produk.php" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-plus"></i> Tambah Produk
                            </a>
                        </div>
                        <a href="../index.php" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i> Lihat Website
                        </a>
                    </div>
                </div>
                
                <!-- Welcome Message -->
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <h4 class="alert-heading">Selamat Datang, <?php echo $admin['nama']; ?>!</h4>
                    <p>Ini adalah panel admin Toko Jenang Kudus. Anda dapat mengelola produk, pesanan, laporan, dan lainnya dari sini.</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                
                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Produk</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_produk; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-box fs-2 text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Total Pendapatan</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-currency-dollar fs-2 text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total Pesanan</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_pesanan; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-cart fs-2 text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pesanan Pending</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pesanan_pending; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-clock-history fs-2 text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Content Row -->
                <div class="row">
                    <!-- Recent Orders -->
                    <div class="col-lg-8 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Pesanan Terbaru</h6>
                                <a href="pesanan.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID Pesanan</th>
                                                <th>Tanggal</th>
                                                <th>Customer</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (mysqli_num_rows($result_last_orders) > 0) {
                                                while ($order = mysqli_fetch_assoc($result_last_orders)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $order['id_pesanan']; ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($order['tanggal_pesanan'])); ?></td>
                                                <td><?php echo $order['nama']; ?></td>
                                                <td>Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></td>
                                                <td>
                                                    <?php
                                                    switch ($order['status']) {
                                                        case 'pending':
                                                            echo '<span class="badge bg-warning">Menunggu Pembayaran</span>';
                                                            break;
                                                        case 'dibayar':
                                                            echo '<span class="badge bg-info">Pembayaran Dikonfirmasi</span>';
                                                            break;
                                                        case 'diproses':
                                                            echo '<span class="badge bg-primary">Sedang Diproses</span>';
                                                            break;
                                                        case 'dikirim':
                                                            echo '<span class="badge bg-primary">Dalam Pengiriman</span>';
                                                            break;
                                                        case 'selesai':
                                                            echo '<span class="badge bg-success">Selesai</span>';
                                                            break;
                                                        case 'dibatalkan':
                                                            echo '<span class="badge bg-danger">Dibatalkan</span>';
                                                            break;
                                                        default:
                                                            echo '<span class="badge bg-secondary">Tidak Diketahui</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="detail_pesanan.php?id=<?php echo $order['id_pesanan']; ?>" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                                }
                                            } else {
                                                echo '<tr><td colspan="6" class="text-center">Belum ada pesanan</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sidebar Content -->
                    <div class="col-lg-4 mb-4">
                        <!-- Best Selling Products -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Produk Terlaris</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <?php
                                    if (mysqli_num_rows($result_popular) > 0) {
                                        while ($product = mysqli_fetch_assoc($result_popular)) {
                                    ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <img src="../assets/img/products/<?php echo $product['gambar']; ?>" alt="<?php echo $product['nama_produk']; ?>" class="me-3" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                            <div>
                                                <h6 class="mb-0"><?php echo $product['nama_produk']; ?></h6>
                                                <small class="text-muted">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></small>
                                            </div>
                                        </div>
                                        <span class="badge bg-primary rounded-pill"><?php echo $product['terjual']; ?> terjual</span>
                                    </li>
                                    <?php
                                        }
                                    } else {
                                        echo '<li class="list-group-item">Belum ada produk yang terjual</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Low Stock Products -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Stok Menipis</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <?php
                                    if (mysqli_num_rows($result_low_stock) > 0) {
                                        while ($product = mysqli_fetch_assoc($result_low_stock)) {
                                    ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <img src="../assets/img/products/<?php echo $product['gambar']; ?>" alt="<?php echo $product['nama_produk']; ?>" class="me-3" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                            <div>
                                                <h6 class="mb-0"><?php echo $product['nama_produk']; ?></h6>
                                                <small class="text-muted">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></small>
                                            </div>
                                        </div>
                                        <span class="badge bg-danger rounded-pill"><?php echo $product['stok']; ?> tersisa</span>
                                    </li>
                                    <?php
                                        }
                                    } else {
                                        echo '<li class="list-group-item">Tidak ada produk dengan stok menipis</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <?php include 'assets/includes/footer.php'; ?>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
</body>
</html>