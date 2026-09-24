<?php
// admin/users/create.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$error = '';

// Fetch UPTs for dropdown
$stmt = $conn->query("SELECT id, kode_upt, nama_upt FROM upts ORDER BY nama_upt ASC");
$upts = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "Sesi telah kedaluwarsa. Silakan coba lagi.";
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'upt';
        $upt_id = !empty($_POST['upt_id']) ? $_POST['upt_id'] : null;
        
        if (empty($username) || empty($password)) {
            $error = "Username dan password harus diisi.";
        } elseif ($role === 'upt' && empty($upt_id)) {
            $error = "UPT asal harus dipilih jika role adalah UPT.";
        } else {
            // Check if username exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = "Username sudah digunakan.";
            } else {
                try {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role, upt_id) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$username, $hash, $role, $upt_id]);
                    
                    $_SESSION['success'] = "Pengguna berhasil ditambahkan.";
                    header("Location: index.php");
                    exit;
                } catch(PDOException $e) {
                    $error = "Terjadi kesalahan: " . $e->getMessage();
                }
            }
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Tambah Pengguna</h1>
    <a href="index.php" class="btn btn-secondary" style="width: auto;">&larr; Kembali</a>
</div>

<div class="card" style="max-width: 600px;">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" required maxlength="50" autocomplete="off">
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required autocomplete="new-password">
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role" class="form-control" required onchange="toggleUPT(this.value)">
                <option value="upt">User UPT</option>
                <option value="admin">Administrator</option>
            </select>
        </div>

        <div class="form-group" id="upt_group">
            <label for="upt_id">Asal UPT</label>
            <select id="upt_id" name="upt_id" class="form-control">
                <option value="">-- Pilih UPT --</option>
                <?php foreach($upts as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['kode_upt'] . ' - ' . $u['nama_upt']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: auto;">Simpan Data</button>
    </form>
</div>

<script>
function toggleUPT(role) {
    var uptGroup = document.getElementById('upt_group');
    var uptSelect = document.getElementById('upt_id');
    if (role === 'admin') {
        uptGroup.style.display = 'none';
        uptSelect.removeAttribute('required');
        uptSelect.value = '';
    } else {
        uptGroup.style.display = 'block';
        uptSelect.setAttribute('required', 'required');
    }
}
// Init script
toggleUPT(document.getElementById('role').value);
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

