<?php
// Validation Helper Functions
// Only essential, used validation functions

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Check if string is empty or only whitespace
function isEmpty($string) {
    return empty(trim($string));
}

// Check if string length is within range
function validateLength($string, $min = 0, $max = PHP_INT_MAX) {
    $length = strlen(trim($string));
    return $length >= $min && $length <= $max;
}

// Validate password strength (6 characters minimum)
function validatePassword($password) {
    // Minimum 6 characters
    return strlen($password) >= 6;
}

// Sanitize input (XSS protection)
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Sanitize email
function sanitizeEmail($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

// Check if email already exists
function emailExists($email) {
    return recordExists('users', 'email = ?', [$email]);
}

// Hash password using bcrypt
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify password against hash
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Validate array required fields
function validateRequired($data, $requiredFields = []) {
    $errors = [];
    
    foreach ($requiredFields as $field) {
        if (isEmpty($data[$field] ?? '')) {
            $errors[$field] = ucfirst($field) . ' is required';
        }
    }
    
    return $errors;
}

?>