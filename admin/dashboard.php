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

<?php
// Mengambil data untuk grafik (Jumlah transaksi masuk & keluar per bulan)
$monthly_data = [];

try {
    // Data Masuk
    $stmtMasuk = $conn->query("
        SELECT 
            CONVERT(varchar(7), tanggal_masuk, 126) as bulan, 
            COUNT(id) as total
        FROM transaksi_masuk
        GROUP BY CONVERT(varchar(7), tanggal_masuk, 126)
    ");
    while ($row = $stmtMasuk->fetch()) {
        $bulan = $row['bulan'];
        if (!isset($monthly_data[$bulan])) {
            $monthly_data[$bulan] = ['masuk' => 0, 'keluar' => 0];
        }
        $monthly_data[$bulan]['masuk'] = $row['total'];
    }

    // Data Keluar
    $stmtKeluar = $conn->query("
        SELECT 
            CONVERT(varchar(7), tanggal_keluar, 126) as bulan, 
            COUNT(id) as total
        FROM transaksi_keluar
        GROUP BY CONVERT(varchar(7), tanggal_keluar, 126)
    ");
    while ($row = $stmtKeluar->fetch()) {
        $bulan = $row['bulan'];
        if (!isset($monthly_data[$bulan])) {
            $monthly_data[$bulan] = ['masuk' => 0, 'keluar' => 0];
        }
        $monthly_data[$bulan]['keluar'] = $row['total'];
    }
} catch(PDOException $e) {
    // Ignore error jika tabel kosong
}

// Urutkan berdasarkan bulan (kunci array)
ksort($monthly_data);

$chart_labels = array_keys($monthly_data);
$data_masuk = [];
$data_keluar = [];

foreach($monthly_data as $data) {
    $data_masuk[] = $data['masuk'];
    $data_keluar[] = $data['keluar'];
}
?>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3 class="card-title">Grafik Transaksi Masuk & Keluar per Bulan</h3>
    </div>
    <div style="width: 100%; max-height: 400px; display: flex; justify-content: center;">
        <canvas id="transaksiChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('transaksiChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [
                {
                    label: 'Limbah Masuk (Dari UPT)',
                    data: <?= json_encode($data_masuk) ?>,
                    backgroundColor: 'rgba(79, 70, 229, 0.7)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Limbah Keluar (Ke Vendor)',
                    data: <?= json_encode($data_keluar) ?>,
                    backgroundColor: 'rgba(239, 68, 68, 0.7)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
