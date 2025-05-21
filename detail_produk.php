<?php
// File: detail_produk.php
// Halaman detail produk

// Include koneksi database
require_once 'config/koneksi.php';
require_once 'config/functions.php';

// Periksa id produk
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: produk.php");
    exit;
}

$id_produk = mysqli_real_escape_string($koneksi, $_GET['id']);
$query = "SELECT p.*, k.nama_kategori, k.id_kategori FROM produk p 
          JOIN kategori k ON p.id_kategori = k.id_kategori 
          WHERE p.id_produk = '$id_produk'";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: produk.php");
    exit;
}

$row = mysqli_fetch_assoc($result);

// Set page title
$page_title = $row['nama_produk'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $row['nama_produk']; ?> - Jenang Kudus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Include navbar -->
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <!-- Gambar Produk -->
            <div class="col-md-5">
                <img src="assets/img/products/<?php echo $row['gambar']; ?>" alt="<?php echo $row['nama_produk']; ?>" class="img-fluid rounded main-product-image">
                <div class="row mt-3">
                    <?php
                    // Get additional product images
                    $query_images = "SELECT * FROM produk_gambar WHERE id_produk = '$id_produk' LIMIT 4";
                    $result_images = mysqli_query($koneksi, $query_images);
                    
                    // Add main image to thumbnails
                    ?>
                    <div class="col-3">
                        <img src="assets/img/products/<?php echo $row['gambar']; ?>" alt="<?php echo $row['nama_produk']; ?>" class="img-fluid rounded product-thumbnail active" data-img="<?php echo $row['gambar']; ?>">
                    </div>
                    <?php
                    while ($img = mysqli_fetch_assoc($result_images)) {
                    ?>
                    <div class="col-3">
                        <img src="assets/img/products/<?php echo $img['gambar']; ?>" alt="<?php echo $row['nama_produk']; ?>" class="img-fluid rounded product-thumbnail" data-img="<?php echo $img['gambar']; ?>">
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
            
            <!-- Informasi Produk -->
            <div class="col-md-7">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="produk.php">Produk</a></li>
                        <li class="breadcrumb-item"><a href="produk.php?kategori=<?php echo $row['id_kategori']; ?>"><?php echo $row['nama_kategori']; ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $row['nama_produk']; ?></li>
                    </ol>
                </nav>

                <h1 class="mb-3"><?php echo $row['nama_produk']; ?></h1>
                
                <!-- Badge Info -->
                <div class="mb-3">
                    <span class="badge bg-primary">Kategori: <?php echo $row['nama_kategori']; ?></span>
                    <?php if ($row['stok'] > 0): ?>
                    <span class="badge bg-success">Stok: <?php echo $row['stok']; ?></span>
                    <?php else: ?>
                    <span class="badge bg-danger">Stok Habis</span>
                    <?php endif; ?>
                    <span class="badge bg-info">Terjual: <?php echo $row['terjual']; ?></span>
                </div>
                
                <!-- Harga -->
                <h3 class="text-primary mb-4">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></h3>
                
                <!-- Deskripsi -->
                <div class="mb-4">
                    <h5>Deskripsi Produk:</h5>
                    <p><?php echo nl2br($row['deskripsi']); ?></p>
                </div>
                
                <!-- Form untuk menambahkan ke keranjang / beli langsung -->
                <form id="product-form">
                    <input type="hidden" id="product-id" value="<?php echo $row['id_produk']; ?>">
                    <input type="hidden" id="product-price" value="<?php echo $row['harga']; ?>">
                    <input type="hidden" id="product-stock" value="<?php echo $row['stok']; ?>">
                    
                    <!-- Quantity -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quantity" class="form-label">Jumlah:</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-minus">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16">
                                            <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
                                        </svg>
                                    </button>
                                    <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="<?php echo $row['stok']; ?>">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-plus">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label">Subtotal:</label>
                                <h4 id="subtotal" class="text-primary">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></h4>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex">
                        <?php if ($row['stok'] > 0): ?>
                        <button type="button" class="btn btn-primary btn-lg flex-grow-1" id="btn-add-to-cart">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-plus me-2" viewBox="0 0 16 16">
                                <path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9V5.5z"/>
                                <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                            </svg>
                            Tambah ke Keranjang
                        </button>
                        <button type="button" class="btn btn-success btn-lg flex-grow-1" id="btn-buy-now">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lightning me-2" viewBox="0 0 16 16">
                                <path d="M5.52.359A.5.5 0 0 1 6 0h4a.5.5 0 0 1 .474.658L8.694 6H12.5a.5.5 0 0 1 .395.807l-7 9a.5.5 0 0 1-.873-.454L6.823 9.5H3.5a.5.5 0 0 1-.48-.641l2.5-8.5zM6.374 1 4.168 8.5H7.5a.5.5 0 0 1 .478.647L6.78 13.04 11.478 7H8a.5.5 0 0 1-.474-.658L9.306 1H6.374z"/>
                            </svg>
                            Beli Sekarang
                        </button>
                        <?php else: ?>
                        <button type="button" class="btn btn-danger btn-lg w-100" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle me-2" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                            Stok Habis
                        </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab Section -->
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">Informasi Produk</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Ulasan</button>
                    </li>
                </ul>
                <div class="tab-content p-3 border border-top-0 rounded-bottom" id="myTabContent">
                    <!-- Tab Informasi -->
                    <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                        <h5>Informasi Produk</h5>
                        <table class="table">
                            <tr>
                                <td width="20%">Berat</td>
                                <td><?php echo $row['berat']; ?> gram</td>
                            </tr>
                            <tr>
                                <td>Masa Kadaluarsa</td>
                                <td><?php echo $row['kadaluarsa']; ?> hari</td>
                            </tr>
                            <tr>
                                <td>Komposisi</td>
                                <td><?php echo $row['komposisi']; ?></td>
                            </tr>
                            <tr>
                                <td>Cara Penyimpanan</td>
                                <td><?php echo $row['penyimpanan']; ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Tab Ulasan -->
                    <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                        <?php
                        // Get product reviews
                        $query_review = "SELECT r.*, c.nama FROM review r 
                                         JOIN customer c ON r.id_customer = c.id_customer 
                                         WHERE r.id_produk = '$id_produk'
                                         ORDER BY r.tanggal DESC";
                        $result_review = mysqli_query($koneksi, $query_review);
                        
                        if (mysqli_num_rows($result_review) > 0) {
                            while ($review = mysqli_fetch_assoc($result_review)) {
                        ?>
                        <div class="review-item mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-0"><?php echo $review['nama']; ?></h6>
                                    <small class="text-muted"><?php echo date('d F Y', strtotime($review['tanggal'])); ?></small>
                                </div>
                                <div class="rating">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $review['rating']) {
                                            echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill text-warning" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg>';
                                        } else {
                                            echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star text-warning" viewBox="0 0 16 16"><path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z"/></svg>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <p class="mb-0"><?php echo $review['ulasan']; ?></p>
                        </div>
                        <?php
                            }
                        } else {
                            echo '<div class="alert alert-info">Belum ada ulasan untuk produk ini.</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Produk Terkait</h3>
                <div class="row">
                    <?php
                    $kategori_id = $row['id_kategori'];
                    $produk_id = $row['id_produk'];
                    
                    $query_related = "SELECT * FROM produk 
                                     WHERE id_kategori = '$kategori_id' 
                                     AND id_produk != '$produk_id' 
                                     LIMIT 4";
                    $result_related = mysqli_query($koneksi, $query_related);
                    
                    while ($related = mysqli_fetch_assoc($result_related)) {
                    ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <img src="assets/img/products/<?php echo $related['gambar']; ?>" class="card-img-top" alt="<?php echo $related['nama_produk']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $related['nama_produk']; ?></h5>
                                <p class="card-text">Rp <?php echo number_format($related['harga'], 0, ',', '.'); ?></p>
                                <div class="d-flex justify-content-between">
                                    <a href="detail_produk.php?id=<?php echo $related['id_produk']; ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                    <button class="btn btn-sm btn-primary add-to-cart" data-id="<?php echo $related['id_produk']; ?>">+ Keranjang</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Product image gallery
            const mainImage = document.querySelector('.main-product-image');
            const thumbnails = document.querySelectorAll('.product-thumbnail');
            
            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                    // Update main image
                    mainImage.src = 'assets/img/products/' + this.getAttribute('data-img');
                    
                    // Update active thumbnail
                    thumbnails.forEach(thumb => thumb.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Quantity adjustment
            const btnMinus = document.getElementById('btn-minus');
            const btnPlus = document.getElementById('btn-plus');
            const quantityInput = document.getElementById('quantity');
            const productPrice = parseFloat(document.getElementById('product-price').value);
            const productStock = parseInt(document.getElementById('product-stock').value);
            const subtotalElement = document.getElementById('subtotal');
            
            // Function to update subtotal
            function updateSubtotal() {
                const quantity = parseInt(quantityInput.value);
                const subtotal = productPrice * quantity;
                subtotalElement.textContent = 'Rp ' + formatNumber(subtotal);
            }
            
            // Decrease quantity
            btnMinus.addEventListener('click', function() {
                let quantity = parseInt(quantityInput.value);
                if (quantity > 1) {
                    quantityInput.value = quantity - 1;
                    updateSubtotal();
                }
            });
            
            // Increase quantity
            btnPlus.addEventListener('click', function() {
                let quantity = parseInt(quantityInput.value);
                if (quantity < productStock) {
                    quantityInput.value = quantity + 1;
                    updateSubtotal();
                }
            });
            
            // Update on manual input
            quantityInput.addEventListener('change', function() {
                let quantity = parseInt(this.value);
                
                if (isNaN(quantity) || quantity < 1) {
                    this.value = 1;
                } else if (quantity > productStock) {
                    this.value = productStock;
                }
                
                updateSubtotal();
            });
            
            // Add to cart button
            const btnAddToCart = document.getElementById('btn-add-to-cart');
            if (btnAddToCart) {
                btnAddToCart.addEventListener('click', function() {
                    const productId = document.getElementById('product-id').value;
                    const quantity = parseInt(quantityInput.value);
                    
                    addToCart(productId, quantity);
                    
                    // Show feedback
                    showSuccessToast('Produk berhasil ditambahkan ke keranjang');
                });
            }
            
            // Buy now button
            const btnBuyNow = document.getElementById('btn-buy-now');
            if (btnBuyNow) {
                btnBuyNow.addEventListener('click', function() {
                    const productId = document.getElementById('product-id').value;
                    const quantity = parseInt(quantityInput.value);
                    
                    // First add to cart
                    addToCart(productId, quantity);
                    
                    // Then redirect to checkout
                    window.location.href = 'checkout.php';
                });
            }
            
            // Format number helper
            function formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }
        });
    </script>
</body>
</html>