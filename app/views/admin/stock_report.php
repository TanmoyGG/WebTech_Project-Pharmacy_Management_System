<!-- Stock Report - Admin -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Stock Report - <?php echo date('F Y'); ?></title>
    <style>
        @media print {
            .no-print { display: none !important; }
        }
        body { font-family: Arial, sans-serif; padding: 20px; }
        .report-header { text-align: center; margin-bottom: 40px; border-bottom: 3px solid #2c3e50; padding-bottom: 20px; }
        .report-title { color: #2c3e50; font-size: 28px; margin-bottom: 10px; }
        .report-subtitle { color: #6c757d; font-size: 16px; }
        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 40px; }
        .summary-card { background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; }
        .summary-label { color: #6c757d; font-size: 14px; margin-bottom: 8px; }
        .summary-value { color: #2c3e50; font-size: 24px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #2c3e50; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #dee2e6; }
        tr:hover { background: #f8f9fa; }
        .print-btn { background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .section-title { color: #2c3e50; font-size: 20px; margin: 30px 0 15px 0; border-bottom: 2px solid #e9ecef; padding-bottom: 10px; }
        .status-badge { padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .status-available { background: #d1e7dd; color: #0f5132; }
        .status-low { background: #fff3cd; color: #856404; }
        .status-out { background: #f8d7da; color: #842029; }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom: 20px;">
    <button onclick="window.print()" class="print-btn">
        <i class="fas fa-print"></i> Print / Save as PDF
    </button>
    <a href="<?php echo BASE_URL; ?>admin/reports" style="background: #6c757d; color: white; padding: 12px 24px; border-radius: 5px; text-decoration: none; margin-left: 10px;">
        <i class="fas fa-arrow-left"></i> Back to Reports
    </a>
</div>

<div class="report-header">
    <div class="report-title">Inventory Stock Report</div>
    <div class="report-subtitle">Current Stock Levels and Inventory Analysis</div>
    <div style="color: #868e96; font-size: 14px; margin-top: 10px;">
        Generated on: <?php echo $generated_at; ?>
    </div>
</div>

<!-- Pharmacy Info -->
<div style="text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #e9ecef;">
    <h3 style="margin: 0 0 5px 0; color: #2c3e50; font-size: 18px;">
        <?php echo htmlspecialchars($pharmacy_name); ?>
    </h3>
    <p style="margin: 3px 0; color: #6c757d; font-size: 14px;">
        <?php echo htmlspecialchars($pharmacy_address); ?>
    </p>
    <p style="margin: 3px 0; color: #6c757d; font-size: 14px;">
        <?php echo htmlspecialchars($pharmacy_phone); ?>
    </p>
</div>

<!-- Summary Section -->
<div style="margin-top: 30px; padding: 16px; border-top: 1px solid #e9ecef;">
    <h3 style="color: #2c3e50; margin: 0 0 10px 0; font-size: 18px;">Summary</h3>
    <div style="display: flex; flex-direction: column; gap: 8px; color: #2c3e50;">
        <div><strong>Total Products:</strong> <?php echo $total_products; ?></div>
        <div><strong>Total Inventory Value:</strong> ৳ <?php echo number_format($total_value, 2); ?></div>
        <div><strong>Low Stock Items:</strong> <?php echo count($low_stock); ?></div>
        <div><strong>Out of Stock:</strong> <?php echo $out_of_stock_count; ?></div>
    </div>
</div>

<!-- Low Stock Items -->
<?php if (!empty($low_stock)): ?>
<h2 class="section-title">⚠️ Low Stock Alert</h2>
<table>
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Category</th>
            <th>Current Qty</th>
            <th>Threshold</th>
            <th>Unit Price</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($low_stock as $product): ?>
        <tr>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
            <td><strong style="color: #dc3545;"><?php echo $product['quantity']; ?></strong></td>
            <td><?php echo $product['low_stock_threshold']; ?></td>
            <td>৳ <?php echo number_format($product['price'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<!-- Expired Items -->
<?php if (!empty($expired)): ?>
<h2 class="section-title">🚫 Expired Products</h2>
<table>
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Expiry Date</th>
            <th>Value Loss</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($expired as $product): ?>
        <tr>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
            <td><?php echo $product['quantity']; ?></td>
            <td style="color: #dc3545;"><?php echo date('M d, Y', strtotime($product['expiry_date'])); ?></td>
            <td>৳ <?php echo number_format($product['price'] * $product['quantity'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<!-- Expiring Soon -->
<?php if (!empty($expiring_soon)): ?>
<h2 class="section-title">⏰ Expiring Soon (Next 30 Days)</h2>
<table>
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Expiry Date</th>
            <th>Days Until Expiry</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($expiring_soon as $product): ?>
        <?php 
        $days_until_expiry = (strtotime($product['expiry_date']) - time()) / (60 * 60 * 24);
        ?>
        <tr>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
            <td><?php echo $product['quantity']; ?></td>
            <td style="color: #ffc107;"><?php echo date('M d, Y', strtotime($product['expiry_date'])); ?></td>
            <td><strong><?php echo ceil($days_until_expiry); ?> days</strong></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<!-- All Products Inventory -->
<h2 class="section-title">📦 Complete Inventory List</h2>
<table>
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Total Value</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
            <td><?php echo $product['quantity']; ?></td>
            <td>৳ <?php echo number_format($product['price'], 2); ?></td>
            <td>৳ <?php echo number_format($product['price'] * $product['quantity'], 2); ?></td>
            <td>
                <?php
                $status = $product['status'];
                $badge_class = match($status) {
                    'available' => 'status-available',
                    'out_of_stock' => 'status-out',
                    'discontinued' => 'status-out',
                    default => 'status-available'
                };
                ?>
                <span class="status-badge <?php echo $badge_class; ?>"><?php echo ucfirst(str_replace('_', ' ', $status)); ?></span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="margin-top: 20px; text-align: center; color: #6c757d; font-size: 14px;">
    <p>This report provides insights into inventory levels and stock alerts.</p>
</div>

<!-- Report Footer -->
<div style="margin-top: 60px; padding-top: 20px; border-top: 2px solid #dee2e6; text-align: center; color: #6c757d; font-size: 12px;">
    <p>This is a computer-generated report. No signature required.</p>
    <p><?php echo htmlspecialchars($pharmacy_name); ?> - A Digital Solution of Pharmacy Management System</p>
    <p><?php echo htmlspecialchars($pharmacy_phone); ?></p>
    <p>Page 1 of 1</p>
</div>
