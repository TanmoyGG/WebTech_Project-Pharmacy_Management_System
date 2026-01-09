<?php
// Inventory Manager Controller - Manages products, stock, categories
// All functions follow procedural pattern: inventory_manager_[action]()

// Display inventory manager dashboard
function inventory_manager_dashboard() {
    requireRole('inventory_manager');
    
    $totalProducts = countRecords('products');
    $lowStockProducts = getLowStockProducts();
    $expiringProducts = getExpiringProducts();
    
    $data = [
        'totalProducts' => $totalProducts,
        'lowStockProducts' => $lowStockProducts,
        'expiringProducts' => $expiringProducts
    ];
    
    render('inventory_manager/dashboard', $data);
}

// Helper functions
function getLowStockProducts($threshold = 10) {
    return fetchAll('SELECT * FROM products WHERE quantity <= ? ORDER BY quantity ASC', 'i', [$threshold]);
}

function getExpiringProducts($days = 30) {
    $expiryDate = date('Y-m-d', strtotime("+$days days"));
    return fetchAll('SELECT * FROM products WHERE expiry_date <= ? AND expiry_date > NOW() ORDER BY expiry_date ASC', 's', [$expiryDate]);
}
?>