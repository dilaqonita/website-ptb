<?php
// login.php
session_start();

// Inisialisasi variabel error
$login_error = $_SESSION['login_error'] ?? null;
if (isset($_SESSION['login_error'])) {
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="asse logo/fav-icon-ptb.png" type="image/png" />
    <title>Login Admin PTB</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="min-h-screen flex">
    <div 
        class="hidden lg:flex lg:w-1/2 relative overflow-hidden" 
        style="border-top-right-radius: 30px; border-bottom-right-radius: 30px;"
    >
      <img
        src="https://images.unsplash.com/photo-1625246333195-78d9c38ad449?auto-format&fit=crop&w=1400&q=80" alt="Tanaman"
        class="absolute inset-0 w-full h-full object-cover"
      />
      <div 
        class="absolute inset-0" 
        style="background-color: rgba(0, 0, 0, 0.4);"
      ></div>
      <div class="relative z-10 flex flex-col justify-center px-16 text-white">
        <h1 class="text-5xl font-bold leading-tight mb-6">
          Menumbuhkan Inovasi<br />Benih Unggul
        </h1>
        <p class="text-xl font-light">
          Program Studi Pemuliaan Tanaman dan Teknologi Benih
        </p>
      </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center bg-white p-8">
      <div class="w-full max-w-md">
        <div class="flex justify-center mb-12">
          <div class="relative">
            <img
              src="asse logo/logo.png"
              alt="PTB Logo"
              class=" w-40 object-contain"
              onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
            />
            <svg
              class="w-28 h-28 hidden"
              viewBox="0 0 120 120"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <circle cx="60" cy="60" r="58" fill="#f0fdf4" stroke="#15803d" stroke-width="2"/>
              <ellipse cx="60" cy="65" rx="12" ry="18" fill="#15803d" transform="rotate(-25 60 65)"/>
              <ellipse cx="60" cy="65" rx="12" ry="18" fill="#16a34a" transform="rotate(25 60 65)"/>
              <ellipse cx="60" cy="60" rx="10" ry="16" fill="#22c55e"/>
              <rect x="58" y="76" width="4" height="12" rx="2" fill="#15803d"/>
              <text x="60" y="102" font-family="Arial, sans-serif" font-size="14" font-weight="bold" fill="#15803d" text-anchor="middle">PTB</text>
            </svg>
          </div>
        </div>

        <div class="text-center mb-5">
          <h2 class="text-3xl font-bold text-gray-900 mb-2">Login Admin</h2>
          <p class="text-gray-600">Masuk ke dashboard admin PTB</p>
        </div>
        
        <?php if ($login_error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm" role="alert">
                <strong class="font-bold">Gagal!</strong>
                <span class="block sm:inline"><?php echo $login_error; ?></span>
            </div>
        <?php endif; ?>

        <form class="space-y-6" action="login_process.php" method="POST">
          <div>
            <label
              for="email"
              class="block text-sm font-medium text-gray-700 mb-2"
            >
              Email Admin
            </label>
            <input
              id="email"
              type="email"
              name="email"
              class="w-full px-4 py-3.5 rounded-lg bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent focus:bg-white transition-all"
              placeholder="admin@ptb.ac.id"
              required
            />
          </div>

          <div>
            <label
              for="password"
              class="block text-sm font-medium text-gray-700 mb-2"
            >
              Kata Sandi
            </label>
            <input
              id="password"
              type="password"
              name="password"
              class="w-full px-4 py-3.5 rounded-lg bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent focus:bg-white transition-all"
              placeholder="••••••••"
              required
            />
          </div>

          <div class="flex items-center justify-between">
            <label
              class="flex items-center space-x-2 text-gray-700 cursor-pointer"
            >
              <input
                type="checkbox"
                class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer"
              />
              <span class="text-sm font-medium">Ingat saya</span>
            </label>
            <a
              href="verifemail.html"
              class="text-sm text-green-700 hover:text-green-800 hover:underline font-medium transition-colors"
            >
              Lupa Kata Sandi?
            </a>
          </div>

          <button
            type="submit"
            class="w-full bg-green-700 hover:bg-green-800 active:bg-green-900 text-white py-3.5 rounded-lg font-medium transition-all shadow-sm hover:shadow-md"
          >
            Login
          </button>
        </form>

        <div class="mt-8 text-center">
          <p class="text-sm text-gray-500">© 2024 Program Studi PTB</p>
        </div>
      </div>
    </div>
  </body>
</html>