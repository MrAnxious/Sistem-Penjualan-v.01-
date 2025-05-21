<?php
// File: admin/pesanan.php
// Halaman daftar pesanan admin

// Start session
session_start();

// Cek login
if (!isset($_SESSION['admin_login'])) {
    header("Location: login.php");
    exit;
}

// Include koneksi database
require_once '../config/koneksi.php';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search and filter
$where = "1=1";
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($koneksi, $_GET['status']) : '';
$tanggal_awal = isset($_GET['tanggal_awal']) ? mysqli_real_escape_string($koneksi, $_GET['tanggal_awal']) : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? mysqli_real_escape_string($koneksi, $_GET['tanggal_akhir']) : '';

if (!empty($search)) {
    $where .= " AND (p.id_pesanan LIKE '%$search%' OR c.nama LIKE '%$search%' OR c.email LIKE '%$search%')";
}

if (!empty($status)) {
    $where .= " AND p.status = '$status'";
}

if (!empty($tanggal_awal)) {
    $where .= " AND DATE(p.tanggal_pesanan) >= '$tanggal_awal'";
}

if (!empty($tanggal_akhir)) {
    $where .= " AND DATE(p.tanggal_pesanan) <= '$tanggal_akhir'";
}

// Get total orders
$query_count = "SELECT COUNT(*) as total FROM pesanan p 
               JOIN customer c ON p.id_customer = c.id_customer 
               WHERE $where";
