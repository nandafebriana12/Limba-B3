<?php
// upt/dashboard.php
require_once __DIR__ . '/../includes/auth.php';
requireRole('upt');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

// Get UPT ID for current user
$user_id = $_SESSION['user_id'];

// Get statistics
$stats = [
    'transaksi_masuk' => 0,
    'total_kg_masuk' => 0
];

try {
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM transaksi_masuk WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $stats['transaksi_masuk'] = $stmt->fetch()['cnt'];
    
    // Asumsikan semua total dalam bentuk general aja untuk dashboard (kg, liter dll digabung jd angka aja)
    // Atau hanya tampilkan total transaksi.
} catch(PDOException $e) {
    // Ignore
}
?>

<div class="page-header">
    <h1 class="page-title">Dashboard UPT</h1>
</div>

<div class="card" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
    <div style="background: rgba(79, 70, 229, 0.1); padding: 20px; border-radius: 8px; text-align: center;">
        <h3 style="color: var(--primary); margin-bottom: 10px;">Total Transaksi Masuk</h3>
        <p style="font-size: 2rem; font-weight: bold;"><?= $stats['transaksi_masuk'] ?></p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Selamat Datang di Portal UPT</h2>
    </div>
    <p>Di sini Anda dapat menginput data limbah B3 yang akan disalurkan, serta melihat riwayat data masuk dan keluar yang terkait dengan UPT Anda.</p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
