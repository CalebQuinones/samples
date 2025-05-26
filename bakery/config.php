<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Triple J's Bakery Configuration
 * 
 * IMPORTANT: To use the AICakes feature, you must configure a Stability AI API key
 * in the stability_api.php file. See README.md for more information.
 */

// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'bakerydb'); 

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection with detailed error reporting
if($conn === false){
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed',
        'error' => mysqli_connect_error(),
        'errno' => mysqli_connect_errno()
    ]));
}

// Set charset to utf8mb4
if (!mysqli_set_charset($conn, "utf8mb4")) {
    die(json_encode([
        'success' => false,
        'message' => 'Failed to set charset',
        'error' => mysqli_error($conn)
    ]));
}

// Debug: Check if database exists and tables are accessible
$test_query = "SHOW TABLES";
$result = mysqli_query($conn, $test_query);
if (!$result) {
    die(json_encode([
        'success' => false,
        'message' => 'Failed to query database',
        'error' => mysqli_error($conn)
    ]));
}
?>