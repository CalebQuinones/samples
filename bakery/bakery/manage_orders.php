<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "admin"){
    header("location: login.php");
    exit;
}

// Handle order status update
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["order_id"])){
    $sql = "UPDATE orders SET status=?, payment_status=? WHERE order_id=?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "ssi", $param_status, $param_payment_status, $param_id);
        $param_status = $_POST["status"];
        $param_payment_status = $_POST["payment_status"];
        $param_id = $_POST["order_id"];
        
        if(mysqli_stmt_execute($stmt)){
            header("location: manage_orders.php");
            exit();
        }
    }
}

// Fetch all orders with user information
$sql = "SELECT o.*, u.full_name, u.email, u.phone_number 
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="adminstyles.css">
</head>
<body>
    <div class="admin-container">
        <h2>Manage Orders</h2>
        <div class="admin-actions">
            <a href="dashbrd.php" class="back-btn">Back to Dashboard</a>
        </div>

        <!-- Orders Table -->
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Delivery Date</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['order_id']; ?></td>
                    <td>
                        <?php echo $row['full_name']; ?><br>
                        <?php echo $row['email']; ?><br>
                        <?php echo $row['phone_number']; ?>
                    </td>
                    <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                    <td><?php echo date('M d, Y', strtotime($row['delivery_date'])); ?></td>
                    <td>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="status-form">
                            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                            <select name="status" onchange="this.form.submit()">
                                <option value="Pending" <?php echo $row['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Processing" <?php echo $row['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="Completed" <?php echo $row['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="Cancelled" <?php echo $row['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="status-form">
                            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                            <select name="payment_status" onchange="this.form.submit()">
                                <option value="Pending" <?php echo $row['payment_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Paid" <?php echo $row['payment_status'] == 'Paid' ? 'selected' : ''; ?>>Paid</option>
                                <option value="Failed" <?php echo $row['payment_status'] == 'Failed' ? 'selected' : ''; ?>>Failed</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <a href="order_details.php?id=<?php echo $row['order_id']; ?>" class="view-btn">View Details</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 