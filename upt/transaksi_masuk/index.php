<?php
// upt/transaksi_masuk/index.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('upt');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/header.php';

$user_id = $_SESSION['user_id'];

// Fetch Transaksi Masuk
$sql = "SELECT tm.*, (SELECT SUM(quantity) FROM detail_masuk WHERE transaksi_masuk_id = tm.id) as total_items 
        FROM transaksi_masuk tm 
        WHERE tm.user_id = ? 
        ORDER BY tm.id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$transaksi = $stmt->fetchAll();
?>

<div class="page-header">
    <h1 class="page-title">Riwayat Limbah Masuk</h1>
    <a href="create.php" class="btn btn-primary" style="width: auto;">+ Input Limbah Masuk</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tanggal Masuk</th>
                    <th>Total Item (Qty)</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($transaksi) > 0): ?>
                    <?php foreach($transaksi as $row): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['no_transaksi']) ?></strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['tanggal_masuk'])) ?></td>
                        <td><?= (float)$row['total_items'] ?></td>
                        <td><?= htmlspecialchars($row['keterangan']) ?></td>
                        <td>
                            <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem;">Lihat Detail</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Belum ada transaksi masuk.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
