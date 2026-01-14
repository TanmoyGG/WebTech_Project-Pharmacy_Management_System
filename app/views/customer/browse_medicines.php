<?php 
$pageTitle = 'Browse Medicines';
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header">Browse Medicines</div>
    <div class="card-body">
        <?php $products = $products ?? []; ?>
        <?php if (!empty($products)): ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="text-muted">Generic: <?php echo htmlspecialchars($product['generic_name'] ?? 'N/A'); ?></p>
                        <p class="price">৳ <?php echo number_format($product['price'], 2); ?></p>
                        <p class="stock">Stock: <?php echo (int) $product['quantity']; ?></p>
                        <form action="<?php echo BASE_URL; ?>/customer/addToCart" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                            <div style="margin-bottom: 10px;">
                                <label for="qty_<?php echo (int) $product['id']; ?>">Quantity:</label>
                                <input type="number" id="qty_<?php echo (int) $product['id']; ?>" name="quantity" value="1" min="1" max="<?php echo (int) $product['quantity']; ?>" style="width: 60px; padding: 5px;">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Add to Cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No medicines available.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
