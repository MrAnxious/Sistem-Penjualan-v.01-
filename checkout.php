<?php
// File: checkout.php
// Halaman checkout
session_start();

// Include koneksi database
require_once 'config/koneksi.php';
require_once 'config/functions.php';

// Generate order ID
if (!isset($_SESSION['order_id'])) {
    $_SESSION['order_id'] = 'INV-' . date('YmdHis') . rand(100, 999);
}
$order_id = $_SESSION['order_id'];

// Handle form submission
$checkout_success = false;
if (isset($_POST['submit_checkout'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $telepon = mysqli_real_escape_string($koneksi, $_POST['telepon']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kota = mysqli_real_escape_string($koneksi, $_POST['kota']);
    $kode_pos = mysqli_real_escape_string($koneksi, $_POST['kode_pos']);
    $metode_pembayaran = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    
    // Validasi data
    if (empty($nama) || empty($email) || empty($telepon) || empty($alamat) || empty($kota) || empty($kode_pos)) {
        $error = "Semua field harus diisi";
    } else {
        // Save customer data
        $query_customer = "INSERT INTO customer (nama, email, telepon, alamat, kota, kode_pos) 
                           VALUES ('$nama', '$email', '$telepon', '$alamat', '$kota', '$kode_pos')
                           ON DUPLICATE KEY UPDATE 
                           alamat = '$alamat', kota = '$kota', kode_pos = '$kode_pos'";
        $result_customer = mysqli_query($koneksi, $query_customer);
        
        if ($result_customer) {
            $id_customer = mysqli_insert_id($koneksi);
            if ($id_customer == 0) {
                // Get customer ID if already exists
                $query_get_id = "SELECT id_customer FROM customer WHERE email = '$email'";
                $result_get_id = mysqli_query($koneksi, $query_get_id);
                $row_id = mysqli_fetch_assoc($result_get_id);
                $id_customer = $row_id['id_customer'];
            }
            
            // Validate and parse total_harga
            $total_harga = 0;
            if (isset($_POST['total_harga']) && !empty($_POST['total_harga'])) {
                $total_harga = (float)$_POST['total_harga'];
            } else {
                // If total_harga is not provided or is empty, calculate from cart items
                $cart_items = json_decode($_POST['cart_items'], true);
                foreach ($cart_items as $item) {
                    $total_harga += ($item['price'] * $item['quantity']);
                }
                // Add shipping cost (assumed to be 10,000)
                $total_harga += 10000;
            }
            
            // Ensure total_harga is a valid number
            if ($total_harga <= 0) {
                $error = "Total harga tidak valid";
            } else {
                // Insert order data
                $query_order = "INSERT INTO pesanan (id_pesanan, id_customer, total_harga, metode_pembayaran, catatan, status)
                               VALUES ('$order_id', '$id_customer', $total_harga, '$metode_pembayaran', '$catatan', 'pending')";
                $result_order = mysqli_query($koneksi, $query_order);
                
                if ($result_order) {
                    // Get cart items
                    $cart_items = json_decode($_POST['cart_items'], true);
                    
                    foreach ($cart_items as $item) {
                        $id_produk = $item['id'];
                        $jumlah = $item['quantity'];
                        $harga = $item['price'];
                        $subtotal = $item['price'] * $item['quantity'];
                        
                        // Insert order items
                        $query_item = "INSERT INTO pesanan_item (id_pesanan, id_produk, jumlah, harga, subtotal)
                                      VALUES ('$order_id', '$id_produk', '$jumlah', '$harga', '$subtotal')";
                        mysqli_query($koneksi, $query_item);
                        
                        // Update product stock
                        $query_update_stock = "UPDATE produk SET stok = stok - $jumlah, terjual = terjual + $jumlah 
                                              WHERE id_produk = '$id_produk'";
                        mysqli_query($koneksi, $query_update_stock);
                    }
                    
                    $checkout_success = true;
                    
                    // Redirect to confirmation page
                    header("Location: konfirmasi.php?order_id=$order_id");
                    exit;
                } else {
                    $error = "Gagal menyimpan data pesanan: " . mysqli_error($koneksi);
                }
            }
        } else {
            $error = "Gagal menyimpan data customer: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Jenang Kudus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Include navbar -->
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <h2 class="mb-4">Checkout</h2>
        
        <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($checkout_success): ?>
        <div class="alert alert-success">
            Pesanan berhasil dibuat! Silahkan melanjutkan ke pembayaran.
        </div>
        <?php else: ?>
        <form id="checkout-form" method="POST" action="">
            <div class="row">
                <!-- Form Informasi Pengiriman -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Pengiriman</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap *</label>
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="telepon" class="form-label">Nomor Telepon *</label>
                                <input type="tel" class="form-control" id="telepon" name="telepon" required>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat Lengkap *</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="kota" class="form-label">Kota/Kabupaten *</label>
                                    <input type="text" class="form-control" id="kota" name="kota" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="kode_pos" class="form-label">Kode Pos *</label>
                                    <input type="text" class="form-control" id="kode_pos" name="kode_pos" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Metode Pembayaran -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Metode Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="metode_pembayaran" id="transfer_bank" value="transfer_bank" checked>
                                <label class="form-check-label" for="transfer_bank">
                                    Transfer Bank
                                </label>
                                <div class="payment-details bg-light p-3 mt-2" id="transfer_bank_details">
                                    <p class="mb-2">Silahkan transfer ke rekening berikut:</p>
                                    <p class="mb-1"><strong>Bank BCA</strong></p>
                                    <p class="mb-1">No. Rekening: 8720384728</p>
                                    <p class="mb-0">Atas Nama: PT Jenang Kudus Indonesia</p>
                                </div>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="metode_pembayaran" id="e_wallet" value="e_wallet">
                                <label class="form-check-label" for="e_wallet">
                                    E-Wallet (OVO/DANA/GoPay)
                                </label>
                                <div class="payment-details bg-light p-3 mt-2 d-none" id="e_wallet_details">
                                    <p class="mb-2">Silahkan transfer ke nomor berikut:</p>
                                    <p class="mb-1"><strong>OVO/DANA/GoPay</strong></p>
                                    <p class="mb-0">Nomor: 081234567890 (Jenang Kudus)</p>
                                </div>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metode_pembayaran" id="cod" value="cod">
                                <label class="form-check-label" for="cod">
                                    Bayar di Tempat (COD)
                                </label>
                                <div class="payment-details bg-light p-3 mt-2 d-none" id="cod_details">
                                    <p class="mb-0">Anda dapat membayar ketika pesanan sampai di alamat tujuan. COD hanya tersedia untuk wilayah Kudus dan sekitarnya.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Catatan -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Catatan Pesanan</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Tambahkan catatan untuk pesanan Anda (opsional)"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ringkasan Pesanan -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Ringkasan Pesanan</h5>
                        </div>
                        <div class="card-body">
                            <div id="order-items">
                                <!-- Order items will be loaded here via JavaScript -->
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span id="subtotal">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Pengiriman</span>
                                <span id="shipping">Rp 0</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total</strong>
                                <strong id="total">Rp 0</strong>
                            </div>
                            
                            <input type="hidden" name="cart_items" id="cart_items_input">
                            <input type="hidden" name="total_harga" id="total_harga_input" value="0">
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                            
                            <button type="submit" name="submit_checkout" id="btn-place-order" class="btn btn-success w-100 btn-lg">
                                Buat Pesanan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <!-- Include footer -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load cart
            loadOrderSummary();
            
            // Payment method toggle
            const paymentMethods = document.querySelectorAll('input[name="metode_pembayaran"]');
            const paymentDetails = document.querySelectorAll('.payment-details');
            
            paymentMethods.forEach(method => {
                method.addEventListener('change', function() {
                    paymentDetails.forEach(detail => {
                        detail.classList.add('d-none');
                    });
                    
                    const selectedMethod = this.id;
                    document.getElementById(selectedMethod + '_details').classList.remove('d-none');
                });
            });
            
            // Form validation
            document.getElementById('checkout-form').addEventListener('submit', function(e) {
                const cartItems = JSON.parse(localStorage.getItem('cart')) || [];
                
                if (cartItems.length === 0) {
                    e.preventDefault();
                    alert('Keranjang belanja Anda kosong. Silahkan pilih produk terlebih dahulu.');
                    window.location.href = 'produk.php';
                }
                
                // Ensure total_harga is a valid number
                const totalHarga = document.getElementById('total_harga_input').value;
                if (isNaN(parseFloat(totalHarga)) || parseFloat(totalHarga) <= 0) {
                    e.preventDefault();
                    alert('Total harga tidak valid. Silahkan muat ulang halaman.');
                }
            });
        });
        
        function loadOrderSummary() {
            const cartItems = JSON.parse(localStorage.getItem('cart')) || [];
            const orderItemsContainer = document.getElementById('order-items');
            
            if (cartItems.length === 0) {
                window.location.href = 'keranjang.php';
                return;
            }
            
            // Clear container
            orderItemsContainer.innerHTML = '';
            
            // Get product details from server
            fetch('config/functions.php?action=get_cart_products', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ cart: cartItems })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const products = data.products;
                    let subtotal = 0;
                    
                    products.forEach(item => {
                        const itemSubtotal = item.price * item.quantity;
                        subtotal += itemSubtotal;
                        
                        const orderItem = document.createElement('div');
                        orderItem.className = 'mb-3';
                        orderItem.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">${item.name}</h6>
                                    <small class="text-muted">${item.quantity} x Rp ${formatNumber(item.price)}</small>
                                </div>
                                <span>Rp ${formatNumber(itemSubtotal)}</span>
                            </div>
                        `;
                        
                        orderItemsContainer.appendChild(orderItem);
                    });
                    
                    // Calculate shipping (example: fixed shipping cost)
                    const shipping = 10000;
                    const total = subtotal + shipping;
                    
                    // Update summary
                    document.getElementById('subtotal').textContent = `Rp ${formatNumber(subtotal)}`;
                    document.getElementById('shipping').textContent = `Rp ${formatNumber(shipping)}`;
                    document.getElementById('total').textContent = `Rp ${formatNumber(total)}`;
                    
                    // Set hidden inputs
                    document.getElementById('cart_items_input').value = JSON.stringify(products);
                    document.getElementById('total_harga_input').value = total;
                }
            })
            .catch(error => {
                console.error('Error loading order summary:', error);
            });
        }
        
        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }
    </script>
</body>
</html>