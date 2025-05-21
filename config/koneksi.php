<?php
// File: config/koneksi.php
// Koneksi database untuk website toko jenang

// Konfigurasi database
$host = "localhost";
$username = "root";
$password = "";
$database = "toko_jenang";

// Membuat koneksi
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}