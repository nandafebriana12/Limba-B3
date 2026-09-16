<?php
// admin/dashboard.php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';


// Get statistics
$stats = [
    'upt' => 0,
    'sampah' => 0,
    'vendor' => 0,
    'users' => 0
];


try {
    $stmt = $conn->query("SELECT COUNT(*) as cnt FROM upts");
    $stats['upt'] = $stmt->fetch()['cnt'];
    
    $stmt = $conn->query("SELECT COUNT(*) as cnt FROM sampah_b3");
    $stats['sampah'] = $stmt->fetch()['cnt'];
    
    $stmt = $conn->query("SELECT COUNT(*) as cnt FROM vendors");
    $stats['vendor'] = $stmt->fetch()['cnt'];
    
    $stmt = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE role = 'upt'");
    $stats['users'] = $stmt->fetch()['cnt'];
} catch(PDOException $e) {
    // Ignore if tables not yet created
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Selamat Datang</h2>
    </div>
    <p>Selamat datang di Dashboard Administrator TPS Limbah B3. Pada halaman ini, Anda dapat mengelola data master terkait pengelolaan limbah, mencakup UPT, Limbah B3, Vendor, dan pengguna sistem. Pantau proses pengelolaan serta riwayat transaksi limbah secara efektif untuk mendukung pengendalian dan pelaporan lingkungan yang lebih baik..</p>
</div>

<div class="page-header">
    <h1 class="page-title">Dashboard Admin</h1>
</div>

<div class="card" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
    <div style="background: rgba(79, 70, 229, 0.1); padding: 20px; border-radius: 8px; text-align: center;">
        <h3 style="color: var(--primary); margin-bottom: 10px;">Total UPT</h3>
        <p style="font-size: 2rem; font-weight: bold;"><?= $stats['upt'] ?></p>
    </div>
    
    <div style="background: rgba(16, 185, 129, 0.1); padding: 20px; border-radius: 8px; text-align: center;">
        <h3 style="color: var(--secondary); margin-bottom: 10px;">Jenis Limbah</h3>
        <p style="font-size: 2rem; font-weight: bold;"><?= $stats['sampah'] ?></p>
    </div>
    
    <div style="background: rgba(245, 158, 11, 0.1); padding: 20px; border-radius: 8px; text-align: center;">
        <h3 style="color: var(--warning); margin-bottom: 10px;">Total Vendor</h3>
        <p style="font-size: 2rem; font-weight: bold;"><?= $stats['vendor'] ?></p>
    </div>
    
    <div style="background: rgba(239, 68, 68, 0.1); padding: 20px; border-radius: 8px; text-align: center;">
        <h3 style="color: var(--danger); margin-bottom: 10px;">Akun UPT</h3>
        <p style="font-size: 2rem; font-weight: bold;"><?= $stats['users'] ?></p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
