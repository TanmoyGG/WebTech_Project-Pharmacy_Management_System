<?php
// Customer Order Details View
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Order Details</h2>
                <a href="<?php echo BASE_URL; ?>/customer/orderHistory" class="btn btn-secondary">Back to Orders</a>
            </div>
            
            <!-- Order Header Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Number</h6>
                            <p class="h5">#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></p>
                            
                            <h6 class="text-muted mt-3">Order Date</h6>
                            <p><?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Status</h6>
                            <p>
                                <?php
                                $status = $order['status'];
                                $status_colors = [
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'shipped' => 'info',
                                    'delivered' => 'success',
                                    'completed' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                $color = $status_colors[$status] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $color; ?> p-2">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Generic Name</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['generic_name'] ?? '-'); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>৳<?php echo number_format($item['price'], 2); ?></td>
                                <td>৳<?php echo number_format($item['subtotal'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div class="row mb-4">
                <div class="col-md-6 offset-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>৳<?php 
                                    $subtotal = 0;
                                    foreach ($items as $item) {
                                        $subtotal += $item['subtotal'];
                                    }
                                    echo number_format($subtotal, 2);
                                ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Tax (5%):</span>
                                <span>৳<?php 
                                    $tax = $subtotal * 0.05;
                                    echo number_format($tax, 2);
                                ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total Amount:</strong>
                                <strong>৳<?php echo number_format($order['total_amount'], 2); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Info -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Delivery Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Delivery Address</h6>
                            <p><?php echo htmlspecialchars($order['delivery_address'] ?? 'Not provided'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Customer Name</h6>
                            <p><?php echo htmlspecialchars($user['name']); ?></p>
                            
                            <h6 class="text-muted mt-3">Customer Email</h6>
                            <p><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mb-4">
                    <a href="<?php echo BASE_URL; ?>/customer/orderHistory" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
                    <a href="<?php echo BASE_URL; ?>/customer/home" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>
