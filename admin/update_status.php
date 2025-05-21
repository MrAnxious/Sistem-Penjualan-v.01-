<?php
// File: admin/update_status.php
// Proses update status pesanan

// Start session
session_start();

// Cek login
if (!isset($_SESSION['admin_login'])) {
    header("Location: login.php");
    exit;
}

// Include koneksi database
require_once '../config/koneksi.php';

// Cek request method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_pesanan']) && isset($_POST['status'])) {
        $id_pesanan = mysqli_real_escape_string($koneksi, $_POST['id_pesanan']);
        $status = mysqli_real_escape_string($koneksi, $_POST['status']);
        $catatan = isset($_POST['catatan']) ? mysqli_real_escape_string($koneksi, $_POST['catatan']) : '';
        
        // Get current order status
        $query_order = "SELECT status FROM pesanan WHERE id_pesanan = '$id_pesanan'";
        $result_order = mysqli_query($koneksi, $query_order);
        
        if (mysqli_num_rows($result_order) > 0) {
            $order = mysqli_fetch_assoc($result_order);
            $current_status = $order['status'];
            
            // Update fields based on status
            $update_fields = "status = '$status'";
            
            if ($status == 'dibayar' && $current_status != 'dibayar') {
                $update_fields .= ", tanggal_pembayaran = NOW()";
            } else if ($status == 'dikirim' && $current_status != 'dikirim') {
                $update_fields .= ", tanggal_pengiriman = NOW()";
            } else if ($status == 'selesai' && $current_status != 'selesai') {
                $update_fields .= ", tanggal_selesai = NOW()";
            }
            
            // Update order status
            $query_update = "UPDATE pesanan SET $update_fields WHERE id_pesanan = '$id_pesanan'";
            $result_update = mysqli_query($koneksi, $query_update);
            
            if ($result_update) {
                $_SESSION['success'] = "Status pesanan berhasil diperbarui.";
            } else {
                $_SESSION['error'] = "Gagal memperbarui status pesanan: " . mysqli_error($koneksi);
            }
        } else {
            $_SESSION['error'] = "Pesanan tidak ditemukan.";
        }
    } else {
        $_SESSION['error'] = "Data yang dikirimkan tidak lengkap.";
    }
} else {
    $_SESSION['error'] = "Metode request tidak valid.";
}

// Redirect kembali ke halaman sebelumnya
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: {$_SERVER['HTTP_REFERER']}");
} else {
    header("Location: pesanan.php");
}
exit;