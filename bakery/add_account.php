<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Start output buffering
ob_start();

// Function to send JSON response
function sendJsonResponse($success, $message = '', $data = []) {
    // Clear any previous output
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    // Temporarily log what's in the buffer *before* json_encode
    $bufferContent = ob_get_clean();
    if (!empty($bufferContent)) {
        error_log("Pre-JSON Buffer Content: " . $bufferContent);
    }

    // Set content type header
    header('Content-Type: application/json; charset=utf-8');

    // Build response
    $response = ['success' => (bool)$success];
    if ($message !== '') $response['message'] = $message;
    if (!empty($data)) $response = array_merge($response, $data);

    // Send response and exit
    echo json_encode($response);
    exit;
}

// Start session
session_start();

// Include database configuration
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    sendJsonResponse(false, 'Unauthorized access');
}

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get CSRF token from session
$csrf_token = $_SESSION['csrf_token'];

// Check if it's a POST request and has valid CSRF token
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Invalid request method');
}

// Get CSRF token from header
$csrf_token_header = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

// Validate CSRF token
if (empty($csrf_token_header) || $csrf_token_header !== $csrf_token) {
    sendJsonResponse(false, 'Invalid CSRF token');
}

// Get raw POST data
$input = file_get_contents('php://input');
$data = [];
parse_str($input, $data);

// Validate input data
if (json_last_error() !== JSON_ERROR_NONE) {
    sendJsonResponse(false, 'Invalid JSON data');
}

// Log received data for debugging
error_log('Received data: ' . print_r($data, true));

// Required fields
$required = ['first_name', 'last_name', 'email', 'password', 'role'];
$missing = [];
foreach ($required as $field) {
    if (empty(trim($data[$field] ?? ''))) {
        $missing[] = $field;
    }
}

if (!empty($missing)) {
    sendJsonResponse(false, 'Missing required fields: ' . implode(', ', $missing));
}

// Validate email format
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    sendJsonResponse(false, 'Invalid email format');
}

// Validate password strength
if (strlen($data['password']) < 8) {
    sendJsonResponse(false, 'Password must be at least 8 characters long');
}

// Prepare data
$firstName = trim($data['first_name']);
$lastName = trim($data['last_name']);
$email = trim($data['email']);
$password = $data['password'];
$role = trim($data['role']);
$status = isset($data['status']) && $data['status'] === 'active' ? 'active' : 'inactive';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Start database transaction
if (!mysqli_begin_transaction($conn)) {
    error_log("Failed to start transaction: " . mysqli_error($conn));
    throw new Exception("Failed to start database transaction");
}

try {
    // Log the start of the script
    error_log("Starting account creation for email: $email");

    // Check if email already exists
    $checkEmail = "SELECT user_id FROM login WHERE LOWER(TRIM(email)) = LOWER(?)";
    $stmt = mysqli_prepare($conn, $checkEmail);

    if (!$stmt) {
        throw new Exception("Database error: Failed to prepare statement");
    }

    mysqli_stmt_bind_param($stmt, "s", $email);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Database error: Failed to execute query");
    }

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        sendJsonResponse(false, 'An account with this email already exists');
    }

    mysqli_stmt_close($stmt);

    // Insert new account with prepared statement
    $sql = "INSERT INTO login (Fname, Lname, email, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        throw new Exception("Failed to prepare insert statement: " . mysqli_error($conn));
    }

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "ssssss", 
        mysqli_real_escape_string($conn, $firstName),
        mysqli_real_escape_string($conn, $lastName),
        mysqli_real_escape_string($conn, $email),
        $hashedPassword,
        mysqli_real_escape_string($conn, $role),
        $status
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to create account: " . mysqli_error($conn));
    }

    $userId = mysqli_insert_id($conn);

    // Commit transaction
    if (!mysqli_commit($conn)) {
        throw new Exception("Failed to save account information");
    }

    // Log success and send response
    error_log("Account created successfully. ID: $userId, Email: $email");
    sendJsonResponse(true, 'Account created successfully', [
        'user_id' => $userId,
        'email' => $email,
        'name' => "$firstName $lastName"
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn)) {
        mysqli_rollback($conn);
    }

    // Log the error
    error_log("Error in add_account.php: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());

    // Send error response
    sendJsonResponse(false, 'An error occurred: ' . $e->getMessage());

} finally {
    // Close database connection if it exists
    if (isset($conn)) {
        mysqli_close($conn);
    }

    // End output buffering and clean any remaining output
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
}
?>