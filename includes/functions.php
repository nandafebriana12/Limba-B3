<?php
// includes/functions.php

/**
 * Generate a new ID based on a prefix and a table
 */
function generateKode($conn, $table, $column, $prefix) {
    $sql = "SELECT MAX($column) as max_id FROM $table WHERE $column LIKE '$prefix%'";
    $stmt = $conn->query($sql);
    $row = $stmt->fetch();
    
    $maxId = $row['max_id'];
    
    if ($maxId) {
        $noUrut = (int) substr($maxId, strlen($prefix));
        $noUrut++;
    } else {
        $noUrut = 1;
    }
    
    // Generate new ID like B001, TM001 etc
    // Defaulting to 3 digits padded with 0
    return $prefix . sprintf("%03s", $noUrut);
}

/**
 * Generate CSRF Token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF Token
 */
function validateCSRFToken($token) {
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        return true;
    }
    return false;
}

/**
 * Sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency
 */
function formatRupiah($number) {
    return "Rp " . number_format($number, 0, ',', '.');
}
