<?php
require_once 'config.php';

// Check if user is admin
session_start();
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
    die("Unauthorized access");
}

try {
    // Update the products table to include 'Low Stock' in the availability enum
    $sql = "ALTER TABLE products 
            MODIFY COLUMN availability 
            ENUM('In Stock', 'Low Stock', 'Out of Stock') 
            NOT NULL DEFAULT 'In Stock'";
            
    if (mysqli_query($conn, $sql)) {
        echo "Products table updated successfully. 'Low Stock' status has been added.";
    } else {
        throw new Exception("Error updating table: " . mysqli_error($conn));
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
mysqli_close($conn);
?>

<!-- Add a simple button to run the update -->
<!DOCTYPE html>
<html>
<head>
    <title>Update Products Table</title>
</head>
<body>
    <h1>Update Products Table</h1>
    <p>Click the button below to add 'Low Stock' status to the products table.</p>
    <form method="post">
        <button type="submit" name="update">Update Table</button>
    </form>
</body>
</html>
