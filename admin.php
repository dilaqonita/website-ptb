<?php
// admin.php
session_start();
include 'db_connect.php'; // Pastikan file ini ada dan berisi koneksi DB

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// --- PENTING: Tentukan Base URL Proyek Anda di sini ---
$base_url = "/end/"; 
// ----------------------------------------------------

$nama_admin = $_SESSION['nama_lengkap'] ?? 'Admin';
$admin_id = $_SESSION['user_id'] ?? 0;

// --- AMBIL DATA PROFILE PICTURE ---
$profile_pic_path = null;
// Default Avatar (inisial)
$default_avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($nama_admin) . "&background=0D8ABC&color=fff";
$avatar_url = $default_avatar_url;

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

    if ($profile_pic_path) {
        $cleaned_profile_pic_path = ltrim($profile_pic_path, '/\\');
        $avatar_url = $base_url . str_replace('\\', '/', $cleaned_profile_pic_path);
    }
}

// --- QUERY TOTAL ARTIKEL (JUMLAHKAN SEMUA ARTIKEL) ---
$total_articles = 0;
$stmt_articles = $conn->prepare("SELECT COUNT(id) AS total FROM articles");
if ($stmt_articles) {
    $stmt_articles->execute();
    $result_articles = $stmt_articles->get_result();
    $total_articles = $result_articles->fetch_assoc()['total'] ?? 0;
    $stmt_articles->close();
}

// --- QUERY TOTAL KEGIATAN (JUMLAHKAN SEMUA KEGIATAN) ---
$total_activities = 0;
$stmt_activities = $conn->prepare("SELECT COUNT(id) AS total FROM activities");
if ($stmt_activities) {
    $stmt_activities->execute();
    $result_activities = $stmt_activities->get_result();
    $total_activities = $result_activities->fetch_assoc()['total'] ?? 0;
    $stmt_activities->close();
}


// === QUERY PREVIEW ARTIKEL TERBARU (MENGAMBIL 3 TERBARU, TIDAK DIFILTER PER USER) ===
$articles_preview = null;
$sql_articles_preview = "
    SELECT a.id, a.title, a.content, a.thumbnail_path, a.created_at, u.nama_lengkap AS author_name 
    FROM articles a
    LEFT JOIN users u ON a.id_user = u.id
    ORDER BY a.created_at DESC 
    LIMIT 3
";
$articles_preview = $conn->query($sql_articles_preview);


// === QUERY PREVIEW KEGIATAN TERBARU (MENGAMBIL 3 TERBARU, TIDAK DIFILTER PER USER) ===
$activities_preview = null;
$sql_activities_preview = "
    SELECT id, title, description, image_path, date_event 
    FROM activities
    ORDER BY date_event DESC 
    LIMIT 3
";
$activities_preview = $conn->query($sql_activities_preview);


// Ambil pesan sukses/error dari session dan hapus setelah digunakan
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;

if (isset($_SESSION['success_message'])) { unset($_SESSION['success_message']); }
if (isset($_SESSION['error_message'])) { unset($_SESSION['error_message']); }

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin PTB</title>
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

