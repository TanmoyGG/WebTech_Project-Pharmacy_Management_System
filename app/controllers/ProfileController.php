<?php
// Profile Controller - User profile management
// All functions follow procedural pattern: profile_[action]()

// Display user profile
function profile_view() {
    requireAuth();
    
    $userId = getCurrentUserId();
    $user = getById('users', $userId);
    
    render('profile/view', ['user' => $user]);
}

// Display edit profile page
function profile_edit() {
    requireAuth();
    
    $userId = getCurrentUserId();
    $user = getById('users', $userId);
    
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
    $email = sanitizeEmail(getPost('email', ''));
    $phone = sanitize(getPost('phone', ''));
    $address = sanitize(getPost('address', ''));
    
    if (isEmpty($name) || isEmpty($email)) {
        setFlash('Name and email are required', 'error');
        redirectTo('profile/edit');
    }
    
    $existingUser = fetchOne('SELECT id FROM users WHERE email = ? AND id != ?', 'ss', [$email, $userId]);
    
    if ($existingUser) {
        setFlash('Email is already in use', 'error');
        redirectTo('profile/edit');
    }
    
    $updateData = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    updateRecord('users', $updateData, 'id = ?', [$userId]);
    
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    
    setFlash('Profile updated successfully', 'success');
    redirectTo('profile/view');
}
?>
