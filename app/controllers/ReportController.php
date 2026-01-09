<?php
// Report Controller - Generate reports and analytics
// All functions follow procedural pattern: report_[action]()

// Display reports page
function report_index() {
    requireRole('admin');
    
    render('admin/reports');
}

// Generate sales report
function report_generateSales() {
    requireRole('admin');
    
    $month = getGet('month', date('m'));
    $year = getGet('year', date('Y'));
    
    $startDate = "$year-$month-01";
    $endDate = date('Y-m-t', strtotime($startDate));
    
    $sales = fetchAll(
        'SELECT * FROM orders WHERE DATE(created_at) BETWEEN ? AND ? AND status = ?',
        'sss',
        [$startDate, $endDate, 'completed']
    );
    
    $totalRevenue = 0;
    foreach ($sales as $order) {
        $totalRevenue += $order['total_amount'];
    }
    
    $data = [
        'sales' => $sales,
        'totalRevenue' => $totalRevenue,
        'month' => $month,
        'year' => $year
    ];
    
    render('admin/sales_report', $data);
}
?>