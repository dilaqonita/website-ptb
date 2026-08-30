<?php
// galeri.php
include 'db_connect.php'; // Sertakan koneksi database

// Ambil semua kegiatan dari database
// Menggunakan image_path, sesuai dengan struktur tabel activities yang Anda miliki.
$sql_activities = "SELECT id, title, description, image_path, date_event FROM activities ORDER BY date_event DESC, created_at DESC";
$activities_data = $conn->query($sql_activities);

// --- PENTING: Tentukan Base URL Proyek Anda di sini ---
$base_url = "/end/"; 
// ----------------------------------------------------

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="assets logo/fav-icon-ptb.png" type="image/png" />
  <title>Galeri Kegiatan - PTB</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            poppins: ["Poppins", "sans-serif"],
          },
        },
      },
    };
  </script>
</head>

<body class="font-poppins bg-gray-50">

  <header id="main-navbar"
    class="bg-white backdrop-blur-lg fixed top-0 left-0 right-0 w-full z-50 shadow-sm py-3 transition-transform duration-500 ease-in-out transform-gpu">
    <nav class="max-w-7xl mx-auto py-4 px-6 flex justify-between items-center text-gray-400">
      <a href="landingpage.php"> <img src="assets logo/logo.png" class="h-12" alt="Logo PTB" />
      </a>

      <ul class="hidden md:flex gap-8 font-semibold">
        <li>
          <a href="landingpage.php" class="hover:text-purple-600 transition">Beranda</a> </li>
        <li>
          <a href="sejarahprodi.html" class="hover:text-purple-600 transition">Profil</a>
        </li>
        <li>
          <a href="galeri.php"
            class="hover:text-purple-600 transition text-purple-600 border-b-4 pb-9 border-b-purple-600">Kegiatan</a> </li>
        <li>
          <a href="alur pendaftaran.html" class="hover:text-purple-600 transition">Pendaftaran</a>
        </li>
        <li>
          <a href="kontak.html" class="hover:text-purple-600 transition">Kontak</a>
        </li>
      </ul>

      <button id="menu-btn" class="md:hidden text-gray-700 text-3xl z-50" aria-label="menu">
        ☰
      </button>
    </nav>
  </header>

  <div id="mobileMenu"
    class="fixed top-0 right-0 h-full w-64 bg-white text-gray-700 p-8 transform translate-x-full transition-transform duration-300 ease-in-out z-40 shadow-lg">
    <ul class="flex flex-col gap-6 pt-20 text-lg font-medium">
      <li>
        <a href="landingpage.php" class="hover:text-purple-600 block">Beranda</a> </li>
      <li><a href="sejarahprodi.html" class="hover:text-purple-600 block">Profil</a></li>
      <li>
        <a href="galeri.php" class="hover:text-purple-600 block">Kegiatan</a> </li>
      <li>
        <a href="alur pendaftaran.html" class="hover:text-purple-600 block">Pendaftaran</a>
      </li>
      <li><a href="kontak.html" class="hover:text-purple-600 block">Kontak</a></li>
    </ul>
  </div>

  <section class="relative pt-[80px] pb-14">
    <div
      class="relative text-white flex flex-col items-center justify-center bg-center bg-no-repeat bg-cover px-6 py-24 sm:py-28 md:py-32"
      style="background-image: url('Union.png')">
      <div class="relative z-10 text-center max-w-2xl">
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight">
          Galeri Kegiatan
        </h1>
        <p class="mt-4 text-gray-200 text-sm md:text-base">
          Intip Berbagai Momen di Prodi PTB
        </p>
      </div>
    </div>
  </section>

  <section class="py-16 px-4">
    <div class="max-w-7xl mx-auto">

        <div class="columns-1 md:columns-2 lg:columns-3 gap-6 space-y-6">

            <?php if ($activities_data->num_rows > 0): ?>
                <?php while($row = $activities_data->fetch_assoc()):
                    $full_image_path = $base_url . htmlspecialchars($row['image_path']);
                    $is_pdf = (pathinfo($row['image_path'], PATHINFO_EXTENSION) == 'pdf');
                    
                    // Siapkan data JSON untuk modal
                    $data_json = htmlspecialchars(json_encode([
                        'path' => $full_image_path,
                        'title' => $row['title'],
                        'description' => $row['description'],
                        'date' => date('d M Y', strtotime($row['date_event'])),
                        'is_pdf' => $is_pdf
                    ]), ENT_QUOTES, 'UTF-8');
                ?>
                    <div onclick='openModal(<?php echo $data_json; ?>)'
                       class="group relative break-inside-avoid rounded-2xl overflow-hidden cursor-pointer shadow-lg block">
                       
                        <?php if ($is_pdf): ?>
                            <div class="w-full h-auto min-h-[300px] flex items-center justify-center bg-gray-700/90">
                                <i class="fa-solid fa-file-pdf text-6xl text-red-400"></i>
                            </div>
                        <?php else: ?>
                            <img src="<?php echo $full_image_path; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" loading="lazy"
                                class="w-full h-auto object-cover transform transition-transform duration-500 group-hover:scale-110" />
                        <?php endif; ?>

                        <div
                          class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-center items-center text-center p-4">
                          <h3 class="text-white text-xl font-bold"><?php echo htmlspecialchars($row['title']); ?></h3>
                          <p class="text-gray-200 text-sm mt-2">Tanggal: <?php echo date('d M Y', strtotime($row['date_event'])); ?></p>
                          <p class="text-gray-200 text-sm mt-1 truncate"><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center text-gray-600 p-10 col-span-full">Belum ada kegiatan yang diunggah.</div>
            <?php endif; ?>

        </div>
        </div>
