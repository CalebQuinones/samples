<?php
/**
 * Process Custom Cake Orders
 * Handles storing AI-generated custom cake designs to the database
 */

// Include database configuration
require_once 'config.php';
session_start();

/**
 * Store a custom cake order in the database
 * 
 * @param int $user_id The ID of the user placing the order
 * @param int $order_id The ID of the parent order if available
 * @param array $cakeData The cake details
 * @return array Response with success status and message
 */
function storeCustomCakeOrder($user_id, $order_id, $cakeData) {
    global $conn;
    
    // Extract cake details
    $cake_type = $cakeData['cakeType'] ?? '';
    $cake_tiers = intval($cakeData['cakeTiers'] ?? 1);
    $cake_size = $cakeData['cakeSize'] ?? '';
    $cake_flavor = $cakeData['cakeFlavor'] ?? '';
    $filling_type = $cakeData['fillingType'] ?? '';
    $frosting_type = $cakeData['frostingType'] ?? '';
    $special_instructions = $cakeData['cakeDescription'] ?? '';
    $reference_image = $cakeData['referenceImage'] ?? '';
    $base_price = floatval($cakeData['estimatedPrice'] ?? 0);
    
    // Clean up size (remove "inch" if present)
    $cake_size = str_replace('inch', '', $cake_size);
    
    // Set default delivery method
    $delivery_method = 'pickup'; // Default to pickup
    
    try {
        // Prepare SQL statement
        $sql = "INSERT INTO custom_orders (
                    user_id, order_id, cake_type, cake_tiers, cake_size, 
                    cake_flavor, filling_type, frosting_type, special_instructions, 
                    reference_image, base_price, delivery_method
                ) VALUES (
                    ?, ?, ?, ?, ?, 
                    ?, ?, ?, ?,
                    ?, ?, ?
                )";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Failed to prepare statement: ' . mysqli_error($conn)
            ];
        }
        
        // Bind parameters
        mysqli_stmt_bind_param(
            $stmt,
            "iisisssssds",
            $user_id,
            $order_id,
            $cake_type,
            $cake_tiers,
            $cake_size,
            $cake_flavor,
            $filling_type,
            $frosting_type,
            $special_instructions,
            $reference_image,
            $base_price,
            $delivery_method
        );
        
        // Execute statement
        $result = mysqli_stmt_execute($stmt);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Failed to store custom cake order: ' . mysqli_stmt_error($stmt)
            ];
        }
        
        // Get the inserted custom order ID
        $custom_order_id = mysqli_insert_id($conn);
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        return [
            'success' => true,
            'message' => 'Custom cake order stored successfully',
            'custom_order_id' => $custom_order_id
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error storing custom cake order: ' . $e->getMessage()
        ];
    }
}

// If this file is accessed directly via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process only AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // Get JSON data from request
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
        
        // Validate data
        if (!isset($data['user_id']) || !isset($data['cake_details'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing required data'
            ]);
            exit;
        }
        
        // Store order
        $result = storeCustomCakeOrder(
            $data['user_id'],
            $data['order_id'] ?? null,
            $data['cake_details']
        );
        
        // Return response as JSON
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
} 