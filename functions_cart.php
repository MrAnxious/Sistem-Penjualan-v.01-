<?php
// File: functions_cart.php
// File khusus untuk menangani operasi keranjang

// Aktifkan error reporting untuk debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Tentukan koneksi database langsung
$host = "localhost";
$username = "root";  // Ganti dengan username database Anda
$password = "";      // Ganti dengan password database Anda
$database = "jenang_kudus"; // Ganti dengan nama database Anda

// Buat koneksi
$koneksi = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $koneksi->connect_error
    ]);
    exit;
}

// Set header untuk response JSON
header('Content-Type: application/json');

// Ambil data dari request
$json_input = file_get_contents('php://input');

// Parse JSON
$data = json_decode($json_input, true);

// Cek JSON valid
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON: ' . json_last_error_msg()
    ]);
    exit;
}

// Cek data cart ada
if (!isset($data['cart']) || empty($data['cart'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Cart data not found or empty'
    ]);
    exit;
}

$cart = $data['cart'];
$products = [];

// Proses setiap item di cart
foreach ($cart as $item) {
    if (!isset($item['id'])) {
        continue;
    }

    $id_produk = intval($item['id']);
    
    // Query sederhana dengan validasi manual
    $query = "SELECT * FROM produk WHERE id_produk = " . $id_produk;
    $result = $koneksi->query($query);
    
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // Dapatkan data dengan pengecekan nilai default
        $product_id = $product['id_produk'] ?? $product['id'] ?? $id_produk;
        $product_name = $product['nama_produk'] ?? $product['nama'] ?? 'Produk #'.$id_produk;
        $product_price = $product['harga'] ?? $product['price'] ?? 0;
        $product_image = $product['gambar'] ?? $product['image'] ?? 'default.jpg';
        $product_weight = $product['berat'] ?? $product['weight'] ?? 0;
        $product_stock = $product['stok'] ?? $product['stock'] ?? 10;
        
        // Tambahkan ke array produk
        $products[] = [
            'id' => $product_id,
            'name' => $product_name,
            'price' => (float)$product_price,
            'image' => $product_image,
            'weight' => (int)$product_weight,
            'stock' => (int)$product_stock,
            'quantity' => (int)($item['quantity'] ?? 1)
        ];
    }
}

// Tutup koneksi database
$koneksi->close();

// Kembalikan data sebagai JSON
$response = [
    'success' => true, 
    'products' => $products
];

echo json_encode($response);