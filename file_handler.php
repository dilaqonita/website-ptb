<?php
// file_handler.php

/**
 * Mengupload file ke direktori yang ditentukan.
 * @param array $file Array $_FILES['name']
 * @param string $upload_dir Direktori tujuan (e.g., 'uploads/articles/documents/')
 * @param array $allowed_types Array ekstensi yang diizinkan (e.g., ['jpg', 'png', 'pdf'])
 * @return string|false Path relatif file yang diupload atau false jika gagal.
 */
function uploadFile($file, $upload_dir, $allowed_types) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    
    // Cek batasan ukuran file (Misal: 5MB)
    $max_size = 5 * 1024 * 1024; 
    if ($file['size'] > $max_size) {
        return false; 
    }

    // Cek apakah ekstensi ada di daftar yang diizinkan
    if (!in_array(strtolower($file_extension), $allowed_types)) {
        return false; // Jenis file tidak diizinkan
    }

    // Buat folder jika belum ada
    if (!is_dir($upload_dir)) {
        // Menggunakan 0777 untuk izin penuh
        if (!mkdir($upload_dir, 0777, true)) { 
            return false; // Gagal membuat direktori
        }
    }

    // Buat nama file unik
    $new_file_name = uniqid('file_', true) . '.' . $file_extension;
    $destination = $upload_dir . $new_file_name;

    // Pindahkan file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $destination; 
    } else {
        return false; // Gagal memindahkan file
    }
}
?>