<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get and decode JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['user_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

$userId = mysqli_real_escape_string($conn, $data['user_id']);

// Check if trying to delete an admin account
$checkSql = "SELECT role FROM login WHERE user_id = ?";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "i", $userId);
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);

if ($row = mysqli_fetch_assoc($result)) {
    if ($row['role'] === 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Cannot delete admin accounts']);
        exit;
    }
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Delete from customerinfo first (due to foreign key constraint)
    $sql = "DELETE FROM customerinfo WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    // Delete from login table
    $sql = "DELETE FROM login WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error deleting account");
    }

    // Commit transaction
    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Account deleted successfully']);

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Close connection
mysqli_close($conn);
?> 