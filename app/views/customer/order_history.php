<?php 
$pageTitle = 'Order History';
include_once __DIR__ . '/../layouts/header.php';
$orders = $orders ?? [];
?>

<div class="card">
    <div class="card-header">My Orders</div>
    <div class="card-body">
        <?php if (!empty($orders)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?php echo (int) $order['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($order['created_at'] ?? date('Y-m-d')); ?></td>
                                <td><?php echo (int) ($order['item_count'] ?? 1); ?> items</td>
                                <td>৳ <?php echo number_format($order['total_amount'] ?? 0, 2); ?></td>
                                <td>
                                    <?php 
                                    $status = $order['status'] ?? 'pending';
                                    $badge_class = match($status) {
                                        'completed' => 'badge-success',
                                        'cancelled' => 'badge-danger',
                                        'confirmed' => 'badge-info',
                                        default => 'badge-warning'
                                    };
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>" style="padding: 5px 10px;"><?php echo ucfirst($status); ?></span>
                                </td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/customer/orderDetails?id=<?php echo (int) $order['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center" style="padding: 40px;">No orders found.</p>
            <a href="<?php echo BASE_URL; ?>/customer/browseMedicines" class="btn btn-primary">Start Shopping</a>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