<body class="bg-gray-50">
    <div class="flex h-screen">
        <aside class="sidebar">
            <div class="text-2xl font-bold mb-10 text-center">PTB Admin</div>
            <nav class="flex-grow">
                <ul>
                    <li class="mb-3">
                        <a href="admin.php" class="flex items-center p-3 rounded-lg bg-green-700 text-white font-medium transition duration-150">
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

        <main class="main-content flex flex-col">
            <header class="px-10 py-6 flex justify-between items-center bg-white shadow-md">
                
                <form action="artikel.php" method="GET" class="relative w-1/3 max-w-sm">
                    <input
                        type="text"
                        placeholder="Cari artikel"
                        name="q" 
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
                    <div class="text-sm font-medium text-gray-700">
                        <?php echo htmlspecialchars($nama_admin); ?>
                    </div>
                </div>
            </header>

            <div class="px-10 pt-6">
                <?php if ($success_message): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Sukses</p>
                        <p><?php echo htmlspecialchars($success_message); ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Error</p>
                        <p><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="p-10 pt-0 flex-grow">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                    <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-600">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Artikel</p>
                            <i class="fas fa-newspaper text-3xl text-green-600 opacity-70"></i>
                        </div>
                        <p class="text-4xl font-extrabold text-gray-900 mt-1"><?php echo $total_articles; ?></p>
                        <p class="text-xs text-gray-400 mt-2">Konten informatif yang diunggah</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-yellow-600">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Kegiatan</p>
                            <i class="fas fa-calendar-alt text-3xl text-yellow-600 opacity-70"></i>
                        </div>
                        <p class="text-4xl font-extrabold text-gray-900 mt-1"><?php echo $total_activities; ?></p>
                            <p class="text-xs text-gray-400 mt-2">Kegiatan dan event yang dipublikasi</p>
                    </div>
                </div>

                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">3 Artikel Terbaru</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <?php 
                    $image_placeholder = $base_url . 'assets/placeholder.jpg'; 
                    if ($articles_preview && $articles_preview->num_rows > 0): ?>
                        <?php while ($row = $articles_preview->fetch_assoc()): 
                            $full_image_path = $base_url . ($row['thumbnail_path'] ?? 'assets/placeholder.jpg'); 
                            $is_pdf = (strtolower(pathinfo($row['thumbnail_path'] ?? '', PATHINFO_EXTENSION)) === 'pdf');
                        ?>
                            <a href="detail_artikel.php?id=<?php echo $row['id']; ?>" target="_blank" class="block bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300 relative">
                                <div class="h-40 overflow-hidden relative">
                                    <?php if ($is_pdf): ?>
                                    <div class="absolute inset-0 bg-gray-600/80 flex items-center justify-center">
                                        <i class="fa-solid fa-file-pdf text-6xl text-red-400"></i>
                                    </div>
                                    <?php else: ?>
                                    <img src="<?php echo htmlspecialchars($full_image_path); ?>" 
                                         alt="<?php echo htmlspecialchars($row['title']); ?>" 
                                         class="w-full h-full object-cover"
                                         onerror="this.src='<?php echo htmlspecialchars($image_placeholder); ?>'; this.onerror=null;">
                                    <?php endif; ?>
                                </div>
                                <div class="p-4">
                                    <h3 class="text-md font-bold text-gray-800 mb-2 truncate"><?php echo htmlspecialchars($row['title']); ?></h3>
                                    <p class="text-xs text-gray-500 line-clamp-2 mb-2">
                                        <?php echo strip_tags(substr($row['content'], 0, 100)) . (strlen($row['content']) > 100 ? '...' : ''); ?>
                                    </p>
                                    <p class="text-xs text-green-700 font-medium">
                                        <?php echo htmlspecialchars($row['author_name'] ?? 'Admin'); ?> | <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                    </p>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-gray-500 col-span-3">Belum ada artikel terbaru.</p>
                    <?php endif; ?>
                </div>

                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">3 Kegiatan Terbaru</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php if ($activities_preview && $activities_preview->num_rows > 0): ?>
                        <?php while ($row = $activities_preview->fetch_assoc()): 
                            $image_placeholder = $base_url . 'assets/placeholder.jpg'; 
                            $full_image_path = $base_url . ($row['image_path'] ?? 'assets/placeholder.jpg');
                            $is_pdf = (strtolower(pathinfo($row['image_path'] ?? '', PATHINFO_EXTENSION)) === 'pdf'); 
                        ?>
                            <a href="kegiatan.php" class="block bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300 relative">
                                <div class="h-40 overflow-hidden relative">
                                    <?php if ($is_pdf): ?>
                                    <div class="absolute inset-0 bg-gray-600/80 flex items-center justify-center">
                                        <i class="fa-solid fa-file-pdf text-6xl text-red-400"></i>
                                    </div>
                                    <?php else: ?>
                                    <img src="<?php echo htmlspecialchars($full_image_path); ?>" 
                                         alt="<?php echo htmlspecialchars($row['title']); ?>" 
                                         class="w-full h-full object-cover"
                                         onerror="this.src='<?php echo htmlspecialchars($image_placeholder); ?>'; this.onerror=null;">
                                    <?php endif; ?>
                                </div>

                                <div class="absolute inset-0 bg-black/50 flex flex-col justify-end p-4 text-white">
                                    <h3 class="text-lg font-bold mb-1 truncate"><?php echo htmlspecialchars($row['title']); ?></h3>
                                    <p class="text-xs opacity-80">
                                        Admin | Tanggal: <?php echo date('d M Y', strtotime($row['date_event'])); ?>
                                    </p>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-gray-500 col-span-3">Belum ada kegiatan terbaru.</p>
                    <?php endif; ?>
                </div>
                
            </div>
        </main>
    </div>
</body>

</html>