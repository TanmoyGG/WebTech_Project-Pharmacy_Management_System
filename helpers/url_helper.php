<?php
// URL Helper Functions (Procedural)

// Generate URL
function url($path = '') {
    return BASE_URL . $path;
}

// Redirect to URL
function redirectTo($path) {
    header('Location: ' . url($path));
    exit;
}

// Get current page URL
function currentUrl() {
    return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

// Check if current page matches given URL
function isActive($path) {
    $currentPath = isset($_GET['url']) ? $_GET['url'] : '';
    return strpos($currentPath, $path) === 0;
}

// Get query parameter
function getQueryParam($key, $default = null) {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

// Get URL parameter
function getUrlParam($index = 0) {
    $url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
    $urlArray = explode('/', $url);
    return isset($urlArray[$index]) ? $urlArray[$index] : null;
}

// Build query string
function buildQueryString($params = []) {
    return '?' . http_build_query($params);
}

// Get previous page URL (from HTTP referer)
function getPreviousUrl($default = null) {
    return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $default;
}

// Check if current request is AJAX
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Get full URL for a path
function fullUrl($path) {
    return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($path, '/');
}
?>