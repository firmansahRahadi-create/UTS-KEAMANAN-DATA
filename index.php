<?php
require_once 'koneksi.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
} else {
    header("Location: login.php");
}
exit;
?>