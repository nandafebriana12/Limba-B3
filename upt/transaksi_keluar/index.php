<?php
// upt/transaksi_keluar/index.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('upt');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Transaksi Keluar
$sql = "SELECT tk.*, v.nama_vendor, 
        (SELECT SUM(quantity) FROM detail_keluar WHERE transaksi_keluar_id = tk.id) as total_items 
        FROM transaksi_keluar tk 
        JOIN vendors v ON tk.vendor_id = v.id 
        ORDER BY tk.id DESC";
$stmt = $conn->query($sql);
$transaksi = $stmt->fetchAll();
?>

<div class="page-header">
    <h1 class="page-title">Riwayat Limbah Keluar (Ke Vendor)</h1>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Vendor Tujuan</th>
                    <th>Tanggal Keluar</th>
                    <th>Total Item (Qty)</th>
                    <th>Total Biaya</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($transaksi) > 0): ?>
                    <?php foreach($transaksi as $row): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['no_transaksi']) ?></strong></td>
                        <td><?= htmlspecialchars($row['nama_vendor']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['tanggal_keluar'])) ?></td>
                        <td><?= (float)$row['total_items'] ?></td>
                        <td><?= formatRupiah($row['total_biaya']) ?></td>
                        <td>
                            <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem;">Lihat Detail</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Belum ada transaksi keluar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
