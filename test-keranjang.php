<?php
// File: test-keranjang.php
// File sederhana untuk menguji koneksi database dan struktur tabel produk

// Aktifkan error reporting untuk debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Test Database Connection for Jenang Kudus</h1>";

// Informasi koneksi database
$host = "localhost";
$username = "root";  // Ganti dengan username database Anda
$password = "";      // Ganti dengan password database Anda
$database = "jenang_kudus"; // Ganti dengan nama database Anda

// Coba koneksi ke database
try {
    $koneksi = new mysqli($host, $username, $password, $database);
    
    // Cek koneksi
    if ($koneksi->connect_error) {
        die("<div style='color:red'>Database connection failed: " . $koneksi->connect_error . "</div>");
    }
    
    echo "<div style='color:green'>Connected to MySQL successfully!</div>";
    echo "<p>MySQL Version: " . $koneksi->server_info . "</p>";
    
    // Cek apakah tabel produk ada
    echo "<h2>Checking Table Structure</h2>";
    
    $result = $koneksi->query("SHOW TABLES LIKE 'produk'");
    if ($result->num_rows > 0) {
        echo "<div style='color:green'>Table 'produk' exists!</div>";
        
        // Tampilkan struktur tabel
        echo "<h3>Table Structure:</h3>";
        $result = $koneksi->query("DESCRIBE produk");
        echo "<table border='1' cellpadding='5'>";
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
        
        // Periksa data produk
        echo "<h3>Sample Products:</h3>";
        $result = $koneksi->query("SELECT * FROM produk LIMIT 5");
        
        if ($result->num_rows > 0) {
            echo "<table border='1' cellpadding='5'>";
            
            // Header tabel berdasarkan kolom yang ada
            $firstRow = $result->fetch_assoc();
            $result->data_seek(0);
            
            echo "<tr>";
            foreach (array_keys($firstRow) as $colName) {
                echo "<th>" . $colName . "</th>";
            }
            echo "</tr>";
            
            // Data produk
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . $value . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            
            echo "<p style='color:green'>Found " . $result->num_rows . " products in the database.</p>";
        } else {
            echo "<div style='color:red'>No products found in the database.</div>";
            echo "<p>Please make sure you have added products to the 'produk' table.</p>";
        }
    } else {
        echo "<div style='color:red'>Table 'produk' does not exist!</div>";
        echo "<p>Please make sure you have created the 'produk' table with the following structure:</p>";
        echo "<pre>
CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(255) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `berat` int(11) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `gambar` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        </pre>";
    }
    
    // Test localStorage
    echo "<h2>Test localStorage</h2>";
    echo "<p>Click the button below to add a test product to your cart:</p>";
    echo "<button onclick=\"addTestProduct()\">Add Test Product to Cart</button>";
    echo "<div id=\"localStorage-result\"></div>";
    
    echo "<h2>Test API Endpoint</h2>";
    echo "<p>Click the button below to test the cart API endpoint:</p>";
    echo "<button onclick=\"testCartAPI()\">Test Cart API</button>";
    echo "<div id=\"api-result\"></div>";
    
    // Tutup koneksi
    $koneksi->close();
    
} catch (Exception $e) {
    echo "<div style='color:red'>Error: " . $e->getMessage() . "</div>";
}
?>

<script>
function addTestProduct() {
    // Tambahkan produk test ke localStorage
    const testProduct = {
        id: "1", // Gunakan ID produk yang ada di database
        quantity: 1
    };
    
    let cart = [];
    try {
        const existingCart = localStorage.getItem('cart');
        if (existingCart) {
            cart = JSON.parse(existingCart);
        }
    } catch(e) {
        console.error("Error reading cart from localStorage:", e);
    }
    
    // Cek apakah produk sudah ada di cart
    const existingIndex = cart.findIndex(item => item.id === testProduct.id);
    if (existingIndex !== -1) {
        cart[existingIndex].quantity += 1;
    } else {
        cart.push(testProduct);
    }
    
    // Simpan kembali ke localStorage
    try {
        localStorage.setItem('cart', JSON.stringify(cart));
        document.getElementById('localStorage-result').innerHTML = 
            "<div style='color:green'>Test product successfully added to cart!</div>" +
            "<pre>" + JSON.stringify(cart, null, 2) + "</pre>" +
            "<p>Now you can go to <a href='keranjang.php' target='_blank'>the cart page</a> to see if it works.</p>";
    } catch(e) {
        document.getElementById('localStorage-result').innerHTML = 
            "<div style='color:red'>Error saving to localStorage: " + e.message + "</div>";
    }
}

function testCartAPI() {
    // Test cart API dengan data sederhana
    const testCart = [
        {id: "1", quantity: 1}
    ];
    
    document.getElementById('api-result').innerHTML = "<div>Testing API...</div>";
    
    fetch('functions_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ cart: testCart })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`HTTP error ${response.status}: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        document.getElementById('api-result').innerHTML = 
            "<div style='color:green'>API test successful!</div>" +
            "<pre>" + JSON.stringify(data, null, 2) + "</pre>";
    })
    .catch(error => {
        document.getElementById('api-result').innerHTML = 
            "<div style='color:red'>API test failed: " + error.message + "</div>";
    });
}
</script>