<?php
// File: admin/tambah_produk.php
// Halaman tambah produk admin

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

// Handle form submission
if (isset($_POST['submit'])) {
    // Get form data
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $id_kategori = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $berat = mysqli_real_escape_string($koneksi, $_POST['berat']);
    $kadaluarsa = mysqli_real_escape_string($koneksi, $_POST['kadaluarsa']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $komposisi = mysqli_real_escape_string($koneksi, $_POST['komposisi']);
    $penyimpanan = mysqli_real_escape_string($koneksi, $_POST['penyimpanan']);
    $unggulan = isset($_POST['unggulan']) ? 1 : 0;
    
    // Validate form
    if (empty($nama_produk) || empty($harga) || empty($stok) || empty($berat)) {
        $_SESSION['error'] = "Semua field yang ditandai (*) wajib diisi.";
    } else {
        // Upload main image
        $gambar = '';
        if ($_FILES['gambar']['error'] == 0) {
            $upload = uploadGambar($_FILES['gambar'], '../assets/img/products/');
            
            if ($upload['success']) {
                $gambar = $upload['file_name'];
            } else {
                $_SESSION['error'] = $upload['message'];
            }
        }
        
        if (!isset($_SESSION['error'])) {
            // Insert product data
            $query = "INSERT INTO produk (id_kategori, nama_produk, deskripsi, harga, stok, berat, gambar, kadaluarsa, komposisi, penyimpanan, unggulan) 
                     VALUES ('$id_kategori', '$nama_produk', '$deskripsi', '$harga', '$stok', '$berat', '$gambar', '$kadaluarsa', '$komposisi', '$penyimpanan', '$unggulan')";
            $result = mysqli_query($koneksi, $query);
            
            if ($result) {
                $id_produk = mysqli_insert_id($koneksi);
                
                // Upload additional images
                if ($_FILES['gambar_tambahan']['error'][0] == 0) {
                    $total_files = count($_FILES['gambar_tambahan']['name']);
                    
                    for ($i = 0; $i < $total_files; $i++) {
                        $file = [
                            'name' => $_FILES['gambar_tambahan']['name'][$i],
                            'type' => $_FILES['gambar_tambahan']['type'][$i],
                            'tmp_name' => $_FILES['gambar_tambahan']['tmp_name'][$i],
                            'error' => $_FILES['gambar_tambahan']['error'][$i],
                            'size' => $_FILES['gambar_tambahan']['size'][$i]
                        ];
                        
                        if ($file['error'] == 0) {
                            $upload = uploadGambar($file, '../assets/img/products/');
                            
                            if ($upload['success']) {
                                $gambar_tambahan = $upload['file_name'];
                                
                                // Insert additional image
                                $query_img = "INSERT INTO produk_gambar (id_produk, gambar, urutan) 
                                             VALUES ('$id_produk', '$gambar_tambahan', '$i')";
                                mysqli_query($koneksi, $query_img);
                            }
                        }
                    }
                }
                
                $_SESSION['success'] = "Produk berhasil ditambahkan.";
                header("Location: produk.php");
                exit;
            } else {
                $_SESSION['error'] = "Gagal menambahkan produk: " . mysqli_error($koneksi);
            }
        }
    }
}

// Get all categories
$query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($koneksi, $query_kategori);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Admin Jenang Kudus</title>
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
                    <h1 class="h2">Tambah Produk</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="produk.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Add Product Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">Informasi Produk</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="nama_produk" class="form-label">Nama Produk *</label>
                                                <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="id_kategori" class="form-label">Kategori</label>
                                                <select class="form-select" id="id_kategori" name="id_kategori">
                                                    <option value="">Pilih Kategori</option>
                                                    <?php while ($row = mysqli_fetch_assoc($result_kategori)): ?>
                                                    <option value="<?php echo $row['id_kategori']; ?>"><?php echo $row['nama_kategori']; ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="deskripsi" class="form-label">Deskripsi Produk</label>
                                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5"></textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="harga" class="form-label">Harga (Rp) *</label>
                                                    <input type="number" class="form-control" id="harga" name="harga" min="0" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="stok" class="form-label">Stok *</label>
                                                    <input type="number" class="form-control" id="stok" name="stok" min="0" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="berat" class="form-label">Berat (gram) *</label>
                                                    <input type="number" class="form-control" id="berat" name="berat" min="0" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="kadaluarsa" class="form-label">Masa Kadaluarsa (hari)</label>
                                                    <input type="number" class="form-control" id="kadaluarsa" name="kadaluarsa" min="0">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="komposisi" class="form-label">Komposisi</label>
                                                <textarea class="form-control" id="komposisi" name="komposisi" rows="3"></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="penyimpanan" class="form-label">Cara Penyimpanan</label>
                                                <textarea class="form-control" id="penyimpanan" name="penyimpanan" rows="3"></textarea>
                                            </div>
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="unggulan" name="unggulan" value="1">
                                                <label class="form-check-label" for="unggulan">Jadikan Produk Unggulan</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">Gambar Produk</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="gambar" class="form-label">Gambar Utama</label>
                                                <input type="file" class="form-control image-input" id="gambar" name="gambar" accept="image/*" data-preview="#imagePreview">
                                                <div class="image-preview mt-2" id="imagePreview">
                                                    <img src="../assets/img/no-image.jpg" alt="Preview">
                                                </div>
                                                <small class="text-muted">Format: JPG, JPEG, PNG. Maks: 2MB</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">Gambar Tambahan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="gambar_tambahan" class="form-label">Upload Multiple Gambar</label>
                                                <input type="file" class="form-control" id="multiImageInput" name="gambar_tambahan[]" accept="image/*" multiple>
                                                <small class="text-muted">Format: JPG, JPEG, PNG. Maks: 2MB</small>
                                            </div>
                                            <div class="row" id="multiImagePreview">
                                                <!-- Preview will be shown here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end mt-3">
                                <button type="reset" class="btn btn-secondary me-2">Reset</button>
                                <button type="submit" name="submit" class="btn btn-primary">Simpan Produk</button>
                            </div>
                        </form>
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