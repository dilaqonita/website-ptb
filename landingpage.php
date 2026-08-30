<?php
// Ganti nama file ini menjadi landingpage.php
include 'db_connect.php'; // Sertakan koneksi database

// PERBAIKAN: Mengganti 'image_path' menjadi 'thumbnail_path'
$sql_articles = "SELECT id, title, content, thumbnail_path, created_at FROM articles ORDER BY created_at DESC LIMIT 4";
$articles_data = $conn->query($sql_articles);

// --- PENTING: Tentukan Base URL Proyek Anda di sini ---
$base_url = "/end/";
// ------------------------------------------
?>

<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="assets logo/fav-icon-ptb.png" type="image/png" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Program Studi Pemuliaan Tanaman dan Teknologi Benih</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            poppins: ['Poppins', 'sans-serif'],
          },
        },
      },
    }
  </script>

  <style>
    /* ... (Style CSS tetap sama) ... */
    .reveal-on-scroll {
      opacity: 0;
      transform: translateY(30px);
      transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }

    .reveal-on-scroll.is-visible {
      opacity: 1;
      transform: translateY(0);
    }

    .text-reveal-on-scroll {
      color: #d8b4fe;
      transition: color 1s ease-out;
    }

    .is-visible .text-reveal-on-scroll {
      color: #1f2937;
      transition-delay: 400ms;
    }

    .timeline-item .bg-gray-200 {
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    .timeline-item .bg-gray-200::before {
      content: '';
      position: absolute;
      top: 0;
      left: -75%;
      width: 50%;
      height: 100%;
      background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.4) 50%, rgba(255, 255, 255, 0) 100%);
      transform: skewX(-25deg);
      transition: all 0.7s cubic-bezier(0.165, 0.84, 0.44, 1);
      z-index: 10;
    }

    .timeline-item .bg-gray-200:hover::before {
      left: 125%;
    }

    #slides {
      position: relative;
    }

    .slide {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      opacity: 0;
      transition: opacity 1s ease-in-out;
      z-index: 0;
    }

    .slide.active {
      opacity: 1;
      z-index: 1;
    }
  </style>

</head>

