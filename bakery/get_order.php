<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

$order_id = intval($_GET['id']);

// Fetch order info and customer (LEFT JOIN for robustness)
$sql = "SELECT o.*, 
        CONCAT(l.Fname, ' ', l.Lname) as customer_name, 
        l.email, 
        l.created_at as customer_since,
        c.phone,
        o.delivery_method
        FROM orders o
        LEFT JOIN login l ON o.user_id = l.user_id
        LEFT JOIN customerinfo c ON o.user_id = c.user_id
        WHERE o.order_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

// Fetch order items
$sql_items = "SELECT oi.*, p.name as product_name, p.category
              FROM order_items oi
              LEFT JOIN products p ON oi.product_id = p.product_id
              WHERE oi.order_id = ?";
$stmt_items = mysqli_prepare($conn, $sql_items);
mysqli_stmt_bind_param($stmt_items, 'i', $order_id);
mysqli_stmt_execute($stmt_items);
$result_items = mysqli_stmt_get_result($stmt_items);
$items = [];
while ($row = mysqli_fetch_assoc($result_items)) {
    $items[] = $row;
}

// Build response
$response = [
    'success' => true,
    'order' => $order,
    'items' => $items
];
echo json_encode($response);
