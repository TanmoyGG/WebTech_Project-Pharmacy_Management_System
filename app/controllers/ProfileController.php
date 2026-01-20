<?php
// Profile Controller - User profile management

require_once __DIR__ . '/../models/User.php';

// Default profile route (redirect to view)
function profile_index() {
    profile_view();
}

// Display user profile
function profile_view() {
    requireAuth();
    
    $userId = getCurrentUserId();
    $user = userGetById($userId);
    
    if (!$user) {
        setFlash('User not found', 'error');
        redirectTo('home');
    }
    
    render('profile/view', ['user' => $user]);
}

// Display edit profile page
function profile_edit() {
    requireAuth();
    
    $userId = getCurrentUserId();
    $user = userGetById($userId);
    
    if (!$user) {
        setFlash('User not found', 'error');
        redirectTo('home');
    }
    
    render('profile/edit', ['user' => $user]);
}

// Update user profile
function profile_update() {
    requireAuth();
    
    if (!isPost()) {
        redirectTo('profile/view');
    }
    
    $userId = getCurrentUserId();
    $name = sanitize(getPost('name', ''));
    $phone = sanitize(getPost('phone', ''));
    $dob = sanitize(getPost('dob', ''));
    $address = sanitize(getPost('address', ''));
    
    // Validate required fields
    if (isEmpty($name)) {
        setFlash('Name is required', 'error');
        redirectTo('profile/edit');
    }
    
    // Update profile using User model
    $updated = userUpdate($userId, $name, $phone, $dob, $address);
    
    if ($updated) {
        $_SESSION['user_name'] = $name;
        setFlash('Profile updated successfully', 'success');
        redirectTo('profile/view');
    } else {
        setFlash('Failed to update profile', 'error');
        redirectTo('profile/edit');
    }
}

// Display change password page
function profile_changePassword() {
    requireAuth();
    render('profile/changePassword');
}

// Update user password
function profile_updatePassword() {
    requireAuth();
    
    if (!isPost()) {
        redirectTo('profile/view');
    }
    
    $userId = getCurrentUserId();
    $currentPassword = getPost('current_password', '');
    $newPassword = getPost('new_password', '');
    $confirmPassword = getPost('confirm_password', '');
    
    // Validate inputs
    if (isEmpty($currentPassword) || isEmpty($newPassword) || isEmpty($confirmPassword)) {
        setFlash('All password fields are required', 'error');
        redirectTo('profile/changePassword');
    }
    
    if ($newPassword !== $confirmPassword) {
        setFlash('New password and confirmation do not match', 'error');
        redirectTo('profile/changePassword');
    }
    
    if (!validatePassword($newPassword)) {
        setFlash('Password must be at least 6 characters long', 'error');
        redirectTo('profile/changePassword');
    }
    
    // Get current user and verify password
    $user = userGetById($userId);
    if (!$user || !verifyPassword($currentPassword, $user['password'])) {
        setFlash('Current password is incorrect', 'error');
        redirectTo('profile/changePassword');
    }
    
    // Update password using User model
    $updated = userUpdatePassword($userId, $newPassword);
    
    if ($updated) {
        setFlash('Password changed successfully', 'success');
        redirectTo('profile/view');
    } else {
        setFlash('Failed to update password', 'error');
        redirectTo('profile/changePassword');
    }
}
?>
