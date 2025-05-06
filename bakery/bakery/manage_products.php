<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "admin"){
    header("location: login.php");
    exit;
}

// Handle product deletion
if(isset($_GET["delete"]) && !empty($_GET["delete"])){
    $sql = "DELETE FROM products WHERE product_id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $_GET["delete"];
        if(mysqli_stmt_execute($stmt)){
            header("location: manage_products.php");
            exit();
        }
    }
}

// Handle product creation/update
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["product_id"])){
        // Update existing product
        $sql = "UPDATE products SET name=?, description=?, price=?, category=?, availability=? WHERE product_id=?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "ssdssi", $param_name, $param_description, $param_price, $param_category, $param_availability, $param_id);
            $param_name = $_POST["name"];
            $param_description = $_POST["description"];
            $param_price = $_POST["price"];
            $param_category = $_POST["category"];
            $param_availability = $_POST["availability"];
            $param_id = $_POST["product_id"];
            
            if(mysqli_stmt_execute($stmt)){
                header("location: manage_products.php");
                exit();
            }
        }
    } else {
        // Create new product
        $sql = "INSERT INTO products (name, description, price, category, availability) VALUES (?, ?, ?, ?, ?)";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "ssdss", $param_name, $param_description, $param_price, $param_category, $param_availability);
            $param_name = $_POST["name"];
            $param_description = $_POST["description"];
            $param_price = $_POST["price"];
            $param_category = $_POST["category"];
            $param_availability = $_POST["availability"];
            
            if(mysqli_stmt_execute($stmt)){
                header("location: manage_products.php");
                exit();
            }
        }
    }
}

// Fetch all products
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <link rel="stylesheet" href="adminstyles.css">
</head>
<body>
    <div class="admin-container">
        <h2>Manage Products</h2>
        <div class="admin-actions">
            <button onclick="showAddForm()">Add New Product</button>
            <a href="dashbrd.php" class="back-btn">Back to Dashboard</a>
        </div>

        <!-- Add/Edit Product Form -->
        <div id="productForm" class="form-container" style="display: none;">
            <h3 id="formTitle">Add New Product</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="product_id" id="product_id">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="description" required></textarea>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" name="price" id="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" id="category" required>
                        <option value="Birthday Cakes">Birthday Cakes</option>
                        <option value="Wedding Cakes">Wedding Cakes</option>
                        <option value="Shower Cakes">Shower Cakes</option>
                        <option value="Cupcakes">Cupcakes</option>
                        <option value="Breads">Breads</option>
                        <option value="Celebration">Celebration</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Availability</label>
                    <select name="availability" id="availability" required>
                        <option value="In Stock">In Stock</option>
                        <option value="Out of Stock">Out of Stock</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit">Save</button>
                    <button type="button" onclick="hideForm()">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['product_id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td>₱<?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo $row['category']; ?></td>
                    <td><?php echo $row['availability']; ?></td>
                    <td>
                        <button onclick="editProduct(<?php echo $row['product_id']; ?>, '<?php echo $row['name']; ?>', '<?php echo $row['description']; ?>', <?php echo $row['price']; ?>, '<?php echo $row['category']; ?>', '<?php echo $row['availability']; ?>')">Edit</button>
                        <a href="manage_products.php?delete=<?php echo $row['product_id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function showAddForm() {
            document.getElementById('formTitle').textContent = 'Add New Product';
            document.getElementById('productForm').style.display = 'block';
            document.getElementById('product_id').value = '';
            document.getElementById('name').value = '';
            document.getElementById('description').value = '';
            document.getElementById('price').value = '';
            document.getElementById('category').value = 'Birthday Cakes';
            document.getElementById('availability').value = 'In Stock';
        }

        function hideForm() {
            document.getElementById('productForm').style.display = 'none';
        }

        function editProduct(id, name, description, price, category, availability) {
            document.getElementById('formTitle').textContent = 'Edit Product';
            document.getElementById('productForm').style.display = 'block';
            document.getElementById('product_id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('description').value = description;
            document.getElementById('price').value = price;
            document.getElementById('category').value = category;
            document.getElementById('availability').value = availability;
        }
    </script>
</body>
</html> 