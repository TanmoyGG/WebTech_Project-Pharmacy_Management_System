<?php 
$pageTitle = 'Search Products';
include_once __DIR__ . '/../layouts/header.php';
$products = $products ?? [];
$search_query = $search_query ?? '';
?>

<div class="card">
    <div class="card-header">Search Medicines</div>
    <div class="card-body">
        <form method="GET" action="<?php echo BASE_URL; ?>/customer/productSearch" style="margin-bottom: 30px;">
            <div class="form-group">
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="q" class="form-control" placeholder="Search by name, generic name, or category..." value="<?php echo htmlspecialchars($search_query); ?>" style="flex: 1;">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <?php if ($search_query): ?>
            <p class="text-muted" style="margin-bottom: 20px;">
                Results for: <strong><?php echo htmlspecialchars($search_query); ?></strong> 
                (<?php echo count($products); ?> found)
            </p>
        <?php endif; ?>

        <?php if (!empty($products)): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                <?php foreach ($products as $product): ?>
                    <div class="product-card" style="border: 1px solid #e0e0e0; padding: 15px; border-radius: 4px;">
                        <h4 style="margin: 0 0 5px 0; font-size: 14px;"><?php echo htmlspecialchars($product['name'] ?? ''); ?></h4>
                        <p style="margin: 0 0 10px 0; font-size: 12px; color: #666;">
                            <em><?php echo htmlspecialchars($product['generic_name'] ?? ''); ?></em>
                        </p>
                        <p style="margin: 0 0 10px 0; font-size: 13px; color: #666; line-height: 1.4;">
                            <?php echo htmlspecialchars(substr($product['description'] ?? '', 0, 50) . '...'); ?>
                        </p>
                        <p style="margin: 0 0 10px 0; font-size: 16px; color: #007bff;">
                            <strong>৳ <?php echo number_format($product['price'] ?? 0, 2); ?></strong>
                        </p>
                        <p style="margin: 0 0 10px 0; font-size: 12px; color: <?php echo ($product['quantity'] > 10) ? '#27ae60' : (($product['quantity'] > 0) ? '#f39c12' : '#e74c3c'); ?>;">
                            <?php 
                            $qty = $product['quantity'] ?? 0;
                            if ($qty > 10) {
                                echo 'In Stock (' . $qty . ')';
                            } elseif ($qty > 0) {
                                echo 'Low Stock (' . $qty . ')';
                            } else {
                                echo 'Out of Stock';
                            }
                            ?>
                        </p>
                        <div style="display: flex; gap: 8px;">
                            <a href="<?php echo BASE_URL; ?>/customer/productDetails?id=<?php echo (int) $product['id']; ?>" class="btn btn-sm btn-secondary" style="flex: 1; text-align: center; padding: 6px 0;">Details</a>
                            <?php if (($product['quantity'] ?? 0) > 0): ?>
                                <form method="POST" action="<?php echo BASE_URL; ?>/customer/addToCart" style="flex: 1;">
                                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-sm btn-primary" style="width: 100%; padding: 6px 0;">Add</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($search_query): ?>
            <p class="text-muted text-center" style="padding: 40px;">No products found for "<?php echo htmlspecialchars($search_query); ?>"</p>
        <?php else: ?>
            <p class="text-muted text-center" style="padding: 40px;">Enter a search term to find medicines.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
