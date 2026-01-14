<?php
// Session Helper Functions (Procedural)

// Check and refresh session timeout
function checkSessionTimeout() {
    $timeout = defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT : 3600;
    
    if (isset($_SESSION['last_activity'])) {
        $inactive = time() - $_SESSION['last_activity'];
        if ($inactive > $timeout) {
            session_destroy();
            session_start();
            return false;
        }
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

// Check if user is logged in
function userIsLoggedIn() {
    checkSessionTimeout();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Set user session data
function setUserSession($userId, $userName, $userEmail, $userRole) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $userName;
    $_SESSION['user_email'] = $userEmail;
    $_SESSION['role'] = $userRole;
}

// Destroy user session (logout)
function destroyUserSession() {
    session_destroy();
    session_start();
}

// Get user data from session
function getUserData($key = null) {
    if ($key === null) {
        return [
            'user_id' => $_SESSION['user_id'] ?? null,
            'user_name' => $_SESSION['user_name'] ?? null,
            'user_email' => $_SESSION['user_email'] ?? null,
            'role' => $_SESSION['role'] ?? null
        ];
    }
    
    $sessionKey = 'user_' . $key;
    return $_SESSION[$sessionKey] ?? null;
}

// Set flash message (temporary message for next page)
function flashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

// Get flash message
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return [
            'message' => $message,
            'type' => $type
        ];
    }
    return null;
}

// Check if flash message exists
function hasFlashMessage() {
    return isset($_SESSION['flash_message']);
}

// Set error message
function setError($message) {
    $_SESSION['error'] = $message;
}

// Get error message
function getError() {
    if (isset($_SESSION['error'])) {
        $error = $_SESSION['error'];
        unset($_SESSION['error']);
        return $error;
    }
    return null;
}

// Set success message
function setSuccess($message) {
    $_SESSION['success'] = $message;
}

// Get success message
function getSuccess() {
    if (isset($_SESSION['success'])) {
        $success = $_SESSION['success'];
        unset($_SESSION['success']);
        return $success;
    }
    return null;
}
?>