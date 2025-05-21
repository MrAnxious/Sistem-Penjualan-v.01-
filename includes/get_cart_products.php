<?php
// File: config/get_cart_products.php
// Handle request untuk mengambil informasi produk berdasarkan ID di keranjang

// Include koneksi database
require_once 'koneksi.php';

// Set header untuk response JSON
header('Content-Type: application/json');

// Ambil data dari request
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['cart']) || empty($data['cart'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Keranjang kosong'
    ]);
    exit;
}

// Ambil ID produk dari keranjang
$cartItems = $data['cart'];
$productIds = array_map(function($item) {
    return $item['id'];
}, $cartItems);

// Validasi ID produk
$productIds = array_filter($productIds, function($id) {
    return is_numeric($id);
});

if (empty($productIds)) {
    echo json_encode([
        'success' => false,
        'message' => 'ID produk tidak valid'
    ]);
    exit;
}

// Buat string placeholder untuk query
$placeholders = implode(',', array_fill(0, count($productIds), '?'));

// Query untuk mengambil data produk
$query = "SELECT id, name, price, weight, stock, image FROM products WHERE id IN ($placeholders)";
$stmt = $conn->prepare($query);

// Bind parameter ID
$types = str_repeat('i', count($productIds));
$stmt->bind_param($types, ...$productIds);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    // Tambahkan quantity dari cart ke data produk
    $cartItem = array_filter($cartItems, function($item) use ($row) {
        return $item['id'] === $row['id'];
    });
    $cartItem = reset($cartItem);
    
    if ($cartItem) {
        $row['quantity'] = $cartItem['quantity'];
        $products[] = $row;
    }
}

// Kembalikan response
echo json_encode([
    'success' => true,
    'products' => $products
]);