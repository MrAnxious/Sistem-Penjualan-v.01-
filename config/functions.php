<?php
// File: config/functions.php
// Fungsi-fungsi yang digunakan dalam website toko jenang

// Aktifkan error reporting untuk development (matikan di production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Mulai output buffering untuk mencegah output tidak disengaja
ob_start();

// Include koneksi database
require_once 'koneksi.php';

// Fungsi untuk sanitasi input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fungsi untuk mengubah format uang
function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Fungsi untuk memformat tanggal ke Indonesia
function tanggalIndonesia($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $pecah = explode('-', $tanggal);
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}

// Fungsi untuk membuat slug
function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}

// Fungsi untuk upload gambar
function uploadGambar($file, $destination) {
    global $koneksi;
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    $file_name = $file['name'];
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    $file_type = $file['type'];
    
    // Check file type
    if (!in_array($file_type, $allowed_types)) {
        return [
            'success' => false,
            'message' => "Jenis file tidak didukung. Harap unggah gambar (JPG, JPEG, PNG)."
        ];
    }
    
    // Check file size
    if ($file_size > $max_size) {
        return [
            'success' => false,
            'message' => "Ukuran file terlalu besar. Maksimal 2MB."
        ];
    }
    
    // Generate new file name
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    $new_file_name = 'IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file_extension;
    $upload_path = $destination . $new_file_name;
    
    // Move file
    if (move_uploaded_file($file_tmp, $upload_path)) {
        return [
            'success' => true,
            'file_name' => $new_file_name
        ];
    } else {
        return [
            'success' => false,
            'message' => "Gagal mengunggah file. Silahkan coba lagi."
        ];
    }
}

// Fungsi untuk mendapatkan jumlah item di keranjang
function getCartItemCount() {
    if (isset($_COOKIE['cart'])) {
        $cart = json_decode($_COOKIE['cart'], true);
        return count($cart);
    }
    return 0;
}

