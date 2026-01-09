<?php
// Product Model - Medicine/Product data operations (Procedural)

// Get product by ID
function productGetById($id) {
    return getById('products', $id);
}

// Get all products
function productGetAll() {
    return getAllRecords('products');
}

// Get products by category
function productGetByCategory($categoryId) {
    return fetchAll('SELECT * FROM products WHERE category_id = ?', 'i', [$categoryId]);
}

// Create new product
function productCreate($data) {
    $data['created_at'] = date('Y-m-d H:i:s');
    return insertRecord('products', $data);
}

// Update product
function productUpdate($productId, $data) {
    $data['updated_at'] = date('Y-m-d H:i:s');
    return updateRecord('products', $data, 'id = ?', [$productId]);
}

// Delete product
function productDelete($productId) {
    return deleteRecord('products', 'id = ?', [$productId]);
}

// Search products
function productSearch($searchTerm) {
    return fetchAll(
        'SELECT * FROM products WHERE name LIKE ? OR generic_name LIKE ? OR description LIKE ?',
        'sss',
        ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%"]
    );
}

// Get products by price range
function productGetByPriceRange($minPrice, $maxPrice) {
    return fetchAll('SELECT * FROM products WHERE price BETWEEN ? AND ?', 'dd', [$minPrice, $maxPrice]);
}

// Update stock quantity
function productUpdateStock($productId, $quantity) {
    return updateRecord('products', ['quantity' => $quantity], 'id = ?', [$productId]);
}

// Reduce stock (when item is ordered)
function productReduceStock($productId, $quantity) {
    $product = productGetById($productId);
    if ($product && $product['quantity'] >= $quantity) {
        $newQuantity = $product['quantity'] - $quantity;
        return productUpdateStock($productId, $newQuantity);
    }
    return false;
}

// Increase stock
function productIncreaseStock($productId, $quantity) {
    $product = productGetById($productId);
    if ($product) {
        $newQuantity = $product['quantity'] + $quantity;
        return productUpdateStock($productId, $newQuantity);
    }
    return false;
}

// Get low stock products
function productGetLowStock($threshold = 10) {
    return fetchAll('SELECT * FROM products WHERE quantity <= ? ORDER BY quantity ASC', 'i', [$threshold]);
}

// Get expiring products
function productGetExpiring($days = 30) {
    $expiryDate = date('Y-m-d', strtotime("+$days days"));
    return fetchAll('SELECT * FROM products WHERE expiry_date <= ? AND expiry_date > NOW() ORDER BY expiry_date ASC', 's', [$expiryDate]);
}

// Get expired products
function productGetExpired() {
    return fetchAll('SELECT * FROM products WHERE expiry_date < NOW()');
}

// Count total products
function productCount() {
    return countRecords('products');
}

// Get top selling products
function productGetTopSelling($limit = 10) {
    return fetchAll(
        'SELECT p.*, SUM(oi.quantity) as total_sold FROM products p LEFT JOIN order_items oi ON p.id = oi.product_id GROUP BY p.id ORDER BY total_sold DESC LIMIT ?',
        'i',
        [$limit]
    );
}
?>