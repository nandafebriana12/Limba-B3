<?php
// admin/upt/create.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "Sesi telah kedaluwarsa. Silakan coba lagi.";
    } else {
        $nama_upt = sanitize($_POST['nama_upt'] ?? '');
        
        if (empty($nama_upt)) {
            $error = "Nama UPT harus diisi.";
        } else {
            try {
                // Generate Kode UPT (U001)
                $kode_upt = generateKode($conn, 'upts', 'kode_upt', 'U');
                
                $stmt = $conn->prepare("INSERT INTO upts (kode_upt, nama_upt) VALUES (?, ?)");
                $stmt->execute([$kode_upt, $nama_upt]);
                
                $_SESSION['success'] = "UPT berhasil ditambahkan dengan kode $kode_upt.";
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
    <h1 class="page-title">Tambah UPT</h1>
    <a href="index.php" class="btn btn-secondary" style="width: auto;">&larr; Kembali</a>
</div>

<div class="card" style="max-width: 600px;">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div class="form-group">
            <label for="nama_upt">Nama UPT (Fakultas / Prodi)</label>
            <input type="text" id="nama_upt" name="nama_upt" class="form-control" required maxlength="255">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: auto;">Simpan Data</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

