<?php
// File: ultra_simple_cart.php
// File super sederhana untuk menangani keranjang tanpa error

// SANGAT PENTING: Matikan semua tampilan error dan output PHP
ini_set('display_errors', 0);
error_reporting(0);

// Pastikan output bersih - tidak ada whitespace atau karakter lain sebelum <?php
ob_start();

// Set header untuk JSON
header('Content-Type: application/json');

// Log error ke file untuk debugging (opsional)
ini_set('log_errors', 1);
ini_set('error_log', 'cart_errors.log');

// Fungsi untuk mengembalikan respons JSON aman
function safe_json_response($success, $data = null, $message = '') {
    // Pastikan buffer output bersih
    if (ob_get_length()) ob_clean();
    
    // Set header JSON lagi untuk memastikan
    header('Content-Type: application/json');
    
    // Buat response array
    $response = ['success' => $success];
    
    if ($success && $data !== null) {
        $response['products'] = $data;
    }
    
    if (!$success && $message) {
        $response['message'] = $message;
    }
    
    // Encode dan output JSON
    echo json_encode($response);
    exit;
}

// Tangkap error PHP fatal
function fatal_error_handler() {
    $error = error_get_last();
    if ($error && ($error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR))) {
        safe_json_response(false, null, 'Fatal PHP Error: ' . $error['message']);
    }
}
register_shutdown_function('fatal_error_handler');

try {
    // Baca raw input
    $input = file_get_contents('php://input');
    if (empty($input)) {
        safe_json_response(false, null, 'No input data provided');
    }
    
    // Parse JSON
    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        safe_json_response(false, null, 'Invalid JSON: ' . json_last_error_msg());
    }
    
    // Cek data cart
    if (!isset($data['cart']) || !is_array($data['cart']) || empty($data['cart'])) {
        safe_json_response(false, null, 'Cart data missing or invalid');
    }
    
    // Koneksi database sederhana
    $host = "localhost";
    $username = "root";  // Ganti dengan username database Anda
    $password = "";      // Ganti dengan password database Anda
    $database = "jenang_kudus"; // Ganti dengan nama database Anda
    
    $koneksi = new mysqli($host, $username, $password, $database);
    
    // Cek koneksi
    if ($koneksi->connect_error) {
        safe_json_response(false, null, 'Database connection failed');
    }
    
    // Produk untuk respons
    $products = [];
    
    // Dapatkan data produk
    foreach ($data['cart'] as $item) {
        if (!isset($item['id'])) continue;
        
        $id = intval($item['id']);
        $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
        
        // Hindari prepared statement - gunakan query sederhana untuk kompatibilitas
        $query = "SELECT * FROM produk WHERE id_produk = " . $id;
        $result = $koneksi->query($query);
        
        if ($result && $result->num_rows > 0) {
            $product = $result->fetch_assoc();
            
            // Coba deteksi nama kolom yang benar
            $products[] = [
                'id' => $product['id_produk'] ?? $product['id'] ?? $id,
                'name' => $product['nama_produk'] ?? $product['nama'] ?? ('Produk ' . $id),
                'price' => floatval($product['harga'] ?? $product['price'] ?? 0),
                'image' => $product['gambar'] ?? $product['image'] ?? 'default.jpg',
                'weight' => intval($product['berat'] ?? $product['weight'] ?? 0),
                'stock' => intval($product['stok'] ?? $product['stock'] ?? 10),
                'quantity' => $quantity
            ];
        }
    }
    
    // Kembalikan respons sukses
    safe_json_response(true, $products);
    
} catch (Exception $e) {
    // Tangkap semua exception dan kembalikan error yang aman
    safe_json_response(false, null, 'Error: ' . $e->getMessage());
}