<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$inquiryId = $data['id'] ?? null;

if (!$inquiryId) {
    echo json_encode(['success' => false, 'message' => 'Invalid inquiry ID']);
    exit;
}

// Update the inquiry status to archived instead of deleting
$sql = "UPDATE inquiry SET status = 'archived' WHERE ID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $inquiryId);

$response = ['success' => false];

if (mysqli_stmt_execute($stmt)) {
    $response['success'] = true;
} else {
    $response['message'] = 'Error archiving inquiry';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
