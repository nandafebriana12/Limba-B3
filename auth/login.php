<?php
// auth/login.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "Sesi telah kedaluwarsa. Silakan coba lagi.";
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $error = "Username dan password harus diisi.";
        } else {
            $sql = "SELECT id, username, password_hash, role, upt_id FROM users WHERE username = :username";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['upt_id'] = $user['upt_id'];
                
                header("Location: " . BASE_URL);
                exit;
            } else {
                $error = "Username atau password salah.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Tracking Limbah B3</h2>
            <p>Silakan masuk ke akun Anda</p>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required autocomplete="username" autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password">
                </div>
                
                <button type="submit" class="btn btn-primary">Masuk</button>
            </form>
        </div>
    </div>
</body>
</html>
