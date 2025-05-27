<?php
session_start();
require_once 'config.php';

// Prevent PHP errors/warnings from breaking JSON output
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_PARSE);

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get the month and year from the request
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

$sql = "SELECT 
    o.order_id as id,
    o.status,
    DATE(o.delivery_date) as date,
    GROUP_CONCAT(p.name SEPARATOR ', ') as products,
    CONCAT(l.Fname, ' ', l.Lname) as customer
    FROM orders o
    JOIN login l ON o.user_id = l.user_id
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE MONTH(o.delivery_date) = ? AND YEAR(o.delivery_date) = ?
    GROUP BY o.order_id, o.status, o.delivery_date, l.Fname, l.Lname
    ORDER BY o.delivery_date ASC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $month, $year);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orders = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Format the date to match JS expected format
    $row['date'] = date('Y-m-d', strtotime($row['date']));
    $orders[] = $row;
}

// Return the orders as JSON
header('Content-Type: application/json');
echo json_encode($orders);

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}
?>