<?php
// admin/vendors/create.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$error = '';

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
                // Generate Kode Vendor (V001)
                $kode_vendor = generateKode($conn, 'vendors', 'kode_vendor', 'V');
                
                $stmt = $conn->prepare("INSERT INTO vendors (kode_vendor, nama_vendor, kontak, alamat) VALUES (?, ?, ?, ?)");
                $stmt->execute([$kode_vendor, $nama_vendor, $kontak, $alamat]);
                
                $_SESSION['success'] = "Data Vendor berhasil ditambahkan dengan kode $kode_vendor.";
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
    <h1 class="page-title">Tambah Vendor</h1>
    <a href="index.php" class="btn btn-secondary" style="width: auto;">&larr; Kembali</a>
</div>

<div class="card" style="max-width: 600px;">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div class="form-group">
            <label for="nama_vendor">Nama Vendor</label>
            <input type="text" id="nama_vendor" name="nama_vendor" class="form-control" required maxlength="255">
        </div>
        
        <div class="form-group">
            <label for="kontak">Kontak (No. HP / Email)</label>
            <input type="text" id="kontak" name="kontak" class="form-control"required maxlength="100">
        </div>

        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" class="form-control" rows="3"required maxlength="500"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: auto;">Simpan Data</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

