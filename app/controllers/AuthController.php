<?php
// Authentication Controller - Handles login, register, forgot password, change password
// All functions follow procedural pattern: auth_[action]()

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../helpers/cookie_helper.php';

// Display login page
function auth_login() {
    if (isLoggedIn()) {
        // Redirect based on role
        $role = getCurrentUserRole();
        if ($role === 'admin') {
            redirectTo('admin/dashboard');
        } elseif ($role === 'inventory_manager') {
            redirectTo('inventory_manager/dashboard');
        } else {
            redirectTo('customer/home');
        }
    }
    
    render('auth/login');
}

// Process login
function auth_loginProcess() {
    if (!isPost()) {
        redirectTo('auth/login');
    }
    
    $email = sanitizeEmail(getPost('email', ''));
    $password = getPost('password', '');
    
    // Validate inputs
    if (isEmpty($email) || isEmpty($password)) {
        setFlash('Email and password are required', 'error');
        redirectTo('auth/login');
    }
    
    if (!validateEmail($email)) {
        setFlash('Invalid email format', 'error');
        redirectTo('auth/login');
    }
    
    // Check user exists
    $user = userGetByEmail($email);
    
    if (!$user) {
        setFlash('Invalid email or password', 'error');
        redirectTo('auth/login');
    }
    
    // Check password (support both hashed and plain text for testing)
    $password_valid = false;
    if (password_verify($password, $user['password'])) {
        $password_valid = true;
    } elseif ($password === $user['password']) {
        // Plain text comparison for test data
        $password_valid = true;
    }
    
    if (!$password_valid) {
        setFlash('Invalid email or password', 'error');
        redirectTo('auth/login');
    }
    
    // Set session and redirect
    setUserSession($user['id'], $user['name'], $user['email'], $user['role']);
    
    // Handle "Remember Me" functionality
    $remember_me = getPost('remember_me');
    if ($remember_me) {
        // Generate a simple token for remember me
        $token = hash('sha256', $user['id'] . $user['email'] . time() . rand(1000, 9999));
        setCookieRememberMe($user['id'], $user['email'], $token);
    }
    
    setFlash('Login successful', 'success');
    
    // Redirect based on role
    if ($user['role'] === 'admin') {
        redirectTo('admin/dashboard');
    } elseif ($user['role'] === 'inventory_manager') {
        redirectTo('inventory_manager/dashboard');
    } else {
        redirectTo('customer/home');
    }
}

// Display registration page
function auth_register() {
    if (isLoggedIn()) {
        redirectTo('home/index');
    }
    
    render('auth/register');
}

// Process registration
function auth_registerProcess() {
    if (!isPost()) {
        redirectTo('auth/register');
    }
    
    $name = sanitize(getPost('name', ''));
    $email = sanitizeEmail(getPost('email', ''));
    $password = getPost('password', '');
    $confirmPassword = getPost('confirm_password', '');
    $phone = sanitize(getPost('phone', ''));
    $dob = sanitize(getPost('dob', ''));
    $address = sanitize(getPost('address', ''));
    $role = 'customer'; // Always customer for self-registration
    
    // Validate inputs
    $errors = [];
    
    if (isEmpty($name)) {
        $errors['name'] = 'Name is required';
    } elseif (!validateLength($name, 3, 50)) {
        $errors['name'] = 'Name must be between 3 and 50 characters';
    }
    
    if (isEmpty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!validateEmail($email)) {
        $errors['email'] = 'Invalid email format';
    } elseif (userEmailExists($email)) {
        $errors['email'] = 'Email already exists';
    }
    
    if (isEmpty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (!validateLength($password, 6, 255)) {
        $errors['password'] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        redirectTo('auth/register');
    }
    
    // Create user account with additional fields
    $result = userCreate($name, $email, $password, $role, $phone, $dob, $address);
    
    if ($result) {
        setFlash('Registration successful! Please login with your credentials.', 'success');
        redirectTo('auth/login');
    } else {
        setFlash('Registration failed. Please try again.', 'error');
        redirectTo('auth/register');
    }
}

// Display forgot password page
function auth_forgotPassword() {
    if (isLoggedIn()) {
        redirectTo('home/index');
    }
    
    render('auth/forgot_password');
}

// Process forgot password
function auth_forgotPasswordProcess() {
    if (!isPost()) {
        redirectTo('auth/forgot_password');
    }
    
    $email = sanitizeEmail(getPost('email', ''));
    
    if (isEmpty($email) || !validateEmail($email)) {
        setFlash('Invalid email address', 'error');
        redirectTo('auth/forgot_password');
    }
    
    // Check user exists
    if (!userEmailExists($email)) {
        setFlash('If email exists, password reset link has been sent', 'info');
        redirectTo('auth/forgot_password');
    }
    
    // Generate reset token
    $resetToken = bin2hex(random_bytes(32));
    
    // Get user and set reset token
    $user = userGetByEmail($email);
    userSetResetToken($user['id'], $resetToken);
    
    // TODO: Send email with reset link
    
    setFlash('If email exists, password reset link has been sent', 'info');
    redirectTo('auth/forgot_password');
}

// Display change password page
function auth_changePassword() {
    requireAuth();
    render('auth/change_password');
}

// Process change password
function auth_changePasswordProcess() {
    requireAuth();
    
    if (!isPost()) {
        redirectTo('auth/change_password');
    }
    
    $currentPassword = getPost('current_password', '');
    $newPassword = getPost('new_password', '');
    $confirmPassword = getPost('confirm_password', '');
    
    $userId = getCurrentUserId();
    $user = userGetById($userId);
    
    // Validate current password
    if (!verifyPassword($currentPassword, $user['password'])) {
        setFlash('Current password is incorrect', 'error');
        redirectTo('auth/change_password');
    }
    
    // Validate new password
    if (isEmpty($newPassword) || !validateLength($newPassword, 6, 255)) {
        setFlash('New password must be at least 6 characters', 'error');
        redirectTo('auth/change_password');
    }
    
    if ($newPassword !== $confirmPassword) {
        setFlash('New passwords do not match', 'error');
        redirectTo('auth/change_password');
    }
    
    // Update password
    userUpdatePassword($userId, $newPassword);
    
    setFlash('Password changed successfully', 'success');
    redirectTo('profile/view');
}

// Logout
function auth_logout() {
    destroyUserSession();
    clearCookieRememberMe(); // Clear "Remember Me" cookie
    setFlash('Logged out successfully', 'success');
    redirectTo('home/index');
}

?>