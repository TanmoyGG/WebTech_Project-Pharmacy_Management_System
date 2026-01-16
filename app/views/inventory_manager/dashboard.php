<?php 
$pageTitle = 'Inventory Manager Dashboard';
include_once __DIR__ . '/../layouts/header.php';
$productStats = $productStats ?? [];
$lowStockProducts = $lowStockProducts ?? [];
$expiringProducts = $expiringProducts ?? [];
?>

<div class="container" style="margin-top: 20px;">
    <h1>Inventory Manager Dashboard</h1>
    <p class="text-muted">Welcome to your inventory management dashboard</p>

    <!-- Quick Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 30px 0;">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body">
                <h3 style="margin: 0 0 10px 0; font-size: 36px;"><?php echo $productStats['total'] ?? 0; ?></h3>
                <p style="margin: 0; opacity: 0.9;">Total Products</p>
            </div>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <div class="card-body">
                <h3 style="margin: 0 0 10px 0; font-size: 36px;"><?php echo $productStats['low_stock'] ?? 0; ?></h3>
                <p style="margin: 0; opacity: 0.9;">Low Stock Items</p>
            </div>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <div class="card-body">
                <h3 style="margin: 0 0 10px 0; font-size: 36px;"><?php echo $productStats['expiring_soon'] ?? 0; ?></h3>
                <p style="margin: 0; opacity: 0.9;">Expiring Soon</p>
            </div>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <div class="card-body">
                <h3 style="margin: 0 0 10px 0; font-size: 36px;"><?php echo $productStats['available'] ?? 0; ?></h3>
                <p style="margin: 0; opacity: 0.9;">Available Products</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">Quick Actions</div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <a href="<?php echo BASE_URL; ?>inventory_manager/orders" class="btn btn-primary" style="text-decoration: none;">
                    📦 Manage Orders
                </a>
                <a href="<?php echo BASE_URL; ?>inventory_manager/products" class="btn btn-info" style="text-decoration: none;">
                    💊 View Products
                </a>
                <a href="<?php echo BASE_URL; ?>inventory_manager/low_stock" class="btn btn-warning" style="text-decoration: none;">
                    ⚠️ Low Stock Alerts
                </a>
                <a href="<?php echo BASE_URL; ?>inventory_manager/expiring" class="btn btn-danger" style="text-decoration: none;">
                    ⏰ Expiring Items
                </a>
            </div>
        </div>
    </div>

    <!-- Low Stock Products -->
    <?php if (!empty($lowStockProducts)): ?>
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">⚠️ Low Stock Products</div>
            <div class="card-body">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            <th style="padding: 10px; text-align: left;">Product</th>
                            <th style="padding: 10px; text-align: center;">Current Stock</th>
                            <th style="padding: 10px; text-align: center;">Threshold</th>
                            <th style="padding: 10px; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lowStockProducts as $product): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px;"><?php echo htmlspecialchars($product['name']); ?></td>
                                <td style="padding: 10px; text-align: center;">
                                    <span style="background-color: #f8d7da; color: #842029; padding: 4px 8px; border-radius: 3px; font-weight: bold;">
                                        <?php echo $product['quantity']; ?>
                                    </span>
                                </td>
                                <td style="padding: 10px; text-align: center;"><?php echo $product['low_stock_threshold']; ?></td>
                                <td style="padding: 10px; text-align: right;">
                                    <a href="<?php echo BASE_URL; ?>inventory_manager/products?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-info">
                                        Update Stock
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Expiring Products -->
    <?php if (!empty($expiringProducts)): ?>
        <div class="card">
            <div class="card-header">⏰ Products Expiring Soon (Next 30 Days)</div>
            <div class="card-body">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            <th style="padding: 10px; text-align: left;">Product</th>
                            <th style="padding: 10px; text-align: center;">Stock</th>
                            <th style="padding: 10px; text-align: center;">Expiry Date</th>
                            <th style="padding: 10px; text-align: center;">Days Left</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expiringProducts as $product): ?>
                            <?php 
                            $expiryDate = new DateTime($product['expiry_date']);
                            $today = new DateTime();
                            $daysLeft = $today->diff($expiryDate)->days;
                            ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px;"><?php echo htmlspecialchars($product['name']); ?></td>
                                <td style="padding: 10px; text-align: center;"><?php echo $product['quantity']; ?></td>
                                <td style="padding: 10px; text-align: center;"><?php echo date('M d, Y', strtotime($product['expiry_date'])); ?></td>
                                <td style="padding: 10px; text-align: center;">
                                    <span style="background-color: <?php echo $daysLeft <= 7 ? '#f8d7da' : '#fff3cd'; ?>; 
                                                 color: <?php echo $daysLeft <= 7 ? '#842029' : '#856404'; ?>; 
                                                 padding: 4px 8px; border-radius: 3px; font-weight: bold;">
                                        <?php echo $daysLeft; ?> days
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>