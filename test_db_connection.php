<?php
// Test Database Connection

require_once 'config/config.php';

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("<h3 style='color:red;'> Connection Failed: " . $conn->connect_error . "</h3>");
}

echo "<h3 style='color:green;'> Database Connected Successfully!</h3>";
echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";

// List all tables
$result = $conn->query("SHOW TABLES");

if ($result->num_rows > 0) {
    echo "<h4>Tables Created:</h4>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . $row['Tables_in_' . DB_NAME] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:orange;'> No tables found. Make sure schema.sql was executed.</p>";
}

// Check default admin
$admin_check = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='admin'");
$admin_data = $admin_check->fetch_assoc();

if ($admin_data['count'] > 0) {
    echo "<p style='color:green;'> Default admin user exists</p>";
} else {
    echo "<p style='color:orange;'> No admin user found</p>";
}

$conn->close();
?>
