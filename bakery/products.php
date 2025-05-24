<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
    header("location: login.php");
    exit;
}

// Fetch all products
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products - Bakery Admin Dashboard</title>
  <link rel="stylesheet" href="adminstyles.css">
  <link rel="stylesheet" href="adminstyles2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <img src="Logo.png" alt="Triple J & Rose's Bakery" width="50" height="50">
        <div>
          <h1>Triple J & Rose's</h1>
          <p>Admin Dashboard</p>
        </div>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li>
            <a href="dashbrd.php">
              <i class="fas fa-home"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="orders.php">
              <i class="fas fa-shopping-bag"></i>
              <span>Orders</span>
            </a>
          </li>
          <li>
            <a href="products.php" class="active">
              <i class="fas fa-birthday-cake"></i>
              <span>Products</span>
            </a>
          </li>
          <li>
            <a href="accounts.php">
              <i class="fas fa-users"></i>
              <span>Accounts</span>
            </a>
          </li>
          <li>
            <a href="inquiries.php">
              <i class="fas fa-comment-dots"></i>
              <span>Inquiries</span>
            </a>
          </li>
          <li>
            <a href="sales.php" id="salesSidebarLink">
              <i class="fas fa-chart-line"></i>
              Sales
            </a>
          </li>
        </ul>
      </nav>

    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header class="page-header">
        <div class="page-header-content">
          <div class="page-title">
            <h1>Products</h1>
          </div>
          <div>
            <button class="edit-button edit-button-edit" id="addProductButton">
              <i class="fas fa-plus"></i>
              Add Product
            </button>
          </div>
        </div>
      </header>
      

      <div class="page-content">
        <!-- Bulk Actions  -->
        <div class="bulk-actions" id="bulkActions" style="display: none;">
          <div class="bulk-actions-count">
            <span id="selectedCount">0</span> products selected
          </div>
          <div class="bulk-actions-buttons">
            <button class="bulk-button bulk-button-complete">
              <i class="fas fa-check"></i>
              Mark as In Stock
            </button>
            <button class="bulk-button bulk-button-cancel">
              <i class="fas fa-box-archive"></i>
              Archive Products
            </button>
            <button class="bulk-button bulk-button-clear" id="clearSelection">
              Clear Selection
            </button>
          </div>
        </div>

        <!-- Filters -->
        <div class="filters">
          <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search products..." id="searchInput">
          </div>
          <div class="filter-buttons">
            <select class="filter-select" id="categoryFilter">
              <option value="">All Categories</option>
              <option value="Wedding Cakes">Wedding Cakes</option>
              <option value="Birthday Cakes">Birthday Cakes</option>
              <option value="Shower Cakes">Shower Cakes</option>
              <option value="Cupcakes">Cupcakes</option>
              <option value="Celebration">Celebration</option>
              <option value="Breads">Breads</option>
            </select>
            <select class="filter-select" id="availabilityFilter">
              <option value="">All Status</option>
              <option value="In Stock">In Stock</option>
              <option value="Out of Stock">Out of Stock</option>
            </select>
            <select class="filter-select" id="sortBy">
              <option>Sort By: Newest</option>
              <option>Sort By: Oldest</option>
              <option>Sort By: Price (Low to High)</option>
              <option>Sort By: Price (High to Low)</option>
              <option>Sort By: Name (A-Z)</option>
              <option>Sort By: Name (Z-A)</option>
            </select>
          </div>
        </div>

        <!-- Products Table -->
        <div class="order-card">
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>
                    <input type="checkbox" id="selectAll">
                  </th>
                  <th>PRODUCT ID</th>
                  <th>IMAGE</th>
                  <th>NAME</th>
                  <th>CATEGORY</th>
                  <th>PRICE</th>
                  <th>AVAILABILITY</th>
                  <th>ACTIONS</th>
                </tr>
              </thead>
              <tbody id="productsTableBody">
                <?php 
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $productId = $row['product_id'];
                        $name = $row['name'];
                        $category = $row['category'];
                        $price = number_format($row['price'], 2);
                        $image = $row['image'];
                        $availability = $row['availability'];
                        
                        // Get availability badge class
                        $availabilityClass = $availability === 'In Stock' ? 'status-completed' : 'status-cancelled';
                        
                        echo "<tr data-product-id='$productId'>";
                        echo "<td><input type='checkbox' class='product-checkbox' value='$productId'></td>";
                        echo "<td class='product-id'>PRD-" . str_pad($productId, 3, '0', STR_PAD_LEFT) . "</td>";
                        echo "<td class='product-image'><img src='$image' alt='$name' width='50'></td>";
                        echo "<td>$name</td>";
                        echo "<td>$category</td>";
                        echo "<td>₱$price</td>";
                        echo "<td><span class='status-badge $availabilityClass'>$availability</span></td>";
                        echo "<td>
                                <div class='action-buttons'>
                                    <button class='action-button edit-button' data-product-id='$productId' title='Edit Product'>
                                        <i class='fas fa-pen'></i>
                                    </button>
                                    <button class='action-button archive-button' data-product-id='$productId' title='Archive Product'>
                                        <i class='fas fa-box-archive'></i>
                                    </button>
                                </div>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='no-products'>No products found</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="pagination">
            <div class="pagination-mobile">
              <button class="pagination-button pagination-button-prev" id="prevPageMobile" disabled>
                Previous
              </button>
              <button class="pagination-button pagination-button-next" id="nextPageMobile">
                Next
              </button>
            </div>
            <div class="pagination-desktop">
              <div class="pagination-info">
                Showing <span id="startIndex">1</span> to <span id="endIndex">8</span> of <span id="totalItems">12</span> results
              </div>
              <div class="pagination-nav" id="paginationNav">
                <button class="pagination-button pagination-button-prev" id="prevPage" disabled>
                  <i class="fas fa-chevron-left"></i>
                </button>
                <!-- Page buttons will be added by JavaScript -->
                <button class="pagination-button pagination-button-next" id="nextPage">
                  <i class="fas fa-chevron-right"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal Overlay (single, shared) -->
  <div class="modal-overlay" id="modalOverlay" style="display: none;">
    <div class="modal" id="productModal">
        <div class="modal-container">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Add New Product</h3>
                    <button type="button" class="close-modal" id="closeProductModal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm">
                      <div class="form-group">
                        <label for="productName">Product Name</label>
                        <input type="text" id="productName" name="name" required>
                      </div>
                      <div class="form-group">
                        <label for="productCategory">Category</label>
                        <select id="productCategory" name="category" required>
                          <option value="cakes">Cakes</option>
                          <option value="breads">Breads</option>
                          <option value="pastries">Pastries</option>
                          <option value="cookies">Cookies</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="productPrice">Price</label>
                        <input type="number" id="productPrice" name="price" step="0.01" required>
                      </div>
                      <div class="form-group">
                        <label for="productImage">Product Image</label>
                        <input type="file" id="productImage" name="image" accept="image/*">
                      </div>
                      <div class="form-group">
                        <label for="productDescription">Description</label>
                        <textarea id="productDescription" name="description" rows="3"></textarea>
                      </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-button modal-button-secondary" id="cancelProduct">Cancel</button>
                    <button type="submit" class="modal-button modal-button-primary" id="saveProduct">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal" id="editProductModal">
        <div class="modal-container">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Edit Product</h3>
                    <button class="close-modal" id="closeEditProduct">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                      <input type="hidden" id="editProductId">
                      <div class="form-group">
                        <label for="editProductName">Product Name</label>
                        <input type="text" id="editProductName" name="name" required>
                      </div>
                      <div class="form-group">
                        <label for="editProductCategory">Category</label>
                        <select id="editProductCategory" name="category" required>
                          <option value="cakes">Cakes</option>
                          <option value="breads">Breads</option>
                          <option value="pastries">Pastries</option>
                          <option value="cookies">Cookies</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="editProductPrice">Price</label>
                        <input type="number" id="editProductPrice" name="price" step="0.01" required>
                      </div>
                      <div class="form-group">
                        <label for="editProductImage">Product Image</label>
                        <input type="file" id="editProductImage" name="image" accept="image/*">
                      </div>
                      <div class="form-group">
                        <label for="editProductDescription">Description</label>
                        <textarea id="editProductDescription" name="description" rows="3"></textarea>
                      </div>
                      <div class="form-group">
                        <label for="editProductStatus">Status</label>
                        <select id="editProductStatus" name="status" required>
                          <option value="active">Active</option>
                          <option value="inactive">Inactive</option>
                        </select>
                      </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="modal-button modal-button-secondary" id="cancelEditProduct">Cancel</button>
                    <button class="modal-button modal-button-primary" id="saveEditProduct">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
  </div>

  <script src="products.js"></script>
</body>
</html>