// Fungsi untuk mendapatkan detail produk
function getProductDetails($id_produk) {
    global $koneksi;
    
    $id_produk = mysqli_real_escape_string($koneksi, $id_produk);
    $query = "SELECT * FROM produk WHERE id_produk = '$id_produk'";
    $result = mysqli_query($koneksi, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Fungsi untuk mendapatkan detail kategori
function getCategoryDetails($id_kategori) {
    global $koneksi;
    
    $id_kategori = mysqli_real_escape_string($koneksi, $id_kategori);
    $query = "SELECT * FROM kategori WHERE id_kategori = '$id_kategori'";
    $result = mysqli_query($koneksi, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Fungsi untuk mendapatkan produk terkait
function getRelatedProducts($id_produk, $id_kategori, $limit = 4) {
    global $koneksi;
    
    $id_produk = mysqli_real_escape_string($koneksi, $id_produk);
    $id_kategori = mysqli_real_escape_string($koneksi, $id_kategori);
    
    $query = "SELECT * FROM produk 
              WHERE id_kategori = '$id_kategori' AND id_produk != '$id_produk' 
              ORDER BY RAND() 
              LIMIT $limit";
    $result = mysqli_query($koneksi, $query);
    
    $products = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Fungsi untuk mendapatkan produk terlaris
function getPopularProducts($limit = 8) {
    global $koneksi;
    
    $query = "SELECT * FROM produk WHERE stok > 0 ORDER BY terjual DESC LIMIT $limit";
    $result = mysqli_query($koneksi, $query);
    
    $products = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Fungsi untuk mendapatkan produk unggulan
function getFeaturedProducts($limit = 4) {
    global $koneksi;
    
    $query = "SELECT * FROM produk WHERE unggulan = 1 AND stok > 0 LIMIT $limit";
    $result = mysqli_query($koneksi, $query);
    
    $products = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Fungsi untuk mendapatkan semua kategori
function getAllCategories() {
    global $koneksi;
    
    $query = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
    $result = mysqli_query($koneksi, $query);
    
    $categories = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    }
    
    return $categories;
}

// Fungsi untuk mendapatkan detail pesanan
function getOrderDetails($id_pesanan) {
    global $koneksi;
    
    $id_pesanan = mysqli_real_escape_string($koneksi, $id_pesanan);
    
    $query = "SELECT p.*, c.nama, c.email, c.telepon, c.alamat, c.kota, c.kode_pos 
              FROM pesanan p 
              JOIN customer c ON p.id_customer = c.id_customer 
              WHERE p.id_pesanan = '$id_pesanan'";
    $result = mysqli_query($koneksi, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);
        
        // Get order items
        $query_items = "SELECT pi.*, p.nama_produk, p.gambar, p.berat 
                       FROM pesanan_item pi 
                       JOIN produk p ON pi.id_produk = p.id_produk 
                       WHERE pi.id_pesanan = '$id_pesanan'";
        $result_items = mysqli_query($koneksi, $query_items);
        
        $items = [];
        if ($result_items) {
            while ($item = mysqli_fetch_assoc($result_items)) {
                $items[] = $item;
            }
        }
        
        $order['items'] = $items;
        
        return $order;
    }
    
    return null;
}

// API Endpoint untuk get_cart_products
if (isset($_GET['action']) && $_GET['action'] == 'get_cart_products') {
    // Pastikan tidak ada output sebelumnya
    if (ob_get_length()) ob_clean();
    
    // Set header JSON
    header('Content-Type: application/json');
    
    try {
        // Pastikan koneksi database tersedia
        global $koneksi;
        if (!$koneksi) {
            throw new Exception('Koneksi database tidak tersedia');
        }
        
        // Get JSON data
        $json_data = file_get_contents('php://input');
        if (empty($json_data)) {
            throw new Exception('Data tidak diterima');
        }
        
        $data = json_decode($json_data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Format JSON tidak valid: ' . json_last_error_msg());
        }
        
        // Validasi data yang diterima
        if (!isset($data['cart']) || !is_array($data['cart'])) {
            throw new Exception('Format data tidak valid - cart tidak ditemukan');
        }
        
        $cart = $data['cart'];
        $products = [];
        
        // Jika keranjang kosong, kembalikan array kosong
        if (empty($cart)) {
            echo json_encode([
                'success' => true,
                'products' => []
            ]);
            exit;
        }
        
        // Buat prepared statement untuk efisiensi
        $query = "SELECT id_produk, nama_produk, harga, stok, berat, gambar 
                  FROM produk 
                  WHERE id_produk = ? AND stok > 0";
        $stmt = mysqli_prepare($koneksi, $query);
        
        if (!$stmt) {
            throw new Exception('Gagal mempersiapkan query: ' . mysqli_error($koneksi));
        }
        
        // Dapatkan detail produk untuk setiap item di keranjang
        foreach ($cart as $item) {
            // Validasi item
            if (!isset($item['id']) || !isset($item['quantity'])) {
                continue;
            }
            
            $id = intval($item['id']);
            $quantity = intval($item['quantity']);
            
            // Bind parameter dan eksekusi query
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($result && mysqli_num_rows($result) > 0) {
                $product = mysqli_fetch_assoc($result);
                
                // Batasi jumlah dengan stok yang tersedia
                $available_quantity = min($quantity, intval($product['stok']));
                
                $products[] = [
                    'id' => $product['id_produk'],
                    'name' => $product['nama_produk'],
                    'price' => floatval($product['harga']),
                    'image' => $product['gambar'],
                    'weight' => intval($product['berat']),
                    'stock' => intval($product['stok']),
                    'quantity' => $available_quantity
                ];
            }
        }
        
        // Tutup statement
        mysqli_stmt_close($stmt);
        
        // Kembalikan response
        echo json_encode([
            'success' => true,
            'products' => $products
        ]);
        exit;
        
    } catch (Exception $e) {
        // Error handling
        if (ob_get_length()) ob_clean();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

// Tutup output buffering dan flush
ob_end_flush();