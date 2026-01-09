<?php
// Order Model - Order data operations (Procedural)

// Get order by ID
function orderGetById($id) {
    return getById('orders', $id);
}

// Get all orders
function orderGetAll() {
    return getAllRecords('orders');
}

// Get orders by user ID
function orderGetByUser($userId) {
    return fetchAll('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC', 'i', [$userId]);
}

// Create new order
function orderCreate($data) {
    $data['created_at'] = date('Y-m-d H:i:s');
    $data['status'] = $data['status'] ?? 'pending';
    
    return insertRecord('orders', $data);
}

// Update order
function orderUpdate($orderId, $data) {
    $data['updated_at'] = date('Y-m-d H:i:s');
    return updateRecord('orders', $data, 'id = ?', [$orderId]);
}

// Update order status
function orderUpdateStatus($orderId, $status) {
    return updateRecord('orders', ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')], 'id = ?', [$orderId]);
}

// Delete order
function orderDelete($orderId) {
    return deleteRecord('orders', 'id = ?', [$orderId]);
}

// Get order items
function orderGetItems($orderId) {
    return fetchAll(
        'SELECT oi.*, p.name, p.generic_name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?',
        'i',
        [$orderId]
    );
}

// Get orders by status
function orderGetByStatus($status) {
    return fetchAll('SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC', 's', [$status]);
}

// Count orders by status
function orderCountByStatus($status) {
    return countRecords('orders', 'status = ?', [$status]);
}

// Get total revenue
function orderGetTotalRevenue($status = 'completed') {
    $result = fetchOne('SELECT SUM(total_amount) as revenue FROM orders WHERE status = ?', 's', [$status]);
    return $result['revenue'] ?? 0;
}

// Get orders within date range
function orderGetByDateRange($startDate, $endDate) {
    return fetchAll(
        'SELECT * FROM orders WHERE DATE(created_at) BETWEEN ? AND ? ORDER BY created_at DESC',
        'ss',
        [$startDate, $endDate]
    );
}

// Count total orders
function orderCount() {
    return countRecords('orders');
}

// Get average order value
function orderGetAverageValue() {
    $result = fetchOne('SELECT AVG(total_amount) as average FROM orders');
    return $result['average'] ?? 0;
}
?>