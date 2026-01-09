<?php
// Model Helper Functions (Procedural)
// Contains common database operation functions

// Insert record
function insertRecord($table, $data) {
    $keys = array_keys($data);
    $values = array_values($data);
    $placeholders = implode(',', array_fill(0, count($keys), '?'));
    
    $sql = "INSERT INTO $table (" . implode(',', $keys) . ") VALUES ($placeholders)";
    
    execute($sql, str_repeat('s', count($values)), $values);
    
    return lastInsertId();
}

// Update record
function updateRecord($table, $data, $where, $whereParams = []) {
    $updates = [];
    $values = [];
    
    foreach ($data as $key => $value) {
        $updates[] = "$key = ?";
        $values[] = $value;
    }
    
    // Merge where parameters
    $values = array_merge($values, $whereParams);
    
    $sql = "UPDATE $table SET " . implode(', ', $updates) . " WHERE $where";
    
    $types = str_repeat('s', count($values));
    execute($sql, $types, $values);
    
    return affectedRows();
}

// Delete record
function deleteRecord($table, $where, $params = []) {
    $sql = "DELETE FROM $table WHERE $where";
    $types = str_repeat('s', count($params));
    
    execute($sql, $types, $params);
    
    return affectedRows();
}

// Get record by ID
function getById($table, $id) {
    $sql = "SELECT * FROM $table WHERE id = ?";
    return fetchOne($sql, 's', [$id]);
}

// Get all records from table
function getAllRecords($table) {
    $sql = "SELECT * FROM $table";
    return fetchAll($sql);
}

// Check if record exists
function recordExists($table, $where, $params = []) {
    $sql = "SELECT COUNT(*) as count FROM $table WHERE $where";
    $types = str_repeat('s', count($params));
    
    $result = fetchOne($sql, $types, $params);
    return $result['count'] > 0;
}

// Count records
function countRecords($table, $where = "", $params = []) {
    $sql = "SELECT COUNT(*) as count FROM $table";
    
    if (!empty($where)) {
        $sql .= " WHERE $where";
        $types = str_repeat('s', count($params));
        $result = fetchOne($sql, $types, $params);
    } else {
        $result = fetchOne($sql);
    }
    
    return $result['count'];
}

// Get paginated results
function getPaginated($table, $page = 1, $limit = 10, $where = "", $params = []) {
    $offset = ($page - 1) * $limit;
    
    $sql = "SELECT * FROM $table";
    if (!empty($where)) {
        $sql .= " WHERE $where";
    }
    $sql .= " LIMIT ? OFFSET ?";
    
    $types = str_repeat('s', count($params)) . 'ii';
    $queryParams = array_merge($params, [$limit, $offset]);
    
    $data = fetchAll($sql, $types, $queryParams);
    
    // Get total count
    $countSql = "SELECT COUNT(*) as count FROM $table";
    if (!empty($where)) {
        $countSql .= " WHERE $where";
        $countTypes = str_repeat('s', count($params));
        $countResult = fetchOne($countSql, $countTypes, $params);
    } else {
        $countResult = fetchOne($countSql);
    }
    
    $total = $countResult['count'];
    $totalPages = ceil($total / $limit);
    
    return [
        'data' => $data,
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'totalPages' => $totalPages
    ];
}
?>