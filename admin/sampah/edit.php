<?php
// admin/sampah/edit.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$error = '';
$id = $_GET['id'] ?? 0;

// Fetch existing data
$stmt = $conn->prepare("SELECT * FROM sampah_b3 WHERE id = ?");
$stmt->execute([$id]);
$sampah = $stmt->fetch();

if (!$sampah) {
    $_SESSION['error'] = "Data tidak ditemukan.";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "Sesi telah kedaluwarsa. Silakan coba lagi.";
    } else {
        $nama_sampah = sanitize($_POST['nama_sampah'] ?? '');
        $satuan = sanitize($_POST['satuan'] ?? '');
        $harga_per_unit = str_replace(['Rp', '.', ' '], '', $_POST['harga_per_unit'] ?? '0');
        
        if (empty($nama_sampah) || empty($satuan)) {
            $error = "Nama sampah dan satuan harus diisi.";
        } elseif (!is_numeric($harga_per_unit) || $harga_per_unit < 0) {
            $error = "Harga per unit tidak valid.";
        } else {
            try {
                $stmt = $conn->prepare("UPDATE sampah_b3 SET nama_sampah = ?, satuan = ?, harga_per_unit = ? WHERE id = ?");
                $stmt->execute([$nama_sampah, $satuan, $harga_per_unit, $id]);
                
                $_SESSION['success'] = "Data Sampah B3 berhasil diubah.";
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
    <h1 class="page-title">Edit Sampah B3</h1>
    <a href="index.php" class="btn btn-secondary" style="width: auto;">&larr; Kembali</a>
</div>

<div class="card" style="max-width: 600px;">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div class="form-group">
            <label>Kode Sampah</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($sampah['kode_sampah']) ?>" disabled>
        </div>

        <div class="form-group">
            <label for="nama_sampah">Nama Sampah B3</label>
            <input type="text" id="nama_sampah" name="nama_sampah" class="form-control" value="<?= htmlspecialchars($sampah['nama_sampah']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="satuan">Satuan</label>
            <input type="text" id="satuan" name="satuan" class="form-control" value="<?= htmlspecialchars($sampah['satuan']) ?>" required>
        </div>

        <div class="form-group">
            <label for="harga_per_unit">Harga per Unit (Rp)</label>
            <input type="number" id="harga_per_unit" name="harga_per_unit" class="form-control" min="0" step="0.01" value="<?= htmlspecialchars((float)$sampah['harga_per_unit']) ?>" required>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: auto;">Simpan Perubahan</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
