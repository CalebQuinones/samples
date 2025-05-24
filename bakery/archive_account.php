<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? null;

if($userId) {
    // Update the account status to archived instead of deleting
    $sql = "UPDATE login SET status = 'archived' WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    
    if(mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error archiving account']);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
}

mysqli_close($conn);
?>
