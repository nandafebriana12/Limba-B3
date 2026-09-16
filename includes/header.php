<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <?php if (isset($_SESSION['user_id'])): ?>
            <aside class="sidebar">
                <div class="sidebar-header">
                    <h2>Tracking Limbah B3</h2>
                    <p>Halo, <?= htmlspecialchars($_SESSION['username']) ?> (<?= strtoupper($_SESSION['role']) ?>)</p>
                </div>
                <ul class="sidebar-menu">
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="<?= BASE_URL ?>admin/dashboard.php">Dashboard Admin</a></li>
                        <li class="menu-title">Data Master</li>
                        <li><a href="<?= BASE_URL ?>admin/upt/index.php">Data UPT</a></li>
                        <li><a href="<?= BASE_URL ?>admin/sampah/index.php">Data Sampah B3</a></li>
                        <li><a href="<?= BASE_URL ?>admin/vendors/index.php">Data Vendor</a></li>
                        <li><a href="<?= BASE_URL ?>admin/users/index.php">Data Pengguna</a></li>
                        <li class="menu-title">Transaksi & Laporan</li>
                        <li><a href="<?= BASE_URL ?>admin/transaksi/index.php">Riwayat Keluar/Masuk</a></li>
                    <?php elseif ($_SESSION['role'] === 'upt'): ?>
                        <li><a href="<?= BASE_URL ?>upt/dashboard.php">Dashboard UPT</a></li>
                        <li class="menu-title">Transaksi</li>
                        <li><a href="<?= BASE_URL ?>upt/transaksi_masuk/index.php">Limbah Masuk</a></li>
                        <li><a href="<?= BASE_URL ?>upt/transaksi_keluar/index.php">Riwayat Limbah Keluar</a></li>
                    <?php endif; ?>
                    <li class="menu-title">Akun</li>
                    <li><a href="<?= BASE_URL ?>auth/logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Logout</a></li>
                </ul>
            </aside>
        <?php endif; ?>
        
        <main class="main-content">
            <!-- Flash messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success'] ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error'] ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
