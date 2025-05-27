<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
    header("location: login.php");
    exit;
}

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total number of records
$total_sql = "SELECT COUNT(*) as count FROM products";
$total_result = mysqli_query($conn, $total_sql);
$total_records = mysqli_fetch_assoc($total_result)['count'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch products with pagination
$sql = "SELECT * FROM products ORDER BY created_at ASC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $records_per_page, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
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
              <option value="Birthday Cakes">Birthday Cakes</option>
              <option value="Wedding Cakes">Wedding Cakes</option>
              <option value="Shower Cakes">Shower Cakes</option>
              <option value="Cupcakes">Cupcakes</option>
              <option value="Breads">Breads</option>
              <option value="Celebration">Celebration</option>
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
                        $availabilityClass = '';
                        $availabilityText = '';
                        $availabilityLower = strtolower(trim($availability));
                        
                        if (strpos($availabilityLower, 'low') !== false) {
                            $availabilityClass = 'status-low-stock';
                            $availabilityText = 'Low Stock';
                        } elseif (strpos($availabilityLower, 'out') !== false) {
                            $availabilityClass = 'status-out-of-stock';
                            $availabilityText = 'Out of Stock';
                        } else {
                            $availabilityClass = 'status-in-stock';
                            $availabilityText = 'In Stock';
                        }
                        
                        echo "<tr data-product-id='$productId'>";
                        echo "<td><input type='checkbox' class='product-checkbox' value='$productId'></td>";
                        echo "<td class='product-id'>PRD-" . str_pad($productId, 3, '0', STR_PAD_LEFT) . "</td>";
                        echo "<td class='product-image'><img src='$image' alt='$name' width='50'></td>";
                        echo "<td>$name</td>";
                        echo "<td>$category</td>";
                        echo "<td>₱$price</td>";
                        echo "<td><span class='status-badge $availabilityClass'>$availabilityText</span></td>";
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
              <button class="pagination-button pagination-button-prev" <?php if($page <= 1) echo 'disabled'; ?> onclick="window.location.href='?page=<?php echo $page-1; ?>'">
                <i class="fas fa-chevron-left"></i>
                Previous
              </button>
              <button class="pagination-button pagination-button-next" <?php if($page >= $total_pages) echo 'disabled'; ?> onclick="window.location.href='?page=<?php echo $page+1; ?>'">
                Next
                <i class="fas fa-chevron-right"></i>
              </button>
            </div>
            <div class="pagination-desktop">
              <div class="pagination-info">
                Showing <span><?php echo min(($page-1) * $records_per_page + 1, $total_records); ?></span> to 
                <span><?php echo min($page * $records_per_page, $total_records); ?></span> of 
                <span><?php echo $total_records; ?></span> products
              </div>
              <div class="pagination-nav">
                <button class="pagination-button pagination-button-prev" <?php if($page <= 1) echo 'disabled'; ?> onclick="window.location.href='?page=<?php echo $page-1; ?>'">
                  <i class="fas fa-chevron-left"></i>
                </button>
                <?php
                for($i = 1; $i <= $total_pages; $i++) {
                    if($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)) {
                        echo "<button class='pagination-button pagination-button-page".($i == $page ? " active" : "")."' onclick='window.location.href=\"?page=$i\"'>$i</button>";
                    } elseif($i == $page - 3 || $i == $page + 3) {
                        echo "<button class='pagination-button pagination-button-page'>...</button>";
                    }
                }
                ?>
                <button class="pagination-button pagination-button-next" <?php if($page >= $total_pages) echo 'disabled'; ?> onclick="window.location.href='?page=<?php echo $page+1; ?>'">
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
                </div>
                <div class="modal-body">
                    <form id="addProductForm">
                      <div class="form-group">
                        <label for="productName">Product Name</label>
                        <input type="text" id="productName" name="name" required>
                      </div>
                      <div class="form-group">
                        <label for="productCategory">Category</label>
                        <select id="productCategory" class="form-select" name="category" required>
                          <option value="Birthday Cakes">Birthday Cakes</option>
                          <option value="Wedding Cakes">Wedding Cakes</option>
                          <option value="Shower Cakes">Shower Cakes</option>
                          <option value="Cupcakes">Cupcakes</option>
                          <option value="Breads">Breads</option>
                          <option value="Celebration">Celebration</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="product-price" class="form-label">Price</label>
                          <div style="position: relative;">
                            <div style="position: absolute; top: 0; bottom: 0; left: 0; display: flex; align-items: center; padding-left: 0.75rem;">
                              <span style="color: var(--gray-500);">₱</spa>
                            </div>
                              <input type="text" id="product-price" class="form-textarea" style="min-height: auto; padding-left: 1.75rem;" placeholder="0.00">
                          </div>
                      </div>
                      <div class="form-group">
                        <label for="product-status" class="form-label">Status</label>
                          <select id="product-status" class="form-select">
                            <option>In Stock (>30)</option>
                            <option>Low Stock (<5)</option>
                            <option>Out of Stock (<1)</option>
                          </select>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Product Image</label>
                          <div style="border: 2px dashed var(--gray-300); border-radius: 0.375rem; padding: 1.5rem; text-align: center; cursor: pointer;" id="imageUploadArea">
                            <div id="uploadPlaceholder">
                              <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: var(--gray-400);"></i>
                              <p style="margin-top: 0.5rem; font-size: 0.875rem; color: var(--gray-600);">
                                <span style="color: var(--pink-600); font-weight: 500;">Click to upload</span> or drag and drop
                              </p>
                              <p style="font-size: 0.75rem; color: var(--gray-500);">PNG, JPG up to 10MB</p>
                            </div>
                            <input type="file" id="productImage" name="productImage" accept="image/*" style="display: none;">
                            <div id="imagePreview" style="margin-top: 1rem; display: none;">
                              <img id="previewImage" src="#" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 0.25rem; cursor: default;">
                            </div>
                          </div>
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
                </div>
                <div class="modal-body">
                    <form id="editProductForm" enctype="multipart/form-data">
                      <input type="hidden" id="editProductId" name="product_id">
                      <div class="form-group">
                        <label for="editProductName">Product Name</label>
                        <input type="text" id="editProductName" name="name" required>
                      </div>
                      <div class="form-group">
                        <label for="editProductCategory">Category</label>
                        <select id="editProductCategory" class="form-select" name="category" required>
                          <option value="Birthday Cakes">Birthday Cakes</option>
                          <option value="Wedding Cakes">Wedding Cakes</option>
                          <option value="Shower Cakes">Shower Cakes</option>
                          <option value="Cupcakes">Cupcakes</option>
                          <option value="Breads">Breads</option>
                          <option value="Celebration">Celebration</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="editProductPrice" class="form-label">Price</label>
                          <div style="position: relative;">
                            <div style="position: absolute; top: 0; bottom: 0; left: 0; display: flex; align-items: center; padding-left: 0.75rem;">
                              <span style="color: var(--gray-500);">₱</span>
                            </div>
                              <input type="text" id="editProductPrice" name="price" class="form-textarea" style="min-height: auto; padding-left: 1.75rem;" placeholder="0.00" required>
                          </div>
                      </div>
                      <div class="form-group">
                        <label for="editProductStatus" class="form-label">Status</label>
                          <select id="editProductStatus" name="availability" class="form-select" required>
                            <option value="In Stock">In Stock(>30)</option>
                            <option value="Low Stock">Low Stock(<5)</option>
                            <option value="Out of Stock">Out of Stock(<1)</option>
                          </select>
                      </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-button modal-button-secondary" id="cancelEditProduct">Cancel</button>
                    <button type="submit" class="modal-button modal-button-primary" id="saveEditProduct">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
  <script src="products.js"></script>
</body>
</html>
