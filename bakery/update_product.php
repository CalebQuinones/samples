<?php
require_once('../../includes/db.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

try {
    $product_id = intval($_POST['editProductId'] ?? 0);
    $name = $_POST['editProductName'] ?? '';
    $category = $_POST['editProductCategory'] ?? '';
    $price = floatval($_POST['editProductPrice'] ?? 0);
    $description = $_POST['editProductDescription'] ?? '';
    $status = $_POST['editProductStatus'] ?? 'active';

    $stmt = $pdo->prepare("UPDATE products SET name = ?, category = ?, price = ?, description = ?, status = ? WHERE product_id = ?");
    $success = $stmt->execute([$name, $category, $price, $description, $status, $product_id]);

    echo json_encode(['success' => $success]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
