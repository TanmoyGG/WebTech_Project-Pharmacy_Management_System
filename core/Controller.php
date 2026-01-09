<?php
// Controller Helper Functions (Procedural)

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check if user has specific role
function hasRole($requiredRole) {
    if (!isLoggedIn()) {
        return false;
    }
    
    return isset($_SESSION['role']) && $_SESSION['role'] === $requiredRole;
}

// Get current logged-in user ID
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// Get current user role
function getCurrentUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

// Require authentication
function requireAuth() {
    if (!isLoggedIn()) {
        $_SESSION['message'] = 'Please login first';
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }
}

// Require specific role
function requireRole($role) {
    requireAuth();
    
    if (!hasRole($role)) {
        $_SESSION['error'] = 'You do not have permission to access this page';
        header('Location: ' . BASE_URL . 'home/index');
        exit;
    }
}

// Redirect to URL
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// Check if request method is POST
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// Check if request method is GET
function isGet() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

// Get POST data
function getPost($key = null, $default = null) {
    if ($key === null) {
        return $_POST;
    }
    return isset($_POST[$key]) ? $_POST[$key] : $default;
}

// Get GET data
function getGet($key = null, $default = null) {
    if ($key === null) {
        return $_GET;
    }
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

// Set flash message
function setFlash($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

// Get flash message
function getFlash() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = isset($_SESSION['flash_type']) ? $_SESSION['flash_type'] : 'info';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return ['message' => $message, 'type' => $type];
    }
    return null;
}
?>