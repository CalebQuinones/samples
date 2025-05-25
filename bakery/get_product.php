<?php
require_once('../../includes/db.php');

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Product ID not provided']);
    exit;
}

$product_id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error']);
}
