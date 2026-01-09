<?php
// User Model - Procedural Database Functions
// Handles all user-related database operations for unified users table

// Get user by ID
function userGetById($user_id) {
    $db = getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Get user by email
function userGetByEmail($email) {
    $db = getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Get user by name (search)
function userGetByName($name) {
    $db = getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE name LIKE ?");
    $search = '%' . $name . '%';
    $stmt->bind_param('s', $search);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Create new user (unified for all roles)
function userCreate($name, $email, $password, $role, $phone = null, $dob = null, $address = null) {
    $db = getConnection();
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role, phone, dob, address, status) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
    $stmt->bind_param('sssssss', $name, $email, $hashed_password, $role, $phone, $dob, $address);
    
    return $stmt->execute();
}

// Update user profile
function userUpdate($user_id, $name, $phone = null, $dob = null, $address = null) {
    $db = getConnection();
    $stmt = $db->prepare("UPDATE users SET name = ?, phone = ?, dob = ?, address = ?, updated_at = CURRENT_TIMESTAMP 
                          WHERE id = ?");
    $stmt->bind_param('ssssi', $name, $phone, $dob, $address, $user_id);
    
    return $stmt->execute();
}

// Update user password
function userUpdatePassword($user_id, $new_password) {
    $db = getConnection();
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param('si', $hashed_password, $user_id);
    
    return $stmt->execute();
}

// Set password reset token
function userSetResetToken($user_id, $token, $expiry_time = '+1 hour') {
    $db = getConnection();
    $expiry = date('Y-m-d H:i:s', strtotime($expiry_time));
    
    $stmt = $db->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE id = ?");
    $stmt->bind_param('ssi', $token, $expiry, $user_id);
    
    return $stmt->execute();
}

// Verify and reset password by token
function userResetPasswordByToken($token, $new_password) {
    $db = getConnection();
    
    // Check if token exists and is not expired
    $stmt = $db->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }
    
    $user = $result->fetch_assoc();
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param('si', $hashed_password, $user['id']);
    
    return $stmt->execute();
}

// Change user status (activate/deactivate)
function userSetStatus($user_id, $status) {
    $db = getConnection();
    $status = in_array($status, ['active', 'inactive']) ? $status : 'active';
    
    $stmt = $db->prepare("UPDATE users SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param('si', $status, $user_id);
    
    return $stmt->execute();
}

// Delete user
function userDelete($user_id) {
    $db = getConnection();
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    
    return $stmt->execute();
}

// Check if email exists
function userEmailExists($email, $excludeId = null) {
    $db = getConnection();
    
    if ($excludeId !== null) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param('si', $email, $excludeId);
    } else {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
    }
    
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    return $result['count'] > 0;
}

// Get all users with optional filters
function userGetAll($role = null, $status = 'active', $limit = null, $offset = 0) {
    $db = getConnection();
    
    $query = "SELECT * FROM users WHERE status = ?";
    $types = 's';
    $params = [$status];
    
    if ($role !== null) {
        $query .= " AND role = ?";
        $types .= 's';
        $params[] = $role;
    }
    
    $query .= " ORDER BY created_at DESC";
    
    if ($limit !== null) {
        $query .= " LIMIT ? OFFSET ?";
        $types .= 'ii';
        $params[] = $limit;
        $params[] = $offset;
    }
    
    $stmt = $db->prepare($query);
    call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $params));
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Count users by role
function userCountByRole($role = null) {
    $db = getConnection();
    
    if ($role === null) {
        $result = $db->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    } else {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE role = ?");
        $stmt->bind_param('s', $role);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get user count by status
function userCountByStatus($status = null) {
    $db = getConnection();
    
    if ($status === null) {
        $result = $db->query("SELECT status, COUNT(*) as count FROM users GROUP BY status");
    } else {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE status = ?");
        $stmt->bind_param('s', $status);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get total user count
function userCount() {
    $db = getConnection();
    $result = $db->query("SELECT COUNT(*) as count FROM users");
    $data = $result->fetch_assoc();
    
    return $data['count'];
}

// Search users
function userSearch($search_term, $limit = 20) {
    $db = getConnection();
    $search = '%' . $search_term . '%';
    
    $stmt = $db->prepare("SELECT * FROM users WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? 
                          ORDER BY created_at DESC LIMIT ?");
    $stmt->bind_param('sssi', $search, $search, $search, $limit);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get users registered in date range
function userGetByDateRange($start_date, $end_date) {
    $db = getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE created_at BETWEEN ? AND ? 
                          ORDER BY created_at DESC");
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get paginated users
function userGetPaginated($page = 1, $per_page = 10, $role = null, $status = 'active') {
    $offset = ($page - 1) * $per_page;
    
    return userGetAll($role, $status, $per_page, $offset);
}

// Verify user password
function userVerifyPassword($email, $password) {
    $user = userGetByEmail($email);
    
    if (!$user) {
        return false;
    }
    
    return password_verify($password, $user['password']);
}

// Get user role
function userGetRole($user_id) {
    $user = userGetById($user_id);
    
    return $user ? $user['role'] : null;
}

// Update user role (Admin only)
function userUpdateRole($user_id, $new_role) {
    $db = getConnection();
    $valid_roles = ['admin', 'inventory_manager', 'customer'];
    
    if (!in_array($new_role, $valid_roles)) {
        return false;
    }
    
    $stmt = $db->prepare("UPDATE users SET role = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param('si', $new_role, $user_id);
    
    return $stmt->execute();
}

// Get user statistics
function userGetStats() {
    $db = getConnection();
    
    $result = $db->query("SELECT 
                            COUNT(*) as total_users,
                            SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins,
                            SUM(CASE WHEN role = 'inventory_manager' THEN 1 ELSE 0 END) as inventory_managers,
                            SUM(CASE WHEN role = 'customer' THEN 1 ELSE 0 END) as customers,
                            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
                            SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_users
                          FROM users");
    
    return $result->fetch_assoc();
}
?>