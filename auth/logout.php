<?php
// auth/logout.php
require_once __DIR__ . '/../config/config.php';

session_unset();
session_destroy();
session_start();

$_SESSION['success'] = "Anda berhasil keluar dari sistem.";
header("Location: " . BASE_URL . "auth/login.php");
exit;
