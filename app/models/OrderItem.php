<?php
// OrderItem Model - Procedural functions for order_items table

function orderItemGetById($order_item_id) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT * FROM order_items WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $order_item_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function orderItemsGetByOrder($order_id) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT * FROM order_items WHERE order_id = ? ORDER BY id ASC');
    if (!$stmt) {
        return [];
    }
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function orderItemAdd($order_id, $product_id, $quantity, $price) {
    $db = getConnection();
    $subtotal = $quantity * $price;
    $stmt = $db->prepare('INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('iiidd', $order_id, $product_id, $quantity, $price, $subtotal);
    if ($stmt->execute()) {
        return $db->insert_id;
    }
    return false;
}

function orderItemUpdateQuantity($order_item_id, $quantity) {
    $db = getConnection();
    $item = orderItemGetById($order_item_id);
    if (!$item) {
        return false;
    }
    $subtotal = $quantity * $item['price'];
    $stmt = $db->prepare('UPDATE order_items SET quantity = ?, subtotal = ?, updated_at = NOW() WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('idi', $quantity, $subtotal, $order_item_id);
    return $stmt->execute();
}

function orderItemAdjustQuantity($order_item_id, $delta) {
    $item = orderItemGetById($order_item_id);
    if (!$item) {
        return false;
    }
    $new_quantity = max(0, $item['quantity'] + $delta);
    return orderItemUpdateQuantity($order_item_id, $new_quantity);
}

function orderItemUpdatePrice($order_item_id, $price) {
    $db = getConnection();
    $item = orderItemGetById($order_item_id);
    if (!$item) {
        return false;
    }
    $subtotal = $item['quantity'] * $price;
    $stmt = $db->prepare('UPDATE order_items SET price = ?, subtotal = ?, updated_at = NOW() WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('ddi', $price, $subtotal, $order_item_id);
    return $stmt->execute();
}

function orderItemDelete($order_item_id) {
    $db = getConnection();
    $stmt = $db->prepare('DELETE FROM order_items WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $order_item_id);
    return $stmt->execute();
}

function orderItemsDeleteByOrder($order_id) {
    $db = getConnection();
    $stmt = $db->prepare('DELETE FROM order_items WHERE order_id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $order_id);
    return $stmt->execute();
}

function orderItemsTotals($order_id) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT COUNT(*) as item_count, SUM(quantity) as total_quantity, SUM(subtotal) as total_amount FROM order_items WHERE order_id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function orderItemExists($order_id, $product_id) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT id FROM order_items WHERE order_id = ? AND product_id = ? LIMIT 1');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('ii', $order_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function orderItemGetQuantity($order_id, $product_id) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT quantity FROM order_items WHERE order_id = ? AND product_id = ? LIMIT 1');
    if (!$stmt) {
        return 0;
    }
    $stmt->bind_param('ii', $order_id, $product_id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    return $data ? (int)$data['quantity'] : 0;
}
?>