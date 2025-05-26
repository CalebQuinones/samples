<?php
// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON content type
header('Content-Type: application/json');

try {
    // Database connection
    require_once 'config.php';
    
    if (!isset($conn)) {
        throw new Exception("Database connection failed");
    }

    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Only POST method allowed");
    }

    // Get POST data
    $input = file_get_contents('php://input');
    if (empty($input)) {
        throw new Exception("No input data received");
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON: " . json_last_error_msg());
    }

    // Debug: Log received data
    error_log("Received data: " . print_r($data, true));

    // Validate required fields
    if (empty($data['orderId'])) {
        throw new Exception("Order ID is required");
    }
    if (empty($data['status'])) {
        throw new Exception("Status is required");
    }
    if (!isset($data['paymentStatus'])) {
        throw new Exception("Payment status is required");
    }

    $orderId = (int)$data['orderId'];
    $status = trim($data['status']);
    $paymentStatus = trim($data['paymentStatus']);
    $message = isset($data['message']) ? trim($data['message']) : '';

    // Debug: Log processed values
    error_log("Order ID: $orderId, Status: $status, Payment Status: $paymentStatus");

    // Validate status values
    $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    $validPaymentStatuses = ['Pending', 'Paid', 'Failed', 'Refunded'];  // Exact enum values from DB

    if (!in_array(strtolower($status), $validStatuses)) {
        throw new Exception("Invalid status value: $status");
    }

    // Validate payment status
    if (!in_array($paymentStatus, $validPaymentStatuses)) {
        error_log("Invalid payment status: $paymentStatus. Must be one of: " . implode(', ', $validPaymentStatuses));
        throw new Exception("Invalid payment status value: $paymentStatus. Must be one of: " . implode(', ', $validPaymentStatuses));
    }
    
    // Log the update attempt
    error_log("Attempting to update order #$orderId - Status: $status, Payment Status: $paymentStatus");

    // Check if order exists first and get current values
    $checkSql = "SELECT order_id, status, payment_status FROM orders WHERE order_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    
    if (!$checkStmt) {
        throw new Exception("Check query preparation failed: " . $conn->error);
    }

    $checkStmt->bind_param("i", $orderId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Order not found with ID: $orderId");
    }

    $currentOrder = $result->fetch_assoc();
    $checkStmt->close();

    // Debug: Log current order data
    error_log("Current order before update: " . print_r($currentOrder, true));

    // Check current database values before update
    $preSql = "SELECT order_id, status, payment_status FROM orders WHERE order_id = ?";
    $preStmt = $conn->prepare($preSql);
    $preStmt->bind_param("i", $orderId);
    $preStmt->execute();
    $preResult = $preStmt->get_result();
    $preUpdate = $preResult->fetch_assoc();
    $preStmt->close();
    
    error_log("Before update - Current DB values: " . print_r($preUpdate, true));

    // Update order - Make sure column names match your database
    $updateSql = "UPDATE orders SET status = ?, payment_status = ? WHERE order_id = ?";
    error_log("Update SQL: $updateSql");
    error_log("Binding values - Status: '$status', Payment Status: '$paymentStatus', Order ID: $orderId");
    
    $updateStmt = $conn->prepare($updateSql);

    if (!$updateStmt) {
        error_log("Prepare failed: " . $conn->error);
        throw new Exception("Database error: Failed to prepare update statement");
    }
    
    $updateStmt->bind_param("ssi", $status, $paymentStatus, $orderId);
    
    if (!$updateStmt->execute()) {
        error_log("Update failed for order #$orderId: " . $updateStmt->error);
        throw new Exception("Failed to update order: " . $updateStmt->error);
    }
    
    // Log successful update
    error_log("Successfully updated order #$orderId - New Status: $status, New Payment Status: $paymentStatus");

    $affectedRows = $updateStmt->affected_rows;
    error_log("MySQL affected_rows: " . $affectedRows);
    
    // Check if no rows were affected (this means the WHERE clause didn't match or values are the same)
    if ($affectedRows === 0) {
        error_log("WARNING: No rows were affected by the update. This could mean:");
        error_log("1. The values are already the same as what we're trying to update");
        error_log("2. The WHERE clause didn't match any records");
        error_log("3. There's an issue with the data types");
    }
    
    $updateStmt->close();

    // Debug: Log affected rows
    error_log("Affected rows: " . $affectedRows);

    // Test payment_status update separately to isolate the issue
    $testSql = "UPDATE orders SET payment_status = ? WHERE order_id = ?";
    $testStmt = $conn->prepare($testSql);
    if ($testStmt) {
        error_log("Testing payment_status update separately...");
        $testStmt->bind_param("si", $paymentStatus, $orderId);
        $testExecuted = $testStmt->execute();
        $testAffected = $testStmt->affected_rows;
        error_log("Test payment_status update - Executed: " . ($testExecuted ? 'true' : 'false') . ", Affected rows: $testAffected");
        if (!$testExecuted) {
            error_log("Test payment_status update error: " . $testStmt->error);
        }
        $testStmt->close();
    }

    // Verify the update by selecting the record again
    $verifyStmt = $conn->prepare("SELECT status, payment_status FROM orders WHERE order_id = ?");
    $verifyStmt->bind_param("i", $orderId);
    $verifyStmt->execute();
    $verifyResult = $verifyStmt->get_result();
    $updatedOrder = $verifyResult->fetch_assoc();
    $verifyStmt->close();

    // Debug: Log updated values
    error_log("Updated order after update: " . print_r($updatedOrder, true));

    // Optional: Log the update activity
    if (!empty($message)) {
        $logSql = "INSERT INTO order_logs (order_id, status_change, message, created_at) VALUES (?, ?, ?, NOW())";
        $logStmt = $conn->prepare($logSql);
        if ($logStmt) {
            $statusChange = "Status: {$currentOrder['status']} → {$status}, Payment: {$currentOrder['payment_status']} → {$paymentStatus}";
            $logStmt->bind_param("iss", $orderId, $statusChange, $message);
            $logStmt->execute();
            $logStmt->close();
        }
    }

    $conn->close();

    echo json_encode([
        'success' => true,
        'message' => 'Order updated successfully',
        'data' => [
            'orderId' => $orderId,
            'newStatus' => $status,
            'newPaymentStatus' => $paymentStatus,
            'affectedRows' => $affectedRows,
            'beforeUpdate' => $currentOrder,
            'afterUpdate' => $updatedOrder
        ]
    ]);

} catch (Exception $e) {
    // Clean up resources
    if (isset($checkStmt)) $checkStmt->close();
    if (isset($updateStmt)) $updateStmt->close();
    if (isset($logStmt)) $logStmt->close();
    if (isset($verifyStmt)) $verifyStmt->close();
    if (isset($conn)) $conn->close();

    // Log the error
    error_log("Update order error: " . $e->getMessage());

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'file' => __FILE__,
            'line' => __LINE__,
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
            'received_data' => $data ?? null
        ]
    ]);
}
?>