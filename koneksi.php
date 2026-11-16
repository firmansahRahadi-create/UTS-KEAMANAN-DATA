<?php
// koneksi.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_keamanan_data";

// Koneksi ke server MySQL Laragon
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// 1. Otomatis buat database jika belum ada, lalu pilih database
$conn->query("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
$conn->select_db($db);

// 2. Otomatis buat tabel users untuk Login Admin
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 3. Otomatis buat tabel admin_contents untuk seluruh konten terenkripsi
$conn->query("CREATE TABLE IF NOT EXISTS admin_contents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    encrypted_title TEXT NOT NULL,
    encrypted_payload LONGTEXT NOT NULL,
    iv VARCHAR(255) NOT NULL,
    encrypted_key TEXT NOT NULL,
    private_key TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 4. Penyesuaian otomatis jika tabel admin_contents lama masih memakai kolom 'title'
$checkCol = $conn->query("SHOW COLUMNS FROM admin_contents LIKE 'encrypted_title'");
if ($checkCol && $checkCol->num_rows === 0) {
    $conn->query("ALTER TABLE admin_contents CHANGE title encrypted_title TEXT NOT NULL");
}

// 5. Buat akun admin hanya jika kredensial dikonfigurasi di environment.
$adminUsername = getenv('APP_ADMIN_USERNAME');
$adminPassword = getenv('APP_ADMIN_PASSWORD');

if (($adminUsername !== false && $adminUsername !== '') || ($adminPassword !== false && $adminPassword !== '')) {
    if ($adminUsername === false || $adminUsername === '' || $adminPassword === false || $adminPassword === '') {
        die("Konfigurasi admin tidak lengkap: atur APP_ADMIN_USERNAME dan APP_ADMIN_PASSWORD.");
    }

    $stmtAdminCheck = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    $stmtAdminCheck->bind_param("s", $adminUsername);
    $stmtAdminCheck->execute();
    $checkAdmin = $stmtAdminCheck->get_result();

    if ($checkAdmin->num_rows === 0) {
        $adminName = getenv('APP_ADMIN_NAME');
        if ($adminName === false || $adminName === '') {
            $adminName = 'Administrator Sistem';
        }

        $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
        $stmtAdmin = $conn->prepare("INSERT INTO users (username, password, nama_lengkap) VALUES (?, ?, ?)");
        $stmtAdmin->bind_param("sss", $adminUsername, $hashedPassword, $adminName);
        $stmtAdmin->execute();
        $stmtAdmin->close();
    }
    $stmtAdminCheck->close();
}
?>