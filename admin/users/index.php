<?php
// admin/users/index.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

// Handle Delete
if (isset($_GET['delete']) && validateCSRFToken($_GET['csrf'] ?? '')) {
    $id = $_GET['delete'];
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Data Pengguna berhasil dihapus.";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Gagal menghapus data: " . $e->getMessage();
    }
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/../../includes/header.php';

// Fetch Data
$sql = "SELECT u.*, p.nama_upt FROM users u LEFT JOIN upts p ON u.upt_id = p.id ORDER BY u.id DESC";
$stmt = $conn->query($sql);
$users = $stmt->fetchAll();
?>

<div class="page-header">
    <h1 class="page-title">Kelola Data Pengguna</h1>
    <a href="create.php" class="btn btn-primary" style="width: auto;">+ Tambah Pengguna</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Asal UPT</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($users) > 0): ?>
                    <?php $no = 1; foreach($users as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($row['username']) ?></strong></td>
                        <td>
                            <?php if($row['role'] === 'admin'): ?>
                                <span style="background: var(--primary); color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem;">Admin</span>
                            <?php else: ?>
                                <span style="background: var(--secondary); color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem;">UPT</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $row['nama_upt'] ? htmlspecialchars($row['nama_upt']) : '-' ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem;">Edit</a>
                            <?php if($row['id'] != $_SESSION['user_id']): ?>
                                <a href="index.php?delete=<?= $row['id'] ?>&csrf=<?= generateCSRFToken() ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Belum ada data pengguna.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
