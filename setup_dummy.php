<?php
// setup_dummy.php
require_once __DIR__ . '/config/database.php';

try {
    $conn->beginTransaction();

    // 1. Buat UPT Dummy
    // Cek apakah UPT sudah ada
    $stmt = $conn->prepare("SELECT id FROM upts WHERE kode_upt = 'U001'");
    $stmt->execute();
    $upt = $stmt->fetch();
    
    if (!$upt) {
        $stmt = $conn->prepare("INSERT INTO upts (kode_upt, nama_upt) VALUES ('U001', 'Fakultas Teknik Informatika')");
        $stmt->execute();
        $upt_id = $conn->lastInsertId();
    } else {
        $upt_id = $upt['id'];
    }

    // 2. Buat Akun Admin
    $admin_user = 'admin';
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$admin_user]);
    if (!$stmt->fetch()) {
        $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'admin')");
        $stmt->execute([$admin_user, $admin_pass]);
        echo "✅ Akun Admin berhasil dibuat (Username: <b>admin</b>, Password: <b>admin123</b>)<br>";
    } else {
        echo "ℹ️ Akun Admin sudah ada.<br>";
    }

    // 3. Buat Akun UPT
    $upt_user = 'upt_ti';
    $upt_pass = password_hash('upt123', PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$upt_user]);
    if (!$stmt->fetch()) {
        $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role, upt_id) VALUES (?, ?, 'upt', ?)");
        $stmt->execute([$upt_user, $upt_pass, $upt_id]);
        echo "✅ Akun UPT berhasil dibuat (Username: <b>upt_ti</b>, Password: <b>upt123</b>)<br>";
    } else {
        echo "ℹ️ Akun UPT sudah ada.<br>";
    }

    $conn->commit();
    echo "<br>🎉 Setup selesai! Silakan kembali ke <a href='auth/login.php'>Halaman Login</a>.";

} catch (Exception $e) {
    $conn->rollBack();
    echo "❌ Terjadi kesalahan: " . $e->getMessage();
}
