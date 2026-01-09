<?php
// Order Model - Procedural database functions for unified orders table

function orderValidStatus($status) {
    $allowed = ['pending', 'processing', 'completed', 'cancelled', 'shipped', 'delivered'];
    return in_array($status, $allowed) ? $status : 'pending';
}

function orderBindParams($stmt, $types, $params) {
    $bind = array_merge([$types], $params);
    $refs = [];
    foreach ($bind as $k => $v) {
        $refs[$k] = &$bind[$k];
    }
    call_user_func_array([$stmt, 'bind_param'], $refs);
}

function orderGetById($order_id) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT * FROM orders WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function orderCreate($user_id, $total_amount, $status = 'pending', $delivery_address = null, $cart_id = null) {
    $db = getConnection();
    $status = orderValidStatus($status);
    $stmt = $db->prepare('INSERT INTO orders (user_id, total_amount, status, delivery_address, cart_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('idssi', $user_id, $total_amount, $status, $delivery_address, $cart_id);
    if ($stmt->execute()) {
        return $db->insert_id;
    }
    return false;
}

function orderUpdate($order_id, $total_amount = null, $status = null, $delivery_address = null, $cart_id = null) {
    $db = getConnection();
    $updates = [];
    $params = [];
    $types = '';

    if ($total_amount !== null) {
        $updates[] = 'total_amount = ?';
        $params[] = $total_amount;
        $types .= 'd';
    }

    if ($status !== null) {
        $updates[] = 'status = ?';
        $params[] = orderValidStatus($status);
        $types .= 's';
    }

    if ($delivery_address !== null) {
        $updates[] = 'delivery_address = ?';
        $params[] = $delivery_address;
        $types .= 's';
    }

    if ($cart_id !== null) {
        $updates[] = 'cart_id = ?';
        $params[] = $cart_id;
        $types .= 'i';
    }

    if (empty($updates)) {
        return false;
    }

    $updates[] = 'updated_at = NOW()';
    $params[] = $order_id;
    $types .= 'i';

    $query = 'UPDATE orders SET ' . implode(', ', $updates) . ' WHERE id = ?';
    $stmt = $db->prepare($query);
    if (!$stmt) {
        return false;
    }
    orderBindParams($stmt, $types, $params);
    return $stmt->execute();
}

function orderDelete($order_id) {
    $db = getConnection();
    $stmt = $db->prepare('DELETE FROM orders WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $order_id);
    return $stmt->execute();
}

function orderSetStatus($order_id, $status) {
    $db = getConnection();
    $status = orderValidStatus($status);
    $stmt = $db->prepare('UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('si', $status, $order_id);
    return $stmt->execute();
}

function orderGetAll($status = null, $limit = null, $offset = 0) {
    $db = getConnection();
    $query = 'SELECT * FROM orders';
    $params = [];
    $types = '';

    if ($status !== null) {
        $query .= ' WHERE status = ?';
        $params[] = $status;
        $types .= 's';
    }

    $query .= ' ORDER BY created_at DESC';

    if ($limit !== null) {
        $query .= ' LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
    }

    $stmt = $db->prepare($query);
    if (!$stmt) {
        return [];
    }

    if (!empty($params)) {
        orderBindParams($stmt, $types, $params);
    }

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function orderGetByUser($user_id, $status = null, $limit = null, $offset = 0) {
    $db = getConnection();
    $query = 'SELECT * FROM orders WHERE user_id = ?';
    $params = [$user_id];
    $types = 'i';

    if ($status !== null) {
        $query .= ' AND status = ?';
        $params[] = $status;
        $types .= 's';
    }

    $query .= ' ORDER BY created_at DESC';

    if ($limit !== null) {
        $query .= ' LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
    }

    $stmt = $db->prepare($query);
    if (!$stmt) {
        return [];
    }

    orderBindParams($stmt, $types, $params);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function orderGetByDateRange($start_date, $end_date, $status = null, $user_id = null) {
    $db = getConnection();
    $query = 'SELECT * FROM orders WHERE created_at BETWEEN ? AND ?';
    $params = [$start_date, $end_date];
    $types = 'ss';

    if ($status !== null) {
        $query .= ' AND status = ?';
        $params[] = $status;
        $types .= 's';
    }

    if ($user_id !== null) {
        $query .= ' AND user_id = ?';
        $params[] = $user_id;
        $types .= 'i';
    }

    $query .= ' ORDER BY created_at DESC';
    $stmt = $db->prepare($query);
    if (!$stmt) {
        return [];
    }
    orderBindParams($stmt, $types, $params);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function orderSearch($search_term, $limit = 50) {
    $db = getConnection();
    $search = '%' . $search_term . '%';
    $stmt = $db->prepare('SELECT * FROM orders WHERE CAST(id AS CHAR) LIKE ? OR delivery_address LIKE ? ORDER BY created_at DESC LIMIT ?');
    if (!$stmt) {
        return [];
    }
    $stmt->bind_param('ssi', $search, $search, $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function orderCount($status = null) {
    $db = getConnection();
    if ($status === null) {
        $result = $db->query('SELECT COUNT(*) as count FROM orders');
        $data = $result ? $result->fetch_assoc() : ['count' => 0];
        return $data['count'];
    }
    $stmt = $db->prepare('SELECT COUNT(*) as count FROM orders WHERE status = ?');
    if (!$stmt) {
        return 0;
    }
    $stmt->bind_param('s', $status);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    return $data['count'];
}

function orderGetPaginated($page = 1, $per_page = 20, $status = null, $user_id = null) {
    $offset = ($page - 1) * $per_page;
    if ($user_id !== null) {
        return orderGetByUser($user_id, $status, $per_page, $offset);
    }
    return orderGetAll($status, $per_page, $offset);
}

function orderGetRevenue($start_date = null, $end_date = null, $status = 'completed') {
    $db = getConnection();
    $params = [];
    $types = '';
    $query = 'SELECT SUM(total_amount) as total_revenue FROM orders WHERE status = ?';
    $params[] = orderValidStatus($status);
    $types .= 's';

    if ($start_date !== null && $end_date !== null) {
        $query .= ' AND created_at BETWEEN ? AND ?';
        $params[] = $start_date;
        $params[] = $end_date;
        $types .= 'ss';
    }

    $stmt = $db->prepare($query);
    if (!$stmt) {
        return ['total_revenue' => 0];
    }
    orderBindParams($stmt, $types, $params);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function orderGetStats() {
    $db = getConnection();
    $result = $db->query("SELECT 
        COUNT(*) as total_orders,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
        SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
        SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders,
        SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
        SUM(total_amount) as total_revenue
    FROM orders");
    return $result ? $result->fetch_assoc() : [];
}

function orderGetRecent($limit = 10) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT * FROM orders ORDER BY created_at DESC LIMIT ?');
    if (!$stmt) {
        return [];
    }
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function orderUpdateDeliveryAddress($order_id, $delivery_address) {
    $db = getConnection();
    $stmt = $db->prepare('UPDATE orders SET delivery_address = ?, updated_at = NOW() WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('si', $delivery_address, $order_id);
    return $stmt->execute();
}

function orderRecalculateTotals($order_id) {
    $totals = orderItemsTotals($order_id);
    if (!$totals) {
        return false;
    }
    return orderUpdate($order_id, $totals['total_amount']);
}

// Alias for total revenue (all completed orders)
function orderGetTotalRevenue() {
    $result = orderGetRevenue(null, null, 'completed');
    return $result['total_revenue'] ?? 0;
}
?>