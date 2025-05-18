<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Only allow logged-in users
    if (!isset($_SESSION["user_id"])) {
        echo json_encode([
            "message" => "You must be logged in to place an order."
        ]);
        exit;
    }
    // Always use session user_id, ignore any user_id from POST
    $userId = $_SESSION["user_id"];
    // Get the order data from the POST request
    $rawData = file_get_contents("php://input");
    error_log("Raw POST data: " . $rawData);
    
    $orderData = json_decode($rawData, true);
    error_log("Decoded order data: " . print_r($orderData, true));

    // --- FIX: Accept both 'items' and 'cart' as the cart array key ---
    $items = [];
    if (!empty($orderData['items']) && is_array($orderData['items'])) {
        $items = $orderData['items'];
    } elseif (!empty($orderData['cart']) && is_array($orderData['cart'])) {
        $items = $orderData['cart'];
    }
    $orderData['items'] = $items;

    // --- FIX: Accept both 'customer' and flat fields for customer info ---
    $customer = [];
    if (!empty($orderData['customer']) && is_array($orderData['customer'])) {
        $customer = $orderData['customer'];
    } else {
        // Try to build customer array from flat fields (as sent by your JS)
        $customer = [
            'email' => $orderData['email'] ?? '',
            'fullname' => $orderData['fullname'] ?? '',
            'address' => $orderData['address'] ?? '',
            'deliveryDate' => $orderData['deliveryDate'] ?? '',
            'paymentMethod' => $orderData['paymentMethod'] ?? '',
            'deliveryMethod' => $orderData['deliveryOption'] ?? '', // Note: your JS sends 'deliveryOption'
        ];
    }
    $orderData['customer'] = $customer;
    
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

    if (
        empty($orderData['customer']['email']) ||
        empty($orderData['customer']['fullname']) ||
        empty($orderData['customer']['address']) ||
        empty($orderData['customer']['deliveryDate']) ||
        empty($orderData['customer']['paymentMethod']) ||
        empty($orderData['customer']['deliveryMethod'])
    ) {
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
        $totalAmount = isset($orderData['total']) ? floatval($orderData['total']) + $deliveryFee : $deliveryFee;

        // Use the details entered in the checkout form for the order
        $deliveryDate = $customer['deliveryDate'];
        $paymentMethod = $customer['paymentMethod'];
        $deliveryMethod = $customer['deliveryMethod'];
        $deliveryAddress = $customer['address'];
        $email = $customer['email'];
        $fullname = $customer['fullname'];

        // Debug: log all values to be inserted
        error_log("Order Insert Params: user_id=$userId, email=$email, fullname=$fullname, total_amount=$totalAmount, delivery_address=$deliveryAddress, delivery_method=$deliveryMethod, delivery_date=$deliveryDate, payment_method=$paymentMethod, delivery_fee=$deliveryFee");

        // Insert the order into the orders table
        // Columns: user_id, email, fullname, total_amount, status, delivery_address, delivery_method, delivery_date, payment_method, created_at, delivery_fee, payment_status
        // SQL: 9 placeholders for bind_param, status/payment_status set in SQL
        $sql = "INSERT INTO orders (
            user_id, email, fullname, total_amount, status, delivery_address, delivery_method, delivery_date, payment_method, created_at, delivery_fee, payment_status
        ) VALUES (?, ?, ?, ?, 'Pending', ?, ?, ?, ?, NOW(), ?, 'Pending')";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            error_log("Prepare failed: " . mysqli_error($conn));
            throw new Exception("Prepare failed: " . mysqli_error($conn));
        }

        // user_id (i), email (s), fullname (s), total_amount (d), delivery_address (s), delivery_method (s), delivery_date (s), payment_method (s), delivery_fee (d)
        // There are 9 placeholders: i, s, s, d, s, s, s, s, d
        mysqli_stmt_bind_param(
            $stmt,
            "issdssssd",
            $userId,
            $email,
            $fullname,
            $totalAmount,
            $deliveryAddress,
            $deliveryMethod,
            $deliveryDate,
            $paymentMethod,
            $deliveryFee
        );

        if (!mysqli_stmt_execute($stmt)) {
            error_log("Order insert error: " . mysqli_stmt_error($stmt));
            throw new Exception("Error inserting order: " . mysqli_stmt_error($stmt));
        }

        // Get the order ID of the newly inserted order
        $orderId = mysqli_insert_id($conn);

        // Insert order items
        foreach ($orderData['items'] as $item) {
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                throw new Exception("Prepare failed (order_items): " . mysqli_error($conn));
            }

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

        // Log the error for debugging
        error_log("Order processing error: " . $e->getMessage());

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