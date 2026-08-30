<?php
session_start();
// --- Tambahkan include db_connect.php di sini ---
include 'db_connect.php';
// ----------------------------------------------

// Proteksi: Pastikan user sudah login
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

// --- PENTING: Tentukan Base URL Proyek Anda di sini ---
$base_url = "/end/"; 
// ----------------------------------------------------

$upload_error = $_SESSION['upload_error'] ?? null;
if (isset($_SESSION['upload_error'])) {
    unset($_SESSION['upload_error']);
}

// Tentukan jenis konten berdasarkan parameter URL
$selected_type = $_GET['type'] ?? 'article'; 

// Data Admin untuk Header
$nama_admin = $_SESSION['nama_lengkap'] ?? 'Admin';
$admin_id = $_SESSION['user_id'] ?? 0;

// --- LOGIKA PENGAMBILAN PROFILE PICTURE ---
$profile_pic_path = null;
if ($admin_id > 0) {
    // Karena $conn sudah di-include, kita bisa menggunakannya
    $sql_admin_data = "SELECT profile_picture FROM users WHERE id = ?";
    $stmt_admin = $conn->prepare($sql_admin_data);
    
    if ($stmt_admin) {
        $stmt_admin->bind_param("i", $admin_id);
        $stmt_admin->execute();
        $result_admin_data = $stmt_admin->get_result();
        $admin_data = $result_admin_data->fetch_assoc();
        $stmt_admin->close();
        
        $profile_pic_path = $admin_data['profile_picture'] ?? null;
    }
}

// Default Avatar (inisial)
$default_avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($nama_admin) . "&background=0D8ABC&color=fff";
$avatar_url = $default_avatar_url; // Default ke UI Avatar

// Jika path gambar profil ditemukan di DB, gunakan path URL yang benar
if ($profile_pic_path) {
    // Bersihkan path dari karakter slash di awal
    $cleaned_profile_pic_path = ltrim($profile_pic_path, '/\\');
    
    // Bentuk URL lengkap untuk browser
    $avatar_url = $base_url . str_replace('\\', '/', $cleaned_profile_pic_path);
}
// --- END LOGIKA PENGAMBILAN PROFILE PICTURE ---

