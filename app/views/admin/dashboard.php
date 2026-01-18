<?php 
$pageTitle = 'Admin Dashboard';
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="container" style="margin-top: 20px;">
    <h1>Admin Dashboard</h1>
    <p class="text-muted">Platform-wide overview</p>

    <!-- Quick Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 30px 0;">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body">
                <h3 style="margin: 0 0 8px 0; font-size: 28px;">৳ <?php echo number_format($totalRevenue ?? 0, 2); ?></h3>
                <p style="margin: 0; opacity: 0.9; font-size: 13px;">Total Revenue</p>
            </div>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <div class="card-body">
                <h3 style="margin: 0 0 8px 0; font-size: 28px;"><?php echo $userStats['total_users'] ?? 0; ?></h3>
                <p style="margin: 0; opacity: 0.9; font-size: 13px;">Users (<?php echo ($userStats['customers'] ?? 0); ?> customers / <?php echo ($userStats['inventory_managers'] ?? 0); ?> managers)</p>
            </div>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <div class="card-body">
                <h3 style="margin: 0 0 8px 0; font-size: 28px;"><?php echo $orderStats['total_orders'] ?? 0; ?></h3>
                <p style="margin: 0; opacity: 0.9; font-size: 13px;">Total Orders</p>
            </div>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <div class="card-body">
                <h3 style="margin: 0 0 8px 0; font-size: 28px;"><?php echo $productStats['total_products'] ?? 0; ?></h3>
                <p style="margin: 0; opacity: 0.9; font-size: 13px;">Products (<?php echo ($productStats['available_products'] ?? 0); ?> available)</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">Quick Actions</div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <a href="<?php echo BASE_URL; ?>admin/users" class="btn btn-primary" style="text-decoration: none;">👥 Manage Users</a>
                <a href="<?php echo BASE_URL; ?>admin/inventory" class="btn btn-primary" style="text-decoration: none;">📦 Inventory</a>
                <a href="<?php echo BASE_URL; ?>admin/transactionHistory" class="btn btn-primary" style="text-decoration: none;">💳 Transactions</a>
                <a href="<?php echo BASE_URL; ?>admin/systemConfig" class="btn btn-primary" style="text-decoration: none;">⚙️ Settings</a>
                <a href="<?php echo BASE_URL; ?>admin/reports" class="btn btn-primary" style="text-decoration: none;">📈 Reports</a>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 15px;">
        <!-- Top Selling Products -->
        <div class="card">
            <div class="card-header">Top Selling Products</div>
            <div class="card-body">
                <?php if (!empty($mostSoldProducts)): ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                                <th style="padding: 10px; text-align: left;">Product</th>
                                <th style="padding: 10px; text-align: center;">Sold</th>
                                <th style="padding: 10px; text-align: right;">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mostSoldProducts as $product): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 10px;">
                                        <div style="font-weight: 600; color: #2c3e50;"><?php echo htmlspecialchars($product['name']); ?></div>
                                        <div style="font-size: 12px; color: #868e96;"><?php echo htmlspecialchars($product['generic_name']); ?></div>
                                    </td>
                                    <td style="padding: 10px; text-align: center; color: #495057;">
                                        <?php echo $product['total_sold']; ?> units
                                    </td>
                                    <td style="padding: 10px; text-align: right; font-weight: 600; color: #28a745;">
                                        ৳ <?php echo number_format($product['total_revenue'], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted" style="margin: 0;">No sales data available</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card">
            <div class="card-header">Recent Transactions</div>
            <div class="card-body" style="max-height: 420px; overflow-y: auto;">
                <?php if (!empty($recentTransactions)): ?>
                    <?php foreach ($recentTransactions as $transaction): 
                        $statusColors = [
                            'completed' => '#28a745',
                            'pending' => '#ffc107',
                            'failed' => '#dc3545',
                            'refunded' => '#6c757d'
                        ];
                        $color = $statusColors[$transaction['status']] ?? '#6c757d';
                    ?>
                        <div style="padding: 12px; border-bottom: 1px solid #f1f3f5; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 600; color: #2c3e50;">Order #<?php echo $transaction['order_id']; ?></div>
                                <div style="font-size: 12px; color: #868e96;">
                                    <?php echo date('M d, Y H:i', strtotime($transaction['transaction_date'])); ?>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: 600; color: #2c3e50;">৳ <?php echo number_format($transaction['amount'], 2); ?></div>
                                <div style="font-size: 11px; padding: 2px 8px; border-radius: 4px; background: <?php echo $color; ?>; color: white; display: inline-block; margin-top: 4px;">
                                    <?php echo strtoupper($transaction['status']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted" style="margin: 0;">No recent transactions</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Order Statistics by Status -->
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">Order Status Distribution</div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                <div style="text-align: center; padding: 12px; background: #f8d7da; border-radius: 6px;">
                    <div style="font-size: 22px; font-weight: bold; color: #842029;">
                        <?php echo $orderStats['pending_orders'] ?? 0; ?>
                    </div>
                    <div style="color: #58151c; font-size: 13px;">Pending</div>
                </div>
                <div style="text-align: center; padding: 12px; background: #fff3cd; border-radius: 6px;">
                    <div style="font-size: 22px; font-weight: bold; color: #856404;">
                        <?php echo $orderStats['confirmed_orders'] ?? 0; ?>
                    </div>
                    <div style="color: #664d03; font-size: 13px;">Confirmed</div>
                </div>
                <div style="text-align: center; padding: 12px; background: #d1e7dd; border-radius: 6px;">
                    <div style="font-size: 22px; font-weight: bold; color: #0f5132;">
                        <?php echo $orderStats['shipped_orders'] ?? 0; ?>
                    </div>
                    <div style="color: #0a3622; font-size: 13px;">Shipped</div>
                </div>
                <div style="text-align: center; padding: 12px; background: #c3e6cb; border-radius: 6px;">
                    <div style="font-size: 22px; font-weight: bold; color: #155724;">
                        <?php echo $orderStats['completed_orders'] ?? 0; ?>
                    </div>
                    <div style="color: #0b2e13; font-size: 13px;">Completed</div>
                </div>
                <div style="text-align: center; padding: 12px; background: #e2e3e5; border-radius: 6px;">
                    <div style="font-size: 22px; font-weight: bold; color: #383d41;">
                        <?php echo $orderStats['cancelled_orders'] ?? 0; ?>
                    </div>
                    <div style="color: #202326; font-size: 13px;">Cancelled</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>