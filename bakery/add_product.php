<?php
require_once('config.php');

header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Check database connection
if ($conn === false) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

try {
    // Get form data
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $category = $conn->real_escape_string($_POST['category'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    
    // Use the status from the form as the availability
    $availability = $conn->real_escape_string($_POST['status'] ?? 'In Stock');
    $created_at = date('Y-m-d H:i:s');
    $imagePath = '';

    // Handle file upload
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/products/';
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . strtolower($fileExtension);
        $targetPath = $uploadDir . $fileName;
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['productImage']['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG, and GIF are allowed.');
        }
        
        // Move the uploaded file
        if (move_uploaded_file($_FILES['productImage']['tmp_name'], $targetPath)) {
            $imagePath = $targetPath;
        } else {
            throw new Exception('Failed to upload image.');
        }
    }

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("INSERT INTO products (name, category, price, availability, image, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsss", $name, $category, $price, $availability, $imagePath, $created_at);
    
    $success = $stmt->execute();
    $productId = $conn->insert_id;
    $stmt->close();

    if ($success) {
        echo json_encode([
            'success' => true,
            'productId' => $productId,
            'imagePath' => $imagePath
        ]);
    } else {
        // If database insert failed but file was uploaded, delete the uploaded file
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }
        throw new Exception('Failed to save product to database.');
    }
} catch (Exception $e) {
    error_log("Error in add_product.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}

$conn->close();
