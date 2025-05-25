<?php
require_once('../../includes/db.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

try {
    $name = $_POST['productName'] ?? '';
    $category = $_POST['productCategory'] ?? '';
    $price = floatval($_POST['productPrice'] ?? 0);
    $description = $_POST['productDescription'] ?? '';
    $status = $_POST['productStatus'] ?? 'active';

    $stmt = $pdo->prepare("INSERT INTO products (name, category, price, description, status) VALUES (?, ?, ?, ?, ?)");
    $success = $stmt->execute([$name, $category, $price, $description, $status]);

    echo json_encode(['success' => $success]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
