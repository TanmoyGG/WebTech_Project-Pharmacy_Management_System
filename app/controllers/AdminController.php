<?php
// Admin Controller - Master Admin functionalities
// All functions follow procedural pattern: admin_[action]()

// Display admin dashboard
function admin_dashboard() {
    requireRole('admin');
    
    $totalUsers = countRecords('users');
    $totalProducts = countRecords('products');
    $totalOrders = countRecords('orders');
    $totalRevenue = getTotalRevenue();
    
    $data = [
        'totalUsers' => $totalUsers,
        'totalProducts' => $totalProducts,
        'totalOrders' => $totalOrders,
        'totalRevenue' => $totalRevenue
    ];
    
    render('admin/dashboard', $data);
}

// Helper function: Get total revenue
function getTotalRevenue() {
    $result = fetchOne('SELECT SUM(total_amount) as revenue FROM orders WHERE status = ?', 's', ['completed']);
    return $result['revenue'] ?? 0;
}
?>