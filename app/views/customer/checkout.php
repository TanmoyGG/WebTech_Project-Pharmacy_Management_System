<?php 
$pageTitle = 'Checkout';
include_once __DIR__ . '/../layouts/header.php';
$items = $items ?? [];
$totals = $totals ?? [];
$user = $user ?? [];
?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
    <!-- Order Review -->
    <div class="card">
        <div class="card-header">Order Review</div>
        <div class="card-body">
            <div class="table-container" style="font-size: 14px;">
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name'] ?? 'Product'); ?></td>
                                <td><?php echo (int) $item['quantity']; ?></td>
                                <td>৳ <?php echo number_format($item['price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 15px; border-top: 1px solid #ddd; padding-top: 10px;">
                <p>Subtotal: ৳ <?php echo number_format($totals['subtotal'] ?? 0, 2); ?></p>
                <p>Tax (5%): ৳ <?php echo number_format($totals['tax'] ?? 0, 2); ?></p>
                <p style="font-size: 16px; color: #007bff;"><strong>Total: ৳ <?php echo number_format($totals['total'] ?? 0, 2); ?></strong></p>
            </div>
        </div>
    </div>

    <!-- Checkout Form -->
    <div class="card">
        <div class="card-header">Delivery & Payment</div>
        <div class="card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>/customer/checkout">
                <div class="form-group">
                    <label>Delivery Address</label>
                    <textarea name="delivery_address" class="form-control" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Delivery Phone</label>
                    <input type="tel" name="delivery_phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method" class="form-control" required>
                        <option value="">-- Select Method --</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="cash">Cash on Delivery</option>
                        <option value="online_banking">Online Banking</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="terms_agree" required> I agree to the Terms & Conditions
                    </label>
                </div>

                <button type="submit" class="btn btn-success btn-block" style="width: 100%; padding: 10px;">Place Order (৳ <?php echo number_format($totals['total'] ?? 0, 2); ?>)</button>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
