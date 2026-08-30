<?php
// edit_content.php (Dikoreksi untuk Menghilangkan Dokumen Utama/PDF)

session_start();
include 'db_connect.php';

// Proteksi akses
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit; }
if (!isset($_GET['id']) || !isset($_GET['type'])) { header("Location: admin.php"); exit; }

$id = $_GET['id'];
$type = $_GET['type'];
$table_name = ($type == 'article') ? 'articles' : 'activities';
$redirect_page = ($type == 'article') ? 'artikel.php' : 'kegiatan.php';

// Ambil data lama dari DB (Hanya ambil thumbnail_path)
$stmt = $conn->prepare("SELECT * FROM {$table_name} WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$content = $result->fetch_assoc();
$stmt->close();

if (!$content) { header("Location: admin.php"); exit; }
$page_title = ($type == 'article') ? 'Edit Artikel' : 'Edit Kegiatan';

// Ambil data untuk diisi di form
$current_title = $content['title'];
$current_description = $content['content'] ?? $content['description']; 
$current_thumbnail = $content['thumbnail_path'] ?? $content['image_path'] ?? ''; // PATH THUMBNAIL LAMA (Ditambahkan image_path untuk kompatibilitas kegiatan)
// $current_document DIHAPUS
$current_date = $content['date_event'] ?? '';

// Tentukan Base URL
// Perhatian: Base URL sebelumnya adalah /back/, saya kembalikan ke /end/ atau biarkan saja /back/
$base_url = "/back/"; 
$current_thumbnail_full_path = $base_url . $current_thumbnail; 
// $current_document_full_path DIHAPUS

// LOGIC: Tentukan Status File
$is_existing_thumbnail = !empty($current_thumbnail);
// $is_existing_document dan $existing_doc_ext DIHAPUS
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - PTB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; }
        input[type="file"] { display: none; } 
        .ptb-logo-color { color: #4F8A55; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ptb-green': '#4F8A55', 
                        'ptb-border': '#3E6E42', 
                        'input-bg': '#EEEEEE',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-white text-gray-800">
    <aside class="w-64 bg-ptb-green text-white flex flex-col px-6 py-8 shadow-xl fixed h-full">
        <div class="flex items-center gap-3 mb-10">
            <div class="text-3xl text-white ptb-logo-color"><i class="fa-solid fa-seedling"></i></div>
            <div class="flex flex-col">
                <h1 class="text-2xl font-bold tracking-wide">PTB</h1>
                <span class="text-[10px] opacity-80 leading-3">Superior Variety Seeds For Life</span>
            </div>
        </div>
    </aside>

    <div class="flex h-screen overflow-hidden pl-64 w-full"> 
        <main class="flex-1 flex flex-col overflow-y-auto w-full">
            <header class="px-12 py-8 flex justify-between items-center bg-white shadow-sm sticky top-0 z-10">
                <h1 class="text-2xl font-semibold text-gray-800"><?php echo $page_title; ?></h1>
            </header>
            
            <div class="px-12 pb-12 w-full">
                <form action="update_process.php" method="POST" enctype="multipart/form-data" class="space-y-8 max-w-5xl">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                    <input type="hidden" name="existing_thumbnail" value="<?php echo htmlspecialchars($current_thumbnail); ?>">
                    <div>
                        <label class="block text-2xl font-semibold text-gray-800 mb-3">Jenis Konten</label>
                        <p class="w-full bg-input-bg border border-ptb-border rounded-xl px-6 py-4 text-lg text-gray-700 font-medium"><?php echo ($type == 'article') ? 'Artikel' : 'Galeri Kegiatan'; ?></p>
                    </div>

                    <div>
                        <label class="block text-2xl font-semibold text-gray-800 mb-3">Judul Konten</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($current_title); ?>"
                               class="w-full bg-input-bg border border-ptb-border rounded-xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-green-600 text-lg text-gray-700" required>
                    </div>

                    <?php if ($type == 'activity'): ?>
                    <div id="date_field">
                        <label class="block text-2xl font-semibold text-gray-800 mb-3">Tanggal Kegiatan</label>
                        <input type="date" name="date_event" value="<?php echo $current_date; ?>"
                               class="w-full bg-input-bg border border-ptb-border rounded-xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-green-600 text-lg text-gray-700" required>
                    </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-2xl font-semibold text-gray-800 mb-3">Deskripsi Konten</label>
                        <textarea rows="6" name="description"
                                  class="w-full bg-input-bg border border-ptb-border rounded-xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-green-600 text-lg text-gray-700 resize-none" required><?php echo htmlspecialchars($current_description); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-2xl font-semibold text-gray-800 mb-3">Ganti Gambar Thumbnail</label>
                        
                        <label for="file-thumbnail" class="w-full h-32 bg-input-bg border border-ptb-border rounded-xl flex justify-center items-center cursor-pointer hover:bg-gray-200 transition <?php echo $is_existing_thumbnail ? 'hidden' : ''; ?>" id="upload_area_thumb">
                            <i class="fa-solid fa-camera text-4xl text-ptb-border"></i>
                        </label>
                        
                        <div id="image_preview_container_thumb" class="mt-4 <?php echo $is_existing_thumbnail ? '' : 'hidden'; ?>">
                            <img id="image_preview_thumb" src="<?php echo $current_thumbnail_full_path; ?>" alt="Preview Thumbnail" class="w-full max-h-48 object-cover rounded-xl border border-gray-300">
                            
                            <button type="button" onclick="removeThumbnail()" class="mt-2 text-sm text-red-600 hover:text-red-800 font-medium">Hapus/Ganti Thumbnail</button>
                        </div>
                        
                        <input type="file" name="file-thumbnail" accept="image/*" id="file-thumbnail">
                    </div>

                    <div class="pt-4 flex gap-4">
                        <button type="submit" class="flex-1 bg-ptb-green hover:bg-[#3E6E42] text-white text-2xl font-bold py-4 rounded-xl shadow-lg transition duration-300">
                            Simpan Perubahan
                        </button>
                        <a href="<?php echo $redirect_page; ?>" class="flex-1 text-center bg-gray-300 hover:bg-gray-400 text-gray-800 text-2xl font-bold py-4 rounded-xl transition duration-300">
                            Batal
                        </a>
                    </div>

                </form>
            </div>
        </main>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const thumbnailInput = document.getElementById('file-thumbnail');
            // documentInput DIHAPUS
            
            const uploadAreaThumb = document.getElementById('upload_area_thumb');
            const previewContainerThumb = document.getElementById('image_preview_container_thumb');
            const imagePreviewThumb = document.getElementById('image_preview_thumb');
            
            // Variabel terkait Dokumen DIHAPUS

            // --- HANDLER THUMBNAIL ---
            thumbnailInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        imagePreviewThumb.src = event.target.result;
                        uploadAreaThumb.classList.add('hidden'); 
                        previewContainerThumb.classList.remove('hidden'); 
                    }
                    reader.readAsDataURL(file);
                }
            });

            // --- HANDLER DOKUMEN DIHAPUS ---
            // documentInput.addEventListener('change', function(e) { ... });

            // FUNGSI GLOBAL UNTUK MENGHAPUS/RESET THUMBNAIL
            window.removeThumbnail = function() {
                thumbnailInput.value = ''; 
                imagePreviewThumb.src = '#'; 
                previewContainerThumb.classList.add('hidden');
                uploadAreaThumb.classList.remove('hidden');
            };
            
            // FUNGSI GLOBAL UNTUK MENGGANTI DOKUMEN DIHAPUS
            // window.changeDocument = function() { ... };
        });
    </script>
</body>
</html>