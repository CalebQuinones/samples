<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to place an order']);
    exit;
}

$response = ['success' => false, 'message' => ''];

try {
    // Get JSON data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Debug logging
    error_log("Received order data: " . substr(print_r($json, true), 0, 500));

    if (!$data) {
        throw new Exception('Invalid data received');
    }

    // Get user's email from login table
    $user_id = $_SESSION['user_id'];
    $email_query = "SELECT email FROM login WHERE user_id = ?";
    $stmt = $conn->prepare($email_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    
    if (!$user_data) {
        throw new Exception('User information not found');
    }

    // Start transaction
    $conn->begin_transaction();

    // Create main order record
    $order_sql = "INSERT INTO orders (
        user_id,
        total_amount,
        status,
        delivery_address,
        delivery_method,
        delivery_date,
        payment_method
    ) VALUES (?, ?, 'Pending', ?, ?, ?, ?)";

    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param(
        "idssss",
        $user_id,
        $data['total'],
        $data['address'],
        $data['deliveryOption'],
        $data['deliveryDate'],
        $data['paymentMethod']
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to create order: ' . $stmt->error);
    }

    $order_id = $conn->insert_id;

    // Process cart items
    foreach ($data['cart'] as $item) {
        if ($item['type'] === 'custom') {
            // Handle custom cake order
            $custom_sql = "INSERT INTO custom_orders (
                user_id,
                order_id,
                cake_type,
                cake_tiers,
                cake_size,
                cake_flavor,
                filling_type,
                frosting_type,
                special_instructions,
                reference_image,
                base_price,
                delivery_address,
                delivery_date,
                delivery_method,
                payment_method
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            // Get cake type and tiers (for AI-generated cakes)
            $cake_type = isset($item['details']['cakeType']) ? $item['details']['cakeType'] : 'custom';
            $cake_tiers = isset($item['details']['tiers']) ? intval($item['details']['tiers']) : 1;
            
            // Process description/instructions
            $special_instructions = isset($item['details']['description']) 
                ? $item['details']['description'] 
                : (isset($item['details']['instructions']) ? $item['details']['instructions'] : '');
            
            // Determine if this is an AI-generated cake
            $is_ai_generated = isset($item['details']['isAIGenerated']) && $item['details']['isAIGenerated'];
            
            // For AI-generated cakes, store the base64 image directly
            if ($is_ai_generated) {
                $reference_image = $item['image']; // Store base64 image directly
                
                // Debug logging
                error_log("Processing AI-generated cake image");
                
                // Check if the image is a valid base64 string
                if (is_string($reference_image) && strpos($reference_image, 'data:image') === 0) {
                    error_log("Valid base64 image detected");
                } else {
                    error_log("Invalid image format: " . substr(print_r($reference_image, true), 0, 100));
                    // Set a fallback image path if the base64 is invalid
                    $reference_image = 'uploads/custom_cakes/default_cake.jpg';
                }
            } else {
                // For uploaded images, save to file system
                $image_data = $item['image'];
                $image_array = explode(';base64,', $image_data);
                
                if (count($image_array) < 2) {
                    error_log("Invalid image format for uploaded image: " . substr(print_r($image_data, true), 0, 100));
                    $reference_image = 'uploads/custom_cakes/default_cake.jpg';
                } else {
                    $image_type_aux = explode('image/', $image_array[0]);
                    $image_type = isset($image_type_aux[1]) ? $image_type_aux[1] : 'png';
                    $image_base64 = base64_decode($image_array[1]);
                    $file_name = time() . '_' . uniqid() . '_custom_cake.' . $image_type;
                    $upload_dir = __DIR__ . '/uploads/custom_cakes/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $target_path = $upload_dir . $file_name;
                    file_put_contents($target_path, $image_base64);
                    
                    $reference_image = $target_path;
                }
            }

            // Clean up size (remove "inch" if present)
            $cake_size = str_replace('inch', '', $item['details']['size']);

            // Prepare the SQL query
            $stmt = $conn->prepare($custom_sql);
            if (!$stmt) {
                error_log("Failed to prepare custom cake statement: " . $conn->error);
                throw new Exception('Failed to prepare custom cake statement: ' . $conn->error);
            }
            
            // Log cake details for debugging
            error_log("Cake details: type=$cake_type, tiers=$cake_tiers, size=$cake_size, flavor=" . 
                      $item['details']['flavor'] . ", filling=" . $item['details']['filling'] . 
                      ", frosting=" . $item['details']['frosting']);
            
            // Check if reference_image is too large for database
            if (is_string($reference_image) && strpos($reference_image, 'data:image') === 0 && strlen($reference_image) > 1000000) {
                // If image is too large, save to file system instead
                $image_array = explode(';base64,', $reference_image);
                $image_type_aux = explode('image/', $image_array[0]);
                $image_type = isset($image_type_aux[1]) ? $image_type_aux[1] : 'png';
                $image_base64 = base64_decode($image_array[1]);
                $file_name = time() . '_' . uniqid() . '_custom_cake.' . $image_type;
                $upload_dir = __DIR__ . '/uploads/custom_cakes/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $target_path = $upload_dir . $file_name;
                file_put_contents($target_path, $image_base64);
                
                $reference_image = $target_path;
                error_log("Image saved to file: $target_path");
            }
            
            // Calculate item total
            $itemTotal = $item['price'] * $item['quantity'];
            
            // Bind parameters
            $stmt->bind_param(
                "iisissssssdssss",
                $user_id,
                $order_id,
                $cake_type,
                $cake_tiers,
                $cake_size,
                $item['details']['flavor'],
                $item['details']['filling'],
                $item['details']['frosting'],
                $special_instructions,
                $reference_image,
                $item['price'],
                $data['address'],
                $data['deliveryDate'],
                $data['deliveryOption'],
                $data['paymentMethod']
            );

            // Execute statement with detailed error handling
            $exec_result = $stmt->execute();
            if (!$exec_result) {
                $error_msg = $stmt->error;
                error_log("Failed to create custom order: $error_msg");
                
                // Check if it's a database size limitation issue
                if (strpos($error_msg, 'Data too long') !== false) {
                    error_log("Data too long error - likely caused by large image");
                    
                    // Try to save the image to a file instead if it's not already a file path
                    if (is_string($reference_image) && strpos($reference_image, 'data:image') === 0) {
                        try {
                            $image_array = explode(';base64,', $reference_image);
                            $image_type_aux = explode('image/', $image_array[0]);
                            $image_type = isset($image_type_aux[1]) ? $image_type_aux[1] : 'png';
                            $image_base64 = base64_decode($image_array[1]);
                            $file_name = time() . '_' . uniqid() . '_custom_cake.' . $image_type;
                            $upload_dir = __DIR__ . '/uploads/custom_cakes/';
                            if (!file_exists($upload_dir)) {
                                mkdir($upload_dir, 0777, true);
                            }
                            $target_path = $upload_dir . $file_name;
                            file_put_contents($target_path, $image_base64);
                            
                            // Update reference_image to file path
                            $reference_image = $target_path;
                            error_log("Image saved to file as fallback: $target_path");
                            
                            // Try executing again with file path
                            $stmt->close();
                            $stmt = $conn->prepare($custom_sql);
                            $stmt->bind_param(
                                "iisissssssdssss",
                                $user_id,
                                $order_id,
                                $cake_type,
                                $cake_tiers,
                                $cake_size,
                                $item['details']['flavor'],
                                $item['details']['filling'],
                                $item['details']['frosting'],
                                $special_instructions,
                                $reference_image,
                                $item['price'],
                                $data['address'],
                                $data['deliveryDate'],
                                $data['deliveryOption'],
                                $data['paymentMethod']
                            );
                            
                            if (!$stmt->execute()) {
                                throw new Exception('Still failed to create custom order after saving image to file: ' . $stmt->error);
                            }
                        } catch (Exception $e) {
                            error_log("Failed during fallback image save: " . $e->getMessage());
                            throw new Exception('Failed to create custom order: ' . $error_msg);
                        }
                    } else {
                        throw new Exception('Failed to create custom order (image too large): ' . $error_msg);
                    }
                } else {
                    throw new Exception('Failed to create custom order: ' . $error_msg);
                }
            }
        } else {
            // Handle regular product order
            $order_item_sql = "INSERT INTO order_items (
                order_id,
                product_id,
                quantity,
                price
            ) VALUES (?, ?, ?, ?)";

            $stmt = $conn->prepare($order_item_sql);
            $stmt->bind_param(
                "iiid",
                $order_id,
                $item['id'],
                $item['quantity'],
                $item['price']
            );

            if (!$stmt->execute()) {
                throw new Exception('Failed to create order item: ' . $stmt->error);
            }
        }
    }

    // Commit transaction
    $conn->commit();

    $response = [
        'success' => true,
        'message' => 'Order placed successfully!',
        'order_id' => $order_id,
        'user_email' => $user_data['email']
    ];

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn) && $conn->ping()) {
        $conn->rollback();
    }

    $response = [
        'success' => false,
        'message' => 'Error processing order: ' . $e->getMessage()
    ];

    // Delete uploaded file if it exists and there was an error
    if (isset($target_path) && file_exists($target_path)) {
        unlink($target_path);
    }
}

echo json_encode($response);

error_log("SESSION: " . print_r($_SESSION, true));
?>