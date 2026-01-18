<?php 
$pageTitle = 'Manage Products';
include_once __DIR__ . '/../layouts/header.php';
$products = $products ?? [];
$categories = $categories ?? [];
$search = $search ?? '';
$category_filter = $category_filter ?? '';
$status_filter = $status_filter ?? 'all';
?>

<div class="container" style="margin-top: 20px;">
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0;">Product Management</h2>
            <a href="<?php echo BASE_URL; ?>inventory_manager/addProduct" class="btn btn-success" style="text-decoration: none;">
                + Add New Product
            </a>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="<?php echo BASE_URL; ?>inventory_manager/products" style="margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 10px;">
                    <input type="text" name="search" placeholder="Search by name or generic name..." 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    
                    <select name="category" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="status" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                        <option value="available" <?php echo $status_filter === 'available' ? 'selected' : ''; ?>>Available</option>
                        <option value="out_of_stock" <?php echo $status_filter === 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                        <option value="discontinued" <?php echo $status_filter === 'discontinued' ? 'selected' : ''; ?>>Discontinued</option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>

            <!-- Products Table -->
            <?php if (!empty($products)): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            <th style="padding: 12px; text-align: left;">Product Name</th>
                            <th style="padding: 12px; text-align: left;">Generic Name</th>
                            <th style="padding: 12px; text-align: left;">Category</th>
                            <th style="padding: 12px; text-align: right;">Price</th>
                            <th style="padding: 12px; text-align: center;">Stock</th>
                            <th style="padding: 12px; text-align: center;">Status</th>
                            <th style="padding: 12px; text-align: center;">Expiry</th>
                            <th style="padding: 12px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <?php
                            // Check if low stock
                            $isLowStock = $product['quantity'] <= $product['low_stock_threshold'];
                            // Check if expiring soon (within 30 days)
                            $expiryDate = new DateTime($product['expiry_date']);
                            $today = new DateTime();
                            $isExpired = $expiryDate < $today;
                            $daysToExpiry = $today->diff($expiryDate)->days;
                            $isExpiringSoon = $daysToExpiry <= 30 && !$isExpired;
                            ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px;"><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                                <td style="padding: 12px; font-size: 13px; color: #666;"><?php echo htmlspecialchars($product['generic_name'] ?? 'N/A'); ?></td>
                                <td style="padding: 12px; font-size: 13px;"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                                <td style="padding: 12px; text-align: right;"><strong>৳ <?php echo number_format($product['price'], 2); ?></strong></td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="padding: 4px 8px; border-radius: 3px; font-weight: bold;
                                                 background-color: <?php echo $isLowStock ? '#f8d7da' : '#d1e7dd'; ?>;
                                                 color: <?php echo $isLowStock ? '#842029' : '#0f5132'; ?>;">
                                        <?php echo $product['quantity']; ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; white-space: nowrap;
                                                 background-color: <?php 
                                                     if ($product['status'] === 'available') echo '#d1e7dd';
                                                     elseif ($product['status'] === 'out_of_stock') echo '#fff3cd';
                                                     else echo '#f8d7da';
                                                 ?>;
                                                 color: <?php 
                                                     if ($product['status'] === 'available') echo '#0f5132';
                                                     elseif ($product['status'] === 'out_of_stock') echo '#856404';
                                                     else echo '#842029';
                                                 ?>;">
                                        <?php echo ucfirst(str_replace('_', ' ', $product['status'])); ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center; font-size: 12px;">
                                    <div><?php echo date('M d, Y', strtotime($product['expiry_date'])); ?></div>
                                    <?php if ($isExpired): ?>
                                        <div style="color: #842029; font-weight: bold; margin-top: 2px;">
                                            ❌ EXPIRED
                                        </div>
                                    <?php elseif ($isExpiringSoon): ?>
                                        <div style="color: #842029; font-weight: bold; margin-top: 2px;">
                                            ⚠️ <?php echo $daysToExpiry; ?> days left
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <a href="<?php echo BASE_URL; ?>inventory_manager/editProduct?id=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm btn-info" style="text-decoration: none; margin-right: 5px;">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="padding: 40px; text-align: center; background-color: #f9f9f9; border-radius: 4px;">
                    <p style="color: #666; margin: 0;">No products found. <a href="<?php echo BASE_URL; ?>inventory_manager/addProduct">Add your first product</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
