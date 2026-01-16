<?php 
$pageTitle = 'Expiring Items';
include_once __DIR__ . '/../layouts/header.php';
$expiringProducts = $expiringProducts ?? [];
?>

<div class="container" style="margin-top: 20px;">
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0;">⏰ Products Expiring Soon (Next 30 Days)</h2>
            <a href="<?php echo BASE_URL; ?>inventory_manager/dashboard" class="btn btn-secondary" style="text-decoration: none;">
                Back to Dashboard
            </a>
        </div>
        <div class="card-body">
            <?php if (!empty($expiringProducts)): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            <th style="padding: 12px; text-align: left;">Product Name</th>
                            <th style="padding: 12px; text-align: left;">Generic Name</th>
                            <th style="padding: 12px; text-align: center;">Stock</th>
                            <th style="padding: 12px; text-align: center;">Expiry Date</th>
                            <th style="padding: 12px; text-align: center;">Days Left</th>
                            <th style="padding: 12px; text-align: center;">Action</th>
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
                                <td style="padding: 12px;">
                                    <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                </td>
                                <td style="padding: 12px; color: #666; font-size: 13px;">
                                    <?php echo htmlspecialchars($product['generic_name'] ?? 'N/A'); ?>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <?php echo $product['quantity']; ?>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <strong><?php echo date('M d, Y', strtotime($product['expiry_date'])); ?></strong>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="background-color: <?php echo $daysLeft <= 7 ? '#f8d7da' : '#fff3cd'; ?>; 
                                                 color: <?php echo $daysLeft <= 7 ? '#842029' : '#856404'; ?>; 
                                                 padding: 6px 12px; border-radius: 3px; font-weight: bold;">
                                        <?php echo $daysLeft; ?> days
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <a href="<?php echo BASE_URL; ?>inventory_manager/editProduct?id=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm btn-info" style="text-decoration: none;">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="padding: 40px; text-align: center; background-color: #d1e7dd; border-radius: 4px; border-left: 4px solid #0f5132;">
                    <h3 style="color: #0f5132; margin: 0 0 10px 0;">✅ No Expiring Products</h3>
                    <p style="color: #0f5132; margin: 0;">No products are expiring in the next 30 days.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
