<?php 
$pageTitle = 'Shopping Cart';
include_once __DIR__ . '/../layouts/header.php';
$items = $items ?? [];
$totals = $totals ?? [];
?>

<div class="card">
    <div class="card-header">Shopping Cart</div>
    <div class="card-body">
        <?php if (!empty($items)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name'] ?? 'Product'); ?></td>
                                <td>৳ <?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <form action="<?php echo BASE_URL; ?>/customer/updateCartQuantity" method="POST" style="display: flex; gap: 5px; align-items: center;">
                                        <input type="hidden" name="cart_item_id" value="<?php echo (int) $item['id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo (int) $item['quantity']; ?>" min="1" style="width: 60px; padding: 5px;">
                                        <button type="submit" class="btn btn-sm" style="padding: 5px 10px; font-size: 12px;">Update</button>
                                    </form>
                                </td>
                                <td>৳ <?php echo number_format($item['subtotal'] ?? 0, 2); ?></td>
                                <td>
                                    <form action="<?php echo BASE_URL; ?>/customer/removeFromCart" method="POST" style="display: inline;">
                                        <input type="hidden" name="cart_item_id" value="<?php echo (int) $item['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Are you sure you want to remove this item?');">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-20" style="text-align: right; border-top: 1px solid #ddd; padding-top: 15px;">
                <p>Subtotal: <strong>৳ <?php echo number_format($totals['subtotal'] ?? 0, 2); ?></strong></p>
                <p>Tax (5%): <strong>৳ <?php echo number_format($totals['tax'] ?? 0, 2); ?></strong></p>
                <p style="font-size: 18px; color: #007bff;"><strong>Total: ৳ <?php echo number_format($totals['total'] ?? 0, 2); ?></strong></p>
                <a href="<?php echo BASE_URL; ?>/customer/checkout" class="btn btn-primary mt-20">Proceed to Checkout</a>
                <a href="<?php echo BASE_URL; ?>/customer/browseMedicines" class="btn btn-secondary mt-20">Continue Shopping</a>
            </div>
        <?php else: ?>
            <p class="text-muted text-center" style="padding: 40px;">Your cart is empty.</p>
            <a href="<?php echo BASE_URL; ?>/customer/browseMedicines" class="btn btn-primary">Browse Medicines</a>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
