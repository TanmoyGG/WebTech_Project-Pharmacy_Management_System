<?php
// User Model - User data operations (Procedural)

// Get user by email
function userGetByEmail($email) {
    return fetchOne('SELECT * FROM users WHERE email = ?', 's', [$email]);
}

// Get user by ID
function userGetById($id) {
    return getById('users', $id);
}

// Get all users with optional role filter
function userGetAll($role = null) {
    if ($role === null) {
        return getAllRecords('users');
    }
    return fetchAll('SELECT * FROM users WHERE role = ?', 's', [$role]);
}

// Create new user
function userCreate($name, $email, $password, $role = 'customer') {
    $userData = [
        'name' => $name,
        'email' => $email,
        'password' => hashPassword($password),
        'role' => $role,
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    return insertRecord('users', $userData);
}

// Update user
function userUpdate($userId, $data) {
    $data['updated_at'] = date('Y-m-d H:i:s');
    return updateRecord('users', $data, 'id = ?', [$userId]);
}

// Delete user
function userDelete($userId) {
    return deleteRecord('users', 'id = ?', [$userId]);
}

// Check if email exists
function userEmailExists($email, $excludeId = null) {
    if ($excludeId === null) {
        return recordExists('users', 'email = ?', [$email]);
    }
    return recordExists('users', 'email = ? AND id != ?', [$email, $excludeId]);
}

// Update password
function userUpdatePassword($userId, $newPassword) {
    return updateRecord('users', ['password' => hashPassword($newPassword)], 'id = ?', [$userId]);
}

// Activate/Deactivate user
function userSetStatus($userId, $status) {
    return updateRecord('users', ['status' => $status], 'id = ?', [$userId]);
}

// Count users by role
function userCountByRole($role) {
    return countRecords('users', 'role = ?', [$role]);
}
?>