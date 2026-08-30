<?php
session_start();
include 'db_connect.php'; 

// Proteksi: Pastikan admin sudah login
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = $_GET['id'];
    $type = $_GET['type']; // 'article' atau 'activity'
    
    // Tentukan nama tabel dan kolom ID
    $table_name = ($type == 'article') ? 'articles' : 'activities';
    $redirect_page = ($type == 'article') ? 'artikel.php' : 'kegiatan.php';
    $id_col = 'id';

    // 1. Ambil path gambar untuk dihapus dari server
    $stmt_fetch = $conn->prepare("SELECT image_path FROM {$table_name} WHERE {$id_col} = ?");
    $stmt_fetch->bind_param("i", $id);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();
    $row = $result_fetch->fetch_assoc();
    $image_path = $row['image_path'] ?? null;
    $stmt_fetch->close();

    // 2. Hapus entri dari database
    $stmt_delete = $conn->prepare("DELETE FROM {$table_name} WHERE {$id_col} = ?");
    $stmt_delete->bind_param("i", $id);

    if ($stmt_delete->execute()) {
        // 3. Hapus file gambar dari server
        if ($image_path && file_exists($image_path)) {
            unlink($image_path);
        }
        
        $_SESSION['success_message'] = "Konten berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus konten dari database: " . $conn->error;
    }
    
    $stmt_delete->close();
    $conn->close();
    header("Location: {$redirect_page}");
    exit;
} else {
    header("Location: admin.php");
    exit;
}
?>