<?php
// File: keranjang.php
// Halaman keranjang belanja yang sudah diperbaiki

// Include koneksi database
require_once 'config/koneksi.php';
require_once 'config/functions.php';

// Tetapkan judul halaman
$page_title = "Keranjang Belanja";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Jenang Kudus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Include navbar -->
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <h2 class="mb-4">Keranjang Belanja</h2>
        
        <!-- Status Alerts -->
        <div id="cart-alerts">
            <!-- Alert akan muncul di sini -->
        </div>
        
        <div id="cart-content">
            <!-- Cart items will be loaded dynamically -->
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Memuat keranjang belanja...</p>
            </div>
        </div>
    </div>

    <!-- Include footer -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cartAlerts = document.getElementById('cart-alerts');
            const cartContent = document.getElementById('cart-content');
            
            // Show alert 
            function showAlert(type, message) {
                const alertHTML = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                cartAlerts.innerHTML = alertHTML;
            }
            
            // Format number helper
            function formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }
            
            // Update cart count in navbar
            function updateCartCount() {
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
                document.querySelectorAll('.cart-count').forEach(el => {
                    el.textContent = cartCount;
                });
            }
            
            // Load cart with better error handling
            loadCart();
            
            // Function to load cart
            function loadCart() {
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                
                if (cart.length === 0) {
                    // Empty cart
                    cartContent.innerHTML = `
                        <div class="text-center py-5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-cart-x text-secondary mb-3" viewBox="0 0 16 16">
                                <path d="M7.354 5.646a.5.5 0 1 0-.708.708L7.793 7.5 6.646 8.646a.5.5 0 1 0 .708.708L8.5 8.207l1.146 1.147a.5.5 0 0 0 .708-.708L9.207 7.5l1.147-1.146a.5.5 0 0 0-.708-.708L8.5 6.793 7.354 5.646z"/>
                                <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                            </svg>
                            <h4 class="mb-3">Keranjang belanja Anda kosong</h4>
                            <p class="text-muted mb-4">Tambahkan beberapa produk untuk mulai berbelanja.</p>
                            <a href="produk.php" class="btn btn-primary">Lihat Produk</a>
                        </div>
                    `;
                    return;
                }
                
                // Show loading state
                cartContent.innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">Memuat keranjang belanja...</p>
                    </div>
                `;
                
                try {
                    // Prepare cart data for API request
                    const cartData = {
                        action: 'get_cart_products',
                        cart: cart
                    };
                    
                    // Perform API request
                    fetch('config/functions.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(cartData)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const products = data.products;
                            
                            if (products.length === 0) {
                                // No products found
                                cartContent.innerHTML = `
                                    <div class="text-center py-5">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-cart-x text-secondary mb-3" viewBox="0 0 16 16">
                                            <path d="M7.354 5.646a.5.5 0 1 0-.708.708L7.793 7.5 6.646 8.646a.5.5 0 1 0 .708.708L8.5 8.207l1.146 1.147a.5.5 0 0 0 .708-.708L9.207 7.5l1.147-1.146a.5.5 0 0 0-.708-.708L8.5 6.793 7.354 5.646z"/>
                                            <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                        </svg>
                                        <h4 class="mb-3">Produk di keranjang tidak tersedia</h4>
                                        <p class="text-muted mb-4">Produk yang Anda tambahkan mungkin sudah tidak tersedia.</p>
                                        <a href="produk.php" class="btn btn-primary">Lihat Produk</a>
                                    </div>
                                `;
                                return;
                            }
                            
                            // Calculate totals
                            let subtotal = 0;
                            let totalWeight = 0;
                            
                            products.forEach(product => {
                                subtotal += (product.price * product.quantity);
                                totalWeight += (product.weight * product.quantity);
                            });
                            
                            // Fixed shipping cost: Rp 10.000
                            const shipping = 10000;
                            const total = subtotal + shipping;
                            
                            // Build cart table
                            let cartTable = `
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h5 class="mb-0">Daftar Produk</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-hover align-middle">
                                                        <thead>
                                                            <tr>
                                                                <th>Produk</th>
                                                                <th>Harga</th>
                                                                <th>Jumlah</th>
                                                                <th>Subtotal</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                            `;
                            
                            products.forEach(product => {
                                const itemSubtotal = product.price * product.quantity;
                                
                                cartTable += `
                                    <tr data-id="${product.id}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="assets/img/products/${product.image}" alt="${product.name}" class="cart-item-img me-3">
                                                <div>
                                                    <h6 class="mb-0">${product.name}</h6>
                                                    <small class="text-muted">${product.weight} gram</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Rp ${formatNumber(product.price)}</td>
                                        <td>
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <button class="btn btn-outline-secondary btn-decrease" type="button" data-id="${product.id}">
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <input type="number" class="form-control text-center item-quantity" value="${product.quantity}" min="1" max="${product.stock}" data-id="${product.id}">
                                                <button class="btn btn-outline-secondary btn-increase" type="button" data-id="${product.id}">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="fw-bold">Rp <span class="item-subtotal">${formatNumber(itemSubtotal)}</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-danger btn-remove" data-id="${product.id}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `;
                            });
                            
                            cartTable += `
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="d-flex justify-content-between mt-3">
                                                    <a href="produk.php" class="btn btn-outline-primary">
                                                        <i class="bi bi-arrow-left me-2"></i>
                                                        Lanjut Belanja
                                                    </a>
                                                    <button id="btn-empty-cart" class="btn btn-outline-danger">
                                                        <i class="bi bi-trash me-2"></i>
                                                        Kosongkan Keranjang
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0">Ringkasan Pesanan</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Subtotal</span>
                                                    <span>Rp ${formatNumber(subtotal)}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Berat Total</span>
                                                    <span>${formatNumber(totalWeight)} gram</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Pengiriman</span>
                                                    <span>Rp ${formatNumber(shipping)}</span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between mb-3">
                                                    <strong>Total</strong>
                                                    <strong class="text-primary">Rp ${formatNumber(total)}</strong>
                                                </div>
                                                <a href="checkout.php" class="btn btn-success w-100 btn-lg">
                                                    <i class="bi bi-credit-card me-2"></i>
                                                    Proses Checkout
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            cartContent.innerHTML = cartTable;
                            
                            // Add event listeners for cart actions
                            setupCartEventListeners();
                        } else {
                            // API returned success: false
                            cartContent.innerHTML = `
                                <div class="alert alert-warning">
                                    <h5>Gagal memuat data keranjang</h5>
                                    <p>${data.message || 'Terjadi kesalahan saat memuat keranjang.'}</p>
                                    <button class="btn btn-primary mt-2" onclick="location.reload()">Muat Ulang</button>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading cart:', error);
                        cartContent.innerHTML = `
                            <div class="alert alert-danger">
                                <h5>Error: Gagal memuat data keranjang</h5>
                                <p>${error.message}</p>
                                <div class="mt-3">
                                    <button class="btn btn-primary" onclick="location.reload()">Muat Ulang Halaman</button>
                                </div>
                            </div>
                        `;
                    });
                } catch (error) {
                    console.error('Error in try/catch:', error);
                    cartContent.innerHTML = `
                        <div class="alert alert-danger">
                            <h5>Terjadi kesalahan serius</h5>
                            <p>${error.message}</p>
                            <p>Silakan muat ulang halaman.</p>
                        </div>
                    `;
                }
            }
            
            // Setup event listeners for cart actions
            function setupCartEventListeners() {
                // Empty cart button
                document.getElementById('btn-empty-cart').addEventListener('click', function() {
                    if (confirm('Apakah Anda yakin ingin mengosongkan keranjang belanja?')) {
                        localStorage.removeItem('cart');
                        updateCartCount();
                        loadCart();
                    }
                });
                
                // Quantity buttons
                document.querySelectorAll('.btn-decrease').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const input = document.querySelector(`.item-quantity[data-id="${id}"]`);
                        let quantity = parseInt(input.value);
                        
                        if (quantity > 1) {
                            quantity--;
                            input.value = quantity;
                            updateCartItem(id, quantity);
                        }
                    });
                });
                
                document.querySelectorAll('.btn-increase').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const input = document.querySelector(`.item-quantity[data-id="${id}"]`);
                        let quantity = parseInt(input.value);
                        const max = parseInt(input.getAttribute('max'));
                        
                        if (quantity < max) {
                            quantity++;
                            input.value = quantity;
                            updateCartItem(id, quantity);
                        }
                    });
                });
                
                document.querySelectorAll('.item-quantity').forEach(input => {
                    input.addEventListener('change', function() {
                        const id = this.getAttribute('data-id');
                        let quantity = parseInt(this.value);
                        const max = parseInt(this.getAttribute('max'));
                        
                        if (isNaN(quantity) || quantity < 1) {
                            quantity = 1;
                            this.value = quantity;
                        } else if (quantity > max) {
                            quantity = max;
                            this.value = quantity;
                        }
                        
                        updateCartItem(id, quantity);
                    });
                });
                
                // Remove buttons
                document.querySelectorAll('.btn-remove').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        
                        if (confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) {
                            removeCartItem(id);
                        }
                    });
                });
            }
            
            // Update cart item quantity
            function updateCartItem(id, quantity) {
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                const index = cart.findIndex(item => item.id === id);
                
                if (index !== -1) {
                    cart[index].quantity = quantity;
                    localStorage.setItem('cart', JSON.stringify(cart));
                    
                    // Update UI
                    updateUIAfterChange(id, quantity);
                    updateCartCount();
                }
            }
            
            // Remove item from cart
            function removeCartItem(id) {
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                const index = cart.findIndex(item => item.id === id);
                
                if (index !== -1) {
                    cart.splice(index, 1);
                    localStorage.setItem('cart', JSON.stringify(cart));
                    
                    // Reload cart
                    updateCartCount();
                    loadCart();
                }
            }
            
            // Update UI after quantity change
            function updateUIAfterChange(id, quantity) {
                // Get product details
                fetch('config/functions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        action: 'get_cart_products',
                        cart: [{ id: id, quantity: quantity }] 
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.products.length > 0) {
                        const product = data.products[0];
                        const subtotal = product.price * quantity;
                        
                        // Update item subtotal
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if (row) {
                            const subtotalElement = row.querySelector('.item-subtotal');
                            if (subtotalElement) {
                                subtotalElement.textContent = formatNumber(subtotal);
                            }
                        }
                        
                        // Recalculate cart totals
                        recalculateCartTotals();
                    }
                })
                .catch(error => {
                    console.error('Error updating cart item:', error);
                    showAlert('danger', 'Gagal memperbarui item keranjang');
                });
            }
            
            // Recalculate cart totals
            function recalculateCartTotals() {
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                
                // Get product details from server
                fetch('config/functions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        action: 'get_cart_products',
                        cart: cart 
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const products = data.products;
                        
                        // Calculate totals
                        let subtotal = 0;
                        let totalWeight = 0;
                        
                        products.forEach(product => {
                            subtotal += (product.price * product.quantity);
                            totalWeight += (product.weight * product.quantity);
                        });
                        
                        // Fixed shipping cost: Rp 10.000
                        const shipping = 10000;
                        const total = subtotal + shipping;
                        
                        // Update UI
                        const summaryDiv = document.querySelector('.card-body');
                        if (summaryDiv) {
                            const summaryItems = summaryDiv.querySelectorAll('.d-flex');
                            
                            // Update subtotal
                            if (summaryItems[0]) {
                                const subtotalElement = summaryItems[0].querySelector('span:last-child');
                                if (subtotalElement) {
                                    subtotalElement.textContent = `Rp ${formatNumber(subtotal)}`;
                                }
                            }
                            
                            // Update total weight
                            if (summaryItems[1]) {
                                const weightElement = summaryItems[1].querySelector('span:last-child');
                                if (weightElement) {
                                    weightElement.textContent = `${formatNumber(totalWeight)} gram`;
                                }
                            }
                            
                            // Update total
                            const totalElement = summaryDiv.querySelector('strong.text-primary');
                            if (totalElement) {
                                totalElement.textContent = `Rp ${formatNumber(total)}`;
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error recalculating cart totals:', error);
                    showAlert('danger', 'Gagal memperbarui total belanja');
                });
            }
        });
    </script>
    
    <style>
        .cart-item-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</body>
</html>