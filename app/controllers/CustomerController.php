<?php
// Customer Controller - Browse, search, cart, orders
// All functions follow procedural pattern: customer_[action]()

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../helpers/cookie_helper.php';

// Display customer home page
function customer_home() {
    requireAuth(); // Ensure user is logged in
    
    $db = getConnection();
    $today = date('Y-m-d');
    
    // Get featured products (available and not expired, limited to 8)
    $stmt = $db->prepare('SELECT * FROM products WHERE status = "available" AND expiry_date >= ? ORDER BY created_at DESC LIMIT 8');
    $stmt->bind_param('s', $today);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    render('customer/home', ['products' => $products]);
}

// Browse medicines
function customer_browseMedicines() {
    requireAuth(); // Ensure user is logged in
    
    $category_id = getGet('category', null);
    $search_query = getGet('q', '');
    
    $today = date('Y-m-d');
    
    // Get all products (not paginated for browse view) with optional filtering
    if ($search_query) {
        // Search products by name, generic_name, or description
        $products = productSearch($search_query);
        // Filter out expired products
        $products = array_filter($products, function($product) use ($today) {
            return $product['expiry_date'] >= $today;
        });
        // Also track the search in cookies
        addSearchHistory($search_query);
    } else {
        // Get all products, optionally filtered by category, excluding expired ones
        $db = getConnection();
        if ($category_id) {
            $stmt = $db->prepare('SELECT * FROM products WHERE category_id = ? AND status = "available" AND expiry_date >= ? ORDER BY name ASC');
            $stmt->bind_param('is', $category_id, $today);
        } else {
            $stmt = $db->prepare('SELECT * FROM products WHERE status = "available" AND expiry_date >= ? ORDER BY name ASC');
            $stmt->bind_param('s', $today);
        }
        $stmt->execute();
        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    render('customer/browse_medicines', [
        'products' => $products,
        'search_query' => $search_query,
        'selected_category' => $category_id
    ]);
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
        setFlash('Product added to cart successfully! View your cart anytime.', 'success');
    }
    
    // Redirect back to browse page (or home if no referrer)
    redirectTo('customer/browseMedicines');
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

// Remove item from cart
function customer_removeFromCart() {
    requireAuth();
    
    if (!isPost()) {
        redirectTo('customer/cart');
    }
    
    $cart_item_id = getPost('cart_item_id');
    $user_id = getUserData('id');
    $cart_id = cartGetOrCreate($user_id);
    
    // Verify the item belongs to this user's cart
    $db = getConnection();
    $stmt = $db->prepare('SELECT id FROM cart_items WHERE id = ? AND cart_id = ?');
    $stmt->bind_param('ii', $cart_item_id, $cart_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result) {
        cartRemoveItem($cart_item_id);
        setFlash('Item removed from cart', 'success');
    } else {
        setFlash('Item not found in your cart', 'error');
    }
    
    redirectTo('customer/cart');
}

// Update cart item quantity
function customer_updateCartQuantity() {
    requireAuth();
    
    if (!isPost()) {
        redirectTo('customer/cart');
    }
    
    $cart_item_id = getPost('cart_item_id');
    $quantity = (int) getPost('quantity', 1);
    $user_id = getUserData('id');
    $cart_id = cartGetOrCreate($user_id);
    
    // Validate quantity
    if ($quantity < 1) {
        setFlash('Invalid quantity', 'error');
        redirectTo('customer/cart');
    }
    
    // Verify the item belongs to this user's cart
    $db = getConnection();
    $stmt = $db->prepare('SELECT id, product_id FROM cart_items WHERE id = ? AND cart_id = ?');
    $stmt->bind_param('ii', $cart_item_id, $cart_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result) {
        // Check if quantity exceeds available stock
        $product = productGetById($result['product_id']);
        if ($product && $quantity > $product['quantity']) {
            setFlash('Quantity exceeds available stock', 'error');
        } else {
            cartUpdateItemQuantity($cart_item_id, $quantity);
            setFlash('Cart updated successfully', 'success');
        }
    } else {
        setFlash('Item not found in your cart', 'error');
    }
    
    redirectTo('customer/cart');
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

// Product search - AJAX API endpoint (returns JSON)
function customer_searchMedicines() {
    header('Content-Type: application/json');
    
    try {
        // Check authentication without redirect (for AJAX requests)
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'Unauthorized'
            ]);
            exit;
        }
        
        $search_query = getGet('q', '');
        $products = [];
        
        $db = getConnection();
        if (!$db) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Database connection failed'
            ]);
            exit;
        }
        
        if (strlen($search_query) >= 2) {
            // Search products by name, generic_name, or description (partial match)
            $search_param = '%' . $search_query . '%';
            $today = date('Y-m-d');
            
            $stmt = $db->prepare('
                SELECT id, name, generic_name, description, price, quantity 
                FROM products 
                WHERE status = "available" AND expiry_date >= ? AND (
                    name LIKE ? OR 
                    generic_name LIKE ? OR 
                    description LIKE ?
                )
                ORDER BY name ASC
                LIMIT 20
            ');
            
            if (!$stmt) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Query prepare failed: ' . $db->error
                ]);
                exit;
            }
            
            $stmt->bind_param('ssss', $today, $search_param, $search_param, $search_param);
            $stmt->execute();
            $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            // If search is empty, return all products (not expired)
            $today = date('Y-m-d');
            $stmt = $db->prepare('SELECT id, name, generic_name, description, price, quantity FROM products WHERE status = "available" AND expiry_date >= ? ORDER BY name ASC LIMIT 20');
            
            if (!$stmt) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Query prepare failed: ' . $db->error
                ]);
                exit;
            }
            
            $stmt->bind_param('s', $today);
            $stmt->execute();
            $result = $stmt->get_result();
            $products = $result->fetch_all(MYSQLI_ASSOC);
        }
        
        // Return JSON response
        echo json_encode([
            'success' => true,
            'count' => count($products),
            'products' => $products
        ]);
        exit;
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Server error: ' . $e->getMessage()
        ]);
        exit;
    }
}

// Product search - old function (for product_search page)
function customer_productSearch() {
    requireAuth(); // Ensure user is logged in
    
    $search_query = getGet('q', '');
    $products = [];
    
    if ($search_query) {
        // Track search in cookies
        addSearchHistory($search_query);
        $products = productSearch($search_query);
    }
    
    render('customer/product_search', [
        'products' => $products,
        'search_query' => $search_query,
        'recentSearches' => getSearchHistory()
    ]);
}
?>