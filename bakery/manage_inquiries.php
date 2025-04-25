<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "admin"){
    header("location: login.php");
    exit;
}

// Handle inquiry status update
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["inquiry_id"])){
    $sql = "UPDATE inquiries SET status=? WHERE inquiry_id=?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "si", $param_status, $param_id);
        $param_status = $_POST["status"];
        $param_id = $_POST["inquiry_id"];
        
        if(mysqli_stmt_execute($stmt)){
            header("location: manage_inquiries.php");
            exit();
        }
    }
}

// Fetch all inquiries
$sql = "SELECT * FROM inquiries ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Customer Inquiries</title>
    <link rel="stylesheet" href="adminstyles.css">
</head>
<body>
    <div class="admin-container">
        <h2>Manage Customer Inquiries</h2>
        <div class="admin-actions">
            <a href="dashbrd.php" class="back-btn">Back to Dashboard</a>
        </div>

        <!-- Inquiries Table -->
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['inquiry_id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['subject']; ?></td>
                    <td><?php echo $row['message']; ?></td>
                    <td>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="status-form">
                            <input type="hidden" name="inquiry_id" value="<?php echo $row['inquiry_id']; ?>">
                            <select name="status" onchange="this.form.submit()">
                                <option value="New" <?php echo $row['status'] == 'New' ? 'selected' : ''; ?>>New</option>
                                <option value="Read" <?php echo $row['status'] == 'Read' ? 'selected' : ''; ?>>Read</option>
                                <option value="Replied" <?php echo $row['status'] == 'Replied' ? 'selected' : ''; ?>>Replied</option>
                            </select>
                        </form>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                    <td>
                        <button onclick="replyToInquiry('<?php echo $row['email']; ?>', '<?php echo $row['subject']; ?>')">Reply</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function replyToInquiry(email, subject) {
            // You can implement an email form or redirect to your email client
            window.location.href = 'mailto:' + email + '?subject=Re: ' + subject;
        }
    </script>
</body>
</html> 