<?php
// includes/auth.php
require_once __DIR__ . '/../config/config.php';

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "auth/login.php");
        exit;
    }
}

function requireRole($role) {
    checkLogin();
    if ($_SESSION['role'] !== $role) {
        die("Akses ditolak. Anda tidak memiliki izin untuk melihat halaman ini.");
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
