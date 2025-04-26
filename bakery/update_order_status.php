<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['order_id']) || !isset($data['status']) || !isset($data['payment_method'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$order_id = $data['order_id'];
$status = $data['status'];
$payment_method = $data['payment_method'];

// Validate status values
$valid_statuses = ['Pending', 'In Progress', 'Completed', 'Cancelled'];
$valid_payment_methods = ['Card', 'Gcash', 'Cash'];

if (!in_array($status, $valid_statuses) || !in_array($payment_method, $valid_payment_methods)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status or payment method values']);
    exit;
}

// Update order status using mysqli
$sql = "UPDATE orders SET status = ?, payment_method = ? WHERE order_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssi", $status, $payment_method, $order_id);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    // Log the status change if order_logs table exists
    $check_logs_table = mysqli_query($conn, "SHOW TABLES LIKE 'order_logs'");
    if (mysqli_num_rows($check_logs_table) > 0) {
        $log_sql = "INSERT INTO order_logs (order_id, status, payment_method, changed_by) VALUES (?, ?, ?, ?)";
        $log_stmt = mysqli_prepare($conn, $log_sql);
        mysqli_stmt_bind_param($log_stmt, "issi", $order_id, $status, $payment_method, $_SESSION['user_id']);
        mysqli_stmt_execute($log_stmt);
    }

    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update order status: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?> 