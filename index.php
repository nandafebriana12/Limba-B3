<?php
// index.php
require_once 'config/config.php';
header("Location: auth/login.php");

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: " . BASE_URL . "admin/dashboard.php");
    } else {
        header("Location: " . BASE_URL . "upt/dashboard.php");
    }
} else {
    header("Location: " . BASE_URL . "auth/login.php");
}
exit;
