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
              <i class="fas fa-trash"></i>
              Delete Products
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
                                    <button class='action-button edit-button' title='Edit Product' data-product-id='$productId'>
                                        <i class='fas fa-pen'></i>
                                    </button>
                                    <button class='action-button delete-button' title='Delete Product' data-product-id='$productId'>
                                        <i class='fas fa-trash'></i>
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
  <div class="modal-overlay" id="modalOverlay"></div>

  <!-- Add Product Modal -->
  <div class="modal-container" id="productModal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="productModalTitle">Add New Product</h3>
        <button class="close-modal" id="closeProductModal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="product-name" class="form-label">Product Name</label>
          <input type="text" id="product-name" class="form-textarea" style="min-height: auto;">
        </div>
        <div class="form-group">
          <label for="product-category" class="form-label">Category</label>
          <select id="product-category" class="form-select">
            <option>Cakes</option>
            <option>Cupcakes</option>
            <option>Breads</option>
            <option>Pastries</option>
          </select>
        </div>
        <div class="form-group">
          <label for="product-price" class="form-label">Price</label>
          <div style="position: relative;">
            <div style="position: absolute; top: 0; bottom: 0; left: 0; display: flex; align-items: center; padding-left: 0.75rem;">
              <span style="color: var(--gray-500);">$</span>
            </div>
            <input type="text" id="product-price" class="form-textarea" style="min-height: auto; padding-left: 1.75rem;" placeholder="0.00">
          </div>
        </div>
        <div class="form-group">
          <label for="product-status" class="form-label">Status</label>
          <select id="product-status" class="form-select">
            <option>In Stock</option>
            <option>Low Stock</option>
            <option>Out of Stock</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Product Image</label>
          <div style="border: 2px dashed var(--gray-300); border-radius: 0.375rem; padding: 1.5rem; text-align: center;">
            <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: var(--gray-400);"></i>
            <p style="margin-top: 0.5rem; font-size: 0.875rem; color: var(--gray-600);">
              <span style="color: var(--pink-600); font-weight: 500; cursor: pointer;">Upload a file</span>
              or drag and drop
            </p>
            <p style="font-size: 0.75rem; color: var(--gray-500);">PNG, JPG, GIF up to 10MB</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="modal-button modal-button-primary" id="saveProduct">
          Add Product
        </button>
        <button class="modal-button modal-button-secondary" id="cancelProduct">
          Cancel
        </button>
      </div>
    </div>
  </div>
  <!-- Product Edit Modal -->
  <div class="modal-container" id="editProductModal" style="display: none;">
    <div class="modal-content">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
        <h3 class="modal-title">Product Details</h3>
        <div id="productInitials" style="width: 48px; height: 48px; border-radius: 50%; background-color: var(--pink-100); color: var(--pink-600); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 500;">PD</div>
        <button class="close-modal" id="closeEditProduct">&times;</button>
      </div>
      <div class="form-group">
        <label for="edit-product-name" class="form-label">Product Name</label>
        <input type="text" id="edit-product-name" class="form-textarea" style="min-height: auto;">
      </div>
      <div class="form-group">
        <label for="edit-product-category" class="form-label">Category</label>
        <select id="edit-product-category" class="form-select">
          <option>Cakes</option>
          <option>Cupcakes</option>
          <option>Breads</option>
          <option>Pastries</option>
        </select>
      </div>
      <div class="form-group">
        <label for="edit-product-price" class="form-label">Price</label>
        <input type="text" id="edit-product-price" class="form-textarea" style="min-height: auto;">
      </div>
      <div class="form-group">
        <label for="edit-product-status" class="form-label">Status</label>
        <select id="edit-product-status" class="form-select">
          <option>In Stock</option>
          <option>Low Stock</option>
          <option>Out of Stock</option>
        </select>
      </div>
      <div class="modal-footer">
        <button class="modal-button modal-button-primary" id="saveEditProduct">Save Changes</button>
        <button class="modal-button modal-button-secondary" id="cancelEditProduct">Cancel</button>
      </div>
    </div>
  </div>

  <script src="products.js"></script>
</body>
</html>
