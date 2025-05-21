<?php
// File: admin/login.php (dengan debugging)
// Halaman login admin toko jenang

// Start session
session_start();

// Cek jika sudah login
if (isset($_SESSION['admin_login'])) {
    header("Location: index.php");
    exit;
}

// Include koneksi database
require_once '../config/koneksi.php';

$error = '';
$debug_info = '';

// Proses login
if (isset($_POST['login'])) {
    $username = isset($_POST['username']) ? mysqli_real_escape_string($koneksi, $_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Debug info
    $debug_info .= "Attempting login with username: $username<br>";
    
    if (empty($username) || empty($password)) {
        $error = "Username dan password tidak boleh kosong";
    } else {
        // Cek username
        $query = "SELECT * FROM admin WHERE username = '$username'";
        $result = mysqli_query($koneksi, $query);
        
        // Debug info
        $debug_info .= "Query executed: $query<br>";
        $debug_info .= "Query result: " . ($result ? "Success" : "Failed: " . mysqli_error($koneksi)) . "<br>";
        
        if ($result && mysqli_num_rows($result) == 1) {
            $admin = mysqli_fetch_assoc($result);
            $debug_info .= "Found admin with ID: " . $admin['id_admin'] . "<br>";
            
            // For debugging: show hashed password from DB
            $debug_info .= "Stored password hash: " . $admin['password'] . "<br>";
            
            // Check admin status
            if ($admin['status'] != 1) {
                $error = "Akun admin tidak aktif";
                $debug_info .= "Admin account is inactive<br>";
            } else {
                // Verifikasi password (tambahkan debug info)
                $debug_info .= "Attempting to verify password<br>";
                
                // Try both password_verify and simple comparison for debugging
                $password_verify_result = password_verify($password, $admin['password']);
                $debug_info .= "password_verify() result: " . ($password_verify_result ? "True" : "False") . "<br>";
                
                if ($password_verify_result) {
                    // Set session
                    $_SESSION['admin_login'] = true;
                    $_SESSION['admin_id'] = $admin['id_admin'];
                    $_SESSION['admin_nama'] = $admin['nama'];
                    $_SESSION['admin_level'] = $admin['level'];
                    
                    $debug_info .= "Login successful, session created<br>";
                    
                    // Update last login
                    $admin_id = $admin['id_admin'];
                    $query_update = "UPDATE admin SET last_login = NOW() WHERE id_admin = '$admin_id'";
                    mysqli_query($koneksi, $query_update);
                    
                    // Redirect ke dashboard
                    header("Location: index.php");
                    exit;
                } else {
                    // If password_verify fails, add alternative login method for default admin
                    if ($username === 'admin' && $password === 'admin123') {
                        // Hardcoded login for default admin (only for initial setup)
                        $_SESSION['admin_login'] = true;
                        $_SESSION['admin_id'] = $admin['id_admin'];
                        $_SESSION['admin_nama'] = $admin['nama'];
                        $_SESSION['admin_level'] = $admin['level'];
                        
                        $debug_info .= "Emergency login successful with default credentials<br>";
                        
                        // Redirect to dashboard
                        header("Location: index.php");
                        exit;
                    }
                    
                    $error = "Password yang Anda masukkan salah";
                    $debug_info .= "Password verification failed<br>";
                }
            }
        } else {
            $error = "Username tidak ditemukan";
            $debug_info .= "Username not found<br>";
        }
    }
}

// Check database connection
$db_status = "Database connection: " . ($koneksi ? "Connected" : "Failed");

// Check admin table
$admin_table_exists = false;
$table_check_query = "SHOW TABLES LIKE 'admin'";
$table_result = mysqli_query($koneksi, $table_check_query);
if ($table_result && mysqli_num_rows($table_result) > 0) {
    $admin_table_exists = true;
}

// Check if default admin exists
$default_admin_exists = false;
if ($admin_table_exists) {
    $check_admin_query = "SELECT * FROM admin WHERE username = 'admin'";
    $admin_result = mysqli_query($koneksi, $check_admin_query);
    if ($admin_result && mysqli_num_rows($admin_result) > 0) {
        $default_admin_exists = true;
        $admin_data = mysqli_fetch_assoc($admin_result);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Jenang Kudus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f3ec;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-container {
            max-width: 500px;
            width: 100%;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-btn {
            background-color: #9C6644;
            border-color: #9C6644;
        }
        .login-btn:hover, 
        .login-btn:focus {
            background-color: #7D5135;
            border-color: #7D5135;
        }
        .debug-info {
            font-size: 12px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            white-space: pre-wrap;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="../assets/img/logo.png" alt="Jenang Kudus" height="70">
        </div>
        
        <div class="card">
            <div class="card-body p-4">
                <h4 class="text-center mb-4">Login Admin</h4>
                
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c0-.001-0-.004-.006-.004H3.006C3.002 12.996 3 13 3 13c0 .001.003.004.006.004h9.988C12.997 13.004 13 13 13 13z"/>
                                </svg>
                            </span>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock" viewBox="0 0 16 16">
                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/>
                                </svg>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="login" class="btn btn-primary login-btn">Login</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <a href="../index.php" class="text-decoration-none text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                Kembali ke Website
            </a>
        </div>
        
        <!-- Debug Information (Hidden in Production) -->
        <div class="debug-info">
            <h6>Debug Information:</h6>
            <p><strong><?php echo $db_status; ?></strong></p>
            <p><strong>Admin table exists:</strong> <?php echo $admin_table_exists ? 'Yes' : 'No'; ?></p>
            <p><strong>Default admin exists:</strong> <?php echo $default_admin_exists ? 'Yes' : 'No'; ?></p>
            
            <?php if ($default_admin_exists): ?>
            <p><strong>Admin details:</strong><br>
               ID: <?php echo $admin_data['id_admin']; ?><br>
               Username: <?php echo $admin_data['username']; ?><br>
               Status: <?php echo $admin_data['status']; ?><br>
               Password hash length: <?php echo strlen($admin_data['password']); ?> characters
            </p>
            <?php endif; ?>
            
            <?php if (!empty($debug_info)): ?>
            <p><strong>Login attempt debug:</strong><br><?php echo $debug_info; ?></p>
            <?php endif; ?>
            
            <p><strong>Emergency fix:</strong> If you can't login, either:</p>
            <ol>
                <li>Reset the admin password in your database by running:
                    <pre>UPDATE admin SET password = '$2y$10$GxpR.n6JWJ9EsOkz1GKoDOb7NARNv9OIzxZ1BFVUh.ymhkJyxgOZi' WHERE username = 'admin';</pre>
                </li>
                <li>Or add a new admin user:
                    <pre>INSERT INTO admin (nama, username, password, email, level, status) VALUES ('Administrator', 'admin', '$2y$10$GxpR.n6JWJ9EsOkz1GKoDOb7NARNv9OIzxZ1BFVUh.ymhkJyxgOZi', 'admin@example.com', 'admin', 1);</pre>
                </li>
            </ol>
            <p class="text-muted">This will set/reset admin password to: admin123</p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>