<?php 
$pageTitle = 'Inventory Management';
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="container" style="padding: 20px;">
    <h1 style="color: #2c3e50; margin-bottom: 20px;">
        <i class="fas fa-boxes"></i> Inventory Oversight
    </h1>

    <!-- Inventory Summary - Compact -->
    <?php
    $totalProducts = count($products ?? []);
    $availableCount = 0;
    $outOfStockCount = 0;
    $lowStockCount = 0;
    $expiredCount = 0;
    $totalValue = 0.0;
    $today = date('Y-m-d');
    foreach (($products ?? []) as $product) {
        if (($product['status'] ?? '') === 'available') $availableCount++;
        if (($product['status'] ?? '') === 'out_of_stock') $outOfStockCount++;
        if (isset($product['quantity'], $product['low_stock_threshold']) && $product['quantity'] <= $product['low_stock_threshold']) $lowStockCount++;
        if (!empty($product['expiry_date']) && $product['expiry_date'] < $today) $expiredCount++;
        if (isset($product['price'], $product['quantity'])) $totalValue += ((float)$product['price'] * (int)$product['quantity']);
    }
    ?>
    <div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
        <div style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:12px 16px;border-radius:8px;flex:1;min-width:140px;">
            <div style="font-size:11px;opacity:0.9;margin-bottom:3px;">Total Products</div>
            <div style="font-size:20px;font-weight:bold;"><?php echo $totalProducts; ?></div>
        </div>
        <div style="background:linear-gradient(135deg,#43e97b 0%,#38f9d7 100%);color:#fff;padding:12px 16px;border-radius:8px;flex:1;min-width:140px;">
            <div style="font-size:11px;opacity:0.9;margin-bottom:3px;">Available</div>
            <div style="font-size:20px;font-weight:bold;"><?php echo $availableCount; ?></div>
        </div>
        <div style="background:linear-gradient(135deg,#fa709a 0%,#fee140 100%);color:#fff;padding:12px 16px;border-radius:8px;flex:1;min-width:140px;">
            <div style="font-size:11px;opacity:0.9;margin-bottom:3px;">Low Stock</div>
            <div style="font-size:20px;font-weight:bold;"><?php echo $lowStockCount; ?></div>
        </div>
        <div style="background:linear-gradient(135deg,#ff6b6b 0%,#ee5a6f 100%);color:#fff;padding:12px 16px;border-radius:8px;flex:1;min-width:140px;">
            <div style="font-size:11px;opacity:0.9;margin-bottom:3px;">Out of Stock</div>
            <div style="font-size:20px;font-weight:bold;"><?php echo $outOfStockCount; ?></div>
        </div>
        <div style="background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);color:#fff;padding:12px 16px;border-radius:8px;flex:1;min-width:140px;">
            <div style="font-size:11px;opacity:0.9;margin-bottom:3px;">Expired</div>
            <div style="font-size:20px;font-weight:bold;"><?php echo $expiredCount; ?></div>
        </div>
        <div style="background:linear-gradient(135deg,#4facfe 0%,#00f2fe 100%);color:#fff;padding:12px 16px;border-radius:8px;flex:1;min-width:140px;">
            <div style="font-size:11px;opacity:0.9;margin-bottom:3px;">Total Value</div>
            <div style="font-size:20px;font-weight:bold;">৳ <?php echo number_format($totalValue, 0); ?></div>
        </div>
    </div>

    <!-- Compact Overviews -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;margin-bottom:20px;">
        <div class="card">
            <div class="card-header">Low Stock</div>
            <div class="card-body" style="max-height:200px;overflow:auto;padding:10px;">
                <?php $list = array_slice($lowStockProducts ?? [], 0, 5); ?>
                <?php if (!empty($list)): ?>
                    <table style="width:100%;border-collapse:collapse;font-size:12px;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th style="padding:6px;text-align:left;">Product</th>
                                <th style="padding:6px;text-align:center;">Qty</th>
                                <th style="padding:6px;text-align:right;">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list as $p): ?>
                                <tr style="border-bottom:1px solid #eee;">
                                    <td style="padding:6px;"><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td style="padding:6px;text-align:center;"><?php echo (int)$p['quantity']; ?></td>
                                    <td style="padding:6px;text-align:right;">৳ <?php echo number_format((float)$p['price'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="color:#868e96;font-size:12px;">No low stock items</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Expiring Soon (≤30 days)</div>
            <div class="card-body" style="max-height:200px;overflow:auto;padding:10px;">
                <?php $elist = array_slice($expiringProducts ?? [], 0, 5); ?>
                <?php if (!empty($elist)): ?>
                    <table style="width:100%;border-collapse:collapse;font-size:12px;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th style="padding:6px;text-align:left;">Product</th>
                                <th style="padding:6px;text-align:center;">Qty</th>
                                <th style="padding:6px;text-align:center;">Expiry</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($elist as $p): ?>
                                <tr style="border-bottom:1px solid #eee;">
                                    <td style="padding:6px;"><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td style="padding:6px;text-align:center;"><?php echo (int)$p['quantity']; ?></td>
                                    <td style="padding:6px;text-align:center;"><?php echo date('M d', strtotime($p['expiry_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="color:#868e96;font-size:12px;">No expiring items</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Expired Items</div>
            <div class="card-body" style="max-height:200px;overflow:auto;padding:10px;">
                <?php $xlist = array_slice($expiredProducts ?? [], 0, 5); ?>
                <?php if (!empty($xlist)): ?>
                    <table style="width:100%;border-collapse:collapse;font-size:12px;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th style="padding:6px;text-align:left;">Product</th>
                                <th style="padding:6px;text-align:center;">Qty</th>
                                <th style="padding:6px;text-align:center;">Expired</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($xlist as $p): ?>
                                <tr style="border-bottom:1px solid #eee;">
                                    <td style="padding:6px;"><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td style="padding:6px;text-align:center;"><?php echo (int)$p['quantity']; ?></td>
                                    <td style="padding:6px;text-align:center;"><?php echo date('M d', strtotime($p['expiry_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="color:#868e96;font-size:12px;">No expired items</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">Filters</div>
        <div class="card-body">
            <form method="GET" action="<?php echo BASE_URL; ?>admin/inventory" style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;">
                <input type="text" name="search" placeholder="Search by name or generic name..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="padding:10px;border:1px solid #ddd;border-radius:4px;">
                <select name="category_id" style="padding:10px;border:1px solid #ddd;border-radius:4px;">
                    <option value="">All Categories</option>
                    <?php foreach (($categories ?? []) as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo ($category_id ?? '') == $category['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="status" style="padding:10px;border:1px solid #ddd;border-radius:4px;">
                    <option value="all" <?php echo ($status_filter ?? 'all') === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="available" <?php echo ($status_filter ?? '') === 'available' ? 'selected' : ''; ?>>Available</option>
                    <option value="out_of_stock" <?php echo ($status_filter ?? '') === 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                    <option value="discontinued" <?php echo ($status_filter ?? '') === 'discontinued' ? 'selected' : ''; ?>>Discontinued</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0;">All Product</h2>
        </div>
        <div class="card-body">
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
                            $todayDate = new DateTime();
                            $isExpired = $expiryDate < $todayDate;
                            $daysToExpiry = $todayDate->diff($expiryDate)->days;
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
                                    <?php 
                                    $statusVal = trim($product['status'] ?? '');
                                    $bgColor = '#f8d7da';
                                    $textColor = '#842029';
                                    
                                    if ($statusVal === 'available' || $statusVal === 'Available') {
                                        $bgColor = '#d1e7dd';
                                        $textColor = '#0f5132';
                                    } elseif ($statusVal === 'out_of_stock' || $statusVal === 'Out of Stock' || $statusVal === 'out_of_stock' || strpos(strtolower($statusVal), 'out') !== false) {
                                        $bgColor = '#fff3cd';
                                        $textColor = '#856404';
                                    }
                                    ?>
                                    <span style="display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; white-space: nowrap;
                                                 background-color: <?php echo $bgColor; ?>;
                                                 color: <?php echo $textColor; ?>;">
                                        <?php echo ucfirst(str_replace('_', ' ', $statusVal)); ?>
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
                                    <a href="<?php echo BASE_URL; ?>admin/editProduct?id=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm btn-info" style="text-decoration: none; margin-right: 5px;">
                                        Edit
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>admin/deleteProduct/<?php echo $product['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.');" 
                                       class="btn btn-sm btn-danger" style="text-decoration: none;">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="padding: 40px; text-align: center; background-color: #f9f9f9; border-radius: 4px;">
                    <p style="color: #666; margin: 0;">No products found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>