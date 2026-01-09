<?php
// Product Controller - Product management operations
// All functions follow procedural pattern: product_[action]()

// Display product details
function product_view() {
    $productId = getUrlParam(1);
    
    if (isEmpty($productId)) {
        setFlash('Product not found', 'error');
        redirectTo('customer/home');
    }
    
    $product = getById('products', $productId);
    
    if (!$product) {
        setFlash('Product not found', 'error');
        redirectTo('customer/home');
    }
    
    render('customer/product_details', ['product' => $product]);
}

// Edit product (Inventory Manager only)
function product_edit() {
    requireRole('inventory_manager');
    
    $productId = getUrlParam(1);
    
    if (isEmpty($productId)) {
        redirectTo('inventory_manager/dashboard');
    }
    
    $product = getById('products', $productId);
    $categories = getAllRecords('categories');
    
    render('inventory_manager/product_edit', ['product' => $product, 'categories' => $categories]);
}

// Delete product
function product_delete() {
    requireRole('inventory_manager');
    
    if (!isPost()) {
        redirectTo('inventory_manager/dashboard');
    }
    
    $productId = getPost('product_id', '');
    
    if (isEmpty($productId)) {
        setFlash('Invalid product', 'error');
        redirectTo('inventory_manager/dashboard');
    }
    
    deleteRecord('products', 'id = ?', [$productId]);
    
    setFlash('Product deleted successfully', 'success');
    redirectTo('inventory_manager/dashboard');
}
?>