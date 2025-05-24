<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Details - Bakery Admin Dashboard</title>
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
      <header class="order-details-header">
        <div class="order-details-header-content">
          <div class="order-details-title">
            <a href="orders.php"><i class="fas fa-arrow-left"></i></a>
            <h1 id="orderIdTitle">Order #<span id="orderId">...</span></h1>
          </div>
          <div>
            <button class="edit-button edit-button-edit" id="editButton">
              <i class="fas fa-edit"></i>
              Edit Order
            </button>
          </div>
        </div>
      </header>

      <div class="order-details-content">
        <div class="order-grid">
          <!-- Left Column -->
          <div>
            <!-- Order Information -->
            <div class="order-card">
              <div class="order-card-header">
                <h2 class="order-card-title">Order Information</h2>
                <button class="update-status-button" id="updateStatusButton" style="display: none;">
                  Update Status
                </button>
              </div>
              <div class="order-card-content">
                <div class="order-info-grid">
                  <div>
                    <p class="order-info-label">Order ID</p>
                    <p class="order-info-value" id="orderIdValue">...</p>
                  </div>
                  <div>
                    <p class="order-info-label">Order Date</p>
                    <p class="order-info-value" id="orderDate">...</p>
                  </div>
                  <div>
                    <p class="order-info-label">Status</p>
                    <div id="statusContainer">
                      <span class="status-badge" id="orderStatus">...</span>
                    </div>
                  </div>
                  <div>
                    <p class="order-info-label">Payment Method</p>
                    <p class="order-info-value" id="paymentMethod">...</p>
                  </div>
                  <div>
                    <p class="order-info-label">Delivery Date</p>
                    <p class="order-info-value" id="deliveryDate">...</p>
                  </div>
                  <div>
                    <p class="order-info-label">Delivery Method</p>
                    <p class="order-info-value" id="deliveryMethod">...</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Custom Cake Details -->
            <div class="order-card" id="customCakeDetails" style="display:none;">
              <div class="order-card-header">
                <h2 class="order-card-title">Custom Cake Details</h2>
              </div>
              <div class="order-card-content" id="customCakeContent">
                <!-- Populated by JS if custom cake -->
              </div>
            </div>

            <!-- Order Items -->
            <div class="order-card">
              <div class="order-card-header">
                <h2 class="order-card-title">Order Items</h2>
              </div>
              <div class="table-container">
                <table>
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th>Price</th>
                      <th>Quantity</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody id="orderItemsBody">
                    <tr><td colspan="4">Loading...</td></tr>
                  </tbody>
                  <tfoot id="orderItemsFooter">
                  </tfoot>
                </table>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div>
            <!-- Customer Information -->
            <div class="order-card">
              <div class="order-card-header">
                <h2 class="order-card-title">Customer Information</h2>
              </div>
              <div class="order-card-content">
                <div class="customer-header">
                  <div class="customer-avatar">
                    <i class="fas fa-user"></i>
                  </div>
                  <div>
                    <p class="customer-name" id="customerName">...</p>
                    <p class="customer-since" id="customerSince">...</p>
                  </div>
                </div>
                <div class="customer-info">
                  <div class="customer-info-item">
                    <i class="fas fa-phone customer-info-icon"></i>
                    <span class="customer-info-text" id="customerPhone">...</span>
                  </div>
                  <div class="customer-info-item">
                    <i class="fas fa-envelope customer-info-icon"></i>
                    <span class="customer-info-text" id="customerEmail">...</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Shipping Address -->
            <div class="order-card">
              <div class="order-card-header">
                <h2 class="order-card-title">Shipping Address</h2>
              </div>
              <div class="order-card-content">
                <div class="customer-info-item">
                  <i class="fas fa-map-marker-alt customer-info-icon"></i>
                  <div class="shipping-address-container">
                    <p class="customer-name" id="shippingName">...</p>
                    <p class="customer-info-text" id="shippingAddress">...</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Payment Information -->
            <div class="order-card">
              <div class="order-card-header">
                <h2 class="order-card-title">Payment Information</h2>
              </div>
              <div class="order-card-content">
                <div class="customer-info-item">
                  <i class="fas fa-credit-card customer-info-icon"></i>
                  <div>
                    <p class="customer-name" id="paymentType">...</p>
                    <p class="customer-info-text" id="paymentTransaction">...</p>
                    <p class="customer-info-text" id="paymentDate">...</p>
                    <p class="customer-info-text" id="paymentStatus">...</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Delivery Information -->
            <div class="order-card">
              <div class="order-card-header">
                <h2 class="order-card-title">Delivery Information</h2>
              </div>
              <div class="order-card-content">
                <div class="customer-info-item">
                  <i class="fas fa-truck customer-info-icon"></i>
                  <div>
                    <p class="customer-name" id="deliveryType">...</p>
                    <p class="customer-info-text" id="deliveryCarrier">...</p>
                  </div>
                </div>
                <div class="customer-info-item" style="margin-top: 1rem;">
                  <i class="fas fa-calendar-alt customer-info-icon"></i>
                  <div>
                    <p class="customer-name">Delivery Date</p>
                    <p class="customer-info-text" id="deliveryDateInfo">...</p>
                    <p class="customer-info-text" id="deliveryTime">...</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal Overlay (single, shared) -->
  <div class="modal-overlay" id="modalOverlay">
    <!-- Status Update Modal -->
    <div class="modal" id="statusModal">
      <div class="modal-container">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Update Order</h3>
            <button class="close-modal" id="closeStatusModal">&times;</button>
          </div>
          <div class="modal-body">
            <form id="orderUpdateForm">
              <!-- Status Update Section -->
              <div class="form-group">
                <label for="status">Order Status</label>
                <select id="status" name="status" required>
                  <option value="pending">Pending</option>
                  <option value="processing">Processing</option>
                  <option value="shipped">Shipped</option>
                  <option value="delivered">Delivered</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
              <!-- Payment Update Section -->
              <div class="form-group">
                <label for="paymentStatus">Payment Status</label>
                <select id="paymentStatus" name="paymentStatus" required>
                  <option value="Pending">Pending</option>
                  <option value="Paid">Paid</option>
                  <option value="Failed">Failed</option>
                  <option value="Refunded">Refunded</option>
                </select>
              </div>
              <div class="form-group">
                <label for="message">Message to Customer (Optional)</label>
                <textarea id="message" name="message" rows="3" placeholder="Add a message to notify the customer about this update..."></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button class="modal-button modal-button-secondary" id="cancelStatusUpdate">Cancel</button>
            <button class="modal-button modal-button-primary" id="updateStatusConfirm">Update Order</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="order-details.js"></script>
</body>
</html>
