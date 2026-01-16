<?php 
$pageTitle = 'Low Stock Products';
include_once __DIR__ . '/../layouts/header.php';
$lowStockProducts = $lowStockProducts ?? [];
?>

<div class="container" style="margin-top: 20px;">
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0;">⚠️ Low Stock Products</h2>
            <a href="<?php echo BASE_URL; ?>inventory_manager/dashboard" class="btn btn-secondary" style="text-decoration: none;">
                Back to Dashboard
            </a>
        </div>
        <div class="card-body">
            <?php if (!empty($lowStockProducts)): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            <th style="padding: 12px; text-align: left;">Product Name</th>
                            <th style="padding: 12px; text-align: left;">Generic Name</th>
                            <th style="padding: 12px; text-align: center;">Current Stock</th>
                            <th style="padding: 12px; text-align: center;">Low Stock Threshold</th>
                            <th style="padding: 12px; text-align: right;">Price (৳)</th>
                            <th style="padding: 12px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lowStockProducts as $product): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px;">
                                    <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                </td>
                                <td style="padding: 12px; color: #666; font-size: 13px;">
                                    <?php echo htmlspecialchars($product['generic_name'] ?? 'N/A'); ?>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="background-color: #f8d7da; color: #842029; padding: 6px 12px; border-radius: 3px; font-weight: bold;">
                                        <?php echo $product['quantity']; ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="background-color: #e7e7e7; padding: 6px 12px; border-radius: 3px; font-weight: bold;">
                                        <?php echo $product['low_stock_threshold']; ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: right;">
                                    <strong>৳ <?php echo number_format($product['price'], 2); ?></strong>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <a href="<?php echo BASE_URL; ?>inventory_manager/editProduct?id=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm btn-info" style="text-decoration: none;">
                                        Edit Stock
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="padding: 40px; text-align: center; background-color: #d1e7dd; border-radius: 4px; border-left: 4px solid #0f5132;">
                    <h3 style="color: #0f5132; margin: 0 0 10px 0;">✅ All Products in Stock</h3>
                    <p style="color: #0f5132; margin: 0;">No products are below the low stock threshold.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
