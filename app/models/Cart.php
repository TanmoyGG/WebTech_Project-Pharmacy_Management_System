<?php
// Cart Model - Shopping cart data operations (Procedural)

// Get cart by ID
function cartGetById($id) {
    return getById('carts', $id);
}

// Get active cart for user
function cartGetActiveByUser($userId) {
    return fetchOne('SELECT * FROM carts WHERE user_id = ? AND status = ?', 'ss', [$userId, 'active']);
}

// Create new cart
function cartCreate($userId) {
    $cartData = [
        'user_id' => $userId,
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    return insertRecord('carts', $cartData);
}

// Get or create cart for user
function cartGetOrCreate($userId) {
    $cart = cartGetActiveByUser($userId);
    
    if (!$cart) {
        $cartId = cartCreate($userId);
        return cartGetById($cartId);
    }
    
    return $cart;
}

// Get cart items
function cartGetItems($cartId) {
    return fetchAll(
        'SELECT ci.*, p.name, p.price, p.quantity as stock FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.cart_id = ?',
        'i',
        [$cartId]
    );
}

// Add item to cart
function cartAddItem($cartId, $productId, $quantity) {
    $existing = fetchOne('SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?', 'ii', [$cartId, $productId]);
    
    $product = getById('products', $productId);
    
    if ($existing) {
        $newQuantity = $existing['quantity'] + $quantity;
        return updateRecord('cart_items', ['quantity' => $newQuantity], 'id = ?', [$existing['id']]);
    } else {
        $itemData = [
            'cart_id' => $cartId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $product['price']
        ];
        return insertRecord('cart_items', $itemData);
    }
}

// Remove item from cart
function cartRemoveItem($cartItemId) {
    return deleteRecord('cart_items', 'id = ?', [$cartItemId]);
}

// Clear cart
function cartClear($cartId) {
    return deleteRecord('cart_items', 'cart_id = ?', [$cartId]);
}

// Get cart total
function cartGetTotal($cartId) {
    $result = fetchOne('SELECT SUM(ci.quantity * ci.price) as total FROM cart_items ci WHERE ci.cart_id = ?', 'i', [$cartId]);
    return $result['total'] ?? 0;
}

// Get cart item count
function cartGetItemCount($cartId) {
    $result = fetchOne('SELECT COUNT(*) as count FROM cart_items WHERE cart_id = ?', 'i', [$cartId]);
    return $result['count'] ?? 0;
}

// Update cart item quantity
function cartUpdateItemQuantity($cartItemId, $quantity) {
    if ($quantity <= 0) {
        return cartRemoveItem($cartItemId);
    }
    return updateRecord('cart_items', ['quantity' => $quantity], 'id = ?', [$cartItemId]);
}

// Archive/Close cart
function cartClose($cartId) {
    return updateRecord('carts', ['status' => 'closed'], 'id = ?', [$cartId]);
}
?>