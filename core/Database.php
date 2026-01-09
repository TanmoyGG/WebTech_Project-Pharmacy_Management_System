<?php
// Database connection and query functions (Procedural)

// Global database connection variable
$db_connection = null;

// Initialize database connection
function initDatabase($host, $user, $password, $database) {
    global $db_connection;
    
    // Create connection
    $db_connection = new mysqli($host, $user, $password, $database);
    
    // Check connection
    if ($db_connection->connect_error) {
        die("Database Connection Failed: " . $db_connection->connect_error);
    }
    
    // Set charset to utf8
    $db_connection->set_charset("utf8");
    
    return $db_connection;
}

// Get database connection
function getConnection() {
    global $db_connection;
    return $db_connection;
}

// Execute a select query
function query($sql, $types = "", $params = []) {
    $conn = getConnection();
    
    if (empty($types)) {
        $result = $conn->query($sql);
    } else {
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
    }
    
    return $result;
}

// Fetch all results as associative array
function fetchAll($sql, $types = "", $params = []) {
    $result = query($sql, $types, $params);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

// Fetch single result
function fetchOne($sql, $types = "", $params = []) {
    $result = query($sql, $types, $params);
    return $result->fetch_assoc();
}

// Execute insert, update, delete query
function execute($sql, $types = "", $params = []) {
    $conn = getConnection();
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    
    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $result = $stmt->execute();
    
    if (!$result) {
        die("Execute failed: " . $stmt->error);
    }
    
    return $result;
}

// Get last inserted ID
function lastInsertId() {
    $conn = getConnection();
    return $conn->insert_id;
}

// Get affected rows
function affectedRows() {
    $conn = getConnection();
    return $conn->affected_rows;
}

// Escape string
function escape($string) {
    $conn = getConnection();
    return $conn->real_escape_string($string);
}

// Close database connection
function closeDatabase() {
    global $db_connection;
    if ($db_connection) {
        $db_connection->close();
    }
}
?>