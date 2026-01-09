<?php
// Customer Controller - Browse, search, cart, orders
// All functions follow procedural pattern: customer_[action]()

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Order.php';

// Display customer home page
function customer_home() {
    $page = getGet('page', 1);
    $products = productGetPaginated($page, RECORDS_PER_PAGE);
    
    render('customer/home', ['products' => $products]);
}

// Browse medicines
function customer_browseMedicines() {
    $page = getGet('page', 1);
    $category_id = getGet('category', null);
    
    $products = productGetPaginated($page, RECORDS_PER_PAGE, $category_id);
    
    render('customer/browse_medicines', ['products' => $products]);
}

// Add to cart
function customer_addToCart() {
    requireAuth();
    
    if (!isPost()) {
        redirectTo('customer/browseMedicines');
    }
    
    $product_id = getPost('product_id');
    $quantity = getPost('quantity', 1);
    
    $user_id = getUserData('id');
    $cart_id = cartGetOrCreate($user_id);
    
    // Get product price
    $product = productGetById($product_id);
    if (!$product) {
        setFlash('Product not found', 'error');
        redirectTo('customer/browseMedicines');
    }
    
    // Check if already in cart
    if (cartHasProduct($cart_id, $product_id)) {
        setFlash('Product already in cart', 'info');
    } else {
        cartAddItem($cart_id, $product_id, $quantity, $product['price']);
        setFlash('Product added to cart', 'success');
    }
    
    redirectTo('customer/cart');
}

// View cart
function customer_cart() {
    requireAuth();
    
    $user_id = getUserData('id');
    $cart_id = cartGetOrCreate($user_id);
    $items = cartGetItems($cart_id);
    $totals = cartCalculateTotals($cart_id);
    
    render('customer/cart', [
        'items' => $items,
        'totals' => $totals
    ]);
}
?>