<?php
// OrderItem Model - Order items data operations (Procedural)

// Get order item by ID
function orderItemGetById($id) {
    return getById('order_items', $id);
}

// Get all order items for an order
function orderItemGetByOrder($orderId) {
    return fetchAll(
        'SELECT oi.*, p.name, p.generic_name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?',
        'i',
        [$orderId]
    );
}

// Create order item
function orderItemCreate($orderId, $productId, $quantity, $price) {
    $itemData = [
        'order_id' => $orderId,
        'product_id' => $productId,
        'quantity' => $quantity,
        'price' => $price,
        'subtotal' => $quantity * $price
    ];
    
    return insertRecord('order_items', $itemData);
}

// Update order item
function orderItemUpdate($itemId, $data) {
    return updateRecord('order_items', $data, 'id = ?', [$itemId]);
}

// Delete order item
function orderItemDelete($itemId) {
    return deleteRecord('order_items', 'id = ?', [$itemId]);
}

// Delete all items for order
function orderItemDeleteByOrder($orderId) {
    return deleteRecord('order_items', 'order_id = ?', [$orderId]);
}

// Get item count for order
function orderItemCount($orderId) {
    return countRecords('order_items', 'order_id = ?', [$orderId]);
}

// Get order item total (subtotal)
function orderItemGetTotal($orderId) {
    $result = fetchOne('SELECT SUM(subtotal) as total FROM order_items WHERE order_id = ?', 'i', [$orderId]);
    return $result['total'] ?? 0;
}

// Get popular products (by order quantity)
function orderItemGetPopularProducts($limit = 10) {
    return fetchAll(
        'SELECT p.id, p.name, SUM(oi.quantity) as total_sold FROM order_items oi JOIN products p ON oi.product_id = p.id GROUP BY oi.product_id ORDER BY total_sold DESC LIMIT ?',
        'i',
        [$limit]
    );
}
?>