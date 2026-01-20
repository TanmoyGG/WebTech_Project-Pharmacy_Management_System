<?php
// Order Controller - Order processing and management

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';
require_once __DIR__ . '/../models/Product.php';

// Display order details
function order_view() {
    requireAuth();
    
    $orderId = getUrlParam(1);
    
    if (isEmpty($orderId)) {
        setFlash('Order not found', 'error');
        redirectTo('customer/order_history');
    }
    
    $order = orderGetById($orderId);
    
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
    
    $orderItems = orderItemsGetByOrder($orderId);
    
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
    
    orderSetStatus($orderId, $status);
    
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
    
    $order = orderGetById($orderId);
    $userId = getCurrentUserId();
    
    if ($order['user_id'] != $userId) {
        setFlash('Unauthorized access', 'error');
        redirectTo('customer/order_history');
    }
    
    if ($order['status'] !== 'pending') {
        setFlash('Cannot cancel this order', 'error');
        redirectTo('customer/order_history');
    }
    
    orderSetStatus($orderId, 'cancelled');
    
    setFlash('Order cancelled successfully', 'success');
    redirectTo('customer/order_history');
}
?>