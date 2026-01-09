<?php
// Transaction Model - Transaction data operations (Procedural)

// Get transaction by ID
function transactionGetById($id) {
    return getById('transactions', $id);
}

// Get all transactions
function transactionGetAll() {
    return getAllRecords('transactions');
}

// Get transactions by user
function transactionGetByUser($userId) {
    return fetchAll('SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC', 'i', [$userId]);
}

// Create new transaction
function transactionCreate($orderId, $userId, $amount, $paymentMethod = 'cash', $status = 'completed') {
    $transactionData = [
        'order_id' => $orderId,
        'user_id' => $userId,
        'amount' => $amount,
        'payment_method' => $paymentMethod,
        'status' => $status,
        'transaction_date' => date('Y-m-d H:i:s'),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    return insertRecord('transactions', $transactionData);
}

// Update transaction
function transactionUpdate($transactionId, $data) {
    $data['updated_at'] = date('Y-m-d H:i:s');
    return updateRecord('transactions', $data, 'id = ?', [$transactionId]);
}

// Get transactions by date range
function transactionGetByDateRange($startDate, $endDate) {
    return fetchAll(
        'SELECT * FROM transactions WHERE DATE(transaction_date) BETWEEN ? AND ? ORDER BY transaction_date DESC',
        'ss',
        [$startDate, $endDate]
    );
}

// Get total revenue by date range
function transactionGetRevenueByDateRange($startDate, $endDate) {
    $result = fetchOne(
        'SELECT SUM(amount) as revenue FROM transactions WHERE DATE(transaction_date) BETWEEN ? AND ? AND status = ?',
        'sss',
        [$startDate, $endDate, 'completed']
    );
    return $result['revenue'] ?? 0;
}

// Get transactions by status
function transactionGetByStatus($status) {
    return fetchAll('SELECT * FROM transactions WHERE status = ? ORDER BY created_at DESC', 's', [$status]);
}

// Get transactions by payment method
function transactionGetByPaymentMethod($method) {
    return fetchAll('SELECT * FROM transactions WHERE payment_method = ? ORDER BY created_at DESC', 's', [$method]);
}

// Count transactions
function transactionCount() {
    return countRecords('transactions');
}

// Get total transaction amount
function transactionGetTotal() {
    $result = fetchOne('SELECT SUM(amount) as total FROM transactions WHERE status = ?', 's', ['completed']);
    return $result['total'] ?? 0;
}

// Get average transaction amount
function transactionGetAverage() {
    $result = fetchOne('SELECT AVG(amount) as average FROM transactions WHERE status = ?', 's', ['completed']);
    return $result['average'] ?? 0;
}
?>