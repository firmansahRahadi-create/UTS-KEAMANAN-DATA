<?php
// login.php
require_once 'koneksi.php';

// Jika sudah login, langsung arahkan ke admin.php
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, nama_lengkap FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $row['id'];
            $_SESSION['admin_username']  = $row['username'];
            $_SESSION['admin_nama']      = $row['nama_lengkap'];

            header("Location: admin.php");
            exit;
        } else {
            $error = "Password yang Anda masukkan salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sistem Kriptografi Hybrid</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-card {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-top: 0;
            color: #343a40;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            color: #6c757d;
            font-size: 13px;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 6px 0 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            background: #0d6efd;
            color: white;
            border: none;
            padding: 11px;
            border-radius: 4px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #0b5ed7;
        }

        .alert-error {
            background: #f8d7da;
            color: #842029;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 13px;
            border: 1px solid #f5c2c7;
        }

    </style>
</head>

<body>

    <div class="login-card">
        <h2>Login Admin</h2>
        <p class="subtitle">Sistem Informasi Kriptografi Hybrid (AES-256 & RSA-2048)</p>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Masukkan username" required autofocus>

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit" name="login">Masuk ke Dashboard</button>
        </form>

    </div>

</body>

</html>