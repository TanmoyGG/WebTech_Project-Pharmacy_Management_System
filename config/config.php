<?php
// Application Configuration (Procedural)

// Application name
define('APP_NAME', 'Pharmacy Management System');

// Base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$serverName = $_SERVER['HTTP_HOST'];
define('BASE_URL', $protocol . '://' . $serverName . '/WebTech_Project-Pharmacy_Management_System/public/');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', null);  // null for empty password
define('DB_NAME', 'pharmacy_management');

// Application settings
define('APP_VERSION', '1.0.0');
define('DEFAULT_TIMEZONE', 'UTC');

// User roles
define('ROLE_ADMIN', 'admin');
define('ROLE_INVENTORY_MANAGER', 'inventory_manager');
define('ROLE_CUSTOMER', 'customer');

// Pagination
define('RECORDS_PER_PAGE', 10);

// Set timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// Session Configuration
define('SESSION_TIMEOUT', 3600);  // 1 hour in seconds (3600 seconds)
ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>