<?php 
$pageTitle = 'Transaction History';
include_once __DIR__ . '/../layouts/header.php';
$transactions = $transactions ?? [];
$status_filter = $status_filter ?? '';
$date_from = $date_from ?? date('Y-m-d', strtotime('-30 days'));
$date_to = $date_to ?? date('Y-m-d');
?>

<div class="container" style="margin-top: 20px;">
    <h1 style="color: #2c3e50; margin-bottom: 20px;">
        <i class="fas fa-file-invoice-dollar"></i> Transaction History
    </h1>

    <!-- Summary Cards -->
    <?php
    $totalAmount = 0;
    $completedAmount = 0;
    $completedCount = 0;
    $pendingCount = 0;
    foreach (($transactions ?? []) as $transaction) {
        $totalAmount += (float)$transaction['amount'];
        if (($transaction['status'] ?? '') === 'completed') {
            $completedAmount += (float)$transaction['amount'];
            $completedCount++;
        }
        if (($transaction['status'] ?? '') === 'pending') {
            $pendingCount++;
        }
    }
    ?>
    <div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
        <div style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:12px 16px;border-radius:8px;flex:1;min-width:140px;">
            <div style="font-size:11px;opacity:0.9;margin-bottom:3px;">Total Transactions</div>
            <div style="font-size:20px;font-weight:bold;"><?php echo count($transactions); ?></div>
        </div>
        <div style="background:linear-gradient(135deg,#43e97b 0%,#38f9d7 100%);color:#fff;padding:12px 16px;border-radius:8px;flex:1;min-width:140px;">
            <div style="font-size:11px;opacity:0.9;margin-bottom:3px;">Completed</div>
            <div style="font-size:20px;font-weight:bold;"><?php echo $completedCount; ?></div>
        </div>
        <div style="background:linear-gradient(135deg,#fa709a 0%,#fee140 100%);color:#fff;padding:12px 16px;border-radius:8px;flex:1;min-width:140px;">
            <div style="font-size:11px;opacity:0.9;margin-bottom:3px;">Pending</div>
            <div style="font-size:20px;font-weight:bold;"><?php echo $pendingCount; ?></div>
        </div>
        <div style="background:linear-gradient(135deg,#4facfe 0%,#00f2fe 100%);color:#fff;padding:12px 16px;border-radius:8px;flex:1;min-width:140px;">
            <div style="font-size:11px;opacity:0.9;margin-bottom:3px;">Total Amount</div>
            <div style="font-size:20px;font-weight:bold;">৳ <?php echo number_format($totalAmount, 2); ?></div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">Filters</div>
        <div class="card-body">
            <form method="GET" action="<?php echo BASE_URL; ?>admin/transactionHistory" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 10px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px;">From Date</label>
                    <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px;">To Date</label>
                    <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px;">Status</label>
                    <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All Status</option>
                        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="failed" <?php echo $status_filter === 'failed' ? 'selected' : ''; ?>>Failed</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="align-self: flex-end;">Filter</button>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">>
        <div class="card-header">
            <h2 style="margin: 0;">Transaction Details</h2>
        </div>
        <div class="card-body">
            <?php if (!empty($transactions)): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            <th style="padding: 12px; text-align: left;">Transaction ID</th>
                            <th style="padding: 12px; text-align: left;">Customer</th>
                            <th style="padding: 12px; text-align: right;">Amount</th>
                            <th style="padding: 12px; text-align: center;">Status</th>
                            <th style="padding: 12px; text-align: center;">Payment Method</th>
                            <th style="padding: 12px; text-align: left;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px;"><strong>#<?php echo $transaction['id']; ?></strong></td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($transaction['user_name'] ?? 'Unknown'); ?></td>
                                <td style="padding: 12px; text-align: right;"><strong>৳ <?php echo number_format((float)$transaction['amount'], 2); ?></strong></td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; white-space: nowrap;
                                                 background-color: <?php 
                                                     $status = trim($transaction['status'] ?? '');
                                                     if ($status === 'completed') echo '#d1e7dd';
                                                     elseif ($status === 'pending') echo '#fff3cd';
                                                     else echo '#f8d7da';
                                                 ?>;
                                                 color: <?php 
                                                     $status = trim($transaction['status'] ?? '');
                                                     if ($status === 'completed') echo '#0f5132';
                                                     elseif ($status === 'pending') echo '#856404';
                                                     else echo '#842029';
                                                 ?>;">
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center;"><?php echo htmlspecialchars($transaction['payment_method'] ?? 'Cash'); ?></td>
                                <td style="padding: 12px;"><?php echo date('M d, Y H:i', strtotime($transaction['transaction_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="padding: 40px; text-align: center; background-color: #f9f9f9; border-radius: 4px;">
                    <p style="color: #666; margin: 0;">No transactions found.</p>
                </div>
            <?php endif; ?>
        </div>

</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>