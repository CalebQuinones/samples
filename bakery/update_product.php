<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Set headers first
header('Content-Type: application/json');

// Start session and log session data
session_start();
error_log('Session data: ' . print_r($_SESSION, true));

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
    error_log('Unauthorized access attempt');
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

// Log database connection attempt
error_log('Including config.php');
require_once('config.php');

// Check database connection
if (!$conn) {
    $error = 'Database connection failed: ' . mysqli_connect_error();
    error_log($error);
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $error]);
    exit;
}

error_log('Database connection successful');

// Initialize response array
$response = ['success' => false];

try {
    // Log the raw input
    $rawInput = file_get_contents('php://input');
    error_log('Raw input received: ' . $rawInput);
    
    // Check if input is empty
    if (empty($rawInput)) {
        throw new Exception('No input data received');
    }
    
    // Decode JSON input
    $json = $rawInput;
    $input = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }
    
    error_log('Decoded input: ' . print_r($input, true));
    
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Check if required fields exist
    $requiredFields = ['product_id', 'name', 'price', 'category', 'availability'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!array_key_exists($field, $input) || trim($input[$field]) === '') {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        throw new Exception('Missing required fields: ' . implode(', ', $missingFields));
    }
    
    // Sanitize and validate input
    $product_id = filter_var($input['product_id'], FILTER_VALIDATE_INT);
    if ($product_id === false) {
        throw new Exception('Invalid product ID');
    }
    
    $name = trim($input['name']);
    $price = filter_var($input['price'], FILTER_VALIDATE_FLOAT);
    $category = trim($input['category']);
    $availability = trim($input['availability']);
    
    if ($price === false || $price < 0) {
        throw new Exception('Invalid price');
    }
    
    // Log the values that will be used in the query
    error_log('Values for update:');
    error_log('- Product ID: ' . $product_id);
    error_log('- Name: ' . $name);
    error_log('- Price: ' . $price);
    error_log('- Category: ' . $category);
    error_log('- Availability: ' . $availability);
    
    // Handle file upload if a new image is provided
    $imagePath = '';
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/products/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $fileExtension;
        $targetPath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($_FILES['productImage']['tmp_name'], $targetPath)) {
            $imagePath = $targetPath;
        } else {
            throw new Exception('Failed to upload image');
        }
    }

    // Prepare the SQL query
    if (!empty($imagePath)) {
        $sql = "UPDATE products SET 
                name = ?,
                price = ?,
                category = ?,
                availability = ?,
                image = ?
                WHERE product_id = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            throw new Exception('Database error: ' . mysqli_error($conn));
        }
        
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "sdsssi", 
            $name, 
            $price, 
            $category, 
            $availability, 
            $imagePath,
            $product_id
        );
    } else {
        $sql = "UPDATE products SET 
                name = ?,
                price = ?,
                category = ?,
                availability = ?
                WHERE product_id = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            throw new Exception('Database error: ' . mysqli_error($conn));
        }
        
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "sdssi", 
            $name, 
            $price, 
            $category, 
            $availability,
            $product_id
        );
    }
    
    error_log('Preparing SQL: ' . $sql);
    
    // Execute the query
    if (mysqli_stmt_execute($stmt)) {
        $response = [
            'success' => true,
            'message' => 'Product updated successfully',
            'imagePath' => $imagePath ?: ''
        ];
        error_log('Product updated successfully');
    } else {
        // If database update failed but new file was uploaded, delete the uploaded file
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }
        throw new Exception('Failed to update product: ' . mysqli_stmt_error($stmt));
    }
    
    mysqli_stmt_close($stmt);
    
} catch (Exception $e) {
    $errorMsg = 'Error in update_product.php: ' . $e->getMessage();
    error_log($errorMsg);
    http_response_code(400);
    $response['error'] = $e->getMessage();
}

// Close connection
if (isset($conn)) {
    mysqli_close($conn);
}

echo json_encode($response);
?>
