<?php
// File: admin/detail_pesanan.php
// Halaman detail pesanan admin

// Start session
session_start();

// Cek login
if (!isset($_SESSION['admin_login'])) {
    header("Location: login.php");
    exit;
}

// Include koneksi database
require_once '../config/koneksi.php';
require_once '../config/functions.php';

// Cek id pesanan
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: pesanan.php");
    exit;
}

$id_pesanan = mysqli_real_escape_string($koneksi, $_GET['id']);

// Get order data
$query = "SELECT p.*, c.nama, c.email, c.telepon, c.alamat, c.kota, c.kode_pos 
          FROM pesanan p 
          JOIN customer c ON p.id_customer = c.id_customer 
          WHERE p.id_pesanan = '$id_pesanan'";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: pesanan.php");
    exit;
}

$order = mysqli_fetch_assoc($result);

// Get order items
$query_items = "SELECT pi.*, p.nama_produk, p.gambar, p.berat 
               FROM pesanan_item pi 
               JOIN produk p ON pi.id_produk = p.id_produk 
               WHERE pi.id_pesanan = '$id_pesanan'";
$result_items = mysqli_query($koneksi, $query_items);

// Calculate total weight
$total_weight = 0;
$items = [];
while ($item = mysqli_fetch_assoc($result_items)) {
    $total_weight += ($item['berat'] * $item['jumlah']);
    $items[] = $item;
}

// Handle status update
if (isset($_POST['update_status'])) {
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    
    // Update status based on selection
    $update_fields = "status = '$status'";
    
    if ($status == 'dibayar' && $order['status'] != 'dibayar') {
        $update_fields .= ", tanggal_pembayaran = NOW()";
    } else if ($status == 'dikirim' && $order['status'] != 'dikirim') {
        $update_fields .= ", tanggal_pengiriman = NOW()";
    } else if ($status == 'selesai' && $order['status'] != 'selesai') {
        $update_fields .= ", tanggal_selesai = NOW()";
    }
    
    $query_update = "UPDATE pesanan SET $update_fields WHERE id_pesanan = '$id_pesanan'";
    $result_update = mysqli_query($koneksi, $query_update);
    
    if ($result_update) {
        $_SESSION['success'] = "Status pesanan berhasil diperbarui";
        header("Location: detail_pesanan.php?id=$id_pesanan");
        exit;
    } else {
        $_SESSION['error'] = "Gagal memperbarui status pesanan: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - Admin Jenang Kudus</title>
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
                    <h1 class="h2">Detail Pesanan</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="pesanan.php" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <a href="../invoice.php?order_id=<?php echo $id_pesanan; ?>" target="_blank" class="btn btn-primary">
                            <i class="bi bi-printer"></i> Cetak Invoice
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
                
                <!-- Order Details -->
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Basic Information -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Informasi Pesanan #<?php echo $id_pesanan; ?></h5>
                                <span class="badge bg-<?php 
                                    switch ($order['status']) {
                                        case 'pending':
                                            echo 'warning';
                                            break;
                                        case 'dibayar':
                                            echo 'info';
                                            break;
                                        case 'diproses':
                                            echo 'primary';
                                            break;
                                        case 'dikirim':
                                            echo 'primary';
                                            break;
                                        case 'selesai':
                                            echo 'success';
                                            break;
                                        case 'dibatalkan':
                                            echo 'danger';
                                            break;
                                        default:
                                            echo 'secondary';
                                    }
                                ?>">
                                    <?php 
                                    switch ($order['status']) {
                                        case 'pending':
                                            echo 'Menunggu Pembayaran';
                                            break;
                                        case 'dibayar':
                                            echo 'Pembayaran Dikonfirmasi';
                                            break;
                                        case 'diproses':
                                            echo 'Sedang Diproses';
                                            break;
                                        case 'dikirim':
                                            echo 'Dalam Pengiriman';
                                            break;
                                        case 'selesai':
                                            echo 'Selesai';
                                            break;
                                        case 'dibatalkan':
                                            echo 'Dibatalkan';
                                            break;
                                        default:
                                            echo 'Tidak Diketahui';
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="mb-2">Informasi Pemesanan</h6>
                                        <p class="mb-1"><strong>Tanggal Pemesanan:</strong> <?php echo date('d/m/Y H:i', strtotime($order['tanggal_pesanan'])); ?></p>
                                        <p class="mb-1"><strong>Metode Pembayaran:</strong> 
                                            <?php
                                            switch ($order['metode_pembayaran']) {
                                                case 'transfer_bank':
                                                    echo 'Transfer Bank';
                                                    break;
                                                case 'e_wallet':
                                                    echo 'E-Wallet (OVO/DANA/GoPay)';
                                                    break;
                                                case 'cod':
                                                    echo 'Bayar di Tempat (COD)';
                                                    break;
                                                default:
                                                    echo $order['metode_pembayaran'];
                                            }
                                            ?>
                                        </p>
                                        <?php if ($order['tanggal_pembayaran']): ?>
                                        <p class="mb-1"><strong>Tanggal Pembayaran:</strong> <?php echo date('d/m/Y H:i', strtotime($order['tanggal_pembayaran'])); ?></p>
                                        <?php endif; ?>
                                        <?php if ($order['tanggal_pengiriman']): ?>
                                        <p class="mb-1"><strong>Tanggal Pengiriman:</strong> <?php echo date('d/m/Y H:i', strtotime($order['tanggal_pengiriman'])); ?></p>
                                        <?php endif; ?>
                                        <?php if ($order['tanggal_selesai']): ?>
                                        <p class="mb-1"><strong>Tanggal Selesai:</strong> <?php echo date('d/m/Y H:i', strtotime($order['tanggal_selesai'])); ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($order['catatan'])): ?>
                                        <p class="mb-0"><strong>Catatan:</strong> <?php echo $order['catatan']; ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-2">Informasi Customer</h6>
                                        <p class="mb-1"><strong>Nama:</strong> <?php echo $order['nama']; ?></p>
                                        <p class="mb-1"><strong>Email:</strong> <?php echo $order['email']; ?></p>
                                        <p class="mb-1"><strong>Telepon:</strong> <?php echo $order['telepon']; ?></p>
                                        <p class="mb-1"><strong>Alamat:</strong> <?php echo $order['alamat']; ?></p>
                                        <p class="mb-0"><strong>Kota/Kode Pos:</strong> <?php echo $order['kota']; ?>, <?php echo $order['kode_pos']; ?></p>
                                    </div>
                                </div>
                                
                                <!-- Status Update Form -->
                                <form action="" method="POST">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="status" class="form-label">Status Pesanan</label>
                                            <select class="form-select" id="orderStatus" name="status">
                                                <option value="pending" <?php echo ($order['status'] == 'pending') ? 'selected' : ''; ?>>Menunggu Pembayaran</option>
                                                <option value="dibayar" <?php echo ($order['status'] == 'dibayar') ? 'selected' : ''; ?>>Pembayaran Dikonfirmasi</option>
                                                <option value="diproses" <?php echo ($order['status'] == 'diproses') ? 'selected' : ''; ?>>Sedang Diproses</option>
                                                <option value="dikirim" <?php echo ($order['status'] == 'dikirim') ? 'selected' : ''; ?>>Dalam Pengiriman</option>
                                                <option value="selesai" <?php echo ($order['status'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                                                <option value="dibatalkan" <?php echo ($order['status'] == 'dibatalkan') ? 'selected' : ''; ?>>Dibatalkan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8 mb-3">
                                            <label for="catatan" class="form-label">Catatan (Opsional)</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="catatan" name="catatan" placeholder="Tambahkan catatan untuk perubahan status">
                                                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Order Items -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Item Pesanan</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th width="60">Gambar</th>
                                                <th>Produk</th>
                                                <th>Harga</th>
                                                <th>Jumlah</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td>
                                                    <?php if (!empty($item['gambar']) && file_exists('../assets/img/products/' . $item['gambar'])): ?>
                                                    <img src="../assets/img/products/<?php echo $item['gambar']; ?>" alt="<?php echo $item['nama_produk']; ?>" class="table-img" style="width: 40px; height: 40px;">
                                                    <?php else: ?>
                                                    <img src="../assets/img/no-image.jpg" alt="No Image" class="table-img" style="width: 40px; height: 40px;">
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div><?php echo $item['nama_produk']; ?></div>
                                                    <small class="text-muted"><?php echo $item['berat']; ?> gram</small>
                                                </td>
                                                <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                                <td><?php echo $item['jumlah']; ?></td>
                                                <td>Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-end fw-bold">Subtotal</td>
                                                <td>Rp <?php echo number_format($order['total_harga'] - 10000, 0, ',', '.'); ?></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-end fw-bold">Pengiriman</td>
                                                <td>Rp 10.000</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-end fw-bold">Total</td>
                                                <td class="fw-bold">Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <!-- Payment Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Informasi Pembayaran</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-3"><strong>Metode Pembayaran:</strong> 
                                    <?php
                                    switch ($order['metode_pembayaran']) {
                                        case 'transfer_bank':
                                            echo 'Transfer Bank';
                                            break;
                                        case 'e_wallet':
                                            echo 'E-Wallet (OVO/DANA/GoPay)';
                                            break;
                                        case 'cod':
                                            echo 'Bayar di Tempat (COD)';
                                            break;
                                        default:
                                            echo $order['metode_pembayaran'];
                                    }
                                    ?>
                                </p>
                                
                                <?php if ($order['metode_pembayaran'] == 'transfer_bank'): ?>
                                <div class="mb-3">
                                    <strong>Detail Rekening:</strong>
                                    <p class="mb-1">Bank BCA</p>
                                    <p class="mb-1">No. Rekening: 8720384728</p>
                                    <p class="mb-0">Atas Nama: PT Jenang Kudus Indonesia</p>
                                </div>
                                <?php elseif ($order['metode_pembayaran'] == 'e_wallet'): ?>
                                <div class="mb-3">
                                    <strong>Detail E-Wallet:</strong>
                                    <p class="mb-1">OVO/DANA/GoPay</p>
                                    <p class="mb-0">Nomor: 081234567890 (Jenang Kudus)</p>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($order['bukti_pembayaran'])): ?>
                                <div class="mb-3">
                                    <strong>Bukti Pembayaran:</strong>
                                    <div class="mt-2">
                                        <img src="../uploads/bukti_pembayaran/<?php echo $order['bukti_pembayaran']; ?>" alt="Bukti Pembayaran" class="img-fluid rounded">
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Payment Timeline -->
                                <div class="payment-timeline mt-4">
                                    <h6>Status Pembayaran:</h6>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>
                                                <i class="bi bi-circle-fill <?php echo ($order['status'] != 'dibatalkan') ? 'text-success' : 'text-danger'; ?> me-2" style="font-size: 0.6rem;"></i>
                                                Pesanan Dibuat
                                            </span>
                                            <small><?php echo date('d/m/Y H:i', strtotime($order['tanggal_pesanan'])); ?></small>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>
                                                <i class="bi bi-circle-fill <?php echo ($order['status'] == 'dibayar' || $order['status'] == 'diproses' || $order['status'] == 'dikirim' || $order['status'] == 'selesai') ? 'text-success' : (($order['status'] == 'dibatalkan') ? 'text-danger' : 'text-secondary'); ?> me-2" style="font-size: 0.6rem;"></i>
                                                Pembayaran Dikonfirmasi
                                            </span>
                                            <?php if ($order['tanggal_pembayaran']): ?>
                                            <small><?php echo date('d/m/Y H:i', strtotime($order['tanggal_pembayaran'])); ?></small>
                                            <?php else: ?>
                                            <small>-</small>
                                            <?php endif; ?>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>
                                                <i class="bi bi-circle-fill <?php echo ($order['status'] == 'diproses' || $order['status'] == 'dikirim' || $order['status'] == 'selesai') ? 'text-success' : (($order['status'] == 'dibatalkan') ? 'text-danger' : 'text-secondary'); ?> me-2" style="font-size: 0.6rem;"></i>
                                                Pesanan Diproses
                                            </span>
                                            <small>-</small>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>
                                                <i class="bi bi-circle-fill <?php echo ($order['status'] == 'dikirim' || $order['status'] == 'selesai') ? 'text-success' : (($order['status'] == 'dibatalkan') ? 'text-danger' : 'text-secondary'); ?> me-2" style="font-size: 0.6rem;"></i>
                                                Pesanan Dikirim
                                            </span>
                                            <?php if ($order['tanggal_pengiriman']): ?>
                                            <small><?php echo date('d/m/Y H:i', strtotime($order['tanggal_pengiriman'])); ?></small>
                                            <?php else: ?>
                                            <small>-</small>
                                            <?php endif; ?>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>
                                                <i class="bi bi-circle-fill <?php echo ($order['status'] == 'selesai') ? 'text-success' : (($order['status'] == 'dibatalkan') ? 'text-danger' : 'text-secondary'); ?> me-2" style="font-size: 0.6rem;"></i>
                                                Pesanan Selesai
                                            </span>
                                            <?php if ($order['tanggal_selesai']): ?>
                                            <small><?php echo date('d/m/Y H:i', strtotime($order['tanggal_selesai'])); ?></small>
                                            <?php else: ?>
                                            <small>-</small>
                                            <?php endif; ?>
                                        </li>
                                        <?php if ($order['status'] == 'dibatalkan'): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>
                                                <i class="bi bi-circle-fill text-danger me-2" style="font-size: 0.6rem;"></i>
                                                Pesanan Dibatalkan
                                            </span>
                                            <small>-</small>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Shipping Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Informasi Pengiriman</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Alamat Pengiriman:</strong></p>
                                <p class="mb-1"><?php echo $order['nama']; ?></p>
                                <p class="mb-1"><?php echo $order['telepon']; ?></p>
                                <p class="mb-1"><?php echo $order['alamat']; ?></p>
                                <p class="mb-3"><?php echo $order['kota']; ?>, <?php echo $order['kode_pos']; ?></p>
                                
                                <p class="mb-1"><strong>Total Berat:</strong> <?php echo number_format($total_weight, 0, ',', '.'); ?> gram</p>
                                <p class="mb-0"><strong>Biaya Pengiriman:</strong> Rp 10.000</p>
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