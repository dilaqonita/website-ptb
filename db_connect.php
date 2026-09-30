<?php
// db_connect.php

// Mengambil konfigurasi dari Environment Variables Wasmer (dengan fallback untuk localhost jika dites lokal)
$server   = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'ptb_admin_db(1)';
$port     = getenv('DB_PORT') ?: 3306;

// Membuat koneksi dengan menyertakan port
$conn = new mysqli($server, $username, $password, $database, (int)$port);

// Memeriksa koneksi
if ($conn->connect_error) {
    // Keluar jika koneksi gagal
    die("Koneksi gagal: " . $conn->connect_error);
}

// PENTING: Set karakter set ke UTF8 untuk mendukung berbagai karakter
$conn->set_charset("utf8mb4"); 
?>
