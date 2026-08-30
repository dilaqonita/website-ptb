<?php
// db_connect.php

// Konfigurasi Database (Menggunakan Konstanta untuk Praktik yang Lebih Baik)
define('DB_SERVER', "localhost");
define('DB_USERNAME', "root"); // Ganti dengan username MySQL Anda
define('DB_PASSWORD', ""); // Ganti dengan password MySQL Anda
define('DB_NAME', "ptb_admin_db"); // Nama database Anda

// Membuat koneksi
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Memeriksa koneksi
if ($conn->connect_error) {
    // Keluar jika koneksi gagal
    die("Koneksi gagal: " . $conn->connect_error);
}

// PENTING: Set karakter set ke UTF8 untuk mendukung berbagai karakter
$conn->set_charset("utf8mb4"); 
?>