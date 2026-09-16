<?php
// admin/upt/index.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

// Handle Delete
if (isset($_GET['delete']) && validateCSRFToken($_GET['csrf'] ?? '')) {
    $id = $_GET['delete'];
    try {
        $stmt = $conn->prepare("DELETE FROM upts WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Data UPT berhasil dihapus.";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Gagal menghapus data. Pastikan tidak ada data yang terkait.";
    }
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/../../includes/header.php';

// Fetch Data
$stmt = $conn->query("SELECT * FROM upts ORDER BY id DESC");
$upts = $stmt->fetchAll();
?>

<div class="page-header">
    <h1 class="page-title">Kelola Data UPT</h1>
    <a href="create.php" class="btn btn-primary" style="width: auto;">+ Tambah UPT</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode UPT</th>
                    <th>Nama UPT (Fakultas/Prodi)</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($upts) > 0): ?>
                    <?php $no = 1; foreach($upts as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($row['kode_upt']) ?></strong></td>
                        <td><?= htmlspecialchars($row['nama_upt']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem;">Edit</a>
                            <a href="index.php?delete=<?= $row['id'] ?>&csrf=<?= generateCSRFToken() ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Belum ada data UPT.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
