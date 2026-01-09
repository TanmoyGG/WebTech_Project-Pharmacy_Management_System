<?php
// Admin Controller - Master Admin functionalities
// All functions follow procedural pattern: admin_[action]()

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Transaction.php';

// Display admin dashboard
function admin_dashboard() {
    requireRole('admin');
    
    $userStats = userGetStats();
    $productStats = productGetStats();
    $orderStats = orderGetStats();
    $totalRevenue = orderGetTotalRevenue();
    
    $data = [
        'userStats' => $userStats,
        'productStats' => $productStats,
        'orderStats' => $orderStats,
        'totalRevenue' => $totalRevenue
    ];
    
    render('admin/dashboard', $data);
}
?>