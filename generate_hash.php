<?php

// generate_hash.php (Hapus setelah dipakai)
$password_asli = "UndangPass"; // Ganti dengan password yang diinginkan
$hashed_password = password_hash($password_asli, PASSWORD_DEFAULT);

echo "PASSWORD TEKS BIASA YANG HARUS ANDA KETIK SAAT LOGIN: " . $password_asli . "<br>";
echo "HASH BARU YANG HARUS DIMASUKKAN KE DATABASE (SALIN SELURUHNYA): " . $hashed_password;


// generate_hash.php (HAPUS SETELAH DIGUNAKAN)
$password = 'henny123'; // <-- GANTI PASSWORD INI
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password yang diinput: " . $password . "<br>";
echo "Hash BCRYPT yang dihasilkan: " . $hash . "<br>";

// Verifikasi (opsional)
if (password_verify($password, $hash)) {
    echo "Verifikasi berhasil! Hash ini valid.";
} else {
    echo "Verifikasi gagal.";
}
?>