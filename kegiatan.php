<?php
// kegiatan.php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

// --- PENTING: Tentukan Base URL Proyek Anda di sini ---
$base_url = "/end/";
// ----------------------------------------------------

$nama_admin = $_SESSION['nama_lengkap'] ?? 'Admin';
$admin_id = $_SESSION['user_id'] ?? 0; // Ambil user ID

// --- LOGIKA PENGAMBILAN PROFILE PICTURE (BARU DITAMBAHKAN) ---
$profile_pic_path = null;
if ($admin_id > 0) {
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


// --- LOGIKA PENCARIAN (BACKEND - Menggunakan Prepared Statements) ---
$search_query = $_GET['q'] ?? '';

$sql = "SELECT id, title, description, image_path, date_event, created_at FROM activities";
$params = [];
$types = "";

if (!empty($search_query)) {
    $sql .= " WHERE title LIKE ? OR description LIKE ?";
    $search_param = '%' . $search_query . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss"; // Dua string
}

$sql .= " ORDER BY date_event DESC, created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    // Memasukkan array $params ke bind_param
    $stmt->bind_param($types, ...$params); 
}

$stmt->execute();
$activities_data = $stmt->get_result();
$stmt->close();
// -----------------------------------------------------------


// Ambil pesan sukses/error dari session
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;

if (isset($_SESSION['success_message'])) {
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    unset($_SESSION['error_message']);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Kegiatan - PTB</title>
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
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ptb-green': '#4F8A55',
                        'ptb-dark': '#3E6E42',
                        'ptb-light': '#E8F5E9',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="flex h-screen overflow-hidden">
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
                        <a href="kegiatan.php" class="flex items-center p-3 rounded-lg bg-green-700 text-white font-medium transition duration-150">
                            <i class="fas fa-calendar-alt mr-3"></i>
                            Kegiatan
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="upload.php?type=article" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition duration-150">
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
                <form action="kegiatan.php" method="GET" class="relative w-1/3 max-w-sm">
                    <input
                        type="text"
                        placeholder="Cari kegiatan"
                        name="q"
                        value="<?php echo htmlspecialchars($search_query); ?>"
                        class="w-full bg-[#E9EFE9] rounded-lg pl-4 pr-10 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-600 placeholder-gray-400">

                    <button type="submit" class="absolute right-0 top-0 h-full px-3 text-gray-500 hover:text-gray-700 rounded-r-lg">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
                <div class="flex items-center gap-4">
                    <img src="<?php echo htmlspecialchars($avatar_url); ?>" 
                         alt="Profile" 
                         class="w-10 h-10 rounded-full border border-gray-200 object-cover"
                         onerror="this.src='<?php echo htmlspecialchars($default_avatar_url); ?>'; this.onerror=null;">
                    <div class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($nama_admin); ?></div>
                </div>
            </header>
            <div class="px-10 py-6">

                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Galeri Kegiatan</h2>
                    <a href="upload.php?type=activity" class="flex items-center gap-2 bg-ptb-green hover:bg-ptb-dark text-white text-lg font-medium py-2 px-4 rounded-lg transition">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tambah Kegiatan</span>
                    </a>
                </div>

                <?php
                if ($success_message): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Sukses</p>
                        <p><?php echo htmlspecialchars($success_message); ?></p>
                    </div>
                <?php endif;
                if ($error_message): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Error</p>
                        <p><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                <?php endif; ?>


                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-10">

                    <?php if ($activities_data->num_rows > 0): ?>
                        <?php while ($row = $activities_data->fetch_assoc()):

                            // Placeholder dan Image Path
                            $image_placeholder = $base_url . 'assets/placeholder.jpg'; // Pastikan Anda memiliki file ini
                            $full_image_path = $base_url . ($row['image_path'] ?: 'assets/placeholder.jpg');
                            $is_pdf = (strtolower(pathinfo($row['image_path'] ?? '', PATHINFO_EXTENSION)) === 'pdf');
                        ?>
                            <div class="relative h-48 rounded-xl overflow-hidden shadow-lg transition duration-300 hover:shadow-xl">

                                <a href="detail_kegiatan.php?id=<?php echo $row['id']; ?>" target="_blank" class="block w-full h-full">

                                    <?php if ($is_pdf): ?>
                                        <div class="w-full h-full bg-gray-600/80 flex items-center justify-center">
                                            <i class="fa-solid fa-file-pdf text-6xl text-red-400"></i>
                                        </div>
                                    <?php else: ?>
                                        <img src="<?php echo htmlspecialchars($full_image_path); ?>"
                                            alt="<?php echo htmlspecialchars($row['title']); ?>"
                                            class="w-full h-full object-cover"
                                            onerror="this.src='<?php echo htmlspecialchars($image_placeholder); ?>'; this.onerror=null;">
                                    <?php endif; ?>
                                </a>

                                <div class="absolute inset-0 bg-black/60 flex flex-col justify-end p-4 text-white">
                                    <h3 class="text-lg font-bold mb-1 truncate"><?php echo htmlspecialchars($row['title']); ?></h3>
                                    <p class="text-xs opacity-90 truncate"><?php echo htmlspecialchars($row['description']); ?></p>

                                    <div class="flex justify-between items-center text-xs opacity-90 mt-2">
                                        <span>Tanggal: <?php echo date('d M Y', strtotime($row['date_event'])); ?></span>
                                        <div class="space-x-3 text-sm">

                                            <a href="edit_content.php?id=<?php echo $row['id']; ?>&type=activity" class="text-yellow-400 hover:text-yellow-300" title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>

                                            <a href="delete_process.php?id=<?php echo $row['id']; ?>&type=activity" onclick="return confirm('Anda yakin ingin menghapus kegiatan ini?')" class="text-red-400 hover:text-red-300" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-gray-500 col-span-3">
                            <?php echo empty($search_query) ? 'Belum ada kegiatan yang diupload.' : 'Tidak ditemukan kegiatan yang cocok dengan "' . htmlspecialchars($search_query) . '".'; ?>
                        </p>
                    <?php endif; ?>

                </div>
            </div>
        </main>
    </div>
</body>

</html>