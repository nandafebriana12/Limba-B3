<?php
// admin/vendors/edit.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$error = '';
$id = $_GET['id'] ?? 0;

// Fetch existing data
$stmt = $conn->prepare("SELECT * FROM vendors WHERE id = ?");
$stmt->execute([$id]);
$vendor = $stmt->fetch();

if (!$vendor) {
    $_SESSION['error'] = "Data tidak ditemukan.";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "Sesi telah kedaluwarsa. Silakan coba lagi.";
    } else {
        $nama_vendor = sanitize($_POST['nama_vendor'] ?? '');
        $kontak = sanitize($_POST['kontak'] ?? '');
        $alamat = sanitize($_POST['alamat'] ?? '');
        
        if (empty($nama_vendor)) {
            $error = "Nama vendor harus diisi.";
        } else {
            try {
                $stmt = $conn->prepare("UPDATE vendors SET nama_vendor = ?, kontak = ?, alamat = ? WHERE id = ?");
                $stmt->execute([$nama_vendor, $kontak, $alamat, $id]);
                
                $_SESSION['success'] = "Data Vendor berhasil diubah.";
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
    <h1 class="page-title">Edit Vendor</h1>
    <a href="index.php" class="btn btn-secondary" style="width: auto;">&larr; Kembali</a>
</div>

<div class="card" style="max-width: 600px;">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div class="form-group">
            <label>Kode Vendor</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($vendor['kode_vendor']) ?>" disabled>
        </div>

        <div class="form-group">
            <label for="nama_vendor">Nama Vendor</label>
            <input type="text" id="nama_vendor" name="nama_vendor" class="form-control" value="<?= htmlspecialchars($vendor['nama_vendor']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="kontak">Kontak (No. HP / Email)</label>
            <input type="text" id="kontak" name="kontak" class="form-control" value="<?= htmlspecialchars($vendor['kontak']) ?>">
        </div>

        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" class="form-control" rows="3"><?= htmlspecialchars($vendor['alamat']) ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: auto;">Simpan Perubahan</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
