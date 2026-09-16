<?php
// admin/upt/edit.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$error = '';
$id = $_GET['id'] ?? 0;

// Fetch existing data
$stmt = $conn->prepare("SELECT * FROM upts WHERE id = ?");
$stmt->execute([$id]);
$upt = $stmt->fetch();

if (!$upt) {
    $_SESSION['error'] = "Data tidak ditemukan.";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "Sesi telah kedaluwarsa. Silakan coba lagi.";
    } else {
        $nama_upt = sanitize($_POST['nama_upt'] ?? '');
        
        if (empty($nama_upt)) {
            $error = "Nama UPT harus diisi.";
        } else {
            try {
                $stmt = $conn->prepare("UPDATE upts SET nama_upt = ? WHERE id = ?");
                $stmt->execute([$nama_upt, $id]);
                
                $_SESSION['success'] = "Data UPT berhasil diubah.";
                header("Location: index.php");
                exit;
            } catch(PDOException $e) {
                $error = "Terjadi kesalahan: " . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Edit UPT</h1>
    <a href="index.php" class="btn btn-secondary" style="width: auto;">&larr; Kembali</a>
</div>

<div class="card" style="max-width: 600px;">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div class="form-group">
            <label>Kode UPT</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($upt['kode_upt']) ?>" disabled>
            <small style="color: var(--text-muted);">Kode UPT tidak dapat diubah.</small>
        </div>

        <div class="form-group">
            <label for="nama_upt">Nama UPT (Fakultas / Prodi)</label>
            <input type="text" id="nama_upt" name="nama_upt" class="form-control" value="<?= htmlspecialchars($upt['nama_upt']) ?>" required>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: auto;">Simpan Perubahan</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
