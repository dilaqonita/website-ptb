<?php
// detail_artikel.php
include 'db_connect.php'; // Sertakan koneksi database

// --- 1. Ambil ID Artikel dari URL ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: landingpage.php");
    exit;
}
$article_id = $_GET['id'];

// --- PENTING: Tentukan Base URL Proyek Anda di sini ---
$base_url = "/end/"; // Pastikan ini sesuai dengan folder proyek Anda
// ----------------------------------------------------


// --- 2. Query untuk mengambil data spesifik (JOIN users + profile_picture) ---
// Menambahkan u.profile_picture ke SELECT
$sql = "SELECT a.title, a.content, a.thumbnail_path, a.created_at, u.nama_lengkap AS author_name, u.profile_picture 
        FROM articles a
        LEFT JOIN users u ON a.id_user = u.id
        WHERE a.id = ?";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();
$stmt->close();
$conn->close(); // Tutup koneksi setelah selesai

if (!$article) {
    die("Artikel tidak ditemukan.");
}

// Data yang akan digunakan di HTML
$article_title = htmlspecialchars($article['title'] ?? 'Judul Tidak Ditemukan');
$article_content = $article['content'] ?? ''; 
$image_path = htmlspecialchars($article['thumbnail_path'] ?? ''); 
$publish_date = date('d M Y', strtotime($article['created_at'] ?? 'now'));

// Mengambil nama penulis dan jalur foto dari hasil JOIN
$author_name = htmlspecialchars($article['author_name'] ?? 'Admin PTB');
$profile_pic_path = $article['profile_picture'] ?? null; 

// --- LOGIKA UTAMA PENGATURAN AVATAR (Koreksi Path) ---
$avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($author_name) . "&background=4F8A55&color=fff"; // Default Avatar

$file_exists = false;

if ($profile_pic_path) {
    // 1. Bersihkan path dari database
    $cleaned_profile_pic_path = ltrim($profile_pic_path, '/\\');

    // 2. KOREKSI PATH: Gunakan getcwd() + jalur relatif untuk pengecekan file_exists()
    $current_dir = getcwd();
    
    // Konversi path yang tersimpan di DB agar sesuai dengan separator OS
    $normalized_db_path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $cleaned_profile_pic_path);

    // Jalur Absolut Final untuk file_exists()
    $full_server_path_check = $current_dir . DIRECTORY_SEPARATOR . $normalized_db_path;

    // 3. Lakukan Pengecekan
    if (file_exists($full_server_path_check)) {
        $file_exists = true;
    }
}

if ($file_exists) {
    // Jika file_exists TRUE, gunakan URL web (disertai $base_url)
    $avatar_url = $base_url . $cleaned_profile_pic_path; 
}
// ----------------------------------------------------
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $article_title; ?> - PTB</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <script>
    tailwind.config={
        theme:{
            extend:{
                fontFamily:{
                    poppins: ['Poppins', 'sans-serif'],
                },
            },
        },
    }
  </script>
</head>