</section>
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-[99] hidden">
    <div class="bg-white rounded-lg shadow-2xl max-w-4xl w-full mx-4 p-4 relative transform transition-all duration-300 scale-95" onclick="event.stopPropagation()">
        
        <button onclick="closeModal()" class="absolute top-0 right-0 m-3 text-3xl text-white bg-red-600/70 hover:bg-red-700 w-8 h-8 rounded-full leading-none transition z-50">&times;</button>

        <div id="modalImageContainer" class="flex justify-center items-center max-h-[80vh] overflow-hidden">
            </div>

        <div class="p-4 border-t mt-4">
            <h3 id="modalTitle" class="text-2xl font-bold text-gray-800"></h3>
            <p id="modalDate" class="text-sm text-gray-500 mt-1"></p>
            <p id="modalDescription" class="text-gray-700 mt-3 whitespace-pre-line"></p>
        </div>
    </div>
</div>
<footer class="relative text-white overflow-hidden mt-20">
    <div class="absolute top-0 left-0 w-full h-full -z-10" style="
          background-image: url('footer1.png');
          background-repeat: no-repeat;
          background-position: top center;
          background-size: cover;
        "></div>
    <div class="relative z-10 pt-32 pb-10">
      <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-12">
        <div>
          <div class="flex items-center gap-3 mb-4">
            <img src="assets logo/logoputih.png" alt="PTB Logo" class="h-14" />
          </div>
          <p class="text-sm leading-relaxed">
            Jl. Kumbang No.14, RT.02/RW.06, Babakan,<br />
            Kecamatan Bogor Tengah, Kota Bogor,<br />
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
            <li><a href="landingpage.php" class="hover:text-purple-300">Beranda</a></li> <li><a href="sejarahprodi.html" class="hover:text-purple-300">Profil</a></li>
            <li><a href="galeri.php" class="hover:text-purple-300">Galeri</a></li> <li><a href="alur pendaftaran.html" class="hover:text-purple-300">Pendaftaran</a></li>
            <li><a href="kontak.html" class="hover:text-purple-300">Kontak</a></li>
          </ul>
        </div>

        <div>
          <h4 class="text-xl font-bold mb-5">Kontak</h4>
          <p class="text-sm mb-3">+62 852 1659 0447</p>
          <p class="text-sm">ptb@apps.ipb.ac.id</p>
        </div>
      </div>

      <div class="border-t border-white/30 mt-12 pt-4 text-center">
        <p class="text-sm">© 2025 Pohon Toge Kelompok 4 B2</p>
      </div>
    </div>
  </footer>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

  <script>
    // ==================================================
    // LOGIKA MODAL/POPUP GALERI
    // ==================================================
    const imageModal = document.getElementById('imageModal');
    const modalImageContainer = document.getElementById('modalImageContainer');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const modalDate = document.getElementById('modalDate');

    function openModal(data) {
        let contentHTML = '';
        
        // Memasukkan gambar atau placeholder PDF
        if (data.is_pdf) {
             contentHTML = `
                <div class="flex flex-col items-center justify-center p-8 text-center">
                    <i class="fa-solid fa-file-pdf text-9xl text-red-600"></i>
                    <p class="mt-4 text-xl text-gray-700 font-medium">Dokumen PDF tidak dapat ditampilkan di sini.</p>
                </div>
            `;
        } else {
            contentHTML = `<img src="${data.path}" alt="${data.title}" class="w-full h-full object-contain max-h-[75vh] rounded-lg">`;
        }
        
        modalImageContainer.innerHTML = contentHTML;
        modalTitle.textContent = data.title;
        modalDate.textContent = `Tanggal: ${data.date}`;
        // Menggunakan innerHTML karena description mungkin mengandung line break
        modalDescription.innerHTML = data.description.replace(/\n/g, '<br>'); 
        
        imageModal.classList.remove('hidden');
        imageModal.style.display = 'flex';
        document.body.classList.add('overflow-hidden'); // Mencegah scroll pada body
    }

    function closeModal() {
        imageModal.classList.add('hidden');
        imageModal.style.display = 'none';
        modalImageContainer.innerHTML = '';
        document.body.classList.remove('overflow-hidden'); 
    }

    // Menutup modal saat klik di luar area konten modal
    imageModal.addEventListener('click', function(e) {
        if (e.target === imageModal) {
            closeModal();
        }
    });

    // ==================================================
    // 1. MOBILE MENU
    // ==================================================
    const menuBtn = document.getElementById("menu-btn");
    const mobileMenu = document.getElementById("mobileMenu");

    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            const isHidden = mobileMenu.classList.toggle("translate-x-full");
            menuBtn.textContent = isHidden ? "☰" : "✕";
        });

        document.body.addEventListener("click", (e) => {
            if (!mobileMenu.classList.contains("translate-x-full") &&
                !mobileMenu.contains(e.target) &&
                e.target !== menuBtn) {

                mobileMenu.classList.add("translate-x-full");
                menuBtn.textContent = "☰";
            }
        });

        mobileMenu.addEventListener("click", (e) => e.stopPropagation());
    }

    // ==================================================
    // 2. NAVBAR SMOOTH SCROLL
    // ==================================================
    const navbar = document.getElementById("main-navbar");
    let lastScrollTop = 0;
    const delta = 10; 

    if (navbar) {
        window.addEventListener("scroll", function () {
            let currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            if (Math.abs(lastScrollTop - currentScroll) <= delta) return;
            if (currentScroll > lastScrollTop && currentScroll > 50) {
                navbar.style.transform = "translateY(-100%)";
            } else {
                navbar.style.transform = "translateY(0)";
            }
            lastScrollTop = currentScroll;
        });
    }

    // ==================================================
    // 3. AOS INIT
    // ==================================================
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 900,
            offset: 80,
            easing: 'ease-in-out',
            once: true
        });
    }
  </script>

</body>

</html>