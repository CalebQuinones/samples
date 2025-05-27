<?php
header('Content-Type: application/json');
require_once '../config.php';

if (!$conn) {
    echo json_encode(['connected' => false]);
    exit;
}
echo json_encode(['connected' => true]);
