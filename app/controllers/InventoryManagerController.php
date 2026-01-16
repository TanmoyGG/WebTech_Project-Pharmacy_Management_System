<?php
// Inventory Manager Controller - Manages products, stock, categories, and orders
// All functions follow procedural pattern: inventory_manager_[action]()

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Order.php';

// Display inventory manager dashboard
function inventory_manager_dashboard() {
    requireRole('inventory_manager');
    
    $stats = productGetStats();
    $lowStockProducts = productGetLowStock();
    $expiringProducts = productGetExpiring(30);
    
    // Get low stock count
    $lowStockCount = count($lowStockProducts);
    
    // Get expiring soon count
    $expiringSoonCount = count($expiringProducts);
    
    // Format stats for dashboard
    $productStats = [
        'total' => $stats['total_products'] ?? 0,
        'available' => $stats['available_products'] ?? 0,
        'discontinued' => $stats['discontinued_products'] ?? 0,
        'low_stock' => $lowStockCount,
        'expiring_soon' => $expiringSoonCount,
        'total_stock' => $stats['total_stock'] ?? 0
    ];
    
    $data = [
        'productStats' => $productStats,
        'lowStockProducts' => $lowStockProducts,
        'expiringProducts' => $expiringProducts
    ];
    
    render('inventory_manager/dashboard', $data);
}

// List all products
function inventory_manager_products() {
    requireRole('inventory_manager');
    
    $search = getGet('search', '');
    $category_filter = getGet('category', '');
    $status_filter = getGet('status', 'all');
    
    $db = getConnection();
    
    // Build query based on filters
    $whereConditions = [];
    $params = [];
    $types = '';
    
    if ($search) {
        $whereConditions[] = "(p.name LIKE ? OR p.generic_name LIKE ?)";
        $searchParam = '%' . $search . '%';
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= 'ss';
    }
    
    if ($category_filter) {
        $whereConditions[] = "p.category_id = ?";
        $params[] = $category_filter;
        $types .= 'i';
    }
    
    if ($status_filter && $status_filter !== 'all') {
        $whereConditions[] = "p.status = ?";
        $params[] = $status_filter;
        $types .= 's';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              {$whereClause}
              ORDER BY p.name ASC";
    
    if (!empty($params)) {
        $stmt = $db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $products = $db->query($query)->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get all categories for filter dropdown
    $categories = categoryGetAll();
    
    render('inventory_manager/products', [
        'products' => $products,
        'categories' => $categories,
        'search' => $search,
        'category_filter' => $category_filter,
        'status_filter' => $status_filter
    ]);
}

// Show add product form
function inventory_manager_addProduct() {
    requireRole('inventory_manager');
    
    // Get all categories for dropdown
    $categories = categoryGetAll();
    
    render('inventory_manager/add_product', [
        'categories' => $categories
    ]);
}

// Process add product form
function inventory_manager_addProductProcess() {
    requireRole('inventory_manager');
    
    if (!isPost()) {
        redirectTo('inventory_manager/addProduct');
    }
    
    // Get form data
    $name = sanitize(getPost('name'));
    $generic_name = sanitize(getPost('generic_name'));
    $category_id = (int) getPost('category_id');
    $description = sanitize(getPost('description'));
    $price = (float) getPost('price');
    $quantity = (int) getPost('quantity');
    $low_stock_threshold = (int) getPost('low_stock_threshold', 10);
    $manufacture_date = sanitize(getPost('manufacture_date'));
    $expiry_date = sanitize(getPost('expiry_date'));
    $status = getPost('status', 'available');
    
    // Auto-populate manufacture date with today if empty
    if (isEmpty($manufacture_date)) {
        $manufacture_date = date('Y-m-d');
    }
    
    // Validate required fields
    $errors = [];
    
    if (isEmpty($name) || strlen($name) < 3 || strlen($name) > 150) {
        $errors[] = 'Product name must be 3-150 characters long';
    }
    
    if (!$category_id || $category_id <= 0) {
        $errors[] = 'Please select a valid category';
    }
    
    if ($price <= 0) {
        $errors[] = 'Price must be greater than 0';
    }
    
    if ($quantity < 0) {
        $errors[] = 'Quantity cannot be negative';
    }
    
    if ($low_stock_threshold < 0) {
        $errors[] = 'Low stock threshold cannot be negative';
    }
    
    if (isEmpty($expiry_date)) {
        $errors[] = 'Expiry date is required';
    } else {
        $today = date('Y-m-d');
        if ($expiry_date <= $today) {
            $errors[] = 'Expiry date must be in the future';
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        redirectTo('inventory_manager/addProduct');
    }
    
    // Create product
    $result = productCreate(
        $name,
        $generic_name,
        $category_id,
        $description,
        $price,
        $quantity,
        $low_stock_threshold,
        $manufacture_date,
        $expiry_date,
        $status
    );
    
    if ($result) {
        setFlash('Product added successfully!', 'success');
        redirectTo('inventory_manager/products');
    } else {
        setFlash('Failed to add product. Please try again.', 'error');
        redirectTo('inventory_manager/addProduct');
    }
}

// Edit product - Show edit form
function inventory_manager_editProduct() {
    requireRole('inventory_manager');
    
    $product_id = (int) getGet('id');
    if (!$product_id) {
        setFlash('Product not found', 'error');
        redirectTo('inventory_manager/products');
    }
    
    $product = productGetById($product_id);
    if (!$product) {
        setFlash('Product not found', 'error');
        redirectTo('inventory_manager/products');
    }
    
    $categories = categoryGetAll();
    
    include __DIR__ . '/../views/inventory_manager/edit_product.php';
}

// Edit product - Process form
function inventory_manager_editProductProcess() {
    requireRole('inventory_manager');
    
    $product_id = (int) getPost('product_id');
    if (!$product_id) {
        setFlash('Product not found', 'error');
        redirectTo('inventory_manager/products');
    }
    
    $product = productGetById($product_id);
    if (!$product) {
        setFlash('Product not found', 'error');
        redirectTo('inventory_manager/products');
    }
    
    // Get form data
    $name = sanitize(getPost('name'));
    $generic_name = sanitize(getPost('generic_name'));
    $category_id = (int) getPost('category_id');
    $description = sanitize(getPost('description'));
    $price = (float) getPost('price');
    $quantity = (int) getPost('quantity');
    $low_stock_threshold = (int) getPost('low_stock_threshold', 10);
    $manufacture_date = sanitize(getPost('manufacture_date'));
    $expiry_date = sanitize(getPost('expiry_date'));
    $status = getPost('status', 'available');
    
    // Validate required fields
    $errors = [];
    
    if (isEmpty($name) || strlen($name) < 3 || strlen($name) > 150) {
        $errors[] = 'Product name must be 3-150 characters long';
    }
    
    if (!$category_id || $category_id <= 0) {
        $errors[] = 'Please select a valid category';
    }
    
    if ($price <= 0) {
        $errors[] = 'Price must be greater than 0';
    }
    
    if ($quantity < 0) {
        $errors[] = 'Quantity cannot be negative';
    }
    
    if ($low_stock_threshold < 0) {
        $errors[] = 'Low stock threshold cannot be negative';
    }
    
    if (isEmpty($expiry_date)) {
        $errors[] = 'Expiry date is required';
    } else {
        $today = date('Y-m-d');
        if ($expiry_date <= $today) {
            $errors[] = 'Expiry date must be in the future';
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        redirectTo('inventory_manager/editProduct?id=' . $product_id);
    }
    
    // Update product
    $result = productUpdate(
        $product_id,
        $name,
        $generic_name,
        $description,
        $price,
        $low_stock_threshold,
        $manufacture_date,
        $expiry_date,
        $category_id,
        $quantity,
        $status
    );
    
    if ($result) {
        setFlash('Product updated successfully!', 'success');
        redirectTo('inventory_manager/products');
    } else {
        setFlash('Failed to update product. Please try again.', 'error');
        redirectTo('inventory_manager/editProduct?id=' . $product_id);
    }
}

// View orders (already implemented below)

function inventory_manager_orders() {
    requireRole('inventory_manager');
    
    $db = getConnection();
    
    // Get filter from query string (status: pending, confirmed, shipped, all)
    $statusFilter = getGet('status', 'pending');
    
    if ($statusFilter === 'all') {
        $stmt = $db->prepare('
            SELECT o.id, o.user_id, o.total_amount, o.status, o.delivery_address, o.created_at,
                   u.name as customer_name, u.email as customer_email, u.phone as customer_phone,
                   COUNT(oi.id) as item_count
            FROM orders o
            JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ');
    } else {
        $stmt = $db->prepare('
            SELECT o.id, o.user_id, o.total_amount, o.status, o.delivery_address, o.created_at,
                   u.name as customer_name, u.email as customer_email, u.phone as customer_phone,
                   COUNT(oi.id) as item_count
            FROM orders o
            JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.status = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ');
        $stmt->bind_param('s', $statusFilter);
    }
    
    $stmt->execute();
    $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Count orders by status
    $statusCounts = [];
    foreach (['pending', 'confirmed', 'shipped', 'completed', 'cancelled'] as $status) {
        $countStmt = $db->prepare('SELECT COUNT(*) as count FROM orders WHERE status = ?');
        $countStmt->bind_param('s', $status);
        $countStmt->execute();
        $result = $countStmt->get_result()->fetch_assoc();
        $statusCounts[$status] = $result['count'];
    }
    
    render('inventory_manager/orders', [
        'orders' => $orders,
        'statusFilter' => $statusFilter,
        'statusCounts' => $statusCounts
    ]);
}

// View order details
function inventory_manager_orderDetails() {
    requireRole('inventory_manager');
    
    $order_id = getGet('id');
    if (!$order_id) {
        setFlash('Order not found', 'error');
        redirectTo('inventory_manager/orders');
    }
    
    $db = getConnection();
    
    // Get order details
    $stmt = $db->prepare('
        SELECT o.*, u.name as customer_name, u.email, u.phone, u.address
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ');
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    
    if (!$order) {
        setFlash('Order not found', 'error');
        redirectTo('inventory_manager/orders');
    }
    
    // Get order items
    $itemStmt = $db->prepare('
        SELECT oi.*, p.name as product_name, p.generic_name, c.name as category_name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE oi.order_id = ?
    ');
    $itemStmt->bind_param('i', $order_id);
    $itemStmt->execute();
    $items = $itemStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    render('inventory_manager/order_details', [
        'order' => $order,
        'items' => $items
    ]);
}

// Confirm order (change status from pending to confirmed)
function inventory_manager_confirmOrder() {
    requireRole('inventory_manager');
    
    if (!isPost()) {
        redirectTo('inventory_manager/orders');
    }
    
    $order_id = (int) getPost('order_id');
    
    $db = getConnection();
    
    // Get order and verify it's pending
    $stmt = $db->prepare('SELECT * FROM orders WHERE id = ? AND status = "pending"');
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    
    if (!$order) {
        setFlash('Order not found or already processed', 'error');
        redirectTo('inventory_manager/orders');
    }
    
    // Update order status to confirmed
    $newStatus = 'confirmed';
    $updateStmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $updateStmt->bind_param('si', $newStatus, $order_id);
    $updateStmt->execute();
    
    // Decrement product quantities based on order items
    $itemStmt = $db->prepare('SELECT product_id, quantity FROM order_items WHERE order_id = ?');
    $itemStmt->bind_param('i', $order_id);
    $itemStmt->execute();
    $items = $itemStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    foreach ($items as $item) {
        // Decrement product quantity
        $decrementStmt = $db->prepare('UPDATE products SET quantity = quantity - ? WHERE id = ?');
        $decrementStmt->bind_param('ii', $item['quantity'], $item['product_id']);
        $decrementStmt->execute();
    }
    
    setFlash('Order confirmed and inventory updated!', 'success');
    redirectTo('inventory_manager/orderDetails?id=' . $order_id);
}

// Ship order (change status from confirmed to shipped)
function inventory_manager_shipOrder() {
    requireRole('inventory_manager');
    
    if (!isPost()) {
        redirectTo('inventory_manager/orders');
    }
    
    $order_id = (int) getPost('order_id');
    
    $db = getConnection();
    
    // Get order and verify it's confirmed
    $stmt = $db->prepare('SELECT * FROM orders WHERE id = ? AND status = "confirmed"');
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    
    if (!$order) {
        setFlash('Order not found or not ready to ship', 'error');
        redirectTo('inventory_manager/orders');
    }
    
    // Update order status to shipped
    $newStatus = 'shipped';
    $updateStmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $updateStmt->bind_param('si', $newStatus, $order_id);
    $updateStmt->execute();
    
    setFlash('Order marked as shipped!', 'success');
    redirectTo('inventory_manager/orderDetails?id=' . $order_id);
}

// Complete order (change status from shipped to completed)
function inventory_manager_completeOrder() {
    requireRole('inventory_manager');
    
    if (!isPost()) {
        redirectTo('inventory_manager/orders');
    }
    
    $order_id = (int) getPost('order_id');
    
    $db = getConnection();
    
    // Get order and verify it's shipped
    $stmt = $db->prepare('SELECT * FROM orders WHERE id = ? AND status = "shipped"');
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    
    if (!$order) {
        setFlash('Order not found or not ready to complete', 'error');
        redirectTo('inventory_manager/orders');
    }
    
    // Update order status to completed
    $newStatus = 'completed';
    $updateStmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $updateStmt->bind_param('si', $newStatus, $order_id);
    $updateStmt->execute();
    
    setFlash('Order marked as completed!', 'success');
    redirectTo('inventory_manager/orderDetails?id=' . $order_id);
}

// Cancel order (change status to cancelled and restore inventory)
function inventory_manager_cancelOrder() {
    requireRole('inventory_manager');
    
    if (!isPost()) {
        redirectTo('inventory_manager/orders');
    }
    
    $order_id = (int) getPost('order_id');
    $cancel_reason = sanitize(getPost('cancel_reason', 'No reason provided'));
    
    $db = getConnection();
    
    // Get order and verify it's not already completed/cancelled
    $stmt = $db->prepare('SELECT * FROM orders WHERE id = ? AND status IN ("pending", "confirmed")');
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    
    if (!$order) {
        setFlash('Order cannot be cancelled', 'error');
        redirectTo('inventory_manager/orders');
    }
    
    // Update order status to cancelled
    $newStatus = 'cancelled';
    $updateStmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $updateStmt->bind_param('si', $newStatus, $order_id);
    $updateStmt->execute();
    
    // If order was confirmed, restore inventory
    if ($order['status'] === 'confirmed') {
        $itemStmt = $db->prepare('SELECT product_id, quantity FROM order_items WHERE order_id = ?');
        $itemStmt->bind_param('i', $order_id);
        $itemStmt->execute();
        $items = $itemStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        foreach ($items as $item) {
            // Restore product quantity
            $restoreStmt = $db->prepare('UPDATE products SET quantity = quantity + ? WHERE id = ?');
            $restoreStmt->bind_param('ii', $item['quantity'], $item['product_id']);
            $restoreStmt->execute();
        }
    }
    
    setFlash('Order cancelled successfully!', 'success');
    redirectTo('inventory_manager/orders');
}

// View low stock products
function inventory_manager_lowStock() {
    requireRole('inventory_manager');
    
    $lowStockProducts = productGetLowStock();
    
    $data = [
        'lowStockProducts' => $lowStockProducts
    ];
    
    render('inventory_manager/low_stock', $data);
}

// View expiring products
function inventory_manager_expiringItems() {
    requireRole('inventory_manager');
    
    $expiringProducts = productGetExpiring(30);
    
    $data = [
        'expiringProducts' => $expiringProducts
    ];
    
    render('inventory_manager/expiring_items', $data);
}

// View expired products
function inventory_manager_expiredItems() {
    requireRole('inventory_manager');
    
    $db = getConnection();
    
    // Get all expired products (expiry_date < today)
    $today = date('Y-m-d');
    $stmt = $db->prepare("SELECT * FROM products WHERE expiry_date < ? ORDER BY expiry_date ASC");
    $stmt->bind_param('s', $today);
    $stmt->execute();
    $expiredProducts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $data = [
        'expiredProducts' => $expiredProducts
    ];
    
    render('inventory_manager/expired_items', $data);
}
?>