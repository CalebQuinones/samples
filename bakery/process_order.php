<?php
session_start();
require_once 'config.php';

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Only allow logged-in users
    if (!isset($_SESSION["user_id"])) {
        echo json_encode([
            "success" => false,
            "message" => "You must be logged in to place an order."
        ]);
        exit;
    }
    $userId = $_SESSION["user_id"];
    // Get the order data from the POST request
    $rawData = file_get_contents("php://input");
    error_log("Raw POST data: " . $rawData);
    
    $orderData = json_decode($rawData, true);
    error_log("Decoded order data: " . print_r($orderData, true));
    
    // Validate the order data structure
    if (empty($orderData)) {
        error_log("Empty order data received");
        echo json_encode(["success" => false, "message" => "No order data received"]);
        exit;
    }
    
    if (empty($orderData['items'])) {
        error_log("No items in order data. Full order data: " . print_r($orderData, true));
        echo json_encode(["success" => false, "message" => "No items in cart"]);
        exit;
    }
    
    if (empty($orderData['customer'])) {
        echo json_encode(["success" => false, "message" => "No customer information provided"]);
        exit;
    }
    
    // Validate customer data
    $customer = $orderData['customer'];
    $missingFields = [];
    
    if (empty($customer['email'])) $missingFields[] = "email";
    if (empty($customer['fullname'])) $missingFields[] = "fullname";
    if (empty($customer['address'])) $missingFields[] = "address";
    if (empty($customer['deliveryDate'])) $missingFields[] = "deliveryDate";
    if (empty($customer['paymentMethod'])) $missingFields[] = "paymentMethod";
    if (empty($customer['deliveryMethod'])) $missingFields[] = "deliveryMethod";
    
    if (!empty($missingFields)) {
        echo json_encode([
            "success" => false, 
            "message" => "Missing required fields: " . implode(", ", $missingFields)
        ]);
        exit;
    }
    
    // Validate delivery method
    $validDeliveryMethods = ['standard', 'pickup'];
    if (!in_array($customer['deliveryMethod'], $validDeliveryMethods)) {
        echo json_encode(["success" => false, "message" => "Invalid delivery method"]);
        exit;
    }
    
    // Start a transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Calculate delivery fee based on delivery method
        $deliveryFee = 0;
        if ($customer['deliveryMethod'] === 'standard') {
            $deliveryFee = 50; // Standard delivery fee is 50 pesos
        }
        
        // Add delivery fee to total amount
        $totalAmount = $orderData['total'] + $deliveryFee;
        
        // Insert the order into the orders table
        $sql = "INSERT INTO orders (user_id, total_amount, delivery_date, status, payment_status, payment_method, delivery_method, delivery_fee, created_at) 
                VALUES (?, ?, ?, 'Pending', 'Pending', ?, ?, ?, NOW())";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        $deliveryDate = $orderData['customer']['deliveryDate'];
        $paymentMethod = $orderData['customer']['paymentMethod'];
        $deliveryMethod = $orderData['customer']['deliveryMethod'];
        
        mysqli_stmt_bind_param($stmt, "idsssd", $userId, $totalAmount, $deliveryDate, $paymentMethod, $deliveryMethod, $deliveryFee);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error inserting order: " . mysqli_error($conn));
        }
        
        // Get the order ID of the newly inserted order
        $orderId = mysqli_insert_id($conn);
        
        // Insert order items
        foreach ($orderData['items'] as $item) {
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            
            $productId = $item['id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            
            mysqli_stmt_bind_param($stmt, "iiid", $orderId, $productId, $quantity, $price);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error inserting order item: " . mysqli_error($conn));
            }
        }
        
        // Commit the transaction
        mysqli_commit($conn);
        
        // Return success response with order ID
        echo json_encode([
            "success" => true, 
            "message" => "Order placed successfully", 
            "orderId" => $orderId
        ]);
        
    } catch (Exception $e) {
        // Rollback the transaction on error
        mysqli_rollback($conn);
        
        echo json_encode([
            "success" => false, 
            "message" => "Error processing order: " . $e->getMessage()
        ]);
    }
} else {
    // If not a POST request, return an error
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}

error_log("SESSION: " . print_r($_SESSION, true));
?> 