<?php
// check_db.php
echo "<h2>Pengecekan Server & Database</h2>";

// 1. Cek Ekstensi
echo "<h3>1. Cek Ekstensi PDO_SQLSRV</h3>";
if (extension_loaded('pdo_sqlsrv')) {
    echo "<p style='color:green;'>✅ Ekstensi <b>pdo_sqlsrv</b> terinstal dan aktif.</p>";
} else {
    echo "<p style='color:red;'>❌ Ekstensi <b>pdo_sqlsrv</b> TIDAK DITEMUKAN. Harap instal SQLSRV Drivers for PHP.</p>";
}

if (extension_loaded('sqlsrv')) {
    echo "<p style='color:green;'>✅ Ekstensi <b>sqlsrv</b> terinstal dan aktif.</p>";
} else {
    echo "<p style='color:red;'>❌ Ekstensi <b>sqlsrv</b> TIDAK DITEMUKAN.</p>";
}

// 2. Cek Koneksi (Hanya jika ekstensi pdo_sqlsrv ada)
if (extension_loaded('pdo_sqlsrv')) {
    echo "<h3>2. Tes Koneksi Database</h3>";
    require_once 'config/database.php';
    if (isset($conn)) {
        echo "<p style='color:green;'>✅ Koneksi ke SQL Server berhasil.</p>";
        
        // 3. Tes Query Dummy (cek user)
        try {
            $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
            $row = $stmt->fetch();
            echo "<p style='color:green;'>✅ Tabel `users` ditemukan. Jumlah user: " . $row['count'] . "</p>";
        } catch(PDOException $e) {
            echo "<p style='color:orange;'>⚠️ Koneksi berhasil, tapi tabel mungkin belum dibuat atau ada error query: " . $e->getMessage() . "</p>";
            echo "<p>Jalankan script <code>database/schema.sql</code> di SQL Server Management Studio (SSMS) Anda.</p>";
        }
    }
}

// 4. Generate Password Hash Dummy (Untuk bantuan admin pertama kali)
echo "<h3>3. Generate Password Hash</h3>";
$password_dummy = 'admin123';
$hash = password_hash($password_dummy, PASSWORD_DEFAULT);
echo "<p>Hash untuk password '<b>$password_dummy</b>' adalah: <br><code style='background:#eee;padding:5px;'>$hash</code></p>";
echo "<p>Anda bisa menggunakan hash ini saat melakukan INSERT manual user Admin di database.</p>";