$result_count = mysqli_query($koneksi, $query_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_records = $row_count['total'];

$total_pages = ceil($total_records / $limit);

// Get orders with pagination
$query = "SELECT p.*, c.nama, c.email, c.telepon 
         FROM pesanan p 
         JOIN customer c ON p.id_customer = c.id_customer 
         WHERE $where 
         ORDER BY p.tanggal_pesanan DESC 
         LIMIT $start, $limit";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin Jenang Kudus</title>
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
                    <h1 class="h2">Kelola Pesanan</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="laporan.php?tipe=pesanan" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="bi bi-file-earmark-text"></i> Laporan Pesanan
                        </a>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-printer"></i> Ekspor
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="export.php?tipe=pesanan&format=pdf">PDF</a></li>
                                <li><a class="dropdown-item" href="export.php?tipe=pesanan&format=excel">Excel</a></li>
                            </ul>
                        </div>
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
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search" name="search" placeholder="Cari ID pesanan atau nama customer" value="<?php echo $search; ?>">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="pending" <?php echo ($status == 'pending') ? 'selected' : ''; ?>>Menunggu Pembayaran</option>
                                    <option value="dibayar" <?php echo ($status == 'dibayar') ? 'selected' : ''; ?>>Pembayaran Dikonfirmasi</option>
                                    <option value="diproses" <?php echo ($status == 'diproses') ? 'selected' : ''; ?>>Sedang Diproses</option>
                                    <option value="dikirim" <?php echo ($status == 'dikirim') ? 'selected' : ''; ?>>Dalam Pengiriman</option>
                                    <option value="selesai" <?php echo ($status == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                                    <option value="dibatalkan" <?php echo ($status == 'dibatalkan') ? 'selected' : ''; ?>>Dibatalkan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#filterModal">
                                    <i class="bi bi-funnel"></i> Filter Lanjutan
                                </button>
                            </div>
                            
                            <!-- Hidden inputs for date filters -->
                            <input type="hidden" name="tanggal_awal" value="<?php echo $tanggal_awal; ?>">
                            <input type="hidden" name="tanggal_akhir" value="<?php echo $tanggal_akhir; ?>">
                        </form>
                    </div>
                </div>
                
                <!-- Orders Table -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Pesanan</th>
                                        <th>Tanggal</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Metode Pembayaran</th>
                                        <th>Status</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id_pesanan']; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal_pesanan'])); ?></td>
                                        <td>
                                            <div><?php echo $row['nama']; ?></div>
                                            <small class="text-muted"><?php echo $row['email']; ?></small>
                                        </td>
                                        <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php
                                            switch ($row['metode_pembayaran']) {
                                                case 'transfer_bank':
                                                    echo 'Transfer Bank';
                                                    break;
                                                case 'e_wallet':
                                                    echo 'E-Wallet';
                                                    break;
                                                case 'cod':
                                                    echo 'COD';
                                                    break;
                                                default:
                                                    echo $row['metode_pembayaran'];
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            switch ($row['status']) {
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
                                            <a href="detail_pesanan.php?id=<?php echo $row['id_pesanan']; ?>" class="btn btn-sm btn-info mb-1" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="../invoice.php?order_id=<?php echo $row['id_pesanan']; ?>" target="_blank" class="btn btn-sm btn-secondary mb-1" title="Invoice">
                                                <i class="bi bi-file-text"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-primary mb-1" title="Update Status" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $row['id_pesanan']; ?>">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                            
                                            <!-- Status Update Modal -->
                                            <div class="modal fade" id="statusModal<?php echo $row['id_pesanan']; ?>" tabindex="-1" aria-labelledby="statusModalLabel<?php echo $row['id_pesanan']; ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="statusModalLabel<?php echo $row['id_pesanan']; ?>">Update Status Pesanan</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="update_status.php" method="POST">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="id_pesanan" value="<?php echo $row['id_pesanan']; ?>">
                                                                <div class="mb-3">
                                                                    <label for="status<?php echo $row['id_pesanan']; ?>" class="form-label">Status Pesanan</label>
                                                                    <select class="form-select" id="status<?php echo $row['id_pesanan']; ?>" name="status">
                                                                        <option value="pending" <?php echo ($row['status'] == 'pending') ? 'selected' : ''; ?>>Menunggu Pembayaran</option>
                                                                        <option value="dibayar" <?php echo ($row['status'] == 'dibayar') ? 'selected' : ''; ?>>Pembayaran Dikonfirmasi</option>
                                                                        <option value="diproses" <?php echo ($row['status'] == 'diproses') ? 'selected' : ''; ?>>Sedang Diproses</option>
                                                                        <option value="dikirim" <?php echo ($row['status'] == 'dikirim') ? 'selected' : ''; ?>>Dalam Pengiriman</option>
                                                                        <option value="selesai" <?php echo ($row['status'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                                                                        <option value="dibatalkan" <?php echo ($row['status'] == 'dibatalkan') ? 'selected' : ''; ?>>Dibatalkan</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="catatan<?php echo $row['id_pesanan']; ?>" class="form-label">Catatan (Opsional)</label>
                                                                    <textarea class="form-control" id="catatan<?php echo $row['id_pesanan']; ?>" name="catatan" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="7" class="text-center">Tidak ada pesanan</td></tr>';
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
                                    <a class="page-link" href="?page=1<?php echo (!empty($search)) ? '&search='.$search : ''; ?><?php echo (!empty($status)) ? '&status='.$status : ''; ?><?php echo (!empty($tanggal_awal)) ? '&tanggal_awal='.$tanggal_awal : ''; ?><?php echo (!empty($tanggal_akhir)) ? '&tanggal_akhir='.$tanggal_akhir : ''; ?>" aria-label="First">
                                        <span aria-hidden="true">&laquo;&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo (!empty($search)) ? '&search='.$search : ''; ?><?php echo (!empty($status)) ? '&status='.$status : ''; ?><?php echo (!empty($tanggal_awal)) ? '&tanggal_awal='.$tanggal_awal : ''; ?><?php echo (!empty($tanggal_akhir)) ? '&tanggal_akhir='.$tanggal_akhir : ''; ?>" aria-label="Previous">
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
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo (!empty($search)) ? '&search='.$search : ''; ?><?php echo (!empty($status)) ? '&status='.$status : ''; ?><?php echo (!empty($tanggal_awal)) ? '&tanggal_awal='.$tanggal_awal : ''; ?><?php echo (!empty($tanggal_akhir)) ? '&tanggal_akhir='.$tanggal_akhir : ''; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo (!empty($search)) ? '&search='.$search : ''; ?><?php echo (!empty($status)) ? '&status='.$status : ''; ?><?php echo (!empty($tanggal_awal)) ? '&tanggal_awal='.$tanggal_awal : ''; ?><?php echo (!empty($tanggal_akhir)) ? '&tanggal_akhir='.$tanggal_akhir : ''; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $total_pages; ?><?php echo (!empty($search)) ? '&search='.$search : ''; ?><?php echo (!empty($status)) ? '&status='.$status : ''; ?><?php echo (!empty($tanggal_awal)) ? '&tanggal_awal='.$tanggal_awal : ''; ?><?php echo (!empty($tanggal_akhir)) ? '&tanggal_akhir='.$tanggal_akhir : ''; ?>" aria-label="Last">
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
    
    <!-- Advanced Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Lanjutan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="GET" id="filterForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tanggal_awal" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" value="<?php echo $tanggal_awal; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="<?php echo $tanggal_akhir; ?>">
                        </div>
                        
                        <!-- Hidden inputs for other filters -->
                        <input type="hidden" id="modal_search" name="search" value="<?php echo $search; ?>">
                        <input type="hidden" id="modal_status" name="status" value="<?php echo $status; ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
    <script>
        // Copy filter values to modal form
        document.getElementById('search').addEventListener('change', function() {
            document.getElementById('modal_search').value = this.value;
        });
        
        document.getElementById('status').addEventListener('change', function() {
            document.getElementById('modal_status').value = this.value;
        });
    </script>
</body>
</html>