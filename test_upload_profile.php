<?php
// test_upload_profile.php - File untuk test upload foto profil
session_start();
include 'db_connect.php';

// Pastikan admin sudah login
if (!isset($_SESSION['user_id'])) {
    die("Anda harus login terlebih dahulu!");
}

$admin_id = $_SESSION['user_id'];
$message = "";

// Proses Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $file = $_FILES['profile_pic'];
    
    // Validasi file
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        $message = "❌ Hanya file JPG, PNG, atau GIF yang diperbolehkan!";
    } elseif ($file['size'] > $max_size) {
        $message = "❌ Ukuran file maksimal 5MB!";
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $message = "❌ Terjadi kesalahan saat upload: " . $file['error'];
    } else {
        // Buat folder jika belum ada
        $upload_dir = 'uploads/profiles/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate nama file yang aman (tanpa karakter khusus)
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safe_filename = 'profile_' . $admin_id . '_' . time() . '.' . $file_extension;
        $destination = $upload_dir . $safe_filename;
        
        // Pindahkan file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Update database
            $db_path = $destination; // Simpan sebagai: uploads/profiles/profile_6_1234567890.png
            
            $sql = "UPDATE users SET profile_picture = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $db_path, $admin_id);
            
            if ($stmt->execute()) {
                $message = "✅ Foto profil berhasil diupload! Path: " . $db_path;
                // Redirect ke admin.php setelah 2 detik
                header("refresh:2;url=admin.php");
            } else {
                $message = "❌ Gagal menyimpan ke database: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "❌ Gagal memindahkan file ke folder uploads!";
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Upload Foto Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-4 text-gray-800">🖼️ Upload Foto Profil</h1>
        
        <?php if ($message): ?>
            <div class="mb-4 p-4 rounded <?php echo strpos($message, '✅') !== false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Foto Profil</label>
                <input type="file" 
                       name="profile_pic" 
                       accept="image/*" 
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF (Max 5MB)</p>
            </div>
            
            <button type="submit" 
                    class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition duration-200">
                Upload Foto
            </button>
        </form>
        
        <a href="admin.php" class="block text-center mt-4 text-blue-600 hover:underline">
            ← Kembali ke Dashboard
        </a>
        
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded text-sm">
            <p class="font-bold text-yellow-800">💡 Tips:</p>
            <ul class="list-disc ml-5 text-yellow-700 mt-2">
                <li>Gunakan foto dengan rasio 1:1 (persegi)</li>
                <li>Resolusi minimal 200x200 px</li>
                <li>Hindari nama file dengan karakter khusus</li>
            </ul>
        </div>
    </div>
</body>
</html>