<body class="antialiased font-poppins bg-gray-50">
    <header class="bg-white/80 backdrop-blur-lg fixed top-0 left-0 right-0 w-full z-50 shadow-sm">
      <nav class="max-w-7xl mx-auto py-4 px-6 flex justify-between items-center text-gray-700">
        <a href="landingpage.php"><img src="<?php echo $base_url; ?>assets logo/logo.png" class="h-12" alt="Logo PTB"></a>
        <ul class="hidden md:flex gap-8 font-semibold">
          <li><a href="landingpage.php" class="hover:text-purple-600 transition">Beranda</a></li>
          <li><a href="sejarahprodi.html" class="hover:text-purple-600 transition">Profil</a></li>
          <li><a href="galeri.php" class="hover:text-purple-600 transition">Kegiatan</a></li>
          <li><a href="alur pendaftaran.html"class="hover:text-purple-600 transition">Pendaftaran</a></li>
          <li><a href="kontak.html" class="hover:text-purple-600 transition">Kontak</a></li>
        </ul>
        <button id="menu-btn" class="md:hidden text-gray-700 text-3xl z-50" aria-label="menu">☰</button>
      </nav>
    </header>

    <div id="mobileMenu" class="fixed top-0 right-0 h-full w-64 bg-white p-8 transform translate-x-full transition-transform duration-300 ease-in-out z-40">
      <ul class="flex flex-col gap-6 pt-20 text-lg">
          <li><a href="landingpage.php" class="hover:text-purple-600 block">Beranda</a></li>
          <li><a href="sejarahprodi.html" class="hover:text-purple-600 block">Profil</a></li>
          <li><a href="galeri.php" class="hover:text-purple-600 block">Kegiatan</a></li>
          <li><a href="alur pendaftaran.html" class="hover:text-purple-600 block">Pendaftaran</a></li>
          <li><a href="kontak.html" class="hover:text-purple-600 block">Kontak</a></li>
      </ul>
    </div>
    <div class="h-20"></div>

    <main class="flex-1">
        
        <section class="container mx-auto px-6 py-10 max-w-4xl">
            
            <h2 class="text-3xl font-bold text-green-700 leading-snug mb-4">
                <?php echo $article_title; ?>
            </h2>

            <div class="flex items-center gap-3 mb-8 border-b pb-4">
                <img src="<?php echo $avatar_url; ?>" alt="<?php echo $author_name; ?>'s Profile Picture"
                  class="w-10 h-10 rounded-full object-cover border border-gray-300" />
                <div>
                    <p class="text-sm font-semibold text-gray-700"><?php echo $author_name; ?></p>
                    <p class="text-xs text-gray-500"><?php echo $publish_date; ?></p>
                </div>
            </div>

            <?php if (!empty($image_path)): ?>
                <img src="<?php echo $base_url . $image_path; ?>" alt="<?php echo $article_title; ?>" 
                     class="rounded-2xl shadow-lg w-full object-cover max-h-[400px] mb-8 mx-auto" style="display: block;" />
            <?php endif; ?>

            <div class="prose max-w-none text-justify">
                <?php 
                echo nl2br($article_content);
                ?>
            </div>
            
            <div class="mt-10 pt-5 border-t border-gray-200">
                 <a href="landingpage.php" class="text-green-700 hover:text-green-800 font-medium">
                    &larr; Kembali ke Beranda
                </a>
            </div>
        </section>
    </main>
    
    </body>
<footer class="bg-green-900 text-white pt-16 pb-6">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-12">
        <div>
          <div class="flex items-center gap-3 mb-4">
            <img src="<?php echo $base_url; ?>assets logo/logoputih.png" class="h-14" alt="PTB Logo">
          </div>

          <p class="text-sm leading-relaxed">
            Jl. Kumbang No.14, RT.02/RW.06, Babakan,<br>
            Kecamatan Bogor Tengah, Kota Bogor,<br>
            Jawa Barat 16128
          </p>

          <div class="flex gap-4 mt-5">
            <a href="https://www.instagram.com/ptbsvipb/" class="hover:opacity-70 transition"><i class="fa-brands fa-instagram text-xl"></i></a>
            <a href="https://www.facebook.com/vokasiipb?locale=id_ID" class="hover:opacity-70 transition"><i class="fa-brands fa-facebook text-xl"></i></a>
            <a href="#" class="hover:opacity-70 transition"><i class="fa-brands fa-youtube text-xl"></i></a>
            <a href="#" class="hover:opacity-70 transition"><i class="fa-brands fa-tiktok text-xl"></i></a>
          </div>
        </div>

        <div>
          <h4 class="text-xl font-bold mb-5">Menu</h4>
          <ul class="space-y-3 text-sm">
            <li><a href="landingpage.php" class="hover:text-purple-400">Beranda</a></li>
            <li><a href="sejarahprodi.html" class="hover:text-purple-400">Profil</a></li>
            <li><a href="galeri.php" class="hover:text-purple-300">Galeri</a></li>
            <li><a href="alur pendaftaran.html" class="hover:text-purple-400">Pendaftaran</a></li>
            <li><a href="kontak.html" class="hover:text-purple-400">Kontak</a></li>
          </ul>
        </div>

        <div>
          <h4 class="text-xl font-bold mb-5">Kontak</h4>
          <p class="text-sm mb-3">+62 852 1659 0447</p>
          <p class="text-sm">ptb@apps.ipb.ac.id</p>
        </div>
      </div>

      <div class="border-t border-white/30 mt-12 pt-4 text-center">
        <p class="text-sm">
          © 2025 Pohon Toge Kelompok 4 B2
        </p>
      </div>
    </div>
  </footer>

</html>