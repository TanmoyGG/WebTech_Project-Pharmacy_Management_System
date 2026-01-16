<?php
// Main Entry Point - Single point for the entire application
// This serves as the application bootstrap and router

// Load configuration and core files
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/App.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/helpers/session_helper.php';
require_once __DIR__ . '/helpers/url_helper.php';
require_once __DIR__ . '/helpers/validation_helper.php';

// Disable directory listing
header('X-Powered-By: PHP');

// Handle static assets (css, js, images) - serve from public folder
$request = $_SERVER['REQUEST_URI'];
$base = '/WebTech_Project-Pharmacy_Management_System';
$path = str_replace($base, '', $request);
$path = ltrim($path, '/');

// Remove query string from path
$path = strtok($path, '?');

// Check if requesting a static asset
$publicPath = __DIR__ . '/public/' . $path;
if (file_exists($publicPath) && is_file($publicPath)) {
    // Serve static file
    $ext = pathinfo($publicPath, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2'
    ];
    
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }
    
    readfile($publicPath);
    exit;
}

// For non-static requests, route through application
$_GET['url'] = $path ?: '';

// Initialize the application
$urlArray = initApp();

// Route the request
routeRequest($urlArray);
?>
