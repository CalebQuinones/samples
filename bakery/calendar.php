<?php
session_start();
require_once '../../config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get the month and year from the request
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Calculate the first and last day of the month
$firstDay = date('Y-m-01', strtotime("$year-$month-01"));
$lastDay = date('Y-m-t', strtotime("$year-$month-01"));

// Fetch orders for the specified month
$sql = "SELECT o.*, 
        CONCAT(l.Fname, ' ', l.Lname) as customer_name,
        GROUP_CONCAT(p.name SEPARATOR ', ') as products
        FROM orders o 
        LEFT JOIN login l ON o.user_id = l.user_id 
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.product_id
        WHERE o.delivery_date BETWEEN ? AND ?
        GROUP BY o.order_id
        ORDER BY o.delivery_date ASC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $firstDay, $lastDay);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = [
        'id' => $row['order_id'],
        'customer' => $row['customer_name'],
        'products' => $row['products'],
        'date' => $row['delivery_date'],
        'status' => $row['status']
    ];
}

// Return the orders as JSON
header('Content-Type: application/json');
echo json_encode($orders);
?> 