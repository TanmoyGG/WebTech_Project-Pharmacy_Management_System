<?php
// Category Model - Category management operations (Procedural)

// Get category by ID
function categoryGetById($category_id) {
    $db = getConnection();
    $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Get all categories
function categoryGetAll() {
    $db = getConnection();
    $result = $db->query("SELECT * FROM categories ORDER BY name ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Create new category
function categoryCreate($name, $description = null) {
    $db = getConnection();
    
    $stmt = $db->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    $stmt->bind_param('ss', $name, $description);
    
    return $stmt->execute();
}

// Update category
function categoryUpdate($category_id, $name = null, $description = null) {
    $db = getConnection();
    
    if ($name === null && $description === null) {
        return false;
    }
    
    $updates = [];
    $params = [];
    $types = '';
    
    if ($name !== null) {
        $updates[] = "name = ?";
        $params[] = $name;
        $types .= 's';
    }
    
    if ($description !== null) {
        $updates[] = "description = ?";
        $params[] = $description;
        $types .= 's';
    }
    
    $updates[] = "updated_at = CURRENT_TIMESTAMP";
    $params[] = $category_id;
    $types .= 'i';
    
    $query = "UPDATE categories SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $db->prepare($query);
    call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $params));
    
    return $stmt->execute();
}

// Delete category
function categoryDelete($category_id) {
    $db = getConnection();
    
    // Check if category has products
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] > 0) {
        // Update products to null category before deleting
        $stmt = $db->prepare("UPDATE products SET category_id = NULL WHERE category_id = ?");
        $stmt->bind_param('i', $category_id);
        $stmt->execute();
    }
    
    $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param('i', $category_id);
    
    return $stmt->execute();
}

// Search categories
function categorySearch($search_term) {
    $db = getConnection();
    $search = '%' . $search_term . '%';
    
    $stmt = $db->prepare("SELECT * FROM categories WHERE name LIKE ? OR description LIKE ? ORDER BY name ASC");
    $stmt->bind_param('ss', $search, $search);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Check if category name exists
function categoryNameExists($name, $excludeId = null) {
    $db = getConnection();
    
    if ($excludeId !== null) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM categories WHERE name = ? AND id != ?");
        $stmt->bind_param('si', $name, $excludeId);
    } else {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM categories WHERE name = ?");
        $stmt->bind_param('s', $name);
    }
    
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    return $result['count'] > 0;
}

// Count total categories
function categoryCount() {
    $db = getConnection();
    $result = $db->query("SELECT COUNT(*) as count FROM categories");
    $data = $result->fetch_assoc();
    
    return $data['count'];
}

// Get categories with product count
function categoryGetWithProductCount() {
    $db = getConnection();
    
    $result = $db->query("SELECT c.*, COUNT(p.id) as product_count 
                          FROM categories c 
                          LEFT JOIN products p ON c.id = p.category_id 
                          GROUP BY c.id 
                          ORDER BY c.name ASC");
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get category by name
function categoryGetByName($name) {
    $db = getConnection();
    $stmt = $db->prepare("SELECT * FROM categories WHERE name = ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_assoc();
}

// Get paginated categories
function categoryGetPaginated($page = 1, $per_page = 20) {
    $offset = ($page - 1) * $per_page;
    $db = getConnection();
    
    $stmt = $db->prepare("SELECT * FROM categories ORDER BY name ASC LIMIT ? OFFSET ?");
    $stmt->bind_param('ii', $per_page, $offset);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Check if category exists
function categoryExists($category_id) {
    $category = categoryGetById($category_id);
    return $category !== null;
}
?>

// Search categories
function categorySearch($searchTerm) {
    return fetchAll('SELECT * FROM categories WHERE name LIKE ? OR description LIKE ?', 'ss', ["%$searchTerm%", "%$searchTerm%"]);
}

// Count total categories
function categoryCount() {
    return countRecords('categories');
}
?>