<body class="antialiased font-poppins bg-gray-50">

  <div id="page-loader"
    class="fixed inset-0 z-[9999] bg-white flex flex-col justify-center items-center transition-opacity duration-150 opacity-100">

    <div class="relative w-24 h-24">

      <svg class="animate-spin w-full h-full text-green-600" viewBox="0 0 100 100" fill="none"
        xmlns="http://www.w3.org/2000/svg">
        <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="4" class="opacity-10" />

        <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="6" stroke-linecap="round"
          stroke-dasharray="180" stroke-dashoffset="100" />
      </svg>

      <img src="assets logo/fav-icon-ptb.png" alt="Logo PTB"
        class="w-14 h-14 object-contain absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 rounded-full" />
    </div>

    <p class="text-green-600 font-semibold text-lg animate-pulse mt-4">Memuat Halaman...</p>
  </div>

  <header id="main-navbar"
    class="bg-white backdrop-blur-lg fixed top-0 left-0 right-0 w-full z-50 shadow-sm py-3 transition-transform duration-300 ease-in-out">
    <nav class="max-w-7xl mx-auto py-4 px-6 flex justify-between items-center text-gray-400">
      <a href="landingpage.php"><img src="assets logo/logo.png" class="h-12" alt="Logo PTB"></a>
      <ul class="hidden md:flex gap-8 font-semibold">
        <li><a href="landingpage.php"
            class="hover:text-purple-600 transition text-purple-600 border-b-4 pb-9 border-b-purple-600">Beranda</a>
        </li>
        <li><a href="sejarahprodi.html" class="hover:text-purple-600 transition">Profil</a></li>
        <li><a href="galeri.php" class="hover:text-purple-600 transition">Kegiatan</a></li>
        <li><a href="alur pendaftaran.html" class="hover:text-purple-600 transition">Pendaftaran</a></li>
        <li><a href="kontak.html" class="hover:text-purple-600 transition">Kontak</a></li>
      </ul>
      <button id="menu-btn" class="md:hidden text-gray-700 text-3xl z-50" aria-label="menu">
        ☰
      </button>
    </nav>
  </header>

  <div id="mobileMenu"
    class="fixed top-0 right-0 h-full w-64 bg-white text-gray-700 p-8 transform translate-x-full transition-transform duration-300 ease-in-out z-40">
    <ul class="flex flex-col gap-6 pt-20 text-lg">
      <li><a href="landingpage.php" class="hover:text-purple-600 block">Beranda</a></li>
      <li><a href="sejarahprodi.html" class="hover:text-purple-600 block">Profil</a></li>
      <li><a href="galeri.php" class="hover:text-purple-600 block">Kegiatan</a></li>
      <li><a href="alur pendaftaran.html" class="hover:text-purple-600 block">Pendaftaran</a></li>
      <li><a href="kontak.html" class="hover:text-purple-600 block">Kontak</a></li>
    </ul>
  </div>

  <div class="relative w-full h-screen overflow-hidden bg-gray-900">
    <div id="slides" class="w-full h-full mt-full">
      <img src="assets/slide1.png" class="slide active" alt="Slide 1" />
      <img src="assets/slide2.png" class="slide" alt="Slide 2" />
      <img src="assets/slide4.png" class="slide" alt="Slide 3" />
    </div>

  </div>

  <button id="nextBtn"
    class="absolute top-1/2 right-5 z-20 -translate-y-1/2 bg-white/40 text-white backdrop-blur-md p-4 rounded-full hover:bg-white/60 transition cursor-pointer hover:scale-110">
    <i class="fa-solid fa-chevron-right"></i> </button>

  <button id="prevBtn"
    class="absolute top-1/2 left-5 z-20 -translate-y-1/2 bg-white/40 text-white backdrop-blur-md p-4 rounded-full hover:bg-white/60 transition cursor-pointer hover:scale-110">
    <i class="fa-solid fa-chevron-left"></i>
  </button>

  </div>


  <div id="fitur"
    class="max-w-7xl mx-auto px-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 -mt-10 relative z-20 mb-10">
    <div
      class="bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-center">
      <div class="text-green-600 text-5xl mb-4">🧬</div>
      <h3 class="font-bold text-lg text-gray-800 mb-2">Riset Genetik</h3>
      <p class="text-gray-600 text-sm leading-relaxed">Varietas benih unggul dengan teknologi pemuliaan terkini.</p>
    </div>
    <div
      class="bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-center">
      <div class="text-green-600 text-5xl mb-4">🏭</div>
      <h3 class="font-bold text-lg text-gray-800 mb-2">Akses Industri</h3>
      <p class="text-gray-600 text-sm leading-relaxed">Kemitraan kuat dengan industri benih untuk kesiapan karier.</p>
    </div>
    <div
      class="bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-center">
      <div class="text-green-600 text-5xl mb-4">🎓</div>
      <h3 class="font-bold text-lg text-gray-800 mb-2">Lulusan Kompeten</h3>
      <p class="text-gray-600 text-sm leading-relaxed">Siap bersaing dalam industri teknologi benih modern.</p>
    </div>
    <div
      class="bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-center">
      <div class="text-green-600 text-5xl mb-4">🌱</div>
      <h3 class="font-bold text-lg text-gray-800 mb-2">Ketahanan Pangan</h3>
      <p class="text-gray-600 text-sm leading-relaxed">Berperan aktif mendukung ketahanan pangan masa depan.</p>
    </div>
  </div>


  <section id="visi" class="py-20 mb-6">
    <h2 class="text-center text-5xl font-bold mb-12 text-green-700 ">
      Visi <span class="text-green-800">Prodi</span>
    </h2>

    <div
      class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-y-8 md:gap-y-0 md:gap-x-10 lg:gap-x-16 items-center">

      <div>
        <img src="assets/gabungan_poto.png" alt="">
      </div>

      <div class="text-2xl text-gray-600 leading-loose reveal-on-scroll">
        Menjadi Program Studi Sarjana Terapan unggul yang dapat menghasilkan sumber
        daya manusia yang kompeten dan terampil, serta inovasi terapan di bidang
        pemuliaan tanaman dan teknologi benih.
      </div>

    </div>
  </section>

  <section
    class="relative flex justify-center items-center text-white bg-no-repeat bg-center min-h-[60vh] md:min-h-[80vh]"
    style="
    background-image: url('template1.png');
    background-size: cover;
    background-position: center top;
  ">
    <div class="container mx-auto px-4 pt-24 pb-16 text-center sm:pt-28 md:pt-36">

      <h2 class="text-4xl font-bold mb-10 md:text-5xl md:mb-12">Misi Prodi</h2>

      <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4 md:gap-6 justify-items-center max-w-5xl mx-auto">

        <div class="flex flex-col items-center">
          <div
            class="w-32 h-32 mb-4 flex justify-center items-center bg-contain bg-no-repeat bg-center md:w-36 md:h-36 md:mb-5"
            style="background-image: url('shape.png');">
            <i class="fa-solid fa-users text-4xl md:text-5xl"></i>
          </div>
          <p class="max-w-[200px] text-sm md:text-base">Menciptakan Sumber daya manusia yang berkualitas</p>
        </div>

        <div class="flex flex-col items-center">
          <div
            class="w-32 h-32 mb-4 flex justify-center items-center bg-contain bg-no-repeat bg-center md:w-36 md:h-36 md:mb-5"
            style="background-image: url('shape.png');">
            <i class="fa-solid fa-lightbulb text-4xl md:text-5xl"></i>
          </div>
          <p class="max-w-[200px] text-sm md:text-base">Mengembangkan teknologi terapan yang unggul</p>
        </div>

        <div class="flex flex-col items-center">
          <div
            class="w-32 h-32 mb-4 flex justify-center items-center bg-contain bg-no-repeat bg-center md:w-36 md:h-36 md:mb-5"
            style="background-image: url('shape.png');">
            <i class="fa-solid fa-seedling text-4xl md:text-5xl"></i>
          </div>
          <p class="max-w-[200px] text-sm md:text-base">Menyebarluaskan teknologi benih yang inovatif guna</p>
        </div>

        <div class="flex flex-col items-center">
          <div
            class="w-32 h-32 mb-4 flex justify-center items-center bg-contain bg-no-repeat bg-center md:w-36 md:h-36 md:mb-5"
            style="background-image: url('shape.png');">
            <i class="fa-solid fa-handshake-angle text-4xl md:text-5xl"></i>
          </div>
          <p class="max-w-[200px] text-sm md:text-base">Menjalin kerja sama Nasional dan Internasional</p>
        </div>

      </div>
    </div>
  </section>

  <section class="py-20 bg-white text-center">
    <h2 class="text-3xl md:text-4xl font-extrabold text-green-700 mb-16">
      Mitra Kerjasama
    </h2>

    <div class="max-w-6xl mx-auto px-6">
      <div id="logo-carousel" class="relative w-full overflow-hidden">
        <div id="logo-track" class="flex transition-transform duration-700 ease-in-out">

          <div class="flex-shrink-0 w-1/2 md:w-1/4 p-4 flex justify-center items-center">
            <div class="w-52 h-32 flex justify-center items-center">
              <img src="assets/corteva.png" alt="Logo Mitra Satu"
                class="max-h-full max-w-full object-contain mx-auto grayscale hover:grayscale-0 transition-all duration-300">
            </div>
          </div>

          <div class="flex-shrink-0 w-1/2 md:w-1/4 p-4 flex justify-center items-center">
            <div class="w-52 h-32 flex justify-center items-center">
              <img src="assets/sampoerna.png" alt="Logo Mitra Dua"
                class="max-h-full max-w-full object-contain mx-auto grayscale hover:grayscale-0 transition-all duration-300">
            </div>
          </div>

          <div class="flex-shrink-0 w-1/2 md:w-1/4 p-4 flex justify-center items-center">
            <div class="w-52 h-32 flex justify-center items-center">
              <img src="assets/lomgo3.png" alt="Logo Mitra Tiga"
                class="max-h-full max-w-full object-contain mx-auto grayscale hover:grayscale-0 transition-all duration-300">
            </div>
          </div>

          <div class="flex-shrink-0 w-1/2 md:w-1/4 p-4 flex justify-center items-center">
            <div class="w-52 h-32 flex justify-center items-center">
              <img src="assets/sinarmas.png" alt="Logo Mitra Empat"
                class="max-h-full max-w-full object-contain mx-auto grayscale hover:grayscale-0 transition-all duration-300">
            </div>
          </div>

          <div class="flex-shrink-0 w-1/2 md:w-1/4 p-4 flex justify-center items-center">
            <div class="w-52 h-32 flex justify-center items-center">
              <img src="assets/benihcitra.png" alt="Logo Mitra Lima"
                class="max-h-full max-w-full object-contain mx-auto grayscale hover:grayscale-0 transition-all duration-300">
            </div>
          </div>

          <div class="flex-shrink-0 w-1/2 md:w-1/4 p-4 flex justify-center items-center">
            <div class="w-52 h-32 flex justify-center items-center">
              <img src="assets/risetperkebunan.png" alt="Logo Mitra Enam"
                class="max-h-full max-w-full object-contain mx-auto grayscale hover:grayscale-0 transition-all duration-300">
            </div>
          </div>

          <div class="flex-shrink-0 w-1/2 md:w-1/4 p-4 flex justify-center items-center">
            <div class="w-52 h-32 flex justify-center items-center">
              <img src="assets/botaniseed.png" alt="Logo Mitra Tujuh"
                class="max-h-full max-w-full object-contain mx-auto grayscale hover:grayscale-0 transition-all duration-300">
            </div>
          </div>

          <div class="flex-shrink-0 w-1/2 md:w-1/4 p-4 flex justify-center items-center">
            <div class="w-52 h-32 flex justify-center items-center">
              <img src="assets/PT Bayer Indonesia 1.png" alt="Logo Mitra Delapan"
                class="max-h-full max-w-full object-contain mx-auto grayscale hover:grayscale-0 transition-all duration-300">
            </div>
          </div>

          <div class="flex-shrink-0 w-1/2 md:w-1/4 p-4 flex justify-center items-center">
            <div class="w-52 h-32 flex justify-center items-center">
              <img src="assets/cappanahmerah.png" alt="Logo Mitra Sembilan"
                class="max-h-full max-w-full object-contain mx-auto grayscale hover:grayscale-0 transition-all duration-300">
            </div>
          </div>

          <div class="flex-shrink-0 w-1/2 md:w-1/4 p-4 flex justify-center items-center">
            <div class="w-52 h-32 flex justify-center items-center">
              <img src="assets/sygenta.png" alt="Logo Mitra Sepuluh"
                class="max-h-full max-w-full object-contain mx-auto grayscale hover:grayscale-0 transition-all duration-300">
            </div>
          </div>

          <div class="flex-shrink-0 w-1/2 md:w-1/4 p-4 flex justify-center items-center">
            <div class="w-52 h-32 flex justify-center items-center">
              <img src="assets/bisi.png" alt="Logo Mitra Sebelas"
                class="max-h-full max-w-full object-contain mx-auto grayscale hover:grayscale-0 transition-all duration-300">
            </div>
          </div>

          <div class="flex-shrink-0 w-1/2 md:w-1/4 p-4 flex justify-center items-center">
            <div class="w-52 h-32 flex justify-center items-center">
              <img src="assets/biru.png" alt="Logo Mitra Dua Belas"
                class="max-h-full max-w-full object-contain mx-auto grayscale hover:grayscale-0 transition-all duration-300">
            </div>
          </div>

          <div class="flex-shrink-0 w-1/2 md:w-1/4 p-4 flex justify-center items-center">
            <div class="w-52 h-32 flex justify-center items-center">
              <img src="assets/bumitama.png" alt="Logo Mitra Tiga Belas"
                class="max-h-full max-w-full object-contain mx-auto grayscale hover:grayscale-0 transition-all duration-300">
            </div>
          </div>

          <div class="flex-shrink-0 w-1/2 md:w-1/4 p-4 flex justify-center items-center">
            <div class="w-52 h-32 flex justify-center items-center">
              <img src="assets/tomat.png" alt="Logo Mitra Empat Belas"
                class="max-h-full max-w-full object-contain mx-auto grayscale hover:grayscale-0 transition-all duration-300">
            </div>
          </div>

          <div class="flex-shrink-0 w-1/2 md:w-1/4 p-4 flex justify-center items-center">
            <div class="w-52 h-32 flex justify-center items-center">
              <img src="assets/sanghyang.png" alt="Logo Mitra Lima Belas"
                class="max-h-full max-w-full object-contain mx-auto grayscale hover:grayscale-0 transition-all duration-300">
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
  <section id="artikel" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
      <h2 class="text-3xl md:text-4xl font-extrabold text-green-700 text-center mb-14">
        Artikel Kami
      </h2>
      <div class="relative">
        <div id="artikel-viewport" class="overflow-hidden">
          <div id="artikel-track" class="flex transition-transform duration-500 ease-in-out -mx-4">

            <?php
            $counter = 1;
            // PETA GAMBAR STATIS (Jika Anda ingin thumbnail yang berbeda dari file yang diupload)
            $static_image_map = [
              1 => "mentimun.png",
              2 => "melon.png",
              3 => "padi.jpg",
              4 => "artikel 4.png"
            ];

            if ($articles_data->num_rows > 0): ?>
              <?php while ($row = $articles_data->fetch_assoc()):
                $article_id = $row['id'];

                // Perbaikan: Gunakan thumbnail_path
                $current_path = $row['thumbnail_path'];
                $is_pdf = (pathinfo($current_path, PATHINFO_EXTENSION) == 'pdf');

                // Menentukan sumber gambar yang akan ditampilkan:
                if ($is_pdf || empty($current_path)) {
                  // Gunakan gambar statis jika kontennya PDF atau path-nya kosong (gagal upload/data lama)
                  $image_source = $base_url . "assets/placeholder.png"; 
                } else {
                  // Gunakan gambar yang diupload
                  $image_source = $base_url . htmlspecialchars($current_path); 
                }

                // Tautan dinamis ke halaman detail
                $detail_link = "detail_artikel.php?id={$article_id}";
              ?>
                <div class="w-full md:w-1/2 flex-shrink-0 px-4">
                  <a href="<?php echo $detail_link; ?>" target="_blank" class="block">
                    <div class="relative group rounded-xl overflow-hidden shadow-lg cursor-pointer h-full">

                      <?php if ($is_pdf): ?>
                        <div class="w-full h-72 flex justify-center items-center bg-gray-700/90">
                          <i class="fa-solid fa-file-pdf text-6xl text-red-400"></i>
                        </div>
                        <img src="<?php echo $image_source; ?>"
                          class="w-full h-72 object-cover group-hover:scale-105 transition-transform duration-500 hidden"
                          alt="Dokumen PDF">

                      <?php else: ?>
                        <img src="<?php echo $image_source; ?>"
                          class="w-full h-72 object-cover group-hover:scale-105 transition-transform duration-500"
                          alt="Gambar Artikel <?php echo htmlspecialchars($row['title']); ?>">
                      <?php endif; ?>

                      <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                      <div class="absolute bottom-0 left-0 p-6 text-white">
                        <h3 class="text-2xl font-bold"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class="text-sm mt-1 opacity-90"><?php echo date('d M Y', strtotime($row['created_at'])); ?></p>
                      </div>
                    </div>
                  </a>
                </div>
              <?php
                $counter++; // Naikkan counter
              endwhile;
              ?>
            <?php else: ?>
              <p class="text-gray-500 text-center w-full">Belum ada artikel yang tersedia.</p>
            <?php endif; ?>

          </div>
        </div>

        <button id="prev-btn" type="button" class="absolute top-1/2 -translate-y-1/2 left-2 md:left-4 z-30
                     p-2 bg-white/90 hover:bg-white rounded-full shadow-md
                     transition disabled:opacity-30 disabled:cursor-not-allowed">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-700" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <button id="next-btn" type="button" class="absolute top-1/2 -translate-y-1/2 right-2 md:right-4 z-30
                     p-2 bg-white/90 hover:bg-white rounded-full shadow-md
                     transition disabled:opacity-30 disabled:cursor-not-allowed">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-700" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const menuBtn = document.getElementById('menu-btn');
      const mobileMenu = document.getElementById('mobileMenu');
      const mainContent = document.body;

      // Toggle mobile menu
      menuBtn.addEventListener('click', (event) => {
        event.stopPropagation();
        const isHidden = mobileMenu.classList.toggle('translate-x-full');
        menuBtn.textContent = isHidden ? '☰' : '✕';
      });

      // Close menu when clicking outside
      mainContent.addEventListener('click', () => {
        const isMenuOpen = !mobileMenu.classList.contains('translate-x-full');
        if (isMenuOpen) {
          mobileMenu.classList.add('translate-x-full');
          menuBtn.textContent = '☰';
        }
      });

      // Prevent closing when clicking inside menu
      mobileMenu.addEventListener('click', (event) => {
        event.stopPropagation();
      });
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {

      // --- Mitra Carousel Script ---
      const logoTrack = document.getElementById('logo-track');

      if (logoTrack) {
        const logos = logoTrack.children;
        const totalLogos = logos.length;
        let logosToClone = window.innerWidth >= 768 ? 4 : 2;

        // Duplicate logos for seamless looping
        for (let i = 0; i < logosToClone; i++) {
          const clone = logos[i].cloneNode(true);
          logoTrack.appendChild(clone);
        }

        let logoCurrentIndex = 0;
        const intervalTime = 3500; // 3.5s

        function nextLogoSlide() {
          logoCurrentIndex++;

          const itemWidth = logos[0].getBoundingClientRect().width;
          const moveDistance = -logoCurrentIndex * itemWidth;
          logoTrack.style.transform = `translateX(${moveDistance}px)`;

          // Reset when reaching original end
          if (logoCurrentIndex >= totalLogos) {
            setTimeout(() => {
              logoTrack.style.transition = 'none';
              logoCurrentIndex = 0;
              logoTrack.style.transform = `translateX(0)`;

              setTimeout(() => {
                logoTrack.style.transition = 'transform 0.7s ease-in-out';
              }, 50);
            }, 700);
          }
        }

        setInterval(nextLogoSlide, intervalTime);
      }

    });
  </script>
  <script>
    const track = document.getElementById("artikel-track");
    const prevBtn = document.getElementById("prev-btn");
    const nextBtn = document.getElementById("next-btn");

    let currentIndex = 0;
    let itemsPerView;
    let totalItems = track.children.length;
    let maxIndex;

    function getCardFullWidth() {
      const card = track.children[0];
      const style = window.getComputedStyle(card);
      const width = card.offsetWidth;
      const margin = parseFloat(style.marginLeft) + parseFloat(style.marginRight);
      return width + margin;
    }

    function updateConfig() {
      itemsPerView = window.innerWidth >= 768 ? 2 : 1;
      maxIndex = totalItems - itemsPerView;
      if (currentIndex > maxIndex) currentIndex = maxIndex;
    }

    function updateSlider() {
      updateConfig();

      const moveX = getCardFullWidth() * currentIndex;
      track.style.transform = `translateX(-${moveX}px)`;

      prevBtn.disabled = currentIndex <= 0;
      nextBtn.disabled = currentIndex >= maxIndex;
    }

    nextBtn.addEventListener("click", () => {
      if (currentIndex < maxIndex) {
        currentIndex++;
        updateSlider();
      }
    });

    prevBtn.addEventListener("click", () => {
      if (currentIndex > 0) {
        currentIndex--;
        updateSlider();
      }
    });

    window.addEventListener("resize", () => {
      updateSlider();
    });

    updateSlider();
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Opsi untuk Intersection Observer
      const observerOptions = {
        root: null, // 'root' null berarti relatif terhadap viewport
        rootMargin: '0px',
        threshold: 0.2 // Triger animasi saat 20% elemen terlihat
      };

      // Callback function saat elemen teramati
      const observerCallback = (entries, observer) => {
        entries.forEach(entry => {
          // Jika elemen masuk ke viewport
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible'); // Tambahkan kelas untuk memicu animasi
            observer.unobserve(entry.target); // Berhenti mengamati setelah animasi dipicu
          }
        });
      };

      // Buat observer baru
      const observer = new IntersectionObserver(observerCallback, observerOptions);

      // Ambil semua elemen yang ingin dianimasi
      const elementsToReveal = document.querySelectorAll('.reveal-on-scroll');

      // Amati setiap elemen
      elementsToReveal.forEach(el => {
        observer.observe(el);
      });
    });

    let lastScrollTop = 0;
    const navbar = document.getElementById("main-navbar");

    window.addEventListener("scroll", function() {
      // Ambil posisi scroll saat ini
      let currentScroll = window.pageYOffset || document.documentElement.scrollTop;

      if (currentScroll > lastScrollTop) {
        // Scroll ke BAWAH -> Sembunyikan Navbar (geser ke atas -100%)
        navbar.style.transform = "translateY(-100%)";
      } else {
        // Scroll ke ATAS -> Munculkan Navbar
        navbar.style.transform = "translateY(0)";
      }

      // Update posisi scroll terakhir (hindari nilai negatif untuk safari mobile)
      lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    });
  </script>

  <script>
    const slides = document.querySelectorAll('.slide');
    let index = 0;


    function showSlide(i) {
      slides.forEach((s, idx) => s.classList.toggle('active', idx === i));
    }


    function nextSlide() {
      index = (index + 1) % slides.length;
      showSlide(index);
    }


    // Auto-slide every 4 seconds
    setInterval(nextSlide, 4000);


    // Manual button
    document.getElementById('nextBtn').addEventListener('click', nextSlide);
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // --- KONFIGURASI ---
      const slides = document.querySelectorAll('.slide');
      const nextBtn = document.getElementById('nextBtn');
      const prevBtn = document.getElementById('prevBtn');
      const slideInterval = 5000; // 3 detik
      let index = 0;
      let autoSlide;

      // --- FUNGSI UTAMA ---
      function showSlide(i) {
        // 1. Reset class active pada semua slide
        slides.forEach((slide) => {
          slide.classList.remove('active');
        });

        // 2. Tambahkan class active ke slide saat ini
        if (slides[i]) {
          slides[i].classList.add('active');
        }

        // 3. Cek Visibility Tombol (Hilangkan/Munculkan)
        updateButtonVisibility(i);
      }

      function updateButtonVisibility(currentIndex) {
        // Jika tombol ada, baru kita atur class-nya
        if (prevBtn) {
          // Sembunyikan Prev jika di awal (index 0)
          if (currentIndex === 0) {
            prevBtn.classList.add('hidden');
          } else {
            prevBtn.classList.remove('hidden');
          }
        }

        if (nextBtn) {
          // Sembunyikan Next jika di akhir (index terakhir)
          if (currentIndex === slides.length - 1) {
            nextBtn.classList.add('hidden');
          } else {
            nextBtn.classList.remove('hidden');
          }
        }
      }

      // --- NAVIGASI MANUAL ---
      function nextSlide() {
        // Stop jika sudah di slide terakhir (untuk klik manual)
        if (index >= slides.length - 1) return;

        index++;
        showSlide(index);
      }

      function prevSlide() {
        // Stop jika sudah di slide pertama
        if (index <= 0) return;

        index--;
        showSlide(index);
      }

      // --- AUTO SLIDE (LOOPING) ---
      // Kita pisahkan logika auto slide agar tetap looping meskipun tombol next hilang
      function autoPlayLogic() {
        index = (index + 1) % slides.length; // Kembali ke 0 jika sudah mentok
        showSlide(index);
      }

      function startAutoSlide() {
        autoSlide = setInterval(autoPlayLogic, slideInterval);
      }

      function resetTimer() {
        clearInterval(autoSlide);
        startAutoSlide();
      }

      // --- EVENT LISTENERS ---
      if (nextBtn) {
        nextBtn.addEventListener('click', () => {
          nextSlide();
          resetTimer();
        });
      }

      if (prevBtn) {
        prevBtn.addEventListener('click', () => {
          prevSlide();
          resetTimer();
        });
      }

      // --- INISIALISASI ---
      // Jalankan sekali saat web dimuat
      showSlide(index);
      startAutoSlide();
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const loader = document.getElementById('page-loader');

      // 1. SAAT HALAMAN DIBUKA (HAMPIR INSTAN)
      // Cuma nunggu 200ms (0.2 detik) langsung hilang loadernya
      setTimeout(() => {
        if (loader) {
          loader.classList.add('opacity-0');
          loader.classList.add('pointer-events-none');
        }
      }, 200);

      // 2. SAAT PINDAH HALAMAN (NGEBUT)
      const allLinks = document.querySelectorAll('a');

      allLinks.forEach(link => {
        link.addEventListener('click', (e) => {
          const targetUrl = link.getAttribute('href');

          // Filter link yang tidak perlu loading
          if (!targetUrl || targetUrl === '#' || targetUrl.startsWith('#') || link.target === '_blank') {
            return;
          }

          e.preventDefault();

          // Munculkan loader
          if (loader) {
            loader.classList.remove('opacity-0');
            loader.classList.remove('pointer-events-none');
          }

          // Cuma nunggu 200ms (0.2 detik) langsung pindah
          // Ini cukup buat mata ngeliat flash putih sebentar
          setTimeout(() => {
            window.location.href = targetUrl;
          }, 200);
        });
      });
    });
  </script>

</body>
<footer class="bg-green-900 text-white pt-16 pb-6">

  <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-12">

    <div>
      <div class="flex items-center gap-3 mb-4">
        <img src="assets logo/logoputih.png" alt="PTB Logo" class="h-14">
      </div>

      <p class="text-sm leading-relaxed selection:bg-purple-400">
        Jl. Kumbang No.14, RT.02/RW.06, Babakan,<br>
        Kecamatan Bogor Tengah, Kota Bogor,<br>
        Jawa Barat 16128
      </p>

      <div class="flex gap-4 mt-5">
        <a href="https://www.instagram.com/ptbsvipb/" class="hover:opacity-70  transition">
          <i class="fa-brands fa-instagram text-xl"></i>
        </a>
        <a href="https://www.facebook.com/vokasiipb?locale=id_ID" class="hover:opacity-70 transition">
          <i class="fa-brands fa-facebook text-xl"></i>
        </a>
        <a href="#" class="hover:opacity-70 transition">
          <i class="fa-brands fa-youtube text-xl"></i>
        </a>
        <a href="#" class="hover:opacity-70 transition">
          <i class="fa-brands fa-tiktok text-xl"></i>
        </a>
      </div>
    </div>

    <div>
      <h4 class="text-xl font-bold mb-5">Menu</h4>
      <ul class="space-y-3 text-sm">
        <li><a href="landingpage.php" class="hover:text-purple-600">Beranda</a></li>
        <li><a href="sejarahprodi.html" class="hover:text-purple-600">Profil</a></li>
        <li><a href="galeri.php" class="hover:text-purple-600">Kegiatan</a></li>
        <li><a href="alur pendaftaran.html" class="hover:text-purple-600">Pendaftaran</a></li>
        <li><a href="kontak.html" class="hover:text-purple-600">Kontak</a></li>
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
      Copyright 2025 Pohon Toge Kelompok 4 B2
    </p>
  </div>

</footer>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</html>