<?php
// File: invoice.php
// Halaman invoice/faktur pesanan

// Include koneksi database
require_once 'config/koneksi.php';
require_once 'config/functions.php';

// Cek order_id
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header("Location: index.php");
    exit;
}

$order_id = mysqli_real_escape_string($koneksi, $_GET['order_id']);

// Get order data
$query = "SELECT p.*, c.nama, c.email, c.telepon, c.alamat, c.kota, c.kode_pos 
          FROM pesanan p 
          JOIN customer c ON p.id_customer = c.id_customer 
          WHERE p.id_pesanan = '$order_id'";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit;
}

$order = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $order_id; ?> - Jenang Kudus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        @media print {
            body {
                font-size: 12pt;
            }
            .no-print, .no-print * {
                display: none !important;
            }
            .container {
                width: 100%;
                max-width: 100%;
            }
            .card {
                border: none !important;
            }
            .card-header {
                background-color: #f8f9fa !important;
                color: #000 !important;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar (hidden when printing) -->
    <div class="no-print">
        <?php include 'includes/navbar.php'; ?>
    </div>
    
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Print button (hidden when printing) -->
                <div class="text-end mb-3 no-print">
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Cetak Invoice
                    </button>
                    <a href="konfirmasi.php?order_id=<?php echo $order_id; ?>" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <!-- Invoice -->
                <div class="card">
                    <div class="card-body p-4">
                        <!-- Header -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <img src="assets/img/logo.png" alt="Jenang Kudus" style="height: 50px;">
                                <p class="mt-2">
                                    Jl. Sunan Kudus No. 123<br>
                                    Kudus, Jawa Tengah 59316<br>
                                    Indonesia<br>
                                    Telp: (0291) 123456<br>
                                    Email: info@jenangkudus.com
                                </p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <h2 class="mb-0">INVOICE</h2>
                                <p class="mb-1">#<?php echo $order_id; ?></p>
                                <p class="mb-1">Tanggal: <?php echo date('d F Y', strtotime($order['tanggal_pesanan'])); ?></p>
                                <p class="mb-0">
                                    Status: 
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
                                </p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Customer & Order Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Ditagihkan kepada:</h5>
                                <p class="mb-1"><strong><?php echo $order['nama']; ?></strong></p>
                                <p class="mb-1"><?php echo $order['alamat']; ?></p>
                                <p class="mb-1"><?php echo $order['kota']; ?>, <?php echo $order['kode_pos']; ?></p>
                                <p class="mb-1">Email: <?php echo $order['email']; ?></p>
                                <p class="mb-0">Telepon: <?php echo $order['telepon']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Informasi Pembayaran:</h5>
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
                                <?php if ($order['status'] == 'dibayar' || $order['status'] == 'diproses' || $order['status'] == 'dikirim' || $order['status'] == 'selesai'): ?>
                                <p class="mb-0"><strong>Tanggal Pembayaran:</strong> <?php echo date('d F Y H:i', strtotime($order['tanggal_pembayaran'])); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Order Items -->
                        <h5>Detail Pesanan:</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query_items = "SELECT pi.*, p.nama_produk, p.berat 
                                                   FROM pesanan_item pi 
                                                   JOIN produk p ON pi.id_produk = p.id_produk 
                                                   WHERE pi.id_pesanan = '$order_id'";
                                    $result_items = mysqli_query($koneksi, $query_items);
                                    
                                    $no = 1;
                                    $total_weight = 0;
                                    while ($item = mysqli_fetch_assoc($result_items)) {
                                        $total_weight += ($item['berat'] * $item['jumlah']);
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <?php echo $item['nama_produk']; ?><br>
                                            <small class="text-muted"><?php echo $item['berat']; ?> gram</small>
                                        </td>
                                        <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                        <td><?php echo $item['jumlah']; ?></td>
                                        <td>Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end">Subtotal</td>
                                        <td>Rp <?php echo number_format($order['total_harga'] - 10000, 0, ',', '.'); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end">Berat Total</td>
                                        <td><?php echo number_format($total_weight, 0, ',', '.'); ?> gram</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end">Pengiriman</td>
                                        <td>Rp 10.000</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Total</strong></td>
                                        <td><strong>Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <!-- Notes -->
                        <?php if (!empty($order['catatan'])): ?>
                        <div class="mt-4">
                            <h5>Catatan:</h5>
                            <p class="mb-0"><?php echo $order['catatan']; ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Footer -->
                        <div class="mt-5 text-center">
                            <p class="mb-0">Terima kasih atas kepercayaan Anda berbelanja di Toko Jenang Kudus.</p>
                            <p class="mb-0">Untuk pertanyaan atau bantuan, silahkan hubungi kami di info@jenangkudus.com atau (0291) 123456.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer (hidden when printing) -->
    <div class="no-print">
        <?php include 'includes/footer.php'; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>