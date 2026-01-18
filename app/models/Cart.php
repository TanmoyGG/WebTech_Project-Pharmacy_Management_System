<?php
// Cart Model - Procedural functions for carts and cart_items tables

require_once __DIR__ . '/SystemConfig.php';

// Guard against multiple inclusions
if (function_exists('cartValidStatus')) {
    return;
}

function cartValidStatus($status) {
    $allowed = ['active', 'checked_out', 'abandoned'];
    return in_array($status, $allowed) ? $status : 'active';
}

function cartGetById($cart_id) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT * FROM carts WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $cart_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function cartGetActiveByUser($user_id) {
    $db = getConnection();
    $stmt = $db->prepare("SELECT * FROM carts WHERE user_id = ? AND status = 'active' ORDER BY created_at DESC LIMIT 1");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function cartCreate($user_id) {
    $db = getConnection();
    $stmt = $db->prepare("INSERT INTO carts (user_id, status, created_at) VALUES (?, 'active', NOW())");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $user_id);
    if ($stmt->execute()) {
        return $db->insert_id;
    }
    return false;
}

function cartGetOrCreate($user_id) {
    $cart = cartGetActiveByUser($user_id);
    if ($cart) {
        return $cart['id'];
    }
    return cartCreate($user_id);
}

function cartSetStatus($cart_id, $status) {
    $db = getConnection();
    $status = cartValidStatus($status);
    $stmt = $db->prepare('UPDATE carts SET status = ?, updated_at = NOW() WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('si', $status, $cart_id);
    return $stmt->execute();
}

function cartAddItem($cart_id, $product_id, $quantity, $price) {
    $db = getConnection();
    $stmt = $db->prepare('INSERT INTO cart_items (cart_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('iiid', $cart_id, $product_id, $quantity, $price);
    if ($stmt->execute()) {
        return $db->insert_id;
    }
    return false;
}

function cartUpdateItemQuantity($cart_item_id, $quantity) {
    $db = getConnection();
    $item = cartGetItemById($cart_item_id);
    if (!$item) {
        return false;
    }
    // Only update quantity (subtotal is calculated on the fly in cartGetItems())
    $stmt = $db->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('ii', $quantity, $cart_item_id);
    return $stmt->execute();
}

function cartAdjustItemQuantity($cart_item_id, $delta) {
    $item = cartGetItemById($cart_item_id);
    if (!$item) {
        return false;
    }
    $new_quantity = max(0, $item['quantity'] + $delta);
    if ($new_quantity === 0) {
        return cartRemoveItem($cart_item_id);
    }
    return cartUpdateItemQuantity($cart_item_id, $new_quantity);
}

function cartGetItemById($cart_item_id) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT * FROM cart_items WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $cart_item_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function cartRemoveItem($cart_item_id) {
    $db = getConnection();
    $stmt = $db->prepare('DELETE FROM cart_items WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $cart_item_id);
    return $stmt->execute();
}

function cartRemoveItemByProduct($cart_id, $product_id) {
    $db = getConnection();
    $stmt = $db->prepare('DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('ii', $cart_id, $product_id);
    return $stmt->execute();
}

function cartClear($cart_id) {
    $db = getConnection();
    $stmt = $db->prepare('DELETE FROM cart_items WHERE cart_id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $cart_id);
    return $stmt->execute();
}

function cartDelete($cart_id) {
    cartClear($cart_id);
    $db = getConnection();
    $stmt = $db->prepare('DELETE FROM carts WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $cart_id);
    return $stmt->execute();
}

function cartGetItems($cart_id) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT ci.*, p.name as product_name, (ci.quantity * ci.price) as subtotal FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.cart_id = ? ORDER BY ci.id ASC');
    if (!$stmt) {
        return [];
    }
    $stmt->bind_param('i', $cart_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function cartCalculateTotals($cart_id) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT COUNT(*) as item_count, SUM(quantity) as total_quantity, SUM(quantity * price) as subtotal FROM cart_items WHERE cart_id = ?');
    if (!$stmt) {
        return ['item_count' => 0, 'total_quantity' => 0, 'subtotal' => 0, 'tax' => 0, 'total' => 0];
    }
    $stmt->bind_param('i', $cart_id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    
    if (!$data || $data['item_count'] == 0) {
        return ['item_count' => 0, 'total_quantity' => 0, 'subtotal' => 0, 'tax' => 0, 'total' => 0];
    }
    
    $subtotal = (float) ($data['subtotal'] ?? 0);
    // Get tax rate from system configuration (as percentage)
    $tax_rate_percent = (float) systemConfigGetValue('tax_rate', '5');
    $tax_rate = $tax_rate_percent / 100;  // Convert percentage to decimal
    $tax = $subtotal * $tax_rate;
    $total = $subtotal + $tax;
    
    return [
        'item_count' => $data['item_count'],
        'total_quantity' => $data['total_quantity'],
        'subtotal' => $subtotal,
        'tax' => $tax,
        'total' => $total
    ];
}

function cartGetByStatus($status, $limit = null, $offset = 0) {
    $db = getConnection();
    $status = cartValidStatus($status);
    $query = 'SELECT * FROM carts WHERE status = ? ORDER BY created_at DESC';
    if ($limit !== null) {
        $query .= ' LIMIT ? OFFSET ?';
    }
    $stmt = $db->prepare($query);
    if (!$stmt) {
        return [];
    }
    if ($limit !== null) {
        $stmt->bind_param('sii', $status, $limit, $offset);
    } else {
        $stmt->bind_param('s', $status);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function cartGetPaginated($page = 1, $per_page = 20, $status = 'active') {
    $offset = ($page - 1) * $per_page;
    return cartGetByStatus($status, $per_page, $offset);
}

function cartHasProduct($cart_id, $product_id) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT id FROM cart_items WHERE cart_id = ? AND product_id = ? LIMIT 1');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('ii', $cart_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function cartGetCount($status = null) {
    $db = getConnection();
    if ($status === null) {
        $result = $db->query('SELECT COUNT(*) as count FROM carts');
        $data = $result ? $result->fetch_assoc() : ['count' => 0];
        return $data['count'];
    }
    $status = cartValidStatus($status);
    $stmt = $db->prepare('SELECT COUNT(*) as count FROM carts WHERE status = ?');
    if (!$stmt) {
        return 0;
    }
    $stmt->bind_param('s', $status);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    return $data['count'];
}
?>