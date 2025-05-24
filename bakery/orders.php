<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
    header("location: login.php");
    exit;
}

// Fetch all orders with user information and delivery details
$sql = "SELECT o.*, 
        CONCAT(l.Fname, ' ', l.Lname) as customer_name,
        l.email as customer_email,
        c.phone,
        c.address as customer_address,
        c.payment as preferred_payment,
        GROUP_CONCAT(p.name SEPARATOR ', ') as products,
        GROUP_CONCAT(oi.quantity SEPARATOR ', ') as quantities,
        GROUP_CONCAT(oi.price SEPARATOR ', ') as item_prices
        FROM orders o 
        LEFT JOIN login l ON o.user_id = l.user_id 
        LEFT JOIN customerinfo c ON o.user_id = c.user_id
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.product_id
        GROUP BY o.order_id
        ORDER BY o.created_at ASC";
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if (!$result) {
    // If there's an error, create a simple query that will work
    $sql = "SELECT * FROM orders ORDER BY created_at ASC";
    $result = mysqli_query($conn, $sql);
    
    // If that also fails, create an empty result set
    if (!$result) {
        $result = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders - Bakery Admin Dashboard</title>
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
                <?php 
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $orderId = isset($row['order_id']) ? $row['order_id'] : 'N/A';
                        $customerName = isset($row['customer_name']) ? $row['customer_name'] : 'Guest';
                        $products = isset($row['products']) ? $row['products'] : 'N/A';
                        $orderDate = isset($row['created_at']) ? date('M j, Y', strtotime($row['created_at'])) : 'N/A';
                        $deliveryDate = isset($row['delivery_date']) ? date('M j, Y', strtotime($row['delivery_date'])) : 'N/A';
                        $status = isset($row['status']) ? $row['status'] : 'Pending';
                        $paymentMethod = isset($row['payment_method']) ? ucfirst(strtolower($row['payment_method'])) : 'N/A';
                        $total = isset($row['total_amount']) ? number_format($row['total_amount'], 2) : '0.00';
                        $deliveryAddress = isset($row['delivery_address']) ? $row['delivery_address'] : 'N/A';
                        
                        // Get status badge class
                        $statusClass = '';
                        switch ($status) {
                            case 'Completed':
                                $statusClass = 'status-completed';
                                break;
                            case 'In Progress':
                                $statusClass = 'status-in-progress';
                                break;
                            case 'Pending':
                                $statusClass = 'status-pending';
                                break;
                            case 'Cancelled':
                                $statusClass = 'status-cancelled';
                                break;
                            default:
                                $statusClass = 'status-pending';
                                break;
                        }
                        
                        echo "<tr data-order-id='$orderId'>";
                        echo "<td><input type='checkbox' class='order-checkbox' value='$orderId'></td>";
                        echo "<td class='order-id'>ORD-" . str_pad($orderId, 3, '0', STR_PAD_LEFT) . "</td>";
                        echo "<td>$customerName</td>";
                        echo "<td>$products</td>";
                        echo "<td>$orderDate</td>";
                        echo "<td>$deliveryDate</td>";
                        echo "<td><span class='status-badge $statusClass'>$status</span></td>";
                        echo "<td>$paymentMethod</td>";
                        echo "<td>₱$total</td>";
                        echo "<td>
                                <div class='action-buttons'>
                                    <a class='action-button edit-button' title='Edit Order' href='order-details.php?id=$orderId'>
                                        <i class='fas fa-pen'></i>
                                    </a>
                                    <button class='action-button archive-button' title='Archive Order' data-order-id='$orderId'>
                                        <i class='fas fa-archive'></i>
                                    </button>
                                </div>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' class='no-orders'>No orders found</td></tr>";
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

  <!-- Order Details Modal -->
  <div class="modal" id="orderDetailsModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Order Details</h2>
        <button class="close-modal" id="closeDetailsModal">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <div class="order-details-section">
          <h3>Delivery Information</h3>
          <p><strong>Address:</strong> <span id="modalDeliveryAddress"></span></p>
          <p><strong>Contact:</strong> <span id="modalCustomerPhone"></span></p>
        </div>
        <div class="order-details-section">
          <h3>Order Items</h3>
          <div id="modalOrderItems"></div>
        </div>
        <div class="form-actions">
          <button type="button" class="cancel-button" id="closeDetailsBtn">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Status Update Modal -->
  <div class="modal" id="statusUpdateModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Update Order Status</h2>
        <button class="close-modal" id="closeStatusModal">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <form id="statusUpdateForm">
          <input type="hidden" id="updateOrderId">
          <div class="form-group">
            <label for="newStatus">Order Status</label>
            <select id="newStatus" required>
              <option value="Pending">Pending</option>
              <option value="In Progress">In Progress</option>
              <option value="Completed">Completed</option>
              <option value="Cancelled">Cancelled</option>
            </select>
          </div>
          <div class="form-group">
            <label for="newPaymentMethod">Payment Method</label>
            <select id="newPaymentMethod" required>
              <option value="Card">Card</option>
              <option value="Gcash">GCash</option>
              <option value="Cash">Cash</option>
            </select>
          </div>
          <div class="form-actions">
            <button type="button" class="cancel-button" id="cancelStatusUpdate">Cancel</button>
            <button type="submit" class="submit-button">Update Status</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="orders.js"></script>
</body>
</html>
