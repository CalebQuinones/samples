<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "admin"){
    header("location: login.php");
    exit;
}

// Handle custom order status update
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["custom_order_id"])){
    $sql = "UPDATE custom_cake_orders SET status=? WHERE custom_order_id=?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "si", $param_status, $param_id);
        $param_status = $_POST["status"];
        $param_id = $_POST["custom_order_id"];
        
        if(mysqli_stmt_execute($stmt)){
            header("location: manage_custom_orders.php");
            exit();
        }
    }
}

// Fetch all custom orders with user information
$sql = "SELECT c.*, u.full_name, u.email, u.phone_number 
        FROM custom_cake_orders c 
        JOIN users u ON c.user_id = u.user_id 
        ORDER BY c.created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Custom Cake Orders</title>
    <link rel="stylesheet" href="adminstyles.css">
</head>
<body>
    <div class="admin-container">
        <h2>Manage Custom Cake Orders</h2>
        <div class="admin-actions">
            <a href="dashbrd.php" class="back-btn">Back to Dashboard</a>
        </div>

        <!-- Custom Orders Table -->
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Cake Details</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['custom_order_id']; ?></td>
                    <td>
                        <?php echo $row['full_name']; ?><br>
                        <?php echo $row['email']; ?><br>
                        <?php echo $row['phone_number']; ?>
                    </td>
                    <td>
                        <strong>Size:</strong> <?php echo $row['cake_size']; ?><br>
                        <strong>Flavor:</strong> <?php echo $row['cake_flavor']; ?><br>
                        <strong>Filling:</strong> <?php echo $row['filling_type']; ?><br>
                        <strong>Frosting:</strong> <?php echo $row['frosting_type']; ?><br>
                        <?php if($row['special_instructions']): ?>
                        <strong>Special Instructions:</strong><br>
                        <?php echo $row['special_instructions']; ?>
                        <?php endif; ?>
                        <?php if($row['reference_image']): ?>
                        <br><a href="<?php echo $row['reference_image']; ?>" target="_blank">View Reference Image</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="status-form">
                            <input type="hidden" name="custom_order_id" value="<?php echo $row['custom_order_id']; ?>">
                            <select name="status" onchange="this.form.submit()">
                                <option value="Pending" <?php echo $row['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Processing" <?php echo $row['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="Completed" <?php echo $row['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="Cancelled" <?php echo $row['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </form>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                    <td>
                        <button onclick="viewOrderDetails(<?php echo $row['custom_order_id']; ?>)">View Details</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function viewOrderDetails(orderId) {
            // You can implement a modal or redirect to a detailed view page
            window.location.href = 'custom_order_details.php?id=' + orderId;
        }
    </script>
</body>
</html> 