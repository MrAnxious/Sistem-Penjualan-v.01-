<?php
// File: debug_cart.php
// File ini untuk debugging masalah keranjang

// Aktifkan tampilan error untuk debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include koneksi database
require_once 'config/koneksi.php';

// Pastikan koneksi database berhasil
if (!isset($koneksi) || $koneksi->connect_error) {
    die("Database connection failed: " . ($koneksi->connect_error ?? "Unknown error"));
}

echo "<h1>Debugging Tools</h1>";
echo "<h2>Database Connection</h2>";
echo "Connection status: Connected to MySQL server version: " . $koneksi->server_info;

echo "<h2>Table Structure</h2>";
// Cek struktur tabel produk
$result = $koneksi->query("DESCRIBE produk");
if (!$result) {
    echo "Error checking table structure: " . $koneksi->error;
} else {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h2>Sample Data</h2>";
// Tampilkan beberapa produk contoh
$result = $koneksi->query("SELECT * FROM produk LIMIT 5");
if (!$result) {
    echo "Error querying products: " . $koneksi->error;
} else {
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        // Tampilkan header tabel berdasarkan field yang ada
        $firstRow = $result->fetch_assoc();
        $result->data_seek(0);
        
        echo "<tr>";
        foreach (array_keys($firstRow) as $field) {
            echo "<th>" . $field . "</th>";
        }
        echo "</tr>";
        
        // Tampilkan data
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $field => $value) {
                echo "<td>" . $value . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No products found in database.";
    }
}

echo "<h2>Test Cart API</h2>";
// Test fungsi get_cart_products dengan data sederhana
echo "Testing cart API with simple data...<br>";

// Buat data cart sampel
$testCart = [
    ['id' => '1', 'quantity' => 2],
    ['id' => '2', 'quantity' => 1]
];

// Konversi ke JSON untuk simulasi request
$jsonData = json_encode(['cart' => $testCart]);

echo "Test cart data: " . $jsonData . "<br><br>";

// Echo data products yang akan ditampilkan
echo "Products from database:<br>";
$products = [];

foreach ($testCart as $item) {
    $id_produk = $item['id'];
    
    // Query database untuk mendapatkan data produk
    $query = "SELECT * FROM produk WHERE id_produk = ?";
    
    if ($stmt = $koneksi->prepare($query)) {
        $stmt->bind_param("i", $id_produk);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $product = $result->fetch_assoc();
            
            // Tampilkan detail produk
            echo "Product found: ID=" . $product['id_produk'] . ", Name=" . ($product['nama_produk'] ?? "Unknown") . "<br>";
            
            // Cek nama field yang sebenarnya digunakan di database
            echo "Available fields for this product: " . implode(", ", array_keys($product)) . "<br><br>";
            
            // Tambahkan ke array products
            $products[] = [
                'id' => $product['id_produk'],
                'name' => $product['nama_produk'] ?? $product['nama'] ?? "Unknown Name",
                'price' => (float)($product['harga'] ?? 0),
                'image' => $product['gambar'] ?? "no-image.jpg",
                'weight' => (int)($product['berat'] ?? 0),
                'stock' => (int)($product['stok'] ?? 0),
                'quantity' => (int)$item['quantity']
            ];
        } else {
            echo "No product found with ID: " . $id_produk . "<br>";
        }
        
        $stmt->close();
    } else {
        echo "Failed to prepare statement: " . $koneksi->error . "<br>";
    }
}

echo "<h3>Product data that would be returned:</h3>";
echo "<pre>" . json_encode(['success' => true, 'products' => $products], JSON_PRETTY_PRINT) . "</pre>";

echo "<h2>JavaScript LocalStorage Check</h2>";
?>
<script>
// Tampilkan isi localStorage untuk debugging
document.write('<h3>Current localStorage content:</h3>');
const cart = JSON.parse(localStorage.getItem('cart') || '[]');
document.write('<pre>' + JSON.stringify(cart, null, 2) + '</pre>');

// Tambahkan fungsi untuk menguji cart
document.write('<button onclick="testCart()">Test Add Product to Cart</button>');
document.write('<div id="cartResult"></div>');

function testCart() {
    // Tambahkan produk test ke cart
    const testProduct = {
        id: '1',
        quantity: 1
    };
    
    // Tambahkan/update ke cart
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const existingIndex = cart.findIndex(item => item.id === testProduct.id);
    
    if (existingIndex !== -1) {
        cart[existingIndex].quantity += 1;
    } else {
        cart.push(testProduct);
    }
    
    // Simpan kembali ke localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Tampilkan hasil
    document.getElementById('cartResult').innerHTML = 
        '<p>Test product added to cart. Current cart:</p>' +
        '<pre>' + JSON.stringify(cart, null, 2) + '</pre>' +
        '<p><a href="keranjang.php" target="_blank">Open cart page in new tab</a></p>';
}
</script>