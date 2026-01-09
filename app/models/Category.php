<?php
// Category Model - Category data operations (Procedural)

// Get category by ID
function categoryGetById($id) {
    return getById('categories', $id);
}

// Get all categories
function categoryGetAll() {
    return getAllRecords('categories');
}

// Create new category
function categoryCreate($name, $description = '') {
    $categoryData = [
        'name' => $name,
        'description' => $description,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    return insertRecord('categories', $categoryData);
}

// Update category
function categoryUpdate($categoryId, $name, $description) {
    $updateData = [
        'name' => $name,
        'description' => $description,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    return updateRecord('categories', $updateData, 'id = ?', [$categoryId]);
}

// Delete category
function categoryDelete($categoryId) {
    return deleteRecord('categories', 'id = ?', [$categoryId]);
}

// Check if category exists
function categoryExists($categoryId) {
    return recordExists('categories', 'id = ?', [$categoryId]);
}

// Get products count in a category
function categoryProductCount($categoryId) {
    return countRecords('products', 'category_id = ?', [$categoryId]);
}

// Search categories
function categorySearch($searchTerm) {
    return fetchAll('SELECT * FROM categories WHERE name LIKE ? OR description LIKE ?', 'ss', ["%$searchTerm%", "%$searchTerm%"]);
}

// Count total categories
function categoryCount() {
    return countRecords('categories');
}
?>