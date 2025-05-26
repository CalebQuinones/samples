<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if ID is provided
if(!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

$userId = mysqli_real_escape_string($conn, $_GET['id']);

// Get user details including customer info
$sql = "SELECT l.user_id, l.Fname, l.Lname, l.status, c.phone, c.address
        FROM login l
        LEFT JOIN customerinfo c ON l.user_id = c.user_id
        WHERE l.user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if($row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        'success' => true,
        'user_id' => $row['user_id'], // Ensure user_id is passed
        'Fname' => $row['Fname'],     // Add Fname
        'Lname' => $row['Lname'],     // Add Lname
        'status' => $row['status']
    ]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>