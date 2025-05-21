<?php
// File: admin/logout.php
// Proses logout admin

// Start session
session_start();

// Destroy session
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit;