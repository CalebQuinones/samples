<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['id'])) {
    $id = mysqli_real_escape_string($conn, $data['id']);
    $sql = "DELETE FROM inquiry WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if(mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error deleting inquiry']);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Inquiry ID not provided']);
}
?> 