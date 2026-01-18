<!-- User Activity Report - Admin -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>User Activity Report - <?php echo date('F Y'); ?></title>
    <style>
        @media print {
            .no-print { display: none !important; }
        }
        body { font-family: Arial, sans-serif; padding: 20px; }
        .report-header { text-align: center; margin-bottom: 40px; border-bottom: 3px solid #2c3e50; padding-bottom: 20px; }
        .report-title { color: #2c3e50; font-size: 28px; margin-bottom: 10px; }
        .report-subtitle { color: #6c757d; font-size: 16px; }
        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px; }
        .summary-card { background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; }
        .summary-label { color: #6c757d; font-size: 14px; margin-bottom: 8px; }
        .summary-value { color: #2c3e50; font-size: 24px; font-weight: bold; }
        .role-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px; }
        .role-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #2c3e50; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #dee2e6; }
        tr:hover { background: #f8f9fa; }
        .print-btn { background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .section-title { color: #2c3e50; font-size: 20px; margin: 30px 0 15px 0; border-bottom: 2px solid #e9ecef; padding-bottom: 10px; }
        .status-badge { padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .status-active { background: #d1e7dd; color: #0f5132; }
        .status-inactive { background: #f8d7da; color: #842029; }
        .role-badge { padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; background: #e7f3ff; color: #004085; }
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
    <div class="report-title">User Activity Report</div>
    <div class="report-subtitle">User Registrations, Activity & Account Status</div>
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
<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-label">Total Users</div>
        <div class="summary-value"><?php echo $total_users; ?></div>
    </div>
    <div class="summary-card" style="border-left-color: #43e97b;">
        <div class="summary-label">Active Users</div>
        <div class="summary-value"><?php echo $active_users; ?></div>
    </div>
    <div class="summary-card" style="border-left-color: #dc3545;">
        <div class="summary-label">Inactive Users</div>
        <div class="summary-value"><?php echo $inactive_users; ?></div>
    </div>
</div>

<!-- Role Distribution -->
<h2 class="section-title">👥 User Role Distribution</h2>
<div class="role-grid">
    <div class="role-card">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Administrators</div>
        <div style="font-size: 36px; font-weight: bold;"><?php echo $admin_count; ?></div>
    </div>
    <div class="role-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Inventory Managers</div>
        <div style="font-size: 36px; font-weight: bold;"><?php echo $inventory_manager_count; ?></div>
    </div>
    <div class="role-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Customers</div>
        <div style="font-size: 36px; font-weight: bold;"><?php echo $customer_count; ?></div>
    </div>
</div>

<!-- Recent Registrations -->
<?php if (!empty($recent_registrations)): ?>
<h2 class="section-title">🆕 Recent Registrations (Last 30 Days)</h2>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Registration Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recent_registrations as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['name']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><span class="role-badge"><?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?></span></td>
            <td>
                <span class="status-badge <?php echo $user['status'] === 'active' ? 'status-active' : 'status-inactive'; ?>">
                    <?php echo ucfirst($user['status']); ?>
                </span>
            </td>
            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<!-- All Users -->
<h2 class="section-title">📋 Complete User List</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Phone</th>
            <th>Registration Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo htmlspecialchars($user['name']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><span class="role-badge"><?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?></span></td>
            <td>
                <span class="status-badge <?php echo $user['status'] === 'active' ? 'status-active' : 'status-inactive'; ?>">
                    <?php echo ucfirst($user['status']); ?>
                </span>
            </td>
            <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Summary Stats -->
<div style="margin-top: 30px; padding: 16px; border-top: 1px solid #e9ecef;">
    <h3 style="color: #2c3e50; margin: 0 0 10px 0; font-size: 18px;">Summary Statistics</h3>
    <div style="display: flex; flex-direction: column; gap: 8px; color: #2c3e50;">
        <div><strong>Total Registered Users:</strong> <?php echo $total_users; ?></div>
        <div><strong>Active Rate:</strong> <?php echo $total_users > 0 ? round(($active_users / $total_users) * 100, 1) : 0; ?>%</div>
        <div><strong>New Users (30 days):</strong> <?php echo count($recent_registrations); ?></div>
        <div><strong>Customer Base:</strong> <?php echo $customer_count; ?></div>
    </div>
</div>

<div style="margin-top: 20px; text-align: center; color: #6c757d; font-size: 14px;">
    <p>This report provides insights into user registration trends and account management overview.</p>
</div>

<!-- Report Footer -->
<div style="margin-top: 60px; padding-top: 20px; border-top: 2px solid #dee2e6; text-align: center; color: #6c757d; font-size: 12px;">
    <p>This is a computer-generated report. No signature required.</p>
    <p><?php echo htmlspecialchars($pharmacy_name); ?> - A Digital Solution of Pharmacy Management System</p>
    <p><?php echo htmlspecialchars($pharmacy_phone); ?></p>
    <p>Page 1 of 1</p>
</div>
