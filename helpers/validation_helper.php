<?php
// Validation Helper Functions (Procedural)

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate URL
function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// Validate integer
function validateInt($value) {
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}

// Validate float
function validateFloat($value) {
    return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
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

// Validate password strength
function validatePassword($password) {
    // Minimum 8 characters, at least one uppercase, one lowercase, one digit, one special character
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    return preg_match($pattern, $password) === 1;
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Sanitize email
function sanitizeEmail($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

// Validate phone number (basic)
function validatePhone($phone) {
    $pattern = '/^[\d\s\+\-\(\)]{10,}$/';
    return preg_match($pattern, $phone) === 1;
}

// Validate username
function validateUsername($username) {
    $pattern = '/^[a-zA-Z0-9_]{3,20}$/';
    return preg_match($pattern, $username) === 1;
}

// Check if username already exists
function usernameExists($username) {
    return recordExists('users', 'username = ?', [$username]);
}

// Check if email already exists
function emailExists($email) {
    return recordExists('users', 'email = ?', [$email]);
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify password
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

// Validate file upload
function validateFileUpload($file, $allowedTypes = [], $maxSize = 5242880) {
    $errors = [];
    
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'No file uploaded';
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload error: ' . $file['error'];
    } elseif ($file['size'] > $maxSize) {
        $errors[] = 'File size exceeds maximum limit';
    } elseif (!empty($allowedTypes) && !in_array($file['type'], $allowedTypes)) {
        $errors[] = 'File type not allowed';
    }
    
    return $errors;
}

// Note: database escaping is centralized in core/Database.php via escape()
?>