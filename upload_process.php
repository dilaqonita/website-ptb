<?php
// upload_process.php
session_start();
include 'db_connect.php'; 
include 'file_handler.php'; 

if (!isset($_SESSION['loggedin'])) { 
    header("Location: login.php"); 
    exit; 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- 1. AMBIL DATA POST DAN VALIDASI ---
    $type = $_POST['content_type'] ?? null; 
    $title = $_POST['title'] ?? ''; 
    $description = $_POST['description'] ?? ''; 
    $date_event = $_POST['date_event'] ?? null; 
    
    // BARU: Ambil User ID yang sedang login untuk dicatat sebagai penulis
    $admin_id = $_SESSION['user_id'] ?? 0; 
    
    $redirect_page = ($type == 'article') ? 'artikel.php' : 'kegiatan.php';
    
    // Validasi dasar, termasuk cek jika file benar-benar di-upload (error code 4 = UPLOAD_ERR_NO_FILE)
    if (empty($title) || empty($description) || !isset($_FILES['file-thumbnail']) || $_FILES['file-thumbnail']['error'] === UPLOAD_ERR_NO_FILE) {
        $_SESSION['upload_error'] = "Semua kolom (Judul, Deskripsi, dan Gambar Postingan) harus diisi.";
        header("Location: upload.php?type={$type}"); 
        exit;
    }
    
    $allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];
    $new_thumbnail_path = null;

    // --- 2. HANDLE FILE UPLOAD THUMBNAIL ---
    $upload_dir_base = ($type === 'article') ? "uploads/articles/" : "uploads/activities/";
    $upload_dir_thumb = $upload_dir_base . "thumbnails/";

    $uploaded_thumb_path = uploadFile($_FILES['file-thumbnail'], $upload_dir_thumb, $allowed_image_types);

    if ($uploaded_thumb_path) {
        $new_thumbnail_path = $uploaded_thumb_path;
    } else {
        $_SESSION['error_message'] = "Gagal mengupload gambar postingan. Pastikan format file gambar benar (Max 5MB).";
        header("Location: upload.php?type={$type}"); 
        exit;
    }

    // --- 3. INSERT DATA KE DATABASE (Menggunakan Prepared Statements) ---
    
    $stmt = null;
    if ($type == 'article') {
        // KOREKSI: Tambahkan 'id_user' ke daftar kolom dan 'i' ke bind_param
        $sql = "INSERT INTO articles (title, content, thumbnail_path, id_user, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        // Tipe parameter: title(s), content(s), thumbnail_path(s), id_user(i)
        $stmt->bind_param("sssi", $title, $description, $new_thumbnail_path, $admin_id);
        
    } elseif ($type == 'activity') {
        // Query INSERT untuk Activity
        $sql = "INSERT INTO activities (title, description, image_path, date_event, created_at, id_user) VALUES (?, ?, ?, ?, NOW(), ?)"; 
        $stmt = $conn->prepare($sql);
        // Tipe parameter: title(s), description(s), image_path(s), date_event(s), id_user(i)
        $stmt->bind_param("ssssi", $title, $description, $new_thumbnail_path, $date_event, $admin_id);
        
    } else {
        // Jika content_type tidak valid, hapus file yang sudah terupload sebelum exit
        if ($new_thumbnail_path && file_exists($new_thumbnail_path)) {
            unlink($new_thumbnail_path);
        }
        $_SESSION['error_message'] = "Jenis konten tidak valid.";
        header("Location: upload.php");
        exit;
    }

    if ($stmt && $stmt->execute()) {
        $_SESSION['success_message'] = "Konten berhasil diupload!";
        
        // Tutup koneksi sebelum exit
        $stmt->close();
        $conn->close();
        
        header("Location: {$redirect_page}"); 
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal menyimpan konten ke database: " . ($stmt ? $stmt->error : $conn->error);
        
        // Hapus file yang terupload dan tutup koneksi sebelum exit
        if ($new_thumbnail_path && file_exists($new_thumbnail_path)) {
            unlink($new_thumbnail_path);
        }
        if ($stmt) { $stmt->close(); }
        $conn->close();

        header("Location: upload.php?type={$type}");
        exit;
    }

} else {
    header("Location: upload.php");
    exit;
}
?>