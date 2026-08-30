<?php
// update_process.php
session_start();
include 'db_connect.php'; 
include 'file_handler.php'; 

if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit; }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id = $_POST['id'] ?? null;
    $type = $_POST['type'] ?? null;
    $title = $_POST['title'] ?? ''; 
    $description = $_POST['description'] ?? ''; 
    $date_event = $_POST['date_event'] ?? null;
    
    $existing_thumbnail = $_POST['existing_thumbnail'] ?? null; 
    
    $table_name = ($type == 'article') ? 'articles' : 'activities';
    $redirect_page = ($type == 'article') ? 'artikel.php' : 'kegiatan.php';

    $new_thumbnail_path = $existing_thumbnail;

    $allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];
    
    $upload_dir_base = ($type === 'article') ? "uploads/articles/" : "uploads/activities/";
    $upload_dir_thumb = $upload_dir_base . "thumbnails/";

    // --- 1A. HANDLE THUMBNAIL BARU (file-thumbnail) ---
    if (isset($_FILES['file-thumbnail']) && $_FILES['file-thumbnail']['error'] === UPLOAD_ERR_OK) {
        $uploaded_thumb_path = uploadFile($_FILES['file-thumbnail'], $upload_dir_thumb, $allowed_image_types);

        if ($uploaded_thumb_path) {
            $new_thumbnail_path = $uploaded_thumb_path;
            // Hapus thumbnail lama jika ada
            if ($existing_thumbnail && file_exists($existing_thumbnail)) {
                unlink($existing_thumbnail);
            }
        } else {
            $_SESSION['error_message'] = "Gagal mengupload thumbnail. Pastikan format file gambar benar.";
            header("Location: {$redirect_page}"); exit;
        }
    }

    // --- 2. UPDATE DATABASE (Menggunakan Prepared Statements) ---
    $stmt = null;
    if ($type == 'article') {
        // Query untuk Article: id_user TIDAK diubah
        $sql = "UPDATE articles SET title = ?, content = ?, thumbnail_path = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $title, $description, $new_thumbnail_path, $id);
    } elseif ($type == 'activity') {
        // Query untuk Activity: id_user TIDAK diubah
        $sql = "UPDATE activities SET title = ?, description = ?, image_path = ?, date_event = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        // Parameter disesuaikan: title (s), description (s), image_path (s), date_event (s), id (i)
        $stmt->bind_param("ssssi", $title, $description, $new_thumbnail_path, $date_event, $id);
    } else {
        $_SESSION['error_message'] = "Jenis konten tidak valid.";
        header("Location: admin.php");
        exit;
    }

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Konten berhasil diperbarui!";
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui konten: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: {$redirect_page}");
    exit;
} else {
    header("Location: admin.php");
    exit;
}
?>