<?php
// Enable error reporting for debugging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Set headers first
header('Content-Type: application/json');

// Check if user is logged in and is admin
session_start();
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

require_once('config.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
    exit;
}

$product_id = intval($_GET['id']);
$response = ['success' => false];

try {
    // Using prepared statement to prevent SQL injection
    $sql = "SELECT product_id, name, price, category, availability, image FROM products WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to execute query: ' . mysqli_stmt_error($stmt));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    
    if ($product) {
        $response = [
            'success' => true,
            'product' => $product
        ];
    } else {
        $response['error'] = 'Product not found';
    }
    
    mysqli_stmt_close($stmt);
    
} catch (Exception $e) {
    error_log('Error in get_product.php: ' . $e->getMessage());
    http_response_code(500);
    $response['error'] = 'An error occurred while fetching the product';
}

// Close connection
if (isset($conn)) {
    mysqli_close($conn);
}

echo json_encode($response);
?>
