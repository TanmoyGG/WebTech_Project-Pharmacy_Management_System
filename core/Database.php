<?php
// Database connection and query functions (Procedural)

use mysqli;
use mysqli_result;

// Global database connection variable
/** @var mysqli|null $db_connection */
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

// Get database connection (lazy init) and guarantee mysqli instance
function getConnection(): mysqli {
    global $db_connection;

    // If already initialized, return it
    if ($db_connection instanceof mysqli) {
        return $db_connection;
    }

    // Lazily initialize using config constants if available
    if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASSWORD') && defined('DB_NAME')) {
        initDatabase(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    } else {
        die('Database connection not initialized and DB_* constants are missing.');
    }

    // Assert connection is initialized for type safety
    if (!($db_connection instanceof mysqli)) {
        die('Failed to establish database connection.');
    }

    return $db_connection;
}

// Execute a select query
function query($sql, $types = "", $params = []): mysqli_result|bool {
    /** @var mysqli $conn */
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
function fetchAll($sql, $types = "", $params = []): array {
    $result = query($sql, $types, $params);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

// Fetch single result
function fetchOne($sql, $types = "", $params = []): array|null {
    $result = query($sql, $types, $params);
    return $result instanceof mysqli_result ? $result->fetch_assoc() : null;
}

// Execute insert, update, delete query
function execute($sql, $types = "", $params = []): bool {
    /** @var mysqli $conn */
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
function lastInsertId(): int {
    /** @var mysqli $conn */
    $conn = getConnection();
    return (int)$conn->insert_id;
}

// Get affected rows
function affectedRows(): int {
    /** @var mysqli $conn */
    $conn = getConnection();
    return (int)$conn->affected_rows;
}

// Escape string
function escape($string): string {
    /** @var mysqli $conn */
    $conn = getConnection();
    return $conn->real_escape_string($string);
}

// Close database connection
function closeDatabase(): void {
    global $db_connection;
    if ($db_connection instanceof mysqli) {
        $db_connection->close();
    }
}
?>