<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders - Bakery Admin Dashboard</title>
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
              Dashboard
            </a>
          </li>
          <li>
            <a href="orders.php" class="active">
              <i class="fas fa-shopping-bag"></i>
              Orders
            </a>
          </li>
          <li>
            <a href="products.php">
              <i class="fas fa-birthday-cake"></i>
              Products
            </a>
          </li>
          <li>
            <a href="accounts.php">
              <i class="fas fa-users"></i>
              Accounts
            </a>
          </li>
          <li>
            <a href="inquiries.php">
              <i class="fas fa-comment-dots"></i>
              Inquiries
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
            <h1>Orders</h1>
          </div>
        </div>
      </header>

      <div class="page-content">
        <div class="bulk-actions" id="bulkActions" style="display: none;">
          <div class="bulk-actions-count">
            <span id="selectedCount">0</span> orders selected
          </div>
          <div class="bulk-actions-buttons">
            <button class="bulk-button bulk-button-complete">
              <i class="fas fa-check"></i>
              Mark as Completed
            </button>
            <button class="bulk-button bulk-button-print">
              Print Labels
            </button>
            <button class="bulk-button bulk-button-cancel">
              <i class="fas fa-times"></i>
              Cancel Orders
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
            <input type="text" class="search-input" placeholder="Search orders..." id="searchInput">
          </div>
          <div class="filter-buttons">
            <select class="filter-select" id="statusFilter">
              <option>All Statuses</option>
              <option>Completed</option>
              <option>In Progress</option>
              <option>Pending</option>
              <option>Cancelled</option>
            </select>
            <select class="filter-select" id="productFilter">
              <option>All Products</option>
              <option>Cakes</option>
              <option>Cupcakes</option>
              <option>Breads</option>
              <option>Custom Orders</option>
            </select>
            <select class="filter-select" id="dateFilter">
              <option>Last 30 Days</option>
              <option>Last 7 Days</option>
              <option>Today</option>
              <option>This Month</option>
              <option>Last Month</option>
              <option>Custom Range</option>
            </select>
            <button class="filter-button">
              <i class="fas fa-filter"></i>
              More Filters
            </button>
          </div>
        </div>

        <!-- Orders Table -->
        <div class="order-card">
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>
                    <input type="checkbox" id="selectAll">
                  </th>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Product</th>
                  <th>Order Date</th>
                  <th>Delivery/Pickup Date</th>
                  <th>Status</th>
                  <th>Payment</th>
                  <th>Total</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="ordersTableBody">
                <!-- Orders will be populated sa JavaScript -->
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

  <script src="orders.js"></script>
</body>
</html>
