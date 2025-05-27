<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to place a custom order']);
    exit;
}

$response = ['success' => false, 'message' => ''];

try {
    // Get JSON data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

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

    // Handle base64 image
    $upload_dir = __DIR__ . '/uploads/custom_cakes/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Extract base64 image data and save
    $image_data = $data['photo'];
    $image_array = explode(';base64,', $image_data);
    $image_type_aux = explode('image/', $image_array[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_array[1]);
    $file_name = time() . '_custom_cake.' . $image_type;
    $target_path = $upload_dir . $file_name;
    
    file_put_contents($target_path, $image_base64);

    // Start transaction
    $conn->begin_transaction();

    // First create order record - using only the columns that exist in the orders table
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
    $total_amount = $data['calculated_price'];
    
    $stmt->bind_param(
        "idssss",
        $user_id,
        $total_amount,
        $data['address'],
        $data['delivery_method'],
        $data['delivery_date'],
        $data['payment_method']
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to create order: ' . $stmt->error);
    }

    $order_id = $conn->insert_id;

    // Then create custom order record
    $custom_sql = "INSERT INTO custom_orders (
        user_id,
        order_id,
        cake_size,
        cake_flavor,
        filling_type,
        frosting_type,
        special_instructions,
        reference_image,
        base_price,
        total_price,
        delivery_address,
        delivery_date,
        delivery_method,
        payment_method
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($custom_sql);
    $stmt->bind_param(
        "iissssssddssss",
        $user_id,
        $order_id,
        $data['cake_size'],
        $data['cake_flavor'],
        $data['filling_type'],
        $data['frosting_type'],
        $data['special_instructions'],
        $target_path,
        $total_amount,
        $total_amount,
        $data['address'],
        $data['delivery_date'],
        $data['delivery_method'],
        $data['payment_method']
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to create custom order: ' . $stmt->error);
    }

    $custom_order_id = $conn->insert_id;

    // Commit transaction
    $conn->commit();

    $response = [
        'success' => true,
        'message' => 'Custom order submitted successfully!',
        'custom_order_id' => $custom_order_id,
        'order_id' => $order_id,
        'user_email' => $user_data['email'] // Include email in response
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