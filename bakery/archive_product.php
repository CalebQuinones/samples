<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['product_id'] ?? null;

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit;
}

// Update product status to archived
$sql = "UPDATE products SET status = 'archived' WHERE product_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $productId);

$response = ['success' => false];

if (mysqli_stmt_execute($stmt)) {
    $response = ['success' => true];
} else {
    $response = ['success' => false, 'message' => mysqli_error($conn)];
}

header('Content-Type: application/json');
echo json_encode($response);