// Tutup koneksi di akhir
if (isset($conn)) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Konten - PTB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
        }
        .sidebar {
            width: 250px;
            background-color: #044F1D;
            color: white;
            padding: 20px;
            position: fixed;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            margin-left: 250px;
            padding: 0;
            flex-grow: 1;
        }
        /* Hide Default File Input */
        input[type="file"] { display: none; }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ptb-green': '#4F8A55', 
                        'ptb-dark': '#3E6E42', 
                        'input-bg': '#EEEEEE',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="flex h-screen">
        
        <aside class="sidebar">
            <div class="text-2xl font-bold mb-10 text-center">PTB Admin</div>
            <nav class="flex-grow">
                <ul>
                    <li class="mb-3">
                        <a href="admin.php" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition duration-150">
                            <i class="fas fa-home mr-3"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="artikel.php" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition duration-150">
                            <i class="fas fa-newspaper mr-3"></i>
                            Artikel
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="kegiatan.php" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition duration-150">
                            <i class="fas fa-calendar-alt mr-3"></i>
                            Kegiatan
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="upload.php?type=article" class="flex items-center p-3 rounded-lg bg-green-700 text-white font-medium transition duration-150">
                            <i class="fas fa-upload mr-3"></i>
                            Upload Konten
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="mt-auto">
                <a href="logout.php" class="flex items-center p-3 rounded-lg hover:bg-red-600 transition duration-150 bg-red-700">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    Logout
                </a>
            </div>
        </aside>
        
        <main class="main-content flex flex-col overflow-y-auto">
            
            <header class="px-10 py-6 flex justify-between items-center bg-white shadow-md">
                
                <div class="text-xl font-semibold text-gray-800">Upload Konten</div>
                
                <div class="flex items-center gap-4">
                    <img src="<?php echo htmlspecialchars($avatar_url); ?>" 
                         alt="Profile" 
                         class="w-10 h-10 rounded-full border border-gray-200 object-cover"
                         onerror="this.src='<?php echo htmlspecialchars($default_avatar_url); ?>'; this.onerror=null;">
                    <div class="text-sm font-medium text-gray-700">
                        <?php echo htmlspecialchars($nama_admin); ?>
                    </div>
                </div>
            </header>
            
            <div class="p-10">
                
                <?php if ($upload_error): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Gagal Upload!</p>
                        <span class="block sm:inline"><?php echo $upload_error; ?></span>
                    </div>
                <?php endif; ?>

                <h2 class="text-2xl font-bold text-gray-800 mb-6">Upload Konten Baru</h2>

                <form action="upload_process.php" method="POST" enctype="multipart/form-data" class="space-y-8 max-w-5xl">
                    
                    <div>
                        <label class="block text-lg font-semibold text-gray-800 mb-2">Jenis Konten</label>
                        <select name="content_type" 
                                class="w-full bg-input-bg border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-ptb-green text-base text-gray-700"
                                onchange="toggleDateField(this.value)" required>
                            <option value="article" <?php echo ($selected_type == 'article') ? 'selected' : ''; ?>>Artikel</option>
                            <option value="activity" <?php echo ($selected_type == 'activity') ? 'selected' : ''; ?>>Galeri Kegiatan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-lg font-semibold text-gray-800 mb-2">Judul Konten</label>
                        <input type="text" name="title"
                               class="w-full bg-input-bg border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-ptb-green text-base text-gray-700" required>
                    </div>

                    <div id="date_field" style="display: <?php echo ($selected_type == 'activity') ? 'block' : 'none'; ?>;">
                        <label class="block text-lg font-semibold text-gray-800 mb-2">Tanggal Kegiatan</label>
                        <input type="date" name="date_event"
                               class="w-full bg-input-bg border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-ptb-green text-base text-gray-700">
                    </div>

                    <div>
                        <label class="block text-lg font-semibold text-gray-800 mb-2">Deskripsi Konten</label>
                        <textarea rows="6" name="description"
                                  class="w-full bg-input-bg border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-ptb-green text-base text-gray-700 resize-none" required></textarea>
                    </div>

                    <div>
                        <label class="block text-lg font-semibold text-gray-800 mb-2">Upload Gambar Postingan</label>
                        
                        <label for="file-thumbnail" class="w-full h-48 bg-input-bg border-2 border-dashed border-gray-400 rounded-xl flex flex-col items-center justify-center cursor-pointer hover:bg-gray-200 transition" id="upload_area_thumb">
                            <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-500 mb-2"></i>
                            <span class="text-base text-gray-600">Klik untuk memilih file (JPG/PNG/PDF)</span>
                        </label>
                        
                        <div id="image_preview_container_thumb" class="mt-4 hidden">
                            <img id="image_preview_thumb" src="#" alt="Thumbnail Preview" class="w-full max-h-80 object-contain rounded-xl border border-gray-300">
                            <button type="button" onclick="removeImage('thumb')" class="mt-2 text-sm text-red-600 hover:text-red-800 font-medium">Hapus File</button>
                        </div>
                        
                        <input id="file-thumbnail" type="file" name="file-thumbnail" accept="image/*, application/pdf" required>
                    </div>


                    <div class="pt-4">
                        <button type="submit" class="w-full bg-ptb-green hover:bg-ptb-dark text-white text-xl font-bold py-4 rounded-xl shadow-lg transition duration-300">
                            Kirim Konten
                        </button>
                    </div>

                </form>

            </div>

        </main>
    </div>

    <script>
        // Fungsi untuk menampilkan/menyembunyikan input tanggal
        function toggleDateField(type) {
            const dateField = document.getElementById('date_field');
            const dateInput = dateField.querySelector('input');
            if (type === 'activity') {
                dateField.style.display = 'block';
                dateInput.setAttribute('required', 'required');
            } else {
                dateField.style.display = 'none';
                dateInput.removeAttribute('required');
            }
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            toggleDateField('<?php echo $selected_type; ?>');
        });

        // =======================================================
        // LOGIKA PREVIEW GAMBAR
        // =======================================================
        
        const thumbnailInput = document.getElementById('file-thumbnail');
        
        const uploadAreaThumb = document.getElementById('upload_area_thumb');
        const previewContainerThumb = document.getElementById('image_preview_container_thumb');
        const imagePreviewThumb = document.getElementById('image_preview_thumb');

        // Handler untuk File Preview
        thumbnailInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileName = file.name;
                const fileType = file.type;
                
                // Cek jika itu gambar
                if (fileType.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        imagePreviewThumb.src = event.target.result;
                        imagePreviewThumb.alt = 'Thumbnail Preview';
                        uploadAreaThumb.classList.add('hidden'); 
                        previewContainerThumb.classList.remove('hidden'); 
                    }
                    reader.readAsDataURL(file);
                } 
                // Cek jika itu PDF
                else if (fileType === 'application/pdf') {
                    // Tampilkan ikon PDF atau nama file, karena PDF tidak bisa dipreview langsung
                    imagePreviewThumb.src = 'https://via.placeholder.com/300x150?text=PDF+File'; 
                    imagePreviewThumb.alt = 'File PDF: ' + fileName;
                    uploadAreaThumb.classList.add('hidden'); 
                    previewContainerThumb.classList.remove('hidden'); 
                }
            }
        });
        
        // Fungsi untuk menghapus gambar preview dan mengaktifkan kembali upload area
        function removeImage(type) {
            if (type === 'thumb') {
                thumbnailInput.value = ''; 
                imagePreviewThumb.src = '#';
                previewContainerThumb.classList.add('hidden'); 
                uploadAreaThumb.classList.remove('hidden');
            } 
        }
    </script>
</body>
</html>