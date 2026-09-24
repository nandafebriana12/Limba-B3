<?php
// config/database.php

$serverName = "localhost"; // Atur sesuai dengan server SQL Server Anda (e.g., "localhost\SQLEXPRESS")
$databaseName = "limbah_b3";
$uid = "sa"; // Username SQL Server
$pwd = "asik!"; // Password SQL Server

try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=$databaseName", $uid, $pwd);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}