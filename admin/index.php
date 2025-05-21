<?php
// File: admin/index.php
// Halaman utama admin (redirect ke dashboard)

// Start session
session_start();

// Cek login
if (!isset($_SESSION['admin_login'])) {
    header("Location: login.php");
    exit;
}

// Redirect ke dashboard
header("Location: dashboard.php");
exit;