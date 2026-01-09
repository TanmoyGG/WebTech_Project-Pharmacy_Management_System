<?php
// Order Controller - Order processing and management
// All functions follow procedural pattern: order_[action]()

// Display order details
function order_view() {
    requireAuth();
    
    $orderId = getUrlParam(1);
    
    if (isEmpty($orderId)) {
        setFlash('Order not found', 'error');
        redirectTo('customer/order_history');
    }
    
    $order = getById('orders', $orderId);
    
    if (!$order) {
        setFlash('Order not found', 'error');
        redirectTo('customer/order_history');
    }
    
    $userId = getCurrentUserId();
    $role = getCurrentUserRole();
    
    if ($order['user_id'] != $userId && $role !== 'admin') {
        setFlash('Unauthorized access', 'error');
        redirectTo('customer/order_history');
    }
    
    $orderItems = fetchAll(
        'SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?',
        'i',
        [$orderId]
    );
    
    $data = [
        'order' => $order,
        'orderItems' => $orderItems
    ];
    
    render('customer/order_details', $data);
}

// Update order status (Admin only)
function order_updateStatus() {
    requireRole('admin');
    
    if (!isPost()) {
        redirectTo('admin/transaction_history');
    }
    
    $orderId = getPost('order_id', '');
    $status = getPost('status', '');
    
    updateRecord('orders', ['status' => $status], 'id = ?', [$orderId]);
    
    setFlash('Order status updated successfully', 'success');
    redirectTo('admin/transaction_history');
}

// Cancel order
function order_cancel() {
    requireAuth();
    
    if (!isPost()) {
        redirectTo('customer/order_history');
    }
    
    $orderId = getPost('order_id', '');
    
    if (isEmpty($orderId)) {
        setFlash('Invalid order', 'error');
        redirectTo('customer/order_history');
    }
    
    $order = getById('orders', $orderId);
    $userId = getCurrentUserId();
    
    if ($order['user_id'] != $userId) {
        setFlash('Unauthorized access', 'error');
        redirectTo('customer/order_history');
    }
    
    if ($order['status'] !== 'pending') {
        setFlash('Cannot cancel this order', 'error');
        redirectTo('customer/order_history');
    }
    
    updateRecord('orders', ['status' => 'cancelled'], 'id = ?', [$orderId]);
    
    setFlash('Order cancelled successfully', 'success');
    redirectTo('customer/order_history');
}
?>