<?php
// File: create_admin.php
// Script untuk menambahkan admin baru secara cepat dan memilih metode enkripsi password yang cocok
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Admin Toko Jenang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f3ec;
            padding: 30px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .sql-result {
            font-family: monospace;
            white-space: pre-wrap;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="mb-0">Buat Admin - Toko Jenang</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>Perhatian!</strong> Script ini hanya untuk menyelesaikan masalah login admin. Setelah berhasil login, segera hapus file ini dari server Anda.
                </div>
                
                <?php
                // Process form
                if (isset($_POST['generate'])) {
                    $username = isset($_POST['username']) ? $_POST['username'] : 'admin';
                    $password = isset($_POST['password']) ? $_POST['password'] : 'admin123';
                    $method = isset($_POST['method']) ? $_POST['method'] : 'bcrypt';
                    
                    // Generate SQL based on method
                    $sql = "-- Gunakan SQL ini untuk membuat admin baru\n\n";
                    $password_sql = '';
                    
                    switch ($method) {
                        case 'bcrypt':
                            // Using PHP's password_hash
                            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
                            $password_sql = "'$hash'";
                            break;
                            
                        case 'md5':
                            // Using MD5 (less secure)
                            $hash = md5($password);
                            $password_sql = "'$hash'";
                            break;
                            
                        case 'plaintext':
                            // Plaintext (very insecure)
                            $password_sql = "'$password'";
                            break;
                    }
                    
                    // Check connection and if table exists
                    $db_status = '';
                    try {
                        if (file_exists('config/koneksi.php')) {
                            require_once 'config/koneksi.php';
                            if (isset($koneksi) && $koneksi) {
                                $db_status = "<div class='alert alert-success'>Koneksi database berhasil!</div>";
                                
                                // Check if admin table exists
                                $result = mysqli_query($koneksi, "SHOW TABLES LIKE 'admin'");
                                if ($result && mysqli_num_rows($result) > 0) {
                                    $db_status .= "<div class='alert alert-success'>Tabel 'admin' ditemukan!</div>";
                                    
                                    // Try to insert directly
                                    if (isset($_POST['execute']) && $_POST['execute'] == 'yes') {
                                        // Delete existing admin with same username
                                        $username_esc = mysqli_real_escape_string($koneksi, $username);
                                        mysqli_query($koneksi, "DELETE FROM admin WHERE username = '$username_esc'");
                                        
                                        // Insert new admin
                                        $query = "INSERT INTO admin (nama, username, password, email, level, status) VALUES ('Administrator', '$username_esc', $password_sql, 'admin@example.com', 'admin', 1)";
                                        $insert_result = mysqli_query($koneksi, $query);
                                        
                                        if ($insert_result) {
                                            $db_status .= "<div class='alert alert-success'>Admin berhasil ditambahkan ke database! Silakan coba login dengan username: $username</div>";
                                        } else {
                                            $db_status .= "<div class='alert alert-danger'>Gagal menambahkan admin: " . mysqli_error($koneksi) . "</div>";
                                        }
                                    }
                                } else {
                                    $db_status .= "<div class='alert alert-danger'>Tabel 'admin' tidak ditemukan! Gunakan SQL di bawah untuk membuat tabel dan admin.</div>";
                                    
                                    // Add SQL to create admin table
                                    $sql .= "-- Buat tabel admin jika belum ada\n";
                                    $sql .= "CREATE TABLE IF NOT EXISTS `admin` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `level` varchar(10) NOT NULL DEFAULT 'staff' COMMENT 'admin, staff',
  `last_login` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1 COMMENT '1 = aktif, 0 = nonaktif',
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";
                                }
                            } else {
                                $db_status = "<div class='alert alert-danger'>Koneksi database gagal! Pastikan file config/koneksi.php benar.</div>";
                            }
                        } else {
                            $db_status = "<div class='alert alert-danger'>File config/koneksi.php tidak ditemukan!</div>";
                        }
                    } catch (Exception $e) {
                        $db_status = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                    }
                    
                    // Create SQL to insert/update admin
                    $sql .= "-- Hapus admin dengan username yang sama jika sudah ada\n";
                    $sql .= "DELETE FROM `admin` WHERE `username` = '$username';\n\n";
                    $sql .= "-- Tambahkan admin baru\n";
                    $sql .= "INSERT INTO `admin` (`nama`, `username`, `password`, `email`, `level`, `status`) VALUES ('Administrator', '$username', $password_sql, 'admin@example.com', 'admin', 1);\n";
                    
                    echo $db_status;
                    
                    echo "<h5 class='mt-4'>SQL untuk dijalankan di phpMyAdmin:</h5>";
                    echo "<div class='sql-result mb-3'>$sql</div>";
                    
                    echo "<div class='alert alert-info'>";
                    echo "<strong>Detail Admin:</strong><br>";
                    echo "Username: $username<br>";
                    echo "Password: $password<br>";
                    echo "Metode Enkripsi: $method<br>";
                    if ($method == 'bcrypt') {
                        echo "Hash: $hash<br>";
                    }
                    echo "</div>";
                }
                ?>
                
                <form method="POST" action="">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="admin">
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="text" class="form-control" id="password" name="password" value="admin123">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Metode Enkripsi Password</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="method" id="method_bcrypt" value="bcrypt" checked>
                            <label class="form-check-label" for="method_bcrypt">
                                BCrypt (recommended)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="method" id="method_md5" value="md5">
                            <label class="form-check-label" for="method_md5">
                                MD5 (less secure, but may work if password_verify() fails)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="method" id="method_plaintext" value="plaintext">
                            <label class="form-check-label" for="method_plaintext">
                                Plaintext (very insecure, only for emergency)
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="execute" id="execute" value="yes">
                            <label class="form-check-label" for="execute">
                                Eksekusi SQL langsung ke database (jika koneksi tersedia)
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" name="generate" class="btn btn-primary">Generate SQL & Create Admin</button>
                    </div>
                </form>
                
                <hr>
                
                <h5 class="mt-3">Langkah-langkah manual jika SQL di atas tidak berhasil:</h5>
                <ol>
                    <li>Pastikan Anda memiliki akses ke database MySQL (phpMyAdmin atau metode lain)</li>
                    <li>Buka tabel <code>admin</code> di database <code>toko_jenang</code></li>
                    <li>Cek apakah ada user dengan username 'admin', jika ada hapus</li>
                    <li>Tambahkan user admin baru dengan SQL di atas atau gunakan form ini</li>
                    <li>Coba login kembali dengan username dan password baru</li>
                </ol>
                
                <div class="alert alert-danger">
                    <strong>Penting!</strong> Setelah berhasil login, segera hapus file ini dan ganti password admin Anda untuk keamanan.
                </div>
            </div>
        </div>
        
        <div class="text-center mb-4">
            <a href="index.php" class="btn btn-outline-primary">Kembali ke Halaman Utama</a>
            <a href="admin/login.php" class="btn btn-primary">Ke Halaman Login Admin</a>
        </div>
    </div>
</body>
</html>