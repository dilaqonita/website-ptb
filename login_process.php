<?php
// login_process.php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';


// Cek apakah data dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // --- KOREKSI KEAMANAN UTAMA: Menggunakan Prepared Statement ---
    // Query untuk mencari user berdasarkan email
    $sql = "SELECT id, email, password_hash, nama_lengkap FROM users WHERE email = ?";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        $_SESSION['login_error'] = "Terjadi kesalahan internal: " . $conn->error;
        header("Location: login.php");
        exit;
    }

    // Bind parameter (s = string)
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password_hash'];

        // Verifikasi password menggunakan fungsi bawaan PHP
        if (password_verify($password, $hashed_password)) {
            // Login Berhasil!
            
            // Atur variabel session
            $_SESSION['loggedin'] = true;
            // id admin yang akan digunakan untuk mencatat penulis
            $_SESSION['user_id'] = $row['id']; 
            $_SESSION['email'] = $row['email'];
            $_SESSION['nama_lengkap'] = $row['nama_lengkap'];

            // Tutup statement dan koneksi sebelum redirect
            $stmt->close();
            $conn->close(); 

            // Arahkan ke halaman dashboard
            header("Location: admin.php"); 
            exit;
        } else {
            // Password salah
            $_SESSION['login_error'] = "Email atau Kata Sandi salah.";
        }
    } else {
        // Email tidak ditemukan
        $_SESSION['login_error'] = "Email atau Kata Sandi salah.";
    }

    // Tutup statement jika ada dan koneksi
    if (isset($stmt)) { $stmt->close(); }
    $conn->close();
    header("Location: login.php"); 
    exit;

} else {
    // Jika diakses tanpa POST request, arahkan kembali ke login
    header("Location: login.php");
    exit;
}
?>