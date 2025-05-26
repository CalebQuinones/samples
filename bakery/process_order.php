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

            // Save the image
            $image_data = $item['image'];
            $image_array = explode(';base64,', $image_data);
            $image_type_aux = explode('image/', $image_array[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_array[1]);
            $file_name = time() . '_' . uniqid() . '_custom_cake.' . $image_type;
            $upload_dir = __DIR__ . '/uploads/custom_cakes/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $target_path = $upload_dir . $file_name;
            file_put_contents($target_path, $image_base64);

            $stmt = $conn->prepare($custom_sql);
            $itemTotal = $item['price'] * $item['quantity'];
            $stmt->bind_param(
                "iissssssddssss",
                $user_id,
                $order_id,
                $item['details']['size'],
                $item['details']['flavor'],
                $item['details']['filling'],
                $item['details']['frosting'],
                $item['details']['instructions'],
                $target_path,
                $item['price'],
                $itemTotal,
                $data['address'],
                $data['deliveryDate'],
                $data['deliveryOption'],
                $data['paymentMethod']
            );

            if (!$stmt->execute()) {
                throw new Exception('Failed to create custom order: ' . $stmt->error);
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