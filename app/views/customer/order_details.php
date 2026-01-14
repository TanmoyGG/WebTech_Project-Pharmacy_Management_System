<?php 
$pageTitle = 'Order Details';
include_once __DIR__ . '/../layouts/header.php';
$order = $order ?? [];
$items = $items ?? [];
$user = $user ?? [];
?>

<div style="margin-bottom: 20px;">
    <a href="<?php echo BASE_URL; ?>/customer/orderHistory" class="btn btn-secondary">&larr; Back to Orders</a>
</div>

<div class="card">
    <div class="card-header">Order #<?php echo (int) $order['id']; ?> - Details</div>
    <div class="card-body">
        <!-- Order Information Section -->
        <div style="margin-bottom: 30px;">
            <h5 style="border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 15px;">Order Information</h5>
            <table>
                <tbody>
                    <tr>
                        <th style="width: 25%;">Order Number</th>
                        <td><strong>#<?php echo (int) $order['id']; ?></strong></td>
                        <th style="width: 25%;">Order Date</th>
                        <td><?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></td>
                    </tr>
                    <tr>
                        <th>Order Status</th>
                        <td>
                            <?php
                            $status = $order['status'];
                            $badge_class = match($status) {
                                'completed' => 'badge-success',
                                'cancelled' => 'badge-danger',
                                'pending' => 'badge-warning',
                                'processing' => 'badge-info',
                                'shipped' => 'badge-info',
                                'delivered' => 'badge-success',
                                default => 'badge-warning'
                            };
                            ?>
                            <span class="badge <?php echo $badge_class; ?>" style="padding: 5px 10px;"><?php echo ucfirst($status); ?></span>
                        </td>
                        <th>Last Updated</th>
                        <td><?php echo date('F j, Y g:i A', strtotime($order['updated_at'])); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Customer Information Section -->
        <div style="margin-bottom: 30px;">
            <h5 style="border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 15px;">Customer & Delivery Information</h5>
            <table>
                <tbody>
                    <tr>
                        <th style="width: 25%;">Customer Name</th>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <th style="width: 25%;">Customer Email</th>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Delivery Address</th>
                        <td colspan="3"><?php echo htmlspecialchars($order['delivery_address'] ?? 'Not provided'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Order Items Section -->
        <div style="margin-bottom: 30px;">
            <h5 style="border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 15px;">Order Items</h5>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
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
                            <td><?php echo (int) $item['quantity']; ?></td>
                            <td>৳<?php echo number_format($item['price'], 2); ?></td>
                            <td>৳<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Price Summary Section -->
        <div style="margin-bottom: 30px;">
            <h5 style="border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 15px;">Price Summary</h5>
            <table>
                <tbody>
                    <tr>
                        <th style="width: 75%; text-align: left;">Subtotal:</th>
                        <td style="text-align: right;">৳<?php 
                            $subtotal = 0;
                            foreach ($items as $item) {
                                $subtotal += $item['subtotal'];
                            }
                            echo number_format($subtotal, 2);
                        ?></td>
                    </tr>
                    <tr>
                        <th style="text-align: left;">Tax (5%):</th>
                        <td style="text-align: right;">৳<?php 
                            $tax = $subtotal * 0.05;
                            echo number_format($tax, 2);
                        ?></td>
                    </tr>
                    <tr style="border-top: 2px solid #ddd; font-weight: bold;">
                        <th style="text-align: left;">Total Amount:</th>
                        <td style="text-align: right; font-size: 1.2em; color: #28a745;">৳<?php echo number_format($order['total_amount'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div style="margin-top: 20px; margin-bottom: 20px;">
    <a href="<?php echo BASE_URL; ?>/customer/orderHistory" class="btn btn-secondary">Back to Orders</a>
    <a href="<?php echo BASE_URL; ?>/customer/home" class="btn btn-primary">Continue Shopping</a>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>