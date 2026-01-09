<?php
// Inventory Manager Controller - Manages products, stock, categories
// All functions follow procedural pattern: inventory_manager_[action]()

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

// Display inventory manager dashboard
function inventory_manager_dashboard() {
    requireRole('inventory_manager');
    
    $productStats = productGetStats();
    $lowStockProducts = productGetLowStock();
    $expiringProducts = productGetExpiring(30);
    
    $data = [
        'productStats' => $productStats,
        'lowStockProducts' => $lowStockProducts,
        'expiringProducts' => $expiringProducts
    ];
    
    render('inventory_manager/dashboard', $data);
}
?>