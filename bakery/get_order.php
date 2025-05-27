<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Order ID is required');
    }

    $order_id = intval($_GET['id']);

    // First, check if the order exists
    $check_sql = "SELECT COUNT(*) as count FROM orders WHERE order_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $order_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $count = mysqli_fetch_assoc($check_result)['count'];

    if ($count == 0) {
        throw new Exception("Order not found");
    }

    // Main order query
    $sql = "SELECT 
        o.*,
        l.email,
        l.fname as firstname,
        l.lname as lastname,
        c.phone as phone,
        c.address,
        CONCAT(l.fname, ' ', l.lname) as customer_name
    FROM orders o
    LEFT JOIN customerinfo c ON o.user_id = c.user_id
    LEFT JOIN login l ON c.user_id = l.user_id
    WHERE o.order_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $order_result = mysqli_stmt_get_result($stmt);
    
    if (!$order_result) {
        throw new Exception('Failed to fetch order: ' . mysqli_error($conn));
    }

    $order = mysqli_fetch_assoc($order_result);
    
    if (!$order) {
        throw new Exception('Order data not found');
    }

    // Get order items with product details
    $items_sql = "SELECT 
        oi.*,
        p.name as product_name,
        p.price as unit_price,
        p.category
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?";

    $items_stmt = mysqli_prepare($conn, $items_sql);
    if (!$items_stmt) {
        throw new Exception('Failed to prepare items statement: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($items_stmt, "i", $order_id);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    
    $items = [];
    while ($item = mysqli_fetch_assoc($items_result)) {
        // Ensure price is set
        $item['price'] = $item['unit_price'] ?? 0;
        $items[] = $item;
    }

    // Add default values for missing fields
    $order['delivery_fee'] = $order['delivery_fee'] ?? 0;
    $order['payment_status'] = $order['payment_status'] ?? 'Pending';
    $order['status'] = $order['status'] ?? 'Pending';
    $order['payment_method'] = $order['payment_method'] ?? 'Not specified';
    $order['delivery_method'] = $order['delivery_method'] ?? 'Standard Delivery';

    echo json_encode([
        'success' => true,
        'order' => $order,
        'items' => $items
    ]);

} catch (Exception $e) {
    error_log("Order fetch error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($conn);
