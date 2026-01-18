<?php 
$pageTitle = 'Inventory Management';
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="container" style="padding: 20px;">
    <h1 style="color: #2c3e50; margin-bottom: 30px;">
        <i class="fas fa-boxes"></i> Inventory Oversight
    </h1>

    <!-- Filters -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">Filters</div>
        <div class="card-body">
            <form method="GET" action="<?php echo BASE_URL; ?>admin/inventory" style="display:flex;gap:15px;flex-wrap:wrap;">
                <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="flex:1;min-width:250px;padding:10px;border:1px solid #ced4da;border-radius:5px;">
                <select name="category_id" style="padding:10px;border:1px solid #ced4da;border-radius:5px;min-width:150px;">
                    <option value="">All Categories</option>
                    <?php foreach (($categories ?? []) as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo ($category_id ?? '') == $category['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="status" style="padding:10px;border:1px solid #ced4da;border-radius:5px;">
                    <option value="all" <?php echo ($status_filter ?? 'all') === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="available" <?php echo ($status_filter ?? '') === 'available' ? 'selected' : ''; ?>>Available</option>
                    <option value="out_of_stock" <?php echo ($status_filter ?? '') === 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                    <option value="discontinued" <?php echo ($status_filter ?? '') === 'discontinued' ? 'selected' : ''; ?>>Discontinued</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="<?php echo BASE_URL; ?>admin/inventory" class="btn btn-secondary" style="text-decoration:none;">Reset</a>
            </form>
        </div>
    </div>

    <!-- Inventory Summary -->
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
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:15px;margin-bottom:20px;">
        <div style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:18px;border-radius:10px;">
            <div style="font-size:13px;opacity:0.9;margin-bottom:5px;">Total Products</div>
            <div style="font-size:28px;font-weight:bold;"><?php echo $totalProducts; ?></div>
        </div>
        <div style="background:linear-gradient(135deg,#43e97b 0%,#38f9d7 100%);color:#fff;padding:18px;border-radius:10px;">
            <div style="font-size:13px;opacity:0.9;margin-bottom:5px;">Available</div>
            <div style="font-size:28px;font-weight:bold;"><?php echo $availableCount; ?></div>
        </div>
        <div style="background:linear-gradient(135deg,#fa709a 0%,#fee140 100%);color:#fff;padding:18px;border-radius:10px;">
            <div style="font-size:13px;opacity:0.9;margin-bottom:5px;">Low Stock</div>
            <div style="font-size:28px;font-weight:bold;"><?php echo $lowStockCount; ?></div>
        </div>
        <div style="background:linear-gradient(135deg,#ff6b6b 0%,#ee5a6f 100%);color:#fff;padding:18px;border-radius:10px;">
            <div style="font-size:13px;opacity:0.9;margin-bottom:5px;">Out of Stock</div>
            <div style="font-size:28px;font-weight:bold;"><?php echo $outOfStockCount; ?></div>
        </div>
        <div style="background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);color:#fff;padding:18px;border-radius:10px;">
            <div style="font-size:13px;opacity:0.9;margin-bottom:5px;">Expired</div>
            <div style="font-size:28px;font-weight:bold;"><?php echo $expiredCount; ?></div>
        </div>
        <div style="background:linear-gradient(135deg,#4facfe 0%,#00f2fe 100%);color:#fff;padding:18px;border-radius:10px;">
            <div style="font-size:13px;opacity:0.9;margin-bottom:5px;">Total Value</div>
            <div style="font-size:28px;font-weight:bold;">৳ <?php echo number_format($totalValue, 0); ?></div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-header">Products</div>
        <div class="card-body">
            <?php if (!empty($products)): ?>
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8f9fa;text-align:left;">
                                <th style="padding:12px;border-bottom:2px solid #dee2e6;">ID</th>
                                <th style="padding:12px;border-bottom:2px solid #dee2e6;">Product Name</th>
                                <th style="padding:12px;border-bottom:2px solid #dee2e6;">Category</th>
                                <th style="padding:12px;border-bottom:2px solid #dee2e6;">Price</th>
                                <th style="padding:12px;border-bottom:2px solid #dee2e6;">Quantity</th>
                                <th style="padding:12px;border-bottom:2px solid #dee2e6;">Status</th>
                                <th style="padding:12px;border-bottom:2px solid #dee2e6;">Expiry Date</th>
                                <th style="padding:12px;border-bottom:2px solid #dee2e6;text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <?php 
                                $isExpired = !empty($product['expiry_date']) && $product['expiry_date'] < $today;
                                $isLowStock = isset($product['quantity'],$product['low_stock_threshold']) && $product['quantity'] <= $product['low_stock_threshold'];
                                $daysUntilExpiry = !empty($product['expiry_date']) ? floor((strtotime($product['expiry_date']) - time()) / 86400) : null;
                                $statusColors = [
                                    'available' => ['bg' => '#d4edda', 'color' => '#155724'],
                                    'out_of_stock' => ['bg' => '#fff3cd', 'color' => '#856404'],
                                    'discontinued' => ['bg' => '#e2e3e5', 'color' => '#383d41']
                                ];
                                $colors = $statusColors[$product['status']] ?? ['bg' => '#e2e3e5', 'color' => '#383d41'];
                                ?>
                                <tr style="border-bottom:1px solid #f1f3f5;<?php echo $isExpired ? 'background:#fff5f5;' : ''; ?>">
                                    <td style="padding:12px;color:#495057;font-weight:600;">#<?php echo (int)$product['id']; ?></td>
                                    <td style="padding:12px;">
                                        <div style="font-weight:600;color:#2c3e50;"><?php echo htmlspecialchars($product['name']); ?></div>
                                        <div style="font-size:12px;color:#868e96;"><?php echo htmlspecialchars($product['generic_name'] ?? ''); ?></div>
                                    </td>
                                    <td style="padding:12px;color:#495057;">
                                        <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                                    </td>
                                    <td style="padding:12px;font-weight:600;color:#2c3e50;">৳ <?php echo number_format((float)$product['price'], 2); ?></td>
                                    <td style="padding:12px;">
                                        <span style="padding:6px 12px;border-radius:12px;font-weight:600;background:<?php echo $isLowStock ? '#fff3cd' : '#d4edda'; ?>;color:<?php echo $isLowStock ? '#856404' : '#155724'; ?>;">
                                            <?php echo (int)$product['quantity']; ?> units
                                        </span>
                                    </td>
                                    <td style="padding:12px;">
                                        <span style="padding:6px 14px;border-radius:12px;font-size:12px;font-weight:600;background:<?php echo $colors['bg']; ?>;color:<?php echo $colors['color']; ?>;">
                                            <?php echo strtoupper(str_replace('_',' ', $product['status'])); ?>
                                        </span>
                                    </td>
                                    <td style="padding:12px;">
                                        <?php if (!empty($product['expiry_date'])): ?>
                                            <div style="color:<?php echo $isExpired ? '#dc3545' : (($daysUntilExpiry !== null && $daysUntilExpiry <= 30) ? '#ffc107' : '#28a745'); ?>;font-weight:600;">
                                                <?php echo date('M d, Y', strtotime($product['expiry_date'])); ?>
                                            </div>
                                            <div style="font-size:11px;color:#868e96;">
                                                <?php 
                                                if ($isExpired) {
                                                    echo 'EXPIRED ' . abs((int)$daysUntilExpiry) . ' days ago';
                                                } elseif ($daysUntilExpiry !== null && $daysUntilExpiry <= 30) {
                                                    echo (int)$daysUntilExpiry . ' days left';
                                                } else {
                                                    echo 'Good';
                                                }
                                                ?>
                                            </div>
                                        <?php else: ?>
                                            <div style="color:#868e96;">N/A</div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding:12px;text-align:center;">
                                        <a href="<?php echo BASE_URL; ?>admin/deleteProduct/<?php echo (int)$product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.');" style="background:#dc3545;color:#fff;padding:6px 12px;border-radius:6px;text-decoration:none;font-size:13px;">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align:center;padding:40px;color:#868e96;">
                    <i class="fas fa-boxes" style="font-size:48px;margin-bottom:20px;opacity:0.3;"></i>
                    <p style="font-size:18px;margin:0;">No products found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Compact Overviews -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;margin-top:20px;">
        <div class="card">
            <div class="card-header">Low Stock</div>
            <div class="card-body" style="max-height:260px;overflow:auto;">
                <?php $list = array_slice($lowStockProducts ?? [], 0, 8); ?>
                <?php if (!empty($list)): ?>
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th style="padding:8px;text-align:left;">Product</th>
                                <th style="padding:8px;text-align:center;">Qty</th>
                                <th style="padding:8px;text-align:right;">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list as $p): ?>
                                <tr style="border-bottom:1px solid #eee;">
                                    <td style="padding:8px;"><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td style="padding:8px;text-align:center;"><?php echo (int)$p['quantity']; ?></td>
                                    <td style="padding:8px;text-align:right;">৳ <?php echo number_format((float)$p['price'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="color:#868e96;">No low stock items</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Expiring Soon (≤30 days)</div>
            <div class="card-body" style="max-height:260px;overflow:auto;">
                <?php $elist = array_slice($expiringProducts ?? [], 0, 8); ?>
                <?php if (!empty($elist)): ?>
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th style="padding:8px;text-align:left;">Product</th>
                                <th style="padding:8px;text-align:center;">Qty</th>
                                <th style="padding:8px;text-align:center;">Expiry</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($elist as $p): ?>
                                <tr style="border-bottom:1px solid #eee;">
                                    <td style="padding:8px;"><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td style="padding:8px;text-align:center;"><?php echo (int)$p['quantity']; ?></td>
                                    <td style="padding:8px;text-align:center;"><?php echo date('M d, Y', strtotime($p['expiry_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="color:#868e96;">No expiring items</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Expired Items</div>
            <div class="card-body" style="max-height:260px;overflow:auto;">
                <?php $xlist = array_slice($expiredProducts ?? [], 0, 8); ?>
                <?php if (!empty($xlist)): ?>
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th style="padding:8px;text-align:left;">Product</th>
                                <th style="padding:8px;text-align:center;">Qty</th>
                                <th style="padding:8px;text-align:center;">Expired</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($xlist as $p): ?>
                                <tr style="border-bottom:1px solid #eee;">
                                    <td style="padding:8px;"><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td style="padding:8px;text-align:center;"><?php echo (int)$p['quantity']; ?></td>
                                    <td style="padding:8px;text-align:center;"><?php echo date('M d, Y', strtotime($p['expiry_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="color:#868e96;">No expired items</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>