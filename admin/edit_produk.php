<?php
// File: admin/edit_produk.php
// Halaman edit produk admin

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

// Cek id produk
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: produk.php");
    exit;
}

$id_produk = mysqli_real_escape_string($koneksi, $_GET['id']);

// Get product data
$query = "SELECT * FROM produk WHERE id_produk = '$id_produk'";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: produk.php");
    exit;
}

$produk = mysqli_fetch_assoc($result);

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
        // Upload main image if changed
        $gambar = $produk['gambar'];
        if ($_FILES['gambar']['error'] == 0) {
            $upload = uploadGambar($_FILES['gambar'], '../assets/img/products/');
            
            if ($upload['success']) {
                // Delete old image if exists
                if (!empty($produk['gambar']) && file_exists('../assets/img/products/' . $produk['gambar'])) {
                    unlink('../assets/img/products/' . $produk['gambar']);
                }
                
                $gambar = $upload['file_name'];
            } else {
                $_SESSION['error'] = $upload['message'];
            }
        }
        
        if (!isset($_SESSION['error'])) {
            // Update product data
            $query = "UPDATE produk SET 
                      id_kategori = '$id_kategori', 
                      nama_produk = '$nama_produk', 
                      deskripsi = '$deskripsi', 
                      harga = '$harga', 
                      stok = '$stok', 
                      berat = '$berat', 
                      gambar = '$gambar', 
                      kadaluarsa = '$kadaluarsa', 
                      komposisi = '$komposisi', 
                      penyimpanan = '$penyimpanan', 
                      unggulan = '$unggulan' 
                      WHERE id_produk = '$id_produk'";
            $result = mysqli_query($koneksi, $query);
            
            if ($result) {
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
                
                // Delete selected additional images
                if (isset($_POST['hapus_gambar']) && is_array($_POST['hapus_gambar'])) {
                    foreach ($_POST['hapus_gambar'] as $id_gambar) {
                        $id_gambar = mysqli_real_escape_string($koneksi, $id_gambar);
                        
                        // Get image filename
                        $query_img = "SELECT gambar FROM produk_gambar WHERE id_gambar = '$id_gambar' AND id_produk = '$id_produk'";
                        $result_img = mysqli_query($koneksi, $query_img);
                        $row_img = mysqli_fetch_assoc($result_img);
                        
                        // Delete image file
                        if (!empty($row_img['gambar']) && file_exists('../assets/img/products/' . $row_img['gambar'])) {
                            unlink('../assets/img/products/' . $row_img['gambar']);
                        }
                        
                        // Delete from database
                        $query_delete = "DELETE FROM produk_gambar WHERE id_gambar = '$id_gambar' AND id_produk = '$id_produk'";
                        mysqli_query($koneksi, $query_delete);
                    }
                }
                
                $_SESSION['success'] = "Produk berhasil diperbarui.";
                header("Location: produk.php");
                exit;
            } else {
                $_SESSION['error'] = "Gagal memperbarui produk: " . mysqli_error($koneksi);
            }
        }
    }
}

// Get all categories
$query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($koneksi, $query_kategori);

// Get additional images
$query_gambar = "SELECT * FROM produk_gambar WHERE id_produk = '$id_produk' ORDER BY urutan ASC";
$result_gambar = mysqli_query($koneksi, $query_gambar);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Admin Jenang Kudus</title>
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
                    <h1 class="h2">Edit Produk</h1>
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
                
                <!-- Edit Product Form -->
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
                                                <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?php echo $produk['nama_produk']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="id_kategori" class="form-label">Kategori</label>
                                                <select class="form-select" id="id_kategori" name="id_kategori">
                                                    <option value="">Pilih Kategori</option>
                                                    <?php 
                                                    mysqli_data_seek($result_kategori, 0);
                                                    while ($row = mysqli_fetch_assoc($result_kategori)): 
                                                    ?>
                                                    <option value="<?php echo $row['id_kategori']; ?>" <?php echo ($produk['id_kategori'] == $row['id_kategori']) ? 'selected' : ''; ?>>
                                                        <?php echo $row['nama_kategori']; ?>
                                                    </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="deskripsi" class="form-label">Deskripsi Produk</label>
                                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5"><?php echo $produk['deskripsi']; ?></textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="harga" class="form-label">Harga (Rp) *</label>
                                                    <input type="number" class="form-control" id="harga" name="harga" min="0" value="<?php echo $produk['harga']; ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="stok" class="form-label">Stok *</label>
                                                    <input type="number" class="form-control" id="stok" name="stok" min="0" value="<?php echo $produk['stok']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="berat" class="form-label">Berat (gram) *</label>
                                                    <input type="number" class="form-control" id="berat" name="berat" min="0" value="<?php echo $produk['berat']; ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="kadaluarsa" class="form-label">Masa Kadaluarsa (hari)</label>
                                                    <input type="number" class="form-control" id="kadaluarsa" name="kadaluarsa" min="0" value="<?php echo $produk['kadaluarsa']; ?>">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="komposisi" class="form-label">Komposisi</label>
                                                <textarea class="form-control" id="komposisi" name="komposisi" rows="3"><?php echo $produk['komposisi']; ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="penyimpanan" class="form-label">Cara Penyimpanan</label>
                                                <textarea class="form-control" id="penyimpanan" name="penyimpanan" rows="3"><?php echo $produk['penyimpanan']; ?></textarea>
                                            </div>
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="unggulan" name="unggulan" value="1" <?php echo ($produk['unggulan'] == 1) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="unggulan">Jadikan Produk Unggulan</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">Gambar Utama</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="gambar" class="form-label">Ubah Gambar Utama</label>
                                                <input type="file" class="form-control image-input" id="gambar" name="gambar" accept="image/*" data-preview="#imagePreview">
                                                <div class="image-preview mt-2" id="imagePreview">
                                                    <?php if (!empty($produk['gambar']) && file_exists('../assets/img/products/' . $produk['gambar'])): ?>
                                                    <img src="../assets/img/products/<?php echo $produk['gambar']; ?>" alt="<?php echo $produk['nama_produk']; ?>">
                                                    <?php else: ?>
                                                    <img src="../assets/img/no-image.jpg" alt="No Image">
                                                    <?php endif; ?>
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
                                            <!-- Current Additional Images -->
                                            <?php if (mysqli_num_rows($result_gambar) > 0): ?>
                                            <div class="mb-3">
                                                <label class="form-label">Gambar Tambahan Saat Ini</label>
                                                <div class="row">
                                                    <?php while ($row_gambar = mysqli_fetch_assoc($result_gambar)): ?>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="card h-100">
                                                            <img src="../assets/img/products/<?php echo $row_gambar['gambar']; ?>" class="card-img-top" alt="Gambar Tambahan">
                                                            <div class="card-body p-2 text-center">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" name="hapus_gambar[]" value="<?php echo $row_gambar['id_gambar']; ?>" id="hapus_gambar_<?php echo $row_gambar['id_gambar']; ?>">
                                                                    <label class="form-check-label" for="hapus_gambar_<?php echo $row_gambar['id_gambar']; ?>">
                                                                        Hapus
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endwhile; ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <!-- Add New Additional Images -->
                                            <div class="mb-3">
                                                <label for="gambar_tambahan" class="form-label">Tambah Gambar Baru</label>
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
                                <a href="produk.php" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" name="submit" class="btn btn-primary">Simpan Perubahan</button>
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