<?php
// Authentication Controller - Handles login, register, forgot password, change password
// All functions follow procedural pattern: auth_[action]()

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
    $user = getUserByEmail($email);
    
    if (!$user || !verifyPassword($password, $user['password'])) {
        setFlash('Invalid email or password', 'error');
        redirectTo('auth/login');
    }
    
    // Set session and redirect
    setUserSession($user['id'], $user['name'], $user['email'], $user['role']);
    
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
    $role = getPost('role', 'customer');
    
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
    } elseif (emailExists($email)) {
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
    
    // Insert user
    $hashedPassword = hashPassword($password);
    
    $userData = [
        'name' => $name,
        'email' => $email,
        'password' => $hashedPassword,
        'role' => $role,
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $userId = insertRecord('users', $userData);
    
    if ($userId) {
        setFlash('Registration successful! Please login.', 'success');
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
    if (!emailExists($email)) {
        setFlash('If email exists, password reset link has been sent', 'info');
        redirectTo('auth/forgot_password');
    }
    
    // Generate reset token
    $resetToken = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Update user with reset token
    $user = getUserByEmail($email);
    updateRecord('users', 
        [
            'reset_token' => $resetToken,
            'reset_expiry' => $expiry
        ], 
        'id = ?',
        [$user['id']]
    );
    
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
    $user = getById('users', $userId);
    
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
    $hashedPassword = hashPassword($newPassword);
    updateRecord('users', ['password' => $hashedPassword], 'id = ?', [$userId]);
    
    setFlash('Password changed successfully', 'success');
    redirectTo('profile/view');
}

// Logout
function auth_logout() {
    destroyUserSession();
    setFlash('Logged out successfully', 'success');
    redirectTo('home/index');
}

// Helper function: Get user by email
function getUserByEmail($email) {
    return fetchOne('SELECT * FROM users WHERE email = ?', 's', [$email]);
}
?>