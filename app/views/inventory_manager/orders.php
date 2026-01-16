<?php 
$pageTitle = 'Manage Orders';
include_once __DIR__ . '/../layouts/header.php';
$orders = $orders ?? [];
$statusFilter = $statusFilter ?? 'pending';
$statusCounts = $statusCounts ?? [];
?>

<div class="container" style="margin-top: 20px;">
    <div class="card">
        <div class="card-header">
            <h2 style="margin: 0;">Order Management</h2>
        </div>
        <div class="card-body">
            <!-- Status Filter Tabs -->
            <div style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 15px;">
                <a href="<?php echo BASE_URL; ?>inventory_manager/orders?status=pending" 
                   class="btn <?php echo $statusFilter === 'pending' ? 'btn-primary' : 'btn-outline'; ?>" style="text-decoration: none;">
                    Pending (<?php echo $statusCounts['pending'] ?? 0; ?>)
                </a>
                <a href="<?php echo BASE_URL; ?>inventory_manager/orders?status=confirmed" 
                   class="btn <?php echo $statusFilter === 'confirmed' ? 'btn-primary' : 'btn-outline'; ?>" style="text-decoration: none;">
                    Confirmed (<?php echo $statusCounts['confirmed'] ?? 0; ?>)
                </a>
                <a href="<?php echo BASE_URL; ?>inventory_manager/orders?status=shipped" 
                   class="btn <?php echo $statusFilter === 'shipped' ? 'btn-primary' : 'btn-outline'; ?>" style="text-decoration: none;">
                    Shipped (<?php echo $statusCounts['shipped'] ?? 0; ?>)
                </a>
                <a href="<?php echo BASE_URL; ?>inventory_manager/orders?status=completed" 
                   class="btn <?php echo $statusFilter === 'completed' ? 'btn-primary' : 'btn-outline'; ?>" style="text-decoration: none;">
                    Completed (<?php echo $statusCounts['completed'] ?? 0; ?>)
                </a>
                <a href="<?php echo BASE_URL; ?>inventory_manager/orders?status=cancelled" 
                   class="btn <?php echo $statusFilter === 'cancelled' ? 'btn-primary' : 'btn-outline'; ?>" style="text-decoration: none;">
                    Cancelled (<?php echo $statusCounts['cancelled'] ?? 0; ?>)
                </a>
                <a href="<?php echo BASE_URL; ?>inventory_manager/orders?status=all" 
                   class="btn <?php echo $statusFilter === 'all' ? 'btn-primary' : 'btn-outline'; ?>" style="text-decoration: none;">
                    All Orders
                </a>
            </div>

            <!-- Orders Table -->
            <?php if (!empty($orders)): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            <th style="padding: 12px; text-align: left;">Order ID</th>
                            <th style="padding: 12px; text-align: left;">Customer</th>
                            <th style="padding: 12px; text-align: left;">Items</th>
                            <th style="padding: 12px; text-align: left;">Amount</th>
                            <th style="padding: 12px; text-align: left;">Status</th>
                            <th style="padding: 12px; text-align: left;">Date</th>
                            <th style="padding: 12px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr style="border-bottom: 1px solid #eee; hover-color: #f9f9f9;">
                                <td style="padding: 12px;"><strong>#<?php echo $order['id']; ?></strong></td>
                                <td style="padding: 12px;">
                                    <div><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                    <div style="font-size: 12px; color: #666;"><?php echo htmlspecialchars($order['customer_email']); ?></div>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="background-color: #e3f2fd; padding: 4px 8px; border-radius: 3px;">
                                        <?php echo $order['item_count']; ?> item<?php echo $order['item_count'] != 1 ? 's' : ''; ?>
                                    </span>
                                </td>
                                <td style="padding: 12px;"><strong>৳ <?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                <td style="padding: 12px;">
                                    <span style="padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;
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
                                </td>
                                <td style="padding: 12px; font-size: 12px; color: #666;">
                                    <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <a href="<?php echo BASE_URL; ?>inventory_manager/orderDetails?id=<?php echo $order['id']; ?>" 
                                       class="btn btn-sm btn-info" style="text-decoration: none; padding: 6px 12px; font-size: 12px;">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="padding: 40px; text-align: center; background-color: #f9f9f9; border-radius: 4px;">
                    <p style="color: #666; margin: 0;">No orders found with status "<?php echo ucfirst($statusFilter); ?>"</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
