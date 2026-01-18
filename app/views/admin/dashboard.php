<!-- Master Admin Dashboard -->
<div class="container" style="padding: 20px;">
    <h1 style="color: #2c3e50; margin-bottom: 30px;">
        <i class="fas fa-tachometer-alt"></i> Master Admin Dashboard
    </h1>

    <!-- Quick Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        
        <!-- Total Revenue -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Total Revenue</div>
            <div style="font-size: 32px; font-weight: bold;">GHS <?php echo number_format($totalRevenue ?? 0, 2); ?></div>
        </div>

        <!-- Total Users -->
        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Total Users</div>
            <div style="font-size: 32px; font-weight: bold;"><?php echo $userStats['total_users'] ?? 0; ?></div>
            <div style="font-size: 12px; opacity: 0.8; margin-top: 5px;">
                <?php echo ($userStats['customers'] ?? 0); ?> Customers | <?php echo ($userStats['inventory_managers'] ?? 0); ?> Managers
            </div>
        </div>

        <!-- Total Orders -->
        <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Total Orders</div>
            <div style="font-size: 32px; font-weight: bold;"><?php echo $orderStats['total_orders'] ?? 0; ?></div>
            <div style="font-size: 12px; opacity: 0.8; margin-top: 5px;">
                <?php echo ($orderStats['pending_orders'] ?? 0); ?> Pending
            </div>
        </div>

        <!-- Total Products -->
        <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Total Products</div>
            <div style="font-size: 32px; font-weight: bold;"><?php echo $productStats['total_products'] ?? 0; ?></div>
            <div style="font-size: 12px; opacity: 0.8; margin-top: 5px;">
                <?php echo ($productStats['available_products'] ?? 0); ?> Available
            </div>
        </div>

    </div>

    <!-- Quick Actions -->
    <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h3 style="color: #2c3e50; margin-bottom: 15px;">Quick Actions</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="<?php echo BASE_URL; ?>admin/users" style="background: #667eea; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <a href="<?php echo BASE_URL; ?>admin/inventory" style="background: #43e97b; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-boxes"></i> View Inventory
            </a>
            <a href="<?php echo BASE_URL; ?>admin/transactionHistory" style="background: #f5576c; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-file-invoice-dollar"></i> Transactions
            </a>
            <a href="<?php echo BASE_URL; ?>admin/systemConfig" style="background: #4facfe; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-cog"></i> System Config
            </a>
            <a href="<?php echo BASE_URL; ?>admin/reports" style="background: #764ba2; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-chart-bar"></i> Generate Reports
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">

        <!-- Top Selling Products -->
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="color: #2c3e50; margin-bottom: 15px;">
                <i class="fas fa-fire"></i> Top Selling Products
            </h3>
            <?php if (!empty($mostSoldProducts)): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; text-align: left;">
                            <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Product</th>
                            <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Sold</th>
                            <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mostSoldProducts as $product): ?>
                            <tr style="border-bottom: 1px solid #f1f3f5;">
                                <td style="padding: 10px;">
                                    <div style="font-weight: 600; color: #2c3e50;"><?php echo htmlspecialchars($product['name']); ?></div>
                                    <div style="font-size: 12px; color: #868e96;"><?php echo htmlspecialchars($product['generic_name']); ?></div>
                                </td>
                                <td style="padding: 10px; color: #495057;">
                                    <?php echo $product['total_sold']; ?> units
                                </td>
                                <td style="padding: 10px; font-weight: 600; color: #28a745;">
                                    GHS <?php echo number_format($product['total_revenue'], 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #868e96; text-align: center; padding: 20px;">No sales data available</p>
            <?php endif; ?>
        </div>

        <!-- Recent Transactions -->
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="color: #2c3e50; margin-bottom: 15px;">
                <i class="fas fa-receipt"></i> Recent Transactions
            </h3>
            <?php if (!empty($recentTransactions)): ?>
                <div style="max-height: 400px; overflow-y: auto;">
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
                                <div style="font-weight: 600; color: #2c3e50;">
                                    Order #<?php echo $transaction['order_id']; ?>
                                </div>
                                <div style="font-size: 12px; color: #868e96;">
                                    <?php echo date('M d, Y H:i', strtotime($transaction['transaction_date'])); ?>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: 600; color: #2c3e50;">
                                    GHS <?php echo number_format($transaction['amount'], 2); ?>
                                </div>
                                <div style="font-size: 11px; padding: 2px 8px; border-radius: 4px; background: <?php echo $color; ?>; color: white; display: inline-block; margin-top: 4px;">
                                    <?php echo strtoupper($transaction['status']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #868e96; text-align: center; padding: 20px;">No recent transactions</p>
            <?php endif; ?>
        </div>

    </div>

    <!-- Order Statistics by Status -->
    <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 20px;">
        <h3 style="color: #2c3e50; margin-bottom: 20px;">
            <i class="fas fa-chart-pie"></i> Order Status Distribution
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
            <div style="text-align: center; padding: 15px; background: #f8d7da; border-radius: 8px;">
                <div style="font-size: 24px; font-weight: bold; color: #842029;">
                    <?php echo $orderStats['pending_orders'] ?? 0; ?>
                </div>
                <div style="color: #58151c; font-size: 14px;">Pending</div>
            </div>
            <div style="text-align: center; padding: 15px; background: #fff3cd; border-radius: 8px;">
                <div style="font-size: 24px; font-weight: bold; color: #856404;">
                    <?php echo $orderStats['confirmed_orders'] ?? 0; ?>
                </div>
                <div style="color: #664d03; font-size: 14px;">Confirmed</div>
            </div>
            <div style="text-align: center; padding: 15px; background: #d1e7dd; border-radius: 8px;">
                <div style="font-size: 24px; font-weight: bold; color: #0f5132;">
                    <?php echo $orderStats['shipped_orders'] ?? 0; ?>
                </div>
                <div style="color: #0a3622; font-size: 14px;">Shipped</div>
            </div>
            <div style="text-align: center; padding: 15px; background: #c3e6cb; border-radius: 8px;">
                <div style="font-size: 24px; font-weight: bold; color: #155724;">
                    <?php echo $orderStats['completed_orders'] ?? 0; ?>
                </div>
                <div style="color: #0b2e13; font-size: 14px;">Completed</div>
            </div>
            <div style="text-align: center; padding: 15px; background: #e2e3e5; border-radius: 8px;">
                <div style="font-size: 24px; font-weight: bold; color: #383d41;">
                    <?php echo $orderStats['cancelled_orders'] ?? 0; ?>
                </div>
                <div style="color: #202326; font-size: 14px;">Cancelled</div>
            </div>
        </div>
    </div>

</div>