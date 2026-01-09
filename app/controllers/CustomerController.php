<?php
// Customer Controller - Browse, search, cart, orders
// All functions follow procedural pattern: customer_[action]()

// Display customer home page
function customer_home() {
    $page = getGet('page', 1);
    $products = getPaginated('products', $page, RECORDS_PER_PAGE);
    
    render('customer/home', ['products' => $products]);
}

// Browse medicines
function customer_browseMedicines() {
    $page = getGet('page', 1);
    $products = getPaginated('products', $page, RECORDS_PER_PAGE);
    
    render('customer/browse_medicines', ['products' => $products]);
}

// Helper functions
function getOrCreateCart() {
    $userId = getCurrentUserId();
    
    if (empty($userId)) {
        return null;
    }
    
    $cart = fetchOne('SELECT id FROM carts WHERE user_id = ? AND status = ?', 'ss', [$userId, 'active']);
    
    if (!$cart) {
        $cartData = [
            'user_id' => $userId,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $cartId = insertRecord('carts', $cartData);
        return $cartId;
    }
    
    return $cart['id'];
}

function getCartItems() {
    $cartId = getOrCreateCart();
    
    if (empty($cartId)) {
        return [];
    }
    
    return fetchAll(
        'SELECT ci.*, p.name, p.price FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.cart_id = ?',
        'i',
        [$cartId]
    );
}

function calculateCartTotal($cartItems) {
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['quantity'] * $item['price'];
    }
    return $total;
}
?>