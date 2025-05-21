<?php
// File: konfirmasi.php
// Halaman konfirmasi pesanan
session_start();

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

// Handle file upload
$upload_success = false;
$upload_error = '';

if (isset($_POST['submit_confirmation'])) {
    // Check if file was uploaded
    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        $file_name = $_FILES['bukti_pembayaran']['name'];
        $file_size = $_FILES['bukti_pembayaran']['size'];
        $file_tmp = $_FILES['bukti_pembayaran']['tmp_name'];
        $file_type = $_FILES['bukti_pembayaran']['type'];
        
        // Check file type
        if (!in_array($file_type, $allowed_types)) {
            $upload_error = "Jenis file tidak didukung. Harap unggah gambar (JPG, JPEG, PNG).";
        }
        // Check file size
        else if ($file_size > $max_size) {
            $upload_error = "Ukuran file terlalu besar. Maksimal 2MB.";
        }
        else {
            // Generate new file name
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = $order_id . '_' . date('YmdHis') . '.' . $file_extension;
            $upload_path = 'uploads/bukti_pembayaran/' . $new_file_name;
            
            // Move file
            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Update order status
                $query_update = "UPDATE pesanan SET status = 'dibayar', bukti_pembayaran = '$new_file_name', tanggal_pembayaran = NOW() 
                               WHERE id_pesanan = '$order_id'";
                $result_update = mysqli_query($koneksi, $query_update);
                
                if ($result_update) {
                    $upload_success = true;
                    
                    // Clear cart
                    echo "<script>localStorage.removeItem('cart');</script>";
                    
                    // Clear session order_id
                    unset($_SESSION['order_id']);
                    
                    // Reload page to show updated status
                    header("Location: konfirmasi.php?order_id=$order_id&status=success");
                    exit;
                } else {
                    $upload_error = "Gagal mengupdate status pesanan: " . mysqli_error($koneksi);
                }
            } else {
                $upload_error = "Gagal mengunggah file. Silahkan coba lagi.";
            }
        }
    } else {
        $upload_error = "Harap pilih file bukti pembayaran.";
    }
}

// Check if confirmation was successful
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $upload_success = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembayaran - Jenang Kudus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Include navbar -->
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($upload_success): ?>
                <div class="alert alert-success mb-4">
                    <h4 class="alert-heading">Pembayaran Berhasil Dikonfirmasi!</h4>
                    <p>Terima kasih atas pesanan Anda. Tim kami akan segera memproses pesanan Anda.</p>
                    <hr>
                    <p class="mb-0">Anda dapat melihat status pesanan Anda dengan menggunakan nomor pesanan: <strong><?php echo $order_id; ?></strong></p>
                </div>
                <?php else: ?>
                <?php if (!empty($upload_error)): ?>
                <div class="alert alert-danger mb-4">
                    <?php echo $upload_error; ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0">Detail Pesanan #<?php echo $order_id; ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Informasi Pesanan</h5>
                                <p class="mb-1"><strong>Status:</strong> 
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
                                <p class="mb-1"><strong>Tanggal Pesanan:</strong> <?php echo date('d F Y H:i', strtotime($order['tanggal_pesanan'])); ?></p>
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
                                <?php if (!empty($order['catatan'])): ?>
                                <p class="mb-1"><strong>Catatan:</strong> <?php echo $order['catatan']; ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h5>Informasi Pengiriman</h5>
                                <p class="mb-1"><strong>Nama:</strong> <?php echo $order['nama']; ?></p>
                                <p class="mb-1"><strong>Email:</strong> <?php echo $order['email']; ?></p>
                                <p class="mb-1"><strong>Telepon:</strong> <?php echo $order['telepon']; ?></p>
                                <p class="mb-1"><strong>Alamat:</strong> <?php echo $order['alamat']; ?>, <?php echo $order['kota']; ?>, <?php echo $order['kode_pos']; ?></p>
                            </div>
                        </div>
                        
                        <h5>Item Pesanan</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query_items = "SELECT pi.*, p.nama_produk, p.gambar 
                                                   FROM pesanan_item pi 
                                                   JOIN produk p ON pi.id_produk = p.id_produk 
                                                   WHERE pi.id_pesanan = '$order_id'";
                                    $result_items = mysqli_query($koneksi, $query_items);
                                    
                                    while ($item = mysqli_fetch_assoc($result_items)) {
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="assets/img/products/<?php echo $item['gambar']; ?>" alt="<?php echo $item['nama_produk']; ?>" class="order-item-img me-3">
                                                <span><?php echo $item['nama_produk']; ?></span>
                                            </div>
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
                                        <td colspan="3" class="text-end"><strong>Subtotal</strong></td>
                                        <td>Rp <?php echo number_format($order['total_harga'] - 10000, 0, ',', '.'); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Pengiriman</strong></td>
                                        <td>Rp 10.000</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total</strong></td>
                                        <td><strong>Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <?php if ($order['status'] == 'pending' && $order['metode_pembayaran'] != 'cod'): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0">Konfirmasi Pembayaran</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <p class="mb-0">Silahkan lakukan pembayaran sesuai dengan metode pembayaran yang Anda pilih dan unggah bukti pembayaran di bawah ini.</p>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Informasi Rekening</h5>
                            <?php if ($order['metode_pembayaran'] == 'transfer_bank'): ?>
                            <p class="mb-1"><strong>Bank BCA</strong></p>
                            <p class="mb-1">No. Rekening: 8720384728</p>
                            <p class="mb-0">Atas Nama: PT Jenang Kudus Indonesia</p>
                            <?php elseif ($order['metode_pembayaran'] == 'e_wallet'): ?>
                            <p class="mb-1"><strong>OVO/DANA/GoPay</strong></p>
                            <p class="mb-0">Nomor: 081234567890 (Jenang Kudus)</p>
                            <?php endif; ?>
                        </div>
                        
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="bukti_pembayaran" class="form-label">Unggah Bukti Pembayaran</label>
                                <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/jpeg,image/png,image/jpg" required>
                                <div class="form-text">Format yang didukung: JPG, JPEG, PNG. Maksimal 2MB.</div>
                            </div>
                            <button type="submit" name="submit_confirmation" class="btn btn-primary">Konfirmasi Pembayaran</button>
                        </form>
                    </div>
                </div>
                <?php elseif ($order['status'] == 'dibayar' || $order['status'] == 'diproses' || $order['status'] == 'dikirim'): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0">Bukti Pembayaran</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <p class="mb-0">Pembayaran Anda telah dikonfirmasi. Terima kasih!</p>
                        </div>
                        
                        <?php if (!empty($order['bukti_pembayaran'])): ?>
                        <div class="text-center">
                            <img src="uploads/bukti_pembayaran/<?php echo $order['bukti_pembayaran']; ?>" alt="Bukti Pembayaran" class="img-fluid" style="max-height: 400px;">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <a href="invoice.php?order_id=<?php echo $order_id; ?>" class="btn btn-outline-primary me-2">
                        <i class="bi bi-printer"></i> Cetak Invoice
                    </a>
                    <a href="index.php" class="btn btn-primary">
                        <i class="bi bi-house"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Include footer -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <?php if ($upload_success): ?>
    <script>
        // Clear cart
        localStorage.removeItem('cart');
        updateCartCount();
    </script>
    <?php endif; ?>
</body>
</html>