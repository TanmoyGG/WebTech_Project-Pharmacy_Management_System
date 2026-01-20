<?php
// Handles all product/medicine database operations

// Ensure database helpers are loaded for static analysis and runtime
require_once __DIR__ . '/../../core/Database.php';

// Guard against multiple inclusions
if (function_exists('productGetById')) {
    return;
}

// Get product by ID
function productGetById($product_id) {
    $db = getConnection();
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Get all products
function productGetAll($status = 'available', $limit = null, $offset = 0) {
    $db = getConnection();
    
    if ($status === null) {
        $query = "SELECT * FROM products ORDER BY name ASC";
        $stmt = $db->prepare($query);
    } else {
        $query = "SELECT * FROM products WHERE status = ?";
        if ($limit !== null) {
            $query .= " LIMIT ? OFFSET ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param('sii', $status, $limit, $offset);
        } else {
            $stmt = $db->prepare($query);
            $stmt->bind_param('s', $status);
        }
    }
    
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get products by category
function productGetByCategory($category_id, $status = 'available') {
    $db = getConnection();
    $stmt = $db->prepare("SELECT * FROM products WHERE category_id = ? AND status = ? ORDER BY name ASC");
    $stmt->bind_param('is', $category_id, $status);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Create new product
function productCreate($name, $generic_name, $category_id, $description, $price, $quantity, $low_stock_threshold = 10, $manufacture_date = null, $expiry_date = null, $status = 'available') {
    $db = getConnection();
    
    $stmt = $db->prepare("INSERT INTO products (name, generic_name, category_id, description, price, quantity, low_stock_threshold, manufacture_date, expiry_date, status) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssiidiiiss', $name, $generic_name, $category_id, $description, $price, $quantity, $low_stock_threshold, $manufacture_date, $expiry_date, $status);
    
    return $stmt->execute();
}

// Update product
function productUpdate($product_id, $name = null, $generic_name = null, $description = null, $price = null, $low_stock_threshold = null, $manufacture_date = null, $expiry_date = null, $category_id = null, $quantity = null, $status = null) {
    $db = getConnection();
    $updates = [];
    $params = [];
    $types = '';
    
    if ($name !== null) {
        $updates[] = "name = ?";
        $params[] = &$name;
        $types .= 's';
    }
    if ($generic_name !== null) {
        $updates[] = "generic_name = ?";
        $params[] = &$generic_name;
        $types .= 's';
    }
    if ($description !== null) {
        $updates[] = "description = ?";
        $params[] = &$description;
        $types .= 's';
    }
    if ($price !== null) {
        $updates[] = "price = ?";
        $params[] = &$price;
        $types .= 'd';
    }
    if ($low_stock_threshold !== null) {
        $updates[] = "low_stock_threshold = ?";
        $params[] = &$low_stock_threshold;
        $types .= 'i';
    }
    if ($manufacture_date !== null) {
        $updates[] = "manufacture_date = ?";
        $params[] = &$manufacture_date;
        $types .= 's';
    }
    if ($expiry_date !== null) {
        $updates[] = "expiry_date = ?";
        $params[] = &$expiry_date;
        $types .= 's';
    }
    if ($category_id !== null) {
        $updates[] = "category_id = ?";
        $params[] = &$category_id;
        $types .= 'i';
    }
    if ($quantity !== null) {
        $updates[] = "quantity = ?";
        $params[] = &$quantity;
        $types .= 'i';
    }
    if ($status !== null) {
        $updates[] = "status = ?";
        $params[] = &$status;
        $types .= 's';
    }
    
    if (empty($updates)) {
        return false;
    }
    
    $updates[] = "updated_at = CURRENT_TIMESTAMP";
    $params[] = &$product_id;
    $types .= 'i';
    
    $query = "UPDATE products SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $db->prepare($query);
    call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $params));
    
    return $stmt->execute();
}

// Delete product
function productDelete($product_id) {
    $db = getConnection();
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param('i', $product_id);
    return $stmt->execute();
}

// Search products by name, generic name, or description
function productSearch($search_term, $limit = 50) {
    $db = getConnection();
    $search = '%' . $search_term . '%';
    
    $stmt = $db->prepare("SELECT * FROM products 
                          WHERE (name LIKE ? OR generic_name LIKE ? OR description LIKE ?) 
                          AND status = 'available'
                          ORDER BY name ASC LIMIT ?");
    $stmt->bind_param('sssi', $search, $search, $search, $limit);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get products by price range
function productGetByPriceRange($min_price, $max_price, $status = 'available') {
    $db = getConnection();
    $stmt = $db->prepare("SELECT * FROM products WHERE price BETWEEN ? AND ? AND status = ? ORDER BY price ASC");
    $stmt->bind_param('dds', $min_price, $max_price, $status);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Update product stock quantity
function productUpdateStock($product_id, $quantity) {
    $db = getConnection();
    $stmt = $db->prepare("UPDATE products SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param('ii', $quantity, $product_id);
    
    return $stmt->execute();
}

// Reduce stock (when item is ordered)
function productReduceStock($product_id, $quantity) {
    $product = productGetById($product_id);
    if ($product && $product['quantity'] >= $quantity) {
        $new_quantity = $product['quantity'] - $quantity;
        return productUpdateStock($product_id, $new_quantity);
    }
    return false;
}

// Increase stock (when order is cancelled, return, etc)
function productIncreaseStock($product_id, $quantity) {
    $product = productGetById($product_id);
    if ($product) {
        $new_quantity = $product['quantity'] + $quantity;
        return productUpdateStock($product_id, $new_quantity);
    }
    return false;
}

// Get low stock products (below threshold)
function productGetLowStock($threshold = null) {
    $db = getConnection();
    
    if ($threshold === null) {
        // Use each product's own low_stock_threshold
        $stmt = $db->prepare("SELECT * FROM products WHERE quantity <= low_stock_threshold AND status = 'available' ORDER BY quantity ASC");
    } else {
        $stmt = $db->prepare("SELECT * FROM products WHERE quantity <= ? AND status = 'available' ORDER BY quantity ASC");
        $stmt->bind_param('i', $threshold);
    }
    
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get expiring products (within X days)
function productGetExpiring($days = 30) {
    $db = getConnection();
    $future_date = date('Y-m-d', strtotime("+$days days"));
    
    $stmt = $db->prepare("SELECT * FROM products 
                          WHERE expiry_date IS NOT NULL 
                          AND expiry_date <= ? 
                          AND expiry_date > CURDATE()
                          AND status = 'available'
                          ORDER BY expiry_date ASC");
    $stmt->bind_param('s', $future_date);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get expired products
function productGetExpired() {
    $db = getConnection();
    $stmt = $db->prepare("SELECT * FROM products WHERE expiry_date < CURDATE() AND status = 'available' ORDER BY expiry_date DESC");
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Change product status (available/discontinued)
function productSetStatus($product_id, $status) {
    $db = getConnection();
    $status = in_array($status, ['available', 'discontinued']) ? $status : 'available';
    
    $stmt = $db->prepare("UPDATE products SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param('si', $status, $product_id);
    
    return $stmt->execute();
}

// Count total products
function productCount($status = null) {
    $db = getConnection();
    
    if ($status === null) {
        $result = $db->query("SELECT COUNT(*) as count FROM products");
    } else {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM products WHERE status = ?");
        $stmt->bind_param('s', $status);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    
    $data = $result->fetch_assoc();
    return $data['count'];
}

// Get top selling products
function productGetTopSelling($limit = 10) {
    $db = getConnection();
    
    $stmt = $db->prepare("SELECT p.*, SUM(oi.quantity) as total_sold 
                          FROM products p 
                          LEFT JOIN order_items oi ON p.id = oi.product_id 
                          WHERE p.status = 'available'
                          GROUP BY p.id 
                          ORDER BY total_sold DESC 
                          LIMIT ?");
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get paginated products
function productGetPaginated($page = 1, $per_page = 20, $category_id = null, $status = 'available') {
    $offset = ($page - 1) * $per_page;
    $db = getConnection();
    
    if ($category_id !== null) {
        $stmt = $db->prepare("SELECT * FROM products WHERE category_id = ? AND status = ? ORDER BY name ASC LIMIT ? OFFSET ?");
        $stmt->bind_param('isii', $category_id, $status, $per_page, $offset);
    } else {
        $stmt = $db->prepare("SELECT * FROM products WHERE status = ? ORDER BY name ASC LIMIT ? OFFSET ?");
        $stmt->bind_param('sii', $status, $per_page, $offset);
    }
    
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get product statistics
function productGetStats() {
    $db = getConnection();
    
    $result = $db->query("SELECT 
                            COUNT(*) as total_products,
                            SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_products,
                            SUM(CASE WHEN status = 'discontinued' THEN 1 ELSE 0 END) as discontinued_products,
                            AVG(price) as avg_price,
                            MIN(price) as min_price,
                            MAX(price) as max_price,
                            SUM(quantity) as total_stock
                          FROM products");
    
    return $result->fetch_assoc();
}



// Get revenue by product
function productGetRevenue($product_id) {
    $db = getConnection();
    
    $stmt = $db->prepare("SELECT SUM(oi.quantity * oi.price) as total_revenue 
                          FROM order_items oi 
                          WHERE oi.product_id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_assoc();
}

// Check if product exists
function productExists($product_id) {
    $product = productGetById($product_id);
    return $product !== null;
}
?>