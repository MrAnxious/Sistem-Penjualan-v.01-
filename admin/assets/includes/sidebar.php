<?php
// File: admin/assets/includes/sidebar.php
// Sidebar untuk panel admin
?>
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <img src="../assets/img/logo.png" alt="Jenang Kudus" height="50" class="mb-3">
            <h6 class="sidebar-heading px-3 mt-1 mb-1 text-muted">
                Panel Administrator
            </h6>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'produk.php' || basename($_SERVER['PHP_SELF']) == 'tambah_produk.php' || basename($_SERVER['PHP_SELF']) == 'edit_produk.php') ? 'active' : ''; ?>" href="produk.php">
                    <i class="bi bi-box me-2"></i>
                    Produk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'kategori.php') ? 'active' : ''; ?>" href="kategori.php">
                    <i class="bi bi-tags me-2"></i>
                    Kategori
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'pesanan.php' || basename($_SERVER['PHP_SELF']) == 'detail_pesanan.php') ? 'active' : ''; ?>" href="pesanan.php">
                    <i class="bi bi-cart me-2"></i>
                    Pesanan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'customer.php') ? 'active' : ''; ?>" href="customer.php">
                    <i class="bi bi-people me-2"></i>
                    Customer
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'ulasan.php') ? 'active' : ''; ?>" href="ulasan.php">
                    <i class="bi bi-star me-2"></i>
                    Ulasan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'laporan.php') ? 'active' : ''; ?>" href="laporan.php">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Laporan
                </a>
            </li>
            <?php if ($_SESSION['admin_level'] == 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'pengaturan.php') ? 'active' : ''; ?>" href="pengaturan.php">
                    <i class="bi bi-gear me-2"></i>
                    Pengaturan
                </a>
            </li>
            <?php endif; ?>
        </ul>
        
        <hr>
        
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="../index.php" target="_blank">
                    <i class="bi bi-eye me-2"></i>
                    Lihat Website
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>