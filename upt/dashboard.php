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



<?php
// Mengambil data untuk grafik (Jumlah transaksi masuk untuk UPT ini & keluar global per bulan)
$monthly_data = [];

try {
    // Data Masuk (khusus user ini)
    $stmtChart = $conn->prepare("
        SELECT 
            CONVERT(varchar(7), tanggal_masuk, 126) as bulan, 
            COUNT(id) as total
        FROM transaksi_masuk
        WHERE user_id = ?
        GROUP BY CONVERT(varchar(7), tanggal_masuk, 126)
    ");
    $stmtChart->execute([$user_id]);
    while ($row = $stmtChart->fetch()) {
        $bulan = $row['bulan'];
        if (!isset($monthly_data[$bulan])) {
            $monthly_data[$bulan] = ['masuk' => 0, 'keluar' => 0];
        }
        $monthly_data[$bulan]['masuk'] = $row['total'];
    }

    // Data Keluar (global ke vendor)
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
    // Ignore error
}

ksort($monthly_data);

$chart_labels = array_keys($monthly_data);
$data_masuk = [];
$data_keluar = [];

foreach($monthly_data as $data) {
    $data_masuk[] = $data['masuk'];
    $data_keluar[] = $data['keluar'];
}
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-top: 20px;">
    <!-- Grafik Masuk (UPT Saya) -->
    <div class="card" style="margin-top: 0;">
        <div class="card-header">
            <h3 class="card-title">Trend Limbah Masuk (UPT Saya)</h3>
        </div>
        <div style="width: 100%; height: 300px;">
            <canvas id="chartMasuk"></canvas>
        </div>
    </div>

    <!-- Grafik Keluar (Global Vendor) -->
    <div class="card" style="margin-top: 0;">
        <div class="card-header">
            <h3 class="card-title">Trend Limbah Keluar (Total ke Vendor)</h3>
        </div>
        <div style="width: 100%; height: 300px;">
            <canvas id="chartKeluar"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const stockOptions = {
        responsive: true,
        maintainAspectRatio: false,
        elements: {
            line: { tension: 0 } // Garis lurus ala saham
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        },
        plugins: {
            legend: { display: false }
        }
    };

    // Chart Masuk
    new Chart(document.getElementById('chartMasuk').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                label: 'Limbah Masuk',
                data: <?= json_encode($data_masuk) ?>,
                borderColor: 'rgba(16, 185, 129, 1)', // Hijau
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(16, 185, 129, 1)'
            }]
        },
        options: stockOptions
    });

    // Chart Keluar
    new Chart(document.getElementById('chartKeluar').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                label: 'Limbah Keluar',
                data: <?= json_encode($data_keluar) ?>,
                borderColor: 'rgba(239, 68, 68, 1)', // Merah
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 2,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(239, 68, 68, 1)'
            }]
        },
        options: stockOptions
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
