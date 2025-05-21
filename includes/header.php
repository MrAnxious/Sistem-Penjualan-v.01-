<?php
// File: includes/header.php
// Header untuk website toko jenang
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Jenang Kudus</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Load Bootstrap Icons dari CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if (isset($additional_css)): ?>
        <?php echo $additional_css; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>