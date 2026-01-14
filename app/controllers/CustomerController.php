<?php
// Customer Controller - Browse, search, cart, orders
// All functions follow procedural pattern: customer_[action]()

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';
require_once __DIR__ . '/../models/User.php';

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

// Checkout page
function customer_checkout() {
    requireAuth();
    
    $user_id = getUserData('id');
    $user = userGetById($user_id);
    $cart_id = cartGetOrCreate($user_id);
    $items = cartGetItems($cart_id);
    $totals = cartCalculateTotals($cart_id);
    
    if (isPost()) {
        $delivery_address = getPost('delivery_address');
        $delivery_phone = getPost('delivery_phone');
        $payment_method = getPost('payment_method');
        $terms_agree = getPost('terms_agree');
        
        if (!$delivery_address || !$delivery_phone || !$payment_method || !$terms_agree) {
            setFlash('Please fill all fields', 'error');
            render('customer/checkout', [
                'items' => $items,
                'totals' => $totals,
                'user' => $user
            ]);
            return;
        }
        
        // Create order
        $order_id = orderCreate($user_id, $totals['total'], 'pending', $delivery_address);
        
        if ($order_id) {
            // Add order items from cart
            foreach ($items as $item) {
                $subtotal = $item['quantity'] * $item['price'];
                $stmt = getConnection()->prepare(
                    "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->bind_param('iiidd', $order_id, $item['product_id'], $item['quantity'], $item['price'], $subtotal);
                $stmt->execute();
            }
            
            // Create transaction record
            $stmt = getConnection()->prepare(
                "INSERT INTO transactions (order_id, user_id, amount, payment_method, status) VALUES (?, ?, ?, ?, 'completed')"
            );
            $stmt->bind_param('iids', $order_id, $user_id, $totals['total'], $payment_method);
            $stmt->execute();
            
            // Clear cart
            $stmt = getConnection()->prepare("DELETE FROM cart_items WHERE cart_id = ?");
            $stmt->bind_param('i', $cart_id);
            $stmt->execute();
            
            setFlash('Order placed successfully!', 'success');
            redirectTo('customer/orderHistory');
        } else {
            setFlash('Error placing order', 'error');
        }
    }
    
    render('customer/checkout', [
        'items' => $items,
        'totals' => $totals,
        'user' => $user
    ]);
}

// View order history
function customer_orderHistory() {
    requireAuth();
    
    $user_id = getUserData('id');
    $orders = orderGetByUser($user_id);
    
    render('customer/order_history', ['orders' => $orders]);
}

// View order details
function customer_orderDetails() {
    requireAuth();
    
    $user_id = getUserData('id');
    $order_id = getGet('id');
    
    if (!$order_id) {
        setFlash('Order not found', 'error');
        redirectTo('customer/orderHistory');
    }
    
    // Get order
    $order = orderGetById($order_id);
    
    // Verify order belongs to current user
    if (!$order || $order['user_id'] != $user_id) {
        setFlash('You do not have permission to view this order', 'error');
        redirectTo('customer/orderHistory');
    }
    
    // Get order items with product names
    $db = getConnection();
    $stmt = $db->prepare('
        SELECT oi.*, p.name as product_name, p.generic_name 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
        ORDER BY oi.id ASC
    ');
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get user info
    $user = userGetById($user_id);
    
    render('customer/order_details', [
        'order' => $order,
        'items' => $items,
        'user' => $user
    ]);
}

// Product search
function customer_productSearch() {
    $search_query = getGet('q', '');
    $products = [];
    
    if ($search_query) {
        $products = productSearch($search_query);
    }
    
    render('customer/product_search', [
        'products' => $products,
        'search_query' => $search_query
    ]);
}
?>