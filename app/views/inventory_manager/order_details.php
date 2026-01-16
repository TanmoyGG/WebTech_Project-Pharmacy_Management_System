<?php 
$pageTitle = 'Order Details';
include_once __DIR__ . '/../layouts/header.php';
$order = $order ?? [];
$items = $items ?? [];
?>

<div class="container" style="margin-top: 20px;">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Order Details -->
        <div class="card">
            <div class="card-header">
                <h2 style="margin: 0;">Order #<?php echo $order['id']; ?></h2>
            </div>
            <div class="card-body">
                <!-- Order Status -->
                <div style="background-color: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                    <p style="margin: 0 0 10px 0; color: #666;"><strong>Current Status:</strong></p>
                    <span style="padding: 8px 16px; border-radius: 20px; font-weight: bold; font-size: 16px;
                        background-color: <?php 
                            if ($order['status'] === 'pending') echo '#fff3cd';
                            elseif ($order['status'] === 'confirmed') echo '#cfe2ff';
                            elseif ($order['status'] === 'shipped') echo '#d1e7dd';
                            elseif ($order['status'] === 'completed') echo '#d1e7dd';
                            else echo '#f8d7da';
                        ?>;
                        color: <?php 
                            if ($order['status'] === 'pending') echo '#856404';
                            elseif ($order['status'] === 'confirmed') echo '#084298';
                            elseif ($order['status'] === 'shipped') echo '#0f5132';
                            elseif ($order['status'] === 'completed') echo '#0f5132';
                            else echo '#842029';
                        ?>;
                    ">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>

                <!-- Customer Information -->
                <h3>Customer Information</h3>
                <div style="background-color: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                    <p style="margin: 8px 0;"><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p style="margin: 8px 0;"><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                    <p style="margin: 8px 0;"><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                    <p style="margin: 8px 0;"><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?></p>
                </div>

                <!-- Order Items -->
                <h3>Order Items</h3>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            <th style="padding: 10px; text-align: left;">Product</th>
                            <th style="padding: 10px; text-align: center;">Quantity</th>
                            <th style="padding: 10px; text-align: right;">Price</th>
                            <th style="padding: 10px; text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px;">
                                    <div><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></div>
                                    <div style="font-size: 12px; color: #666;">Generic: <?php echo htmlspecialchars($item['generic_name'] ?? 'N/A'); ?></div>
                                </td>
                                <td style="padding: 10px; text-align: center;"><?php echo $item['quantity']; ?></td>
                                <td style="padding: 10px; text-align: right;">৳ <?php echo number_format($item['price'], 2); ?></td>
                                <td style="padding: 10px; text-align: right;">৳ <?php echo number_format($item['subtotal'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Order Total -->
                <div style="background-color: #f9f9f9; padding: 15px; border-radius: 4px; text-align: right;">
                    <p style="margin: 8px 0; font-size: 18px;">
                        <strong>Total Amount: ৳ <?php echo number_format($order['total_amount'], 2); ?></strong>
                    </p>
                    <p style="margin: 8px 0; font-size: 12px; color: #666;">
                        Ordered on: <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Actions Panel -->
        <div class="card">
            <div class="card-header">Actions</div>
            <div class="card-body">
                <?php if ($order['status'] === 'pending'): ?>
                    <!-- Confirm Order -->
                    <form method="POST" action="<?php echo BASE_URL; ?>inventory_manager/confirmOrder" style="margin-bottom: 10px;">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <p style="margin-bottom: 10px; color: #666; font-size: 14px;">
                            Confirming this order will decrement the product quantities from inventory.
                        </p>
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Confirm this order and decrement inventory?');">
                            Confirm Order & Decrement Stock
                        </button>
                    </form>

                    <!-- Cancel Order -->
                    <form method="POST" action="<?php echo BASE_URL; ?>inventory_manager/cancelOrder" style="margin-bottom: 10px;">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Cancel this order? It can be reopened later.');">
                            Cancel Order
                        </button>
                    </form>

                <?php elseif ($order['status'] === 'confirmed'): ?>
                    <!-- Ship Order -->
                    <form method="POST" action="<?php echo BASE_URL; ?>inventory_manager/shipOrder">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <p style="margin-bottom: 10px; color: #666; font-size: 14px;">
                            Mark this order as shipped. Customer will be notified.
                        </p>
                        <button type="submit" class="btn btn-info btn-block">
                            Mark as Shipped
                        </button>
                    </form>

                    <!-- Cancel Order -->
                    <form method="POST" action="<?php echo BASE_URL; ?>inventory_manager/cancelOrder" style="margin-top: 10px;">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <p style="margin-bottom: 10px; color: #666; font-size: 12px;">
                            ⚠️ Cancelling will restore inventory quantities.
                        </p>
                        <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Cancel this order? Inventory will be restored.');">
                            Cancel Order
                        </button>
                    </form>

                <?php elseif ($order['status'] === 'shipped'): ?>
                    <div style="background-color: #d1e7dd; padding: 15px; border-radius: 4px; text-align: center;">
                        <p style="margin: 0; color: #0f5132;"><strong>✓ Order Shipped</strong></p>
                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #0f5132;">
                            Awaiting customer confirmation of delivery
                        </p>
                    </div>

                <?php elseif ($order['status'] === 'completed'): ?>
                    <div style="background-color: #d1e7dd; padding: 15px; border-radius: 4px; text-align: center;">
                        <p style="margin: 0; color: #0f5132;"><strong>✓ Order Completed</strong></p>
                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #0f5132;">
                            This order has been successfully delivered
                        </p>
                    </div>

                <?php elseif ($order['status'] === 'cancelled'): ?>
                    <div style="background-color: #f8d7da; padding: 15px; border-radius: 4px; text-align: center;">
                        <p style="margin: 0; color: #842029;"><strong>✗ Order Cancelled</strong></p>
                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #842029;">
                            This order has been cancelled
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Back Button -->
                <a href="<?php echo BASE_URL; ?>inventory_manager/orders" class="btn btn-secondary btn-block" style="text-decoration: none; margin-top: 15px;">
                    ← Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
