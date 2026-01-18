<!-- Sales Report - Admin -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sales Report - <?php echo date('F Y'); ?></title>
    <style>
        @media print {
            .no-print { display: none !important; }
        }
        body { font-family: Arial, sans-serif; padding: 20px; }
        .report-header { text-align: center; margin-bottom: 40px; border-bottom: 3px solid #2c3e50; padding-bottom: 20px; }
        .report-title { color: #2c3e50; font-size: 28px; margin-bottom: 10px; }
        .report-period { color: #6c757d; font-size: 16px; }
        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 40px; }
        .summary-card { background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; }
        .summary-label { color: #6c757d; font-size: 14px; margin-bottom: 8px; }
        .summary-value { color: #2c3e50; font-size: 24px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #2c3e50; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #dee2e6; }
        tr:hover { background: #f8f9fa; }
        .print-btn { background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
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
    <div class="report-title">Sales Report</div>
    <div class="report-period">
        <?php echo date('F d, Y', strtotime($start_date)); ?> - <?php echo date('F d, Y', strtotime($end_date)); ?>
    </div>
    <div style="color: #868e96; font-size: 14px; margin-top: 10px;">
        Generated on: <?php echo date('F d, Y h:i A'); ?>
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


<!-- Top Products Section -->
<h2 style="color: #2c3e50; margin-bottom: 20px;">Top Selling Products</h2>
<?php if (count($orders) > 0 && !empty($topProducts)): ?>
<table>
    <thead>
        <tr>
            <th>Rank</th>
            <th>Product Name</th>
            <th>Generic Name</th>
            <th>Units Sold</th>
            <th>Revenue</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($topProducts as $index => $product): ?>
        <tr>
            <td style="font-weight: bold; color: #667eea;">#<?php echo $index + 1; ?></td>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td style="color: #6c757d;"><?php echo htmlspecialchars($product['generic_name']); ?></td>
            <td style="font-weight: bold;"><?php echo $product['total_sold']; ?></td>
            <td style="font-weight: bold; color: #28a745;">৳ <?php echo number_format($product['total_revenue'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php elseif (count($orders) > 0): ?>
    <p style="text-align: center; color: #868e96; padding: 20px;">No product sales data available for this period.</p>
<?php else: ?>
    <p style="text-align: center; color: #868e96; padding: 20px;">No orders found for this period. No top products to display.</p>
<?php endif; ?>

<!-- Orders List -->
<h2 style="color: #2c3e50; margin-bottom: 20px;">Order Details</h2>
<?php if (!empty($orders)): ?>
<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Items</th>
            <th>Total Amount</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td style="font-weight: bold;">#<?php echo $order['id']; ?></td>
            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
            <td>User #<?php echo $order['user_id']; ?></td>
            <td>-</td>
            <td style="font-weight: bold; color: #28a745;">৳ <?php echo number_format($order['total_amount'], 2); ?></td>
            <td style="text-transform: uppercase; color: #667eea;"><?php echo $order['status']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p style="text-align: center; color: #868e96; padding: 20px;">No orders found for this period.</p>
<?php endif; ?>

<!-- Summary Section -->
<div style="margin-top: 30px; padding: 16px; border-top: 1px solid #e9ecef;">
    <h3 style="color: #2c3e50; margin: 0 0 10px 0; font-size: 18px;">Summary</h3>
    <div style="display: flex; flex-direction: column; gap: 8px; color: #2c3e50;">
        <div><strong>Total Orders:</strong> <?php echo count($orders); ?></div>
        <div><strong>Total Revenue:</strong> ৳ <?php echo number_format($revenue, 2); ?></div>
        <div><strong>Average Order:</strong> ৳ <?php echo count($orders) > 0 ? number_format($revenue / count($orders), 2) : '0.00'; ?></div>
        <div><strong>Products Sold:</strong> <?php echo count($orders) > 0 ? count($topProducts) : 0; ?></div>
    </div>
</div>

<div style="margin-top: 20px; text-align: center; color: #6c757d; font-size: 14px;">
    <p>This report provides insights into sales performance and order trends.</p>
</div>

<!-- Report Footer -->
<div style="margin-top: 60px; padding-top: 20px; border-top: 2px solid #dee2e6; text-align: center; color: #6c757d; font-size: 12px;">
    <p>This is a computer-generated report. No signature required.</p>
    <p><?php echo htmlspecialchars($pharmacy_name); ?> - A Digital Solution of Pharmacy Management System</p>
    <p><?php echo htmlspecialchars($pharmacy_phone); ?></p>
    <p>Page 1 of 1</p>
</div>

</body>
</html>
