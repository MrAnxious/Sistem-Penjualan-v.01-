<?php
// File: admin/produk.php
// Halaman daftar produk admin

// Start session
session_start();

// Cek login
if (!isset($_SESSION['admin_login'])) {
    header("Location: login.php");
    exit;
}

// Include koneksi database
require_once '../config/koneksi.php';

// Handle delete product
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id_produk = mysqli_real_escape_string($koneksi, $_GET['delete']);
    
    // Get product image
    $query_img = "SELECT gambar FROM produk WHERE id_produk = '$id_produk'";
    $result_img = mysqli_query($koneksi, $query_img);
    $row_img = mysqli_fetch_assoc($result_img);
    
    // Delete product
    $query_delete = "DELETE FROM produk WHERE id_produk = '$id_produk'";
    $result_delete = mysqli_query($koneksi, $query_delete);
    
    if ($result_delete) {
        // Delete product image if exists
        if (!empty($row_img['gambar']) && file_exists('../assets/img/products/' . $row_img['gambar'])) {
            unlink('../assets/img/products/' . $row_img['gambar']);
        }
        
        // Get additional images
        $query_imgs = "SELECT gambar FROM produk_gambar WHERE id_produk = '$id_produk'";
        $result_imgs = mysqli_query($koneksi, $query_imgs);
        
        while ($row_img = mysqli_fetch_assoc($result_imgs)) {
            if (!empty($row_img['gambar']) && file_exists('../assets/img/products/' . $row_img['gambar'])) {
                unlink('../assets/img/products/' . $row_img['gambar']);
            }
        }
        
        // Set success message
        $_SESSION['success'] = "Produk berhasil dihapus";
    } else {
        // Set error message
        $_SESSION['error'] = "Gagal menghapus produk: " . mysqli_error($koneksi);
    }
    
    // Redirect
    header("Location: produk.php");
    exit;
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search and filter
$where = "1=1";
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($koneksi, $_GET['kategori']) : '';

if (!empty($search)) {
    $where .= " AND (nama_produk LIKE '%$search%' OR deskripsi LIKE '%$search%')";
}

if (!empty($kategori)) {
    $where .= " AND id_kategori = '$kategori'";
}

// Get total products
$query_count = "SELECT COUNT(*) as total FROM produk WHERE $where";
$result_count = mysqli_query($koneksi, $query_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_records = $row_count['total'];

$total_pages = ceil($total_records / $limit);

// Get products with pagination
$query = "SELECT p.*, k.nama_kategori FROM produk p 
          LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
          WHERE $where 
          ORDER BY p.tanggal_input DESC LIMIT $start, $limit";
$result = mysqli_query($koneksi, $query);

// Get all categories for filter
$query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($koneksi, $query_kategori);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin Jenang Kudus</title>
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
                    <h1 class="h2">Kelola Produk</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="tambah_produk.php" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Tambah Produk
                        </a>
                    </div>
                </div>
                
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Search and Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="" method="GET" class="row g-3">
                            <div class="col-md-6">
                                <label for="search" class="form-label">Cari Produk</label>
                                <input type="text" class="form-control" id="search" name="search" placeholder="Nama atau deskripsi produk" value="<?php echo $search; ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="kategori" class="form-label">Filter Kategori</label>
                                <select class="form-select" id="kategori" name="kategori">
                                    <option value="">Semua Kategori</option>
                                    <?php while ($row_kat = mysqli_fetch_assoc($result_kategori)): ?>
                                    <option value="<?php echo $row_kat['id_kategori']; ?>" <?php echo ($kategori == $row_kat['id_kategori']) ? 'selected' : ''; ?>>
                                        <?php echo $row_kat['nama_kategori']; ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Products Table -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">No</th>
                                        <th width="80">Gambar</th>
                                        <th>Nama Produk</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th>Terjual</th>
                                        <th>Unggulan</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($result) > 0) {
                                        $no = $start + 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <?php if (!empty($row['gambar']) && file_exists('../assets/img/products/' . $row['gambar'])): ?>
                                            <img src="../assets/img/products/<?php echo $row['gambar']; ?>" alt="<?php echo $row['nama_produk']; ?>" class="table-img">
                                            <?php else: ?>
                                            <img src="../assets/img/no-image.jpg" alt="No Image" class="table-img">
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $row['nama_produk']; ?></td>
                                        <td><?php echo $row['nama_kategori'] ?? 'Tidak ada kategori'; ?></td>
                                        <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if ($row['stok'] <= 0): ?>
                                            <span class="badge bg-danger">Habis</span>
                                            <?php elseif ($row['stok'] <= 10): ?>
                                            <span class="badge bg-warning text-dark"><?php echo $row['stok']; ?></span>
                                            <?php else: ?>
                                            <span class="badge bg-success"><?php echo $row['stok']; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $row['terjual']; ?></td>
                                        <td>
                                            <?php if ($row['unggulan'] == 1): ?>
                                            <span class="badge bg-primary"><i class="bi bi-star-fill"></i> Ya</span>
                                            <?php else: ?>
                                            <span class="badge bg-secondary">Tidak</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="edit_produk.php?id=<?php echo $row['id_produk']; ?>" class="btn btn-sm btn-primary mb-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="../detail_produk.php?id=<?php echo $row['id_produk']; ?>" target="_blank" class="btn btn-sm btn-info mb-1">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="produk.php?delete=<?php echo $row['id_produk']; ?>" class="btn btn-sm btn-danger mb-1 btn-delete">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="9" class="text-center">Tidak ada produk</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mt-3">
                                <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=1<?php echo (!empty($search)) ? '&search='.$search : ''; ?><?php echo (!empty($kategori)) ? '&kategori='.$kategori : ''; ?>" aria-label="First">
                                        <span aria-hidden="true">&laquo;&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo (!empty($search)) ? '&search='.$search : ''; ?><?php echo (!empty($kategori)) ? '&kategori='.$kategori : ''; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);
                                
                                for ($i = $start_page; $i <= $end_page; $i++):
                                ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo (!empty($search)) ? '&search='.$search : ''; ?><?php echo (!empty($kategori)) ? '&kategori='.$kategori : ''; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo (!empty($search)) ? '&search='.$search : ''; ?><?php echo (!empty($kategori)) ? '&kategori='.$kategori : ''; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $total_pages; ?><?php echo (!empty($search)) ? '&search='.$search : ''; ?><?php echo (!empty($kategori)) ? '&kategori='.$kategori : ''; ?>" aria-label="Last">
                                        <span aria-hidden="true">&raquo;&raquo;</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
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