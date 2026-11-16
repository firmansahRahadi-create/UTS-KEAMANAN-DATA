<?php
// admin.php
require_once 'koneksi.php';
require_once 'crypto_helper.php';

// Proteksi Halaman Admin: Wajib Login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Handling Form Submit (Enkripsi Semua Konten & Simpan ke DB)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $title   = $_POST['title'];
    $content = $_POST['content'];

    // Lakukan Enkripsi Hybrid untuk Judul dan Isi Konten
    $cryptoResult = encryptContent($title, $content);

    $encTitle   = $cryptoResult['encrypted_title'];
    $encPayload = $cryptoResult['encrypted_payload'];
    $iv         = $cryptoResult['iv'];
    $encKey     = $cryptoResult['encrypted_key'];
    $privKey    = $cryptoResult['private_key'];

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO admin_contents (encrypted_title, encrypted_payload, iv, encrypted_key, private_key) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $encTitle, $encPayload, $iv, $encKey, $privKey);
    $stmt->execute();

    // Simpan ID data yang baru ditambahkan untuk ditampilkan langsung proses Enkripsi & Dekripsinya
    $_SESSION['last_added_id'] = $stmt->insert_id;
    $stmt->close();

    header("Location: admin.php");
    exit;
}

// Handling Hapus Data
if (isset($_GET['hapus'])) {
    $idHapus = (int) $_GET['hapus'];
    $stmtDel = $conn->prepare("DELETE FROM admin_contents WHERE id = ?");
    $stmtDel->bind_param("i", $idHapus);
    $stmtDel->execute();
    $stmtDel->close();

    header("Location: admin.php");
    exit;
}

// Cek apakah ada data yang baru saja ditambahkan untuk ditampilkan detail Enkripsi & Dekripsinya
$justAddedData = null;
if (isset($_SESSION['last_added_id'])) {
    $lastId = (int) $_SESSION['last_added_id'];
    unset($_SESSION['last_added_id']);

    $stmtLast = $conn->prepare("SELECT * FROM admin_contents WHERE id = ? LIMIT 1");
    $stmtLast->bind_param("i", $lastId);
    $stmtLast->execute();
    $resLast = $stmtLast->get_result();
    if ($rowLast = $resLast->fetch_assoc()) {
        $decLast = decryptContent(
            $rowLast['encrypted_title'],
            $rowLast['encrypted_payload'],
            $rowLast['iv'],
            $rowLast['encrypted_key'],
            $rowLast['private_key']
        );
        $justAddedData = [
            'raw'       => $rowLast,
            'decrypted' => $decLast
        ];
    }
    $stmtLast->close();
}

// Fetch Semua Data Konten
$result = $conn->query("SELECT * FROM admin_contents ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Data Terenkripsi Hybrid</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f4f6f9;
            color: #212529;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #343a40;
            color: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .topbar h2 {
            margin: 0;
            font-size: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 14px;
        }

        .btn-logout {
            background: #dc3545;
            color: white;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 13px;
        }

        .btn-logout:hover {
            background: #bb2d3b;
        }

        .card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-highlight {
            background: #f8fff9;
            border: 2px solid #28a745;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .box-crypto {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #343a40;
            color: #fff;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 6px 0 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #218838;
        }

        .btn-delete {
            background: #dc3545;
            color: #fff;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            display: inline-block;
        }

        .code-block {
            font-family: monospace;
            font-size: 11px;
            word-break: break-all;
            color: #d63384;
            background: #f8f9fa;
            padding: 4px 6px;
            border-radius: 4px;
            margin: 4px 0 8px;
        }

        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 4px;
            margin-bottom: 4px;
        }

        .badge-aes {
            background: #fff3cd;
            color: #664d03;
        }

        .badge-rsa {
            background: #cff4fc;
            color: #055160;
        }

        .badge-dec {
            background: #d1e7dd;
            color: #0f5132;
        }
    </style>
</head>

