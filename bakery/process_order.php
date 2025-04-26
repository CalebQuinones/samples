<?php
session_start();
require_once 'config.php';

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the order data from the POST request
    $orderData = json_decode(file_get_contents("php://input"), true);
    
    // Validate the order data
    if (empty($orderData) || empty($orderData['items']) || empty($orderData['customer'])) {
        echo json_encode(["success" => false, "message" => "Invalid order data"]);
        exit;
    }
    
    // Start a transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert the order into the orders table
        $sql = "INSERT INTO orders (user_id, total_amount, delivery_date, status, payment_status, payment_method, created_at) 
                VALUES (?, ?, ?, 'Pending', 'Pending', ?, NOW())";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        // If user is logged in, use their ID, otherwise use 0 for guest orders
        $userId = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : 0;
        $totalAmount = $orderData['total'];
        $deliveryDate = $orderData['customer']['deliveryDate'];
        $paymentMethod = $orderData['customer']['paymentMethod'];
        
        mysqli_stmt_bind_param($stmt, "idss", $userId, $totalAmount, $deliveryDate, $paymentMethod);
        
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
        
        // Insert customer information if not logged in
        if ($userId == 0) {
            $sql = "INSERT INTO guest_orders (order_id, full_name, email, address, phone) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            
            $fullName = $orderData['customer']['fullname'];
            $email = $orderData['customer']['email'];
            $address = $orderData['customer']['address'];
            $phone = isset($orderData['customer']['phone']) ? $orderData['customer']['phone'] : '';
            
            mysqli_stmt_bind_param($stmt, "issss", $orderId, $fullName, $email, $address, $phone);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error inserting guest order: " . mysqli_error($conn));
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
?> 