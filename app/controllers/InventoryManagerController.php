<?php
// Inventory Manager Controller - Manages products, stock, categories, and orders
// All functions follow procedural pattern: inventory_manager_[action]()

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Order.php';

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

// View all pending orders
function inventory_manager_orders() {
    requireRole('inventory_manager');
    
    $db = getConnection();
    
    // Get filter from query string (status: pending, confirmed, shipped, all)
    $statusFilter = getGet('status', 'pending');
    
    if ($statusFilter === 'all') {
        $stmt = $db->prepare('
            SELECT o.id, o.user_id, o.total_amount, o.status, o.delivery_address, o.created_at,
                   u.name as customer_name, u.email as customer_email, u.phone as customer_phone,
                   COUNT(oi.id) as item_count
            FROM orders o
            JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ');
    } else {
        $stmt = $db->prepare('
            SELECT o.id, o.user_id, o.total_amount, o.status, o.delivery_address, o.created_at,
                   u.name as customer_name, u.email as customer_email, u.phone as customer_phone,
                   COUNT(oi.id) as item_count
            FROM orders o
            JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.status = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ');
        $stmt->bind_param('s', $statusFilter);
    }
    
    $stmt->execute();
    $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Count orders by status
    $statusCounts = [];
    foreach (['pending', 'confirmed', 'shipped', 'completed', 'cancelled'] as $status) {
        $countStmt = $db->prepare('SELECT COUNT(*) as count FROM orders WHERE status = ?');
        $countStmt->bind_param('s', $status);
        $countStmt->execute();
        $result = $countStmt->get_result()->fetch_assoc();
        $statusCounts[$status] = $result['count'];
    }
    
    render('inventory_manager/orders', [
        'orders' => $orders,
        'statusFilter' => $statusFilter,
        'statusCounts' => $statusCounts
    ]);
}

// View order details
function inventory_manager_orderDetails() {
    requireRole('inventory_manager');
    
    $order_id = getGet('id');
    if (!$order_id) {
        setFlash('Order not found', 'error');
        redirectTo('inventory_manager/orders');
    }
    
    $db = getConnection();
    
    // Get order details
    $stmt = $db->prepare('
        SELECT o.*, u.name as customer_name, u.email, u.phone, u.address
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ');
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    
    if (!$order) {
        setFlash('Order not found', 'error');
        redirectTo('inventory_manager/orders');
    }
    
    // Get order items
    $itemStmt = $db->prepare('
        SELECT oi.*, p.name as product_name, p.generic_name, c.name as category_name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE oi.order_id = ?
    ');
    $itemStmt->bind_param('i', $order_id);
    $itemStmt->execute();
    $items = $itemStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    render('inventory_manager/order_details', [
        'order' => $order,
        'items' => $items
    ]);
}

// Confirm order (change status from pending to confirmed)
function inventory_manager_confirmOrder() {
    requireRole('inventory_manager');
    
    if (!isPost()) {
        redirectTo('inventory_manager/orders');
    }
    
    $order_id = (int) getPost('order_id');
    
    $db = getConnection();
    
    // Get order and verify it's pending
    $stmt = $db->prepare('SELECT * FROM orders WHERE id = ? AND status = "pending"');
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    
    if (!$order) {
        setFlash('Order not found or already processed', 'error');
        redirectTo('inventory_manager/orders');
    }
    
    // Update order status to confirmed
    $newStatus = 'confirmed';
    $updateStmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $updateStmt->bind_param('si', $newStatus, $order_id);
    $updateStmt->execute();
    
    // Decrement product quantities based on order items
    $itemStmt = $db->prepare('SELECT product_id, quantity FROM order_items WHERE order_id = ?');
    $itemStmt->bind_param('i', $order_id);
    $itemStmt->execute();
    $items = $itemStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    foreach ($items as $item) {
        // Decrement product quantity
        $decrementStmt = $db->prepare('UPDATE products SET quantity = quantity - ? WHERE id = ?');
        $decrementStmt->bind_param('ii', $item['quantity'], $item['product_id']);
        $decrementStmt->execute();
    }
    
    setFlash('Order confirmed and inventory updated!', 'success');
    redirectTo('inventory_manager/orderDetails?id=' . $order_id);
}

// Ship order (change status from confirmed to shipped)
function inventory_manager_shipOrder() {
    requireRole('inventory_manager');
    
    if (!isPost()) {
        redirectTo('inventory_manager/orders');
    }
    
    $order_id = (int) getPost('order_id');
    
    $db = getConnection();
    
    // Get order and verify it's confirmed
    $stmt = $db->prepare('SELECT * FROM orders WHERE id = ? AND status = "confirmed"');
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    
    if (!$order) {
        setFlash('Order not found or not ready to ship', 'error');
        redirectTo('inventory_manager/orders');
    }
    
    // Update order status to shipped
    $newStatus = 'shipped';
    $updateStmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $updateStmt->bind_param('si', $newStatus, $order_id);
    $updateStmt->execute();
    
    setFlash('Order marked as shipped!', 'success');
    redirectTo('inventory_manager/orderDetails?id=' . $order_id);
}

// Cancel order (change status to cancelled and restore inventory)
function inventory_manager_cancelOrder() {
    requireRole('inventory_manager');
    
    if (!isPost()) {
        redirectTo('inventory_manager/orders');
    }
    
    $order_id = (int) getPost('order_id');
    $cancel_reason = sanitize(getPost('cancel_reason', 'No reason provided'));
    
    $db = getConnection();
    
    // Get order and verify it's not already completed/cancelled
    $stmt = $db->prepare('SELECT * FROM orders WHERE id = ? AND status IN ("pending", "confirmed")');
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    
    if (!$order) {
        setFlash('Order cannot be cancelled', 'error');
        redirectTo('inventory_manager/orders');
    }
    
    // Update order status to cancelled
    $newStatus = 'cancelled';
    $updateStmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $updateStmt->bind_param('si', $newStatus, $order_id);
    $updateStmt->execute();
    
    // If order was confirmed, restore inventory
    if ($order['status'] === 'confirmed') {
        $itemStmt = $db->prepare('SELECT product_id, quantity FROM order_items WHERE order_id = ?');
        $itemStmt->bind_param('i', $order_id);
        $itemStmt->execute();
        $items = $itemStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        foreach ($items as $item) {
            // Restore product quantity
            $restoreStmt = $db->prepare('UPDATE products SET quantity = quantity + ? WHERE id = ?');
            $restoreStmt->bind_param('ii', $item['quantity'], $item['product_id']);
            $restoreStmt->execute();
        }
    }
    
    setFlash('Order cancelled successfully!', 'success');
    redirectTo('inventory_manager/orders');
}
?>