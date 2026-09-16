<?php
// admin/users/edit.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$error = '';
$id = $_GET['id'] ?? 0;

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "Data pengguna tidak ditemukan.";
    header("Location: index.php");
    exit;
}

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
        
        if (empty($username)) {
            $error = "Username harus diisi.";
        } elseif ($role === 'upt' && empty($upt_id)) {
            $error = "UPT asal harus dipilih jika role adalah UPT.";
        } else {
            // Check if username exists (excluding current user)
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $id]);
            if ($stmt->fetch()) {
                $error = "Username sudah digunakan.";
            } else {
                try {
                    if (!empty($password)) {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $conn->prepare("UPDATE users SET username = ?, password_hash = ?, role = ?, upt_id = ? WHERE id = ?");
                        $stmt->execute([$username, $hash, $role, $upt_id, $id]);
                    } else {
                        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ?, upt_id = ? WHERE id = ?");
                        $stmt->execute([$username, $role, $upt_id, $id]);
                    }
                    
                    $_SESSION['success'] = "Data pengguna berhasil diubah.";
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
    <h1 class="page-title">Edit Pengguna</h1>
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
            <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required autocomplete="off">
        </div>
        
        <div class="form-group">
            <label for="password">Password Baru (Biarkan kosong jika tidak ingin mengubah)</label>
            <input type="password" id="password" name="password" class="form-control" autocomplete="new-password">
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role" class="form-control" required onchange="toggleUPT(this.value)">
                <option value="upt" <?= $user['role'] === 'upt' ? 'selected' : '' ?>>User UPT</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
            </select>
        </div>

        <div class="form-group" id="upt_group">
            <label for="upt_id">Asal UPT</label>
            <select id="upt_id" name="upt_id" class="form-control">
                <option value="">-- Pilih UPT --</option>
                <?php foreach($upts as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= $user['upt_id'] == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['kode_upt'] . ' - ' . $u['nama_upt']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: auto;">Simpan Perubahan</button>
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
