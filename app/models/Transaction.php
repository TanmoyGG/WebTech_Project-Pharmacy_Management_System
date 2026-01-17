<?php
// Transaction Model - Procedural functions for unified transactions table

function transactionValidStatus($status) {
    $allowed = ['pending', 'completed', 'failed', 'refunded'];
    return in_array($status, $allowed) ? $status : 'pending';
}

function transactionGetById($transaction_id) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT * FROM transactions WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $transaction_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function transactionCreate($order_id, $user_id, $amount, $payment_method, $status = 'pending', $transaction_date = null) {
    $db = getConnection();
    $status = transactionValidStatus($status);
    if ($transaction_date === null) {
        $transaction_date = date('Y-m-d H:i:s');
    }
    $stmt = $db->prepare('INSERT INTO transactions (order_id, user_id, amount, payment_method, status, transaction_date) VALUES (?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('iidsss', $order_id, $user_id, $amount, $payment_method, $status, $transaction_date);
    if ($stmt->execute()) {
        return $db->insert_id;
    }
    return false;
}

function transactionUpdateStatus($transaction_id, $status) {
    $db = getConnection();
    $status = transactionValidStatus($status);
    $stmt = $db->prepare('UPDATE transactions SET status = ?, updated_at = NOW() WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('si', $status, $transaction_id);
    return $stmt->execute();
}

function transactionGetByOrder($order_id) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT * FROM transactions WHERE order_id = ? ORDER BY transaction_date DESC');
    if (!$stmt) {
        return [];
    }
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function transactionGetByUser($user_id = null, $status = null, $limit = null, $offset = 0) {
    $db = getConnection();
    $query = 'SELECT * FROM transactions WHERE 1=1';
    $params = [];
    $types = '';

    if ($user_id !== null) {
        $query .= ' AND user_id = ?';
        $params[] = $user_id;
        $types .= 'i';
    }

    if ($status !== null) {
        $query .= ' AND status = ?';
        $params[] = $status;
        $types .= 's';
    }

    $query .= ' ORDER BY transaction_date DESC';

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
        $bind = array_merge([$types], $params);
        $refs = [];
        foreach ($bind as $k => $v) {
            $refs[$k] = &$bind[$k];
        }
        call_user_func_array([$stmt, 'bind_param'], $refs);
    }

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function transactionGetByDateRange($start_date, $end_date, $status = null, $payment_method = null) {
    $db = getConnection();
    $query = 'SELECT * FROM transactions WHERE transaction_date BETWEEN ? AND ?';
    $params = [$start_date, $end_date];
    $types = 'ss';

    if ($status !== null) {
        $query .= ' AND status = ?';
        $params[] = $status;
        $types .= 's';
    }

    if ($payment_method !== null) {
        $query .= ' AND payment_method = ?';
        $params[] = $payment_method;
        $types .= 's';
    }

    $query .= ' ORDER BY transaction_date DESC';
    $stmt = $db->prepare($query);
    if (!$stmt) {
        return [];
    }

    $bind = array_merge([$types], $params);
    $refs = [];
    foreach ($bind as $k => $v) {
        $refs[$k] = &$bind[$k];
    }
    call_user_func_array([$stmt, 'bind_param'], $refs);

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function transactionGetTotalAmount($status = 'completed', $start_date = null, $end_date = null) {
    $db = getConnection();
    $query = 'SELECT SUM(amount) as total_amount FROM transactions WHERE status = ?';
    $params = [transactionValidStatus($status)];
    $types = 's';

    if ($start_date !== null && $end_date !== null) {
        $query .= ' AND transaction_date BETWEEN ? AND ?';
        $params[] = $start_date;
        $params[] = $end_date;
        $types .= 'ss';
    }

    $stmt = $db->prepare($query);
    if (!$stmt) {
        return ['total_amount' => 0];
    }

    $bind = array_merge([$types], $params);
    $refs = [];
    foreach ($bind as $k => $v) {
        $refs[$k] = &$bind[$k];
    }
    call_user_func_array([$stmt, 'bind_param'], $refs);

    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function transactionGetPaginated($page = 1, $per_page = 20, $status = null, $payment_method = null) {
    $offset = ($page - 1) * $per_page;
    $db = getConnection();
    $query = 'SELECT * FROM transactions WHERE 1=1';
    $params = [];
    $types = '';

    if ($status !== null) {
        $query .= ' AND status = ?';
        $params[] = $status;
        $types .= 's';
    }

    if ($payment_method !== null) {
        $query .= ' AND payment_method = ?';
        $params[] = $payment_method;
        $types .= 's';
    }

    $query .= ' ORDER BY transaction_date DESC LIMIT ? OFFSET ?';
    $params[] = $per_page;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = $db->prepare($query);
    if (!$stmt) {
        return [];
    }

    $bind = array_merge([$types], $params);
    $refs = [];
    foreach ($bind as $k => $v) {
        $refs[$k] = &$bind[$k];
    }
    call_user_func_array([$stmt, 'bind_param'], $refs);

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function transactionGetStats() {
    $db = getConnection();
    $result = $db->query("SELECT 
        COUNT(*) as total_transactions,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_transactions,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_transactions,
        SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_transactions,
        SUM(CASE WHEN status = 'refunded' THEN 1 ELSE 0 END) as refunded_transactions,
        SUM(amount) as total_amount
    FROM transactions");
    return $result ? $result->fetch_assoc() : [];
}

function transactionGetRecent($limit = 10) {
    $db = getConnection();
    $stmt = $db->prepare("SELECT * FROM transactions ORDER BY transaction_date DESC LIMIT ?");
    if (!$stmt) {
        return [];
    }
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function transactionGetByStatus($status, $start_date = null, $end_date = null) {
    $db = getConnection();
    
    $query = "SELECT * FROM transactions WHERE status = ?";
    $params = [$status];
    $types = 's';
    
    if ($start_date && $end_date) {
        $query .= " AND transaction_date BETWEEN ? AND ?";
        $params[] = $start_date;
        $params[] = $end_date;
        $types .= 'ss';
    }
    
    $query .= " ORDER BY transaction_date DESC";
    
    $stmt = $db->prepare($query);
    if (!$stmt) {
        return [];
    }
    
    $refs = [];
    $refs[] = $types;
    foreach ($params as $k => $v) {
        $refs[] = &$params[$k];
    }
    call_user_func_array([$stmt, 'bind_param'], $refs);
    
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>