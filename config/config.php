<?php
// config/config.php

// Define base URL dynamically depending on server type
if (php_sapi_name() == 'cli-server') {
    define('BASE_URL', '/');
} else {
    define('BASE_URL', 'http://localhost/Limba%20B3/'); // Sesuaikan jika menggunakan XAMPP/Laragon
}
// App Name
define('APP_NAME', 'Sistem Tracking Limbah B3');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