<body>

    <div class="topbar">
        <h2>Dashboard Admin (Sistem Kriptografi Hybrid)</h2>
        <div class="user-info">
            <span>Halo, <strong><?= htmlspecialchars($_SESSION['admin_nama']) ?></strong></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <?php if ($justAddedData): ?>
        <div class="card card-highlight">
            <h3 style="margin-top:0; color:#198754;">&#10004; Data Berhasil Ditambahkan! Berikut Proses Enkripsi & Dekripsinya:</h3>
            <div class="grid-2">
                <div class="box-crypto">
                    <span class="badge badge-aes">1. Hasil Enkripsi Simetris (AES-256-CBC) & Asimetris (RSA-2048)</span>
                    <p style="font-size:12px; margin:6px 0 2px;"><strong>Ciphertext Judul (AES):</strong></p>
                    <div class="code-block"><?= htmlspecialchars($justAddedData['raw']['encrypted_title']) ?></div>

                    <p style="font-size:12px; margin:6px 0 2px;"><strong>Ciphertext Konten (AES):</strong></p>
                    <div class="code-block"><?= htmlspecialchars($justAddedData['raw']['encrypted_payload']) ?></div>

                    <p style="font-size:12px; margin:6px 0 2px;"><strong>Initialization Vector (IV):</strong></p>
                    <div class="code-block"><?= htmlspecialchars($justAddedData['raw']['iv']) ?></div>

                    <p style="font-size:12px; margin:6px 0 2px;"><strong>Encrypted AES Key (RSA-2048 Public Key):</strong></p>
                    <div class="code-block"><?= htmlspecialchars(substr($justAddedData['raw']['encrypted_key'], 0, 90)) ?>...</div>
                </div>

                <div class="box-crypto">
                    <span class="badge badge-dec">2. Hasil Dekripsi Otomatis (RSA Private Key &rarr; AES Key &rarr; Plaintext)</span>
                    <p style="font-size:13px; margin:10px 0 4px; color:#555;"><strong>Judul Terdekripsi:</strong></p>
                    <div style="font-size:16px; font-weight:bold; color:#0d6efd; margin-bottom:12px;">
                        <?= htmlspecialchars($justAddedData['decrypted']['title']) ?>
                    </div>

                    <p style="font-size:13px; margin:10px 0 4px; color:#555;"><strong>Isi Konten Terdekripsi:</strong></p>
                    <div style="font-size:15px; color:#212529; background:#f8f9fa; padding:10px; border-radius:4px; white-space:pre-line;">
                        <?= htmlspecialchars($justAddedData['decrypted']['content']) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <h3>Tambah Konten Rahasia Admin</h3>
        <form action="" method="POST">
            <label>Judul Dokumen (Akan dienkripsi AES + RSA):</label>
            <input type="text" name="title" placeholder="Contoh: Laporan Keuangan Q4" required>

            <label>Isi Konten (Plaintext - Akan dienkripsi AES + RSA):</label>
            <textarea name="content" rows="4" placeholder="Masukkan isi konten rahasia halaman admin..." required></textarea>

            <button type="submit" name="submit">Enkripsi & Simpan Data</button>
        </form>
    </div>

    <div class="card">
        <h3>Daftar Semua Konten Halaman Admin (Terenkripsi di DB & Terdekripsi di Halaman)</h3>
        <table>
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="45%">Data Terenkripsi di Database (Simetris AES-256 + Asimetris RSA-2048)</th>
                    <th width="42%">Hasil Dekripsi Konten (Plaintext)</th>
                    <th width="8%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        $decrypted = decryptContent(
                            $row['encrypted_title'],
                            $row['encrypted_payload'],
                            $row['iv'],
                            $row['encrypted_key'],
                            $row['private_key']
                        );
                        ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td>
                                <span class="badge badge-aes">Simetris AES-256-CBC</span><br>
                                <strong>Encrypted Title:</strong>
                                <div class="code-block"><?= htmlspecialchars(substr($row['encrypted_title'], 0, 45)) ?>...</div>
                                <strong>Encrypted Payload:</strong>
                                <div class="code-block"><?= htmlspecialchars(substr($row['encrypted_payload'], 0, 45)) ?>...</div>

                                <span class="badge badge-rsa">Asimetris RSA-2048</span><br>
                                <strong>Encrypted AES Key:</strong>
                                <div class="code-block"><?= htmlspecialchars(substr($row['encrypted_key'], 0, 45)) ?>...</div>
                            </td>
                            <td>
                                <span class="badge badge-dec">Hasil Dekripsi (RSA &rarr; AES)</span>
                                <div style="color: #0d6efd; font-weight: bold; font-size: 16px; margin: 6px 0;">
                                    <?= htmlspecialchars($decrypted['title']) ?>
                                </div>
                                <div style="color: #212529; font-size: 14px; white-space: pre-line;">
                                    <?= htmlspecialchars($decrypted['content']) ?>
                                </div>
                                <small style="color:#6c757d; display:block; margin-top:8px;">
                                    Disimpan: <?= $row['created_at'] ?>
                                </small>
                            </td>
                            <td>
                                <a href="admin.php?hapus=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus data terenkripsi ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center; color:#6c757d;">Belum ada data konten rahasia. Silakan tambahkan data melalui form di atas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>