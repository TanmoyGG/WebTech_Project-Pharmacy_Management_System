<?php
// Admin Controller - Master Admin functionalities
// All functions follow procedural pattern: admin_[action]()

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/OrderItem.php';
require_once __DIR__ . '/../models/SystemConfig.php';

// Display admin dashboard with sales analytics
function admin_dashboard() {
    requireRole('admin');
    
    // Get statistics
    $userStats = userGetStats();
    $productStats = productGetStats();
    $orderStats = orderGetStats();
    $totalRevenue = orderGetTotalRevenue();
    
    // Get daily order counts (last 7 days)
    $dailyOrders = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $count = orderCount(null); // We'll get proper daily counts
        $dailyOrders[$date] = $count;
    }
    
    // Get most sold products
    $mostSoldProducts = orderItemGetTopProducts(5);
    
    // Recent transactions
    $recentTransactions = transactionGetRecent(10);
    
    $data = [
        'userStats' => $userStats,
        'productStats' => $productStats,
        'orderStats' => $orderStats,
        'totalRevenue' => $totalRevenue,
        'dailyOrders' => $dailyOrders,
        'mostSoldProducts' => $mostSoldProducts,
        'recentTransactions' => $recentTransactions
    ];
    
    render('admin/dashboard', $data);
}

// ============= USER MANAGEMENT =============

// Display all users
function admin_users() {
    requireRole('admin');
    
    $role_filter = getGet('role', null);
    // Show all statuses by default, and treat empty string as null
    $status_filter = getGet('status', null);
    if ($role_filter === '') { $role_filter = null; }
    if ($status_filter === '') { $status_filter = null; }
    $search = getGet('search', '');
    
    if (!empty($search)) {
        $users = userSearch($search, 100);
    } else {
        $users = userGetAll($role_filter, $status_filter);
    }
    
    $data = [
        'users' => $users,
        'role_filter' => $role_filter,
        'status_filter' => $status_filter,
        'search' => $search
    ];
    
    render('admin/users', $data);
}

// Show create user form
function admin_createUser() {
    requireRole('admin');
    render('admin/create_user');
}

// Process create user
function admin_createUserProcess() {
    requireRole('admin');
    
    // Validation
    $name = getPost('name', '');
    $email = getPost('email', '');
    $password = getPost('password', '');
    $role = getPost('role', 'customer');
    $phone = getPost('phone', '');
    $address = getPost('address', '');
    
    $errors = [];
    
    if (strlen($name) < 3) {
        $errors[] = "Name must be at least 3 characters";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (userEmailExists($email)) {
        $errors[] = "Email already registered";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if (!in_array($role, ['admin', 'inventory_manager', 'customer'])) {
        $errors[] = "Invalid role selected";
    }
    
    if (!empty($errors)) {
        setFlash(implode(', ', $errors), 'error');
        redirectTo('admin/createUser');
        return;
    }
    
    // Create user (ensure correct parameter mapping: phone, dob, address)
    $user_id = userCreate($name, $email, $password, $role, $phone, null, $address);
    
    if ($user_id) {
        setFlash("User created successfully", 'success');
        redirectTo('admin/users');
    } else {
        setFlash("Failed to create user", 'error');
        redirectTo('admin/createUser');
    }
}

// Show edit user form
function admin_editUser($user_id) {
    requireRole('admin');
    
    $user = userGetById($user_id);
    
    if (!$user) {
        setFlash('User not found', 'error');
        redirectTo('admin/users');
        return;
    }
    
    $data = ['user' => $user];
    render('admin/edit_user', $data);
}

// Process edit user
function admin_editUserProcess() {
    requireRole('admin');
    
    $user_id = getPost('user_id', 0);
    // Name and email are non-editable; fetch current values
    $existing = userGetById($user_id);
    if (!$existing) {
        setFlash('User not found', 'error');
        redirectTo('admin/users');
        return;
    }
    $name = $existing['name'];
    $email = $existing['email'];
    $role = getPost('role', '');
    $phone = getPost('phone', '');
    $address = getPost('address', '');
    $password = getPost('password', '');
    
    $errors = [];
    // Skip name/email validation since they are read-only
    
    if (!in_array($role, ['admin', 'inventory_manager', 'customer'])) {
        $errors[] = "Invalid role selected";
    }
    
    if (!empty($errors)) {
        setFlash(implode(', ', $errors), 'error');
        redirectTo('admin/editUser/' . $user_id);
        return;
    }
    
    // Update user (ensure correct parameter mapping: phone, dob, address)
    $success = userUpdate($user_id, $name, $phone, null, $address);
    
    // Update role if changed
    if ($success) {
        userUpdateRole($user_id, $role);
        
        // Update password if provided
        if (!empty($password) && strlen($password) >= 6) {
            userUpdatePassword($user_id, $password);
        }
        
        setFlash("User updated successfully", 'success');
    } else {
        setFlash("Failed to update user", 'error');
    }
    
    redirectTo('admin/users');
}

// Deactivate user
function admin_deactivateUser($user_id) {
    requireRole('admin');
    
    // Prevent self-deactivation
    $currentUserId = getCurrentUserId();
    if ($user_id == $currentUserId) {
        setFlash('Cannot deactivate your own account', 'error');
        redirectTo('admin/users');
        return;
    }
    
    $success = userSetStatus($user_id, 'inactive');
    
    if ($success) {
        setFlash('User deactivated successfully', 'success');
    } else {
        setFlash('Failed to deactivate user', 'error');
    }
    
    redirectTo('admin/users');
}

// Activate user
function admin_activateUser($user_id) {
    requireRole('admin');
    
    $success = userSetStatus($user_id, 'active');
    
    if ($success) {
        setFlash('User activated successfully', 'success');
    } else {
        setFlash('Failed to activate user', 'error');
    }
    
    redirectTo('admin/users');
}

// Delete user
function admin_deleteUser($user_id) {
    requireRole('admin');
    
    // Prevent self-deletion
    $currentUserId = getCurrentUserId();
    if ($user_id == $currentUserId) {
        setFlash('Cannot delete your own account', 'error');
        redirectTo('admin/users');
        return;
    }
    
    $success = userDelete($user_id);
    
    if ($success) {
        setFlash('User deleted successfully', 'success');
    } else {
        setFlash('Failed to delete user', 'error');
    }
    
    redirectTo('admin/users');
}

// ============= TRANSACTION HISTORY =============

function admin_transactionHistory() {
    requireRole('admin');
    
    $status_filter = getGet('status', null);
    $date_from = getGet('date_from', date('Y-m-d', strtotime('-30 days')));
    $date_to = getGet('date_to', date('Y-m-d'));
    
    if ($status_filter) {
        $transactions = transactionGetByStatus($status_filter, $date_from, $date_to);
    } else {
        $transactions = transactionGetByDateRange($date_from, $date_to);
    }
    
    // Get user names for transactions
    foreach ($transactions as &$transaction) {
        $user = userGetById($transaction['user_id']);
        $transaction['user_name'] = $user ? $user['name'] : 'Unknown';
    }
    
    $data = [
        'transactions' => $transactions,
        'status_filter' => $status_filter,
        'date_from' => $date_from,
        'date_to' => $date_to
    ];
    
    render('admin/transaction_history', $data);
}

// ============= SYSTEM CONFIGURATION =============

function admin_systemConfig() {
    requireRole('admin');
    
    // Get all configurations
    $configs = systemConfigGetAll();
    
    // Convert to key-value array for easier access
    $settings = [];
    foreach ($configs as $config) {
        $settings[$config['config_key']] = $config['value'];
    }
    
    $data = ['settings' => $settings];
    render('admin/system_config', $data);
}

function admin_saveSystemConfig() {
    requireRole('admin');
    
    $pharmacy_name = getPost('pharmacy_name', '');
    $contact_email = getPost('contact_email', '');
    $contact_phone = getPost('contact_phone', '');
    $address = getPost('address', '');
    $tax_rate = getPost('tax_rate', '0');
    
    // Save configurations
    systemConfigSet('pharmacy_name', $pharmacy_name);
    systemConfigSet('contact_email', $contact_email);
    systemConfigSet('contact_phone', $contact_phone);
    systemConfigSet('address', $address);
    systemConfigSet('tax_rate', $tax_rate);
    
    setFlash('System configuration updated successfully', 'success');
    redirectTo('admin/systemConfig');
}

// ============= INVENTORY OVERSIGHT =============

function admin_inventory() {
    requireRole('admin');
    
    $search = getGet('search', '');
    $category_filter = getGet('category_id', '');
    $status_filter = getGet('status', 'all');

    // Build products query same as inventory manager (with category join)
    $db = getConnection();
    $where = [];
    $params = [];
    $types = '';

    if ($search) {
        $where[] = "(p.name LIKE ? OR p.generic_name LIKE ?)";
        $s = '%' . $search . '%';
        $params[] = $s; $params[] = $s; $types .= 'ss';
    }
    if (!empty($category_filter)) {
        $where[] = 'p.category_id = ?';
        $params[] = (int)$category_filter; $types .= 'i';
    }
    if ($status_filter && $status_filter !== 'all') {
        $where[] = 'p.status = ?';
        $params[] = $status_filter; $types .= 's';
    }

    $whereClause = !empty($where) ? ('WHERE ' . implode(' AND ', $where)) : '';
    $query = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id {$whereClause} ORDER BY p.name ASC";

    if (!empty($params)) {
        $stmt = $db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $products = $db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    // Categories for filter
    $categories = categoryGetAll();

    // Overview lists for admin
    $lowStockProducts = productGetLowStock();
    $expiringProducts = productGetExpiring(30);
    $expiredProducts = productGetExpired();

    $data = [
        'products' => $products,
        'categories' => $categories,
        'search' => $search,
        'category_id' => $category_filter,
        'status_filter' => $status_filter,
        'lowStockProducts' => $lowStockProducts,
        'expiringProducts' => $expiringProducts,
        'expiredProducts' => $expiredProducts
    ];
    
    render('admin/inventory_oversight', $data);
}

// Admin-only: Delete product
function admin_deleteProduct($product_id) {
    requireRole('admin');
    if (!$product_id || !productExists($product_id)) {
        setFlash('Product not found', 'error');
        redirectTo('admin/inventory');
        return;
    }
    $ok = productDelete((int)$product_id);
    if ($ok) {
        setFlash('Product deleted successfully', 'success');
    } else {
        setFlash('Failed to delete product', 'error');
    }
    redirectTo('admin/inventory');
}

// Admin-only: Edit product form
function admin_editProduct() {
    requireRole('admin');
    
    $product_id = (int)getGet('id', 0);
    
    if (!$product_id || !productExists($product_id)) {
        setFlash('Product not found', 'error');
        redirectTo('admin/inventory');
        return;
    }
    
    $product = productGetById($product_id);
    $categories = categoryGetAll();
    
    $data = [
        'product' => $product,
        'categories' => $categories
    ];
    
    render('admin/edit_product', $data);
}

// Admin-only: Process edit product form
function admin_editProductProcess() {
    requireRole('admin');
    
    $product_id = (int)getPost('product_id');
    $name = trim(getPost('name', ''));
    $generic_name = trim(getPost('generic_name', ''));
    $category_id = (int)getPost('category_id');
    $price = (float)getPost('price', 0);
    $quantity = (int)getPost('quantity', 0);
    $description = trim(getPost('description', ''));
    $status = getPost('status', 'available');
    $manufacture_date = getPost('manufacture_date', '');
    $expiry_date = getPost('expiry_date', '');
    $low_stock_threshold = (int)getPost('low_stock_threshold', 0);
    
    $errors = [];
    
    if (!$product_id || !productExists($product_id)) {
        $errors[] = 'Invalid product';
    }
    
    if (strlen($name) < 3 || strlen($name) > 150) {
        $errors[] = 'Product name must be 3-150 characters';
    }
    
    if (!$category_id) {
        $errors[] = 'Please select a category';
    }
    
    if ($price <= 0) {
        $errors[] = 'Price must be greater than 0';
    }
    
    if ($quantity < 0) {
        $errors[] = 'Quantity cannot be negative';
    }
    
    if (empty($expiry_date)) {
        $errors[] = 'Expiry date is required';
    } elseif ($expiry_date <= date('Y-m-d')) {
        $errors[] = 'Expiry date must be in the future';
    }
    
    if ($low_stock_threshold < 0) {
        $errors[] = 'Low stock threshold cannot be negative';
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = [
            'name' => $name,
            'generic_name' => $generic_name,
            'category_id' => $category_id,
            'price' => $price,
            'quantity' => $quantity,
            'description' => $description,
            'status' => $status,
            'manufacture_date' => $manufacture_date,
            'expiry_date' => $expiry_date,
            'low_stock_threshold' => $low_stock_threshold
        ];
        redirectTo('admin/editProduct?id=' . $product_id);
        return;
    }
    
    $ok = productUpdate(
        $product_id,
        $name,
        $generic_name,
        $description,
        $price,
        $low_stock_threshold,
        $manufacture_date,
        $expiry_date,
        $category_id,
        $quantity,
        $status
    );
    
    if ($ok) {
        setFlash('Product updated successfully', 'success');
    } else {
        setFlash('Failed to update product', 'error');
    }
    
    redirectTo('admin/inventory');
}

// ============= REPORTS =============

function admin_reports() {
    requireRole('admin');
    render('admin/reports');
}

function admin_generateSalesReport() {
    requireRole('admin');
    
    $report_type = getPost('report_type', 'monthly');
    $month = getPost('month', date('m'));
    $year = getPost('year', date('Y'));
    
    // Calculate date range
    if ($report_type === 'monthly') {
        $start_date = "$year-$month-01";
        $end_date = date('Y-m-t', strtotime($start_date));
    } else {
        $start_date = "$year-01-01";
        $end_date = "$year-12-31";
    }
    
    // Get data for report
    $orders = orderGetByDateRange($start_date, $end_date, 'completed');
    $revenue = orderGetRevenue($start_date, $end_date);
    $topProducts = orderItemGetTopProducts(10);
    
    $data = [
        'report_type' => $report_type,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'orders' => $orders,
        'revenue' => $revenue,
        'topProducts' => $topProducts
    ];
    
    render('admin/sales_report', $data);
}
?>