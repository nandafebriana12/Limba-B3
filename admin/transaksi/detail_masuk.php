<?php
// admin/transaksi/detail_masuk.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$id = $_GET['id'] ?? 0;

// Fetch Transaksi Masuk
$sql = "SELECT tm.*, u.username, p.kode_upt, p.nama_upt 
        FROM transaksi_masuk tm 
        JOIN users u ON tm.user_id = u.id 
        LEFT JOIN upts p ON u.upt_id = p.id
        WHERE tm.id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$transaksi = $stmt->fetch();

if (!$transaksi) {
    $_SESSION['error'] = "Data transaksi tidak ditemukan.";
    header("Location: index.php");
    exit;
}

// Fetch Details
$sql = "SELECT d.*, s.kode_sampah, s.nama_sampah, s.satuan 
        FROM detail_masuk d 
        JOIN sampah_b3 s ON d.sampah_b3_id = s.id 
        WHERE d.transaksi_masuk_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$details = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Detail Transaksi Masuk: <?= htmlspecialchars($transaksi['no_transaksi']) ?></h1>
    <a href="index.php" class="btn btn-secondary" style="width: auto;">&larr; Kembali</a>
</div>

<div class="card" style="margin-bottom: 20px;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <p style="margin-bottom: 10px;"><strong style="display: inline-block; width: 120px;">No Transaksi</strong> : <?= htmlspecialchars($transaksi['no_transaksi']) ?></p>
            <p style="margin-bottom: 10px;"><strong style="display: inline-block; width: 120px;">Asal UPT</strong> : <?= htmlspecialchars($transaksi['nama_upt'] ?? 'Admin / Unknown') ?> (<?= htmlspecialchars($transaksi['kode_upt'] ?? '-') ?>)</p>
            <p style="margin-bottom: 10px;"><strong style="display: inline-block; width: 120px;">Diinput Oleh</strong> : <?= htmlspecialchars($transaksi['username']) ?></p>
        </div>
        <div>
            <p style="margin-bottom: 10px;"><strong style="display: inline-block; width: 120px;">Tanggal Masuk</strong> : <?= date('d/m/Y H:i', strtotime($transaksi['tanggal_masuk'])) ?></p>
            <p style="margin-bottom: 10px;"><strong style="display: inline-block; width: 120px;">Keterangan</strong> : <?= htmlspecialchars($transaksi['keterangan']) ?: '-' ?></p>
        </div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 15px;">Daftar Item Limbah</h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Limbah B3</th>
                    <th>Satuan</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($details as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($row['kode_sampah']) ?></strong></td>
                    <td><?= htmlspecialchars($row['nama_sampah']) ?></td>
                    <td><?= htmlspecialchars($row['satuan']) ?></td>
                    <td><?= (float)$row['quantity'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
