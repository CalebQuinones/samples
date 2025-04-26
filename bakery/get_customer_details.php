<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if user_id is provided
if(!isset($_GET['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

$userId = mysqli_real_escape_string($conn, $_GET['user_id']);

// Fetch customer information
$sql = "SELECT * FROM customerinfo WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if($row = mysqli_fetch_assoc($result)) {
    // Format the data
    $response = [
        'success' => true,
        'phone' => $row['phone'],
        'birthday' => $row['birthday'],
        'address' => $row['address'],
        'payment' => $row['payment'],
        'created_at' => $row['created_at']
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'No customer information found'
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?> 