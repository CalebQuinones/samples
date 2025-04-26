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

// Get POST data
$userId = isset($_POST['user_id']) ? mysqli_real_escape_string($conn, $_POST['user_id']) : null;
$firstName = isset($_POST['first_name']) ? mysqli_real_escape_string($conn, $_POST['first_name']) : null;
$lastName = isset($_POST['last_name']) ? mysqli_real_escape_string($conn, $_POST['last_name']) : null;
$email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : null;
$phone = isset($_POST['phone']) ? mysqli_real_escape_string($conn, $_POST['phone']) : null;
$address = isset($_POST['address']) ? mysqli_real_escape_string($conn, $_POST['address']) : null;
$status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : null;

// Validate required fields
if (!$userId || !$firstName || !$lastName || !$email || !$status) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
    exit;
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Update login table
    $sql = "UPDATE login SET 
            Fname = ?, 
            Lname = ?, 
            email = ?, 
            status = ?
            WHERE user_id = ?";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $firstName, $lastName, $email, $status, $userId);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error updating login information");
    }
    
    // Update customerinfo table if phone or address is provided
    if ($phone || $address) {
        // Check if customer info exists
        $checkSql = "SELECT user_id FROM customerinfo WHERE user_id = ?";
        $checkStmt = mysqli_prepare($conn, $checkSql);
        mysqli_stmt_bind_param($checkStmt, "i", $userId);
        mysqli_stmt_execute($checkStmt);
        $result = mysqli_stmt_get_result($checkStmt);
        
        if (mysqli_num_rows($result) > 0) {
            // Update existing record
            $updateSql = "UPDATE customerinfo SET 
                         phone = COALESCE(?, phone),
                         address = COALESCE(?, address)
                         WHERE user_id = ?";
            $updateStmt = mysqli_prepare($conn, $updateSql);
            mysqli_stmt_bind_param($updateStmt, "ssi", $phone, $address, $userId);
            
            if (!mysqli_stmt_execute($updateStmt)) {
                throw new Exception("Error updating customer information");
            }
        } else {
            // Insert new record
            $insertSql = "INSERT INTO customerinfo (user_id, phone, address) VALUES (?, ?, ?)";
            $insertStmt = mysqli_prepare($conn, $insertSql);
            mysqli_stmt_bind_param($insertStmt, "iss", $userId, $phone, $address);
            
            if (!mysqli_stmt_execute($insertStmt)) {
                throw new Exception("Error inserting customer information");
            }
        }
    }
    
    // Commit transaction
    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Account updated successfully']);
    
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Close connection
mysqli_close($conn);
?> 