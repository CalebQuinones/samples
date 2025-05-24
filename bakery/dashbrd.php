<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
    header("location: login.php");
    exit;
}

// Fetch total orders count
$sql = "SELECT COUNT(*) as total_orders FROM orders";
$result = mysqli_query($conn, $sql);
$totalOrders = mysqli_fetch_assoc($result)['total_orders'];

// Fetch custom orders count (orders with custom products)
$sql = "SELECT COUNT(DISTINCT o.order_id) as custom_orders 
        FROM orders o 
        JOIN order_items oi ON o.order_id = oi.order_id 
        JOIN products p ON oi.product_id = p.product_id 
        WHERE p.category = 'Custom'";
$result = mysqli_query($conn, $sql);
$customOrders = mysqli_fetch_assoc($result)['custom_orders'];

// Fetch total customers count
$sql = "SELECT COUNT(*) as total_customers FROM login WHERE role = 'user'";
$result = mysqli_query($conn, $sql);
$totalCustomers = mysqli_fetch_assoc($result)['total_customers'];

// Fetch new inquiries count (last 7 days)
$sql = "SELECT COUNT(*) as new_inquiries FROM inquiry WHERE dateSubmitted >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$result = mysqli_query($conn, $sql);
$newInquiries = mysqli_fetch_assoc($result)['new_inquiries'];

// Fetch recent orders
$sql = "SELECT o.*, 
        CONCAT(l.Fname, ' ', l.Lname) as customer_name,
        SUM(oi.quantity * oi.price) as total_amount
        FROM orders o 
        LEFT JOIN login l ON o.user_id = l.user_id 
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        GROUP BY o.order_id
        ORDER BY o.created_at DESC
        LIMIT 5";
$recentOrders = mysqli_query($conn, $sql);

// Fetch recent inquiries
$sql = "SELECT i.*, 
        CONCAT(i.Fname, ' ', i.Lname) as customer_name
        FROM inquiry i 
        ORDER BY i.dateSubmitted DESC 
        LIMIT 4";
$recentInquiries = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bakery Admin Dashboard</title>
  <link rel="stylesheet" href="adminstyles.css">
  <link rel="stylesheet" href="notification-styles.css">
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
            <a href="dashbrd.php" class="active">
              <i class="fas fa-home"></i>
              Dashboard
            </a>
          </li>
          <li>
            <a href="orders.php">
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
      <header class="header">
        <div class="header-content">
          <div class="header-title">
            <h1>Dashboard</h1>
          </div>
          <div class="header-actions">
            <div class="notifications">
              <button class="notifications-button" id="notificationsButton">
                <i class="fas fa-bell"></i>
                <span class="notifications-badge">3</span>
              </button>
              <div class="notifications-dropdown" id="notificationsDropdown">
                <div class="notifications-header">
                  <h3>Notifications</h3>
                </div>
                <div class="notifications-list">
                  <div class="notification-item">
                    <p class="notification-title">New Order</p>
                    <p class="notification-message">Sarah Johnson placed a new order for a Birthday Cake</p>
                    <p class="notification-time">10 minutes ago</p>
                  </div>
                  <div class="notification-item">
                    <p class="notification-title">Order Status Update</p>
                    <p class="notification-message">Order #ORD-002 has been updated to 'In Progress'</p>
                    <p class="notification-time">1 hour ago</p>
                  </div>
                  <div class="notification-item">
                    <p class="notification-title">New Inquiry</p>
                    <p class="notification-message">Jennifer Smith sent an inquiry about a Wedding Cake</p>
                    <p class="notification-time">2 hours ago</p>
                  </div>
                </div>
                <div class="notifications-footer">
                  <button>Mark all as read</button>
                </div>
              </div>
            </div>
            <div class="user-profile">
              <button id="profileButton" class="profile-button">
                <img src="placeholder.svg" alt="Admin">
                <span class="md:inline hidden">Admin User</span>
                <i class="fas fa-chevron-down"></i>
              </button>
              <div class="profile-dropdown" id="profileDropdown">
                <ul>
                  <li><a href="#" id="logoutButton"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </header>

      <div class="content">
        <!-- Stats Cards -->
        <div class="stats-grid">
          <div class="stats-card">
            <div class="stats-card-content">
              <div class="stats-card-icon">
                <i class="fas fa-shopping-bag"></i>
              </div>
              <div>
                <p class="stats-card-label">Total Orders</p>
                <p class="stats-card-value"><?php echo $totalOrders; ?></p>
              </div>
            </div>
          </div>
          <div class="stats-card">
            <div class="stats-card-content">
              <div class="stats-card-icon">
                <i class="fas fa-birthday-cake"></i>
              </div>
              <div>
                <p class="stats-card-label">Custom Orders</p>
                <p class="stats-card-value"><?php echo $customOrders; ?></p>
              </div>
            </div>
          </div>
          <div class="stats-card">
            <div class="stats-card-content">
              <div class="stats-card-icon">
                <i class="fas fa-users"></i>
              </div>
              <div>
                <p class="stats-card-label">Total Customers</p>
                <p class="stats-card-value"><?php echo $totalCustomers; ?></p>
              </div>
            </div>
          </div>
          <div class="stats-card">
            <div class="stats-card-content">
              <div class="stats-card-icon">
                <i class="fas fa-comment-dots"></i>
              </div>
              <div>
                <p class="stats-card-label">New Inquiries</p>
                <p class="stats-card-value"><?php echo $newInquiries; ?></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Calendar -->
        <div class="calendar-card">
          <div class="calendar-header">
            <h2 class="calendar-title">Order Calendar</h2>
            <div class="calendar-nav">
              <button class="calendar-nav-button" id="prevMonth">
                <i class="fas fa-chevron-left"></i>
              </button>
              <h2 class="calendar-month" id="currentMonth">April 2025</h2>
              <button class="calendar-nav-button" id="nextMonth">
                <i class="fas fa-chevron-right"></i>
              </button>
            </div>
          </div>

          <div class="calendar-legend">
            <div class="legend-item">
              <span class="legend-color legend-completed"></span>
              <span class="legend-label">Completed</span>
            </div>
            <div class="legend-item">
              <span class="legend-color legend-in-progress"></span>
              <span class="legend-label">In Progress</span>
            </div>
            <div class="legend-item">
              <span class="legend-color legend-pending"></span>
              <span class="legend-label">Pending</span>
            </div>
            <div class="legend-item">
              <span class="legend-color legend-cancelled"></span>
              <span class="legend-label">Cancelled</span>
            </div>
          </div>

          <div class="calendar-days">
            <div class="calendar-day-name">Sun</div>
            <div class="calendar-day-name">Mon</div>
            <div class="calendar-day-name">Tue</div>
            <div class="calendar-day-name">Wed</div>
            <div class="calendar-day-name">Thu</div>
            <div class="calendar-day-name">Fri</div>
            <div class="calendar-day-name">Sat</div>
          </div>

          <div class="calendar-grid" id="calendarGrid">
            <!-- Calendar cells will be generated by JavaScript -->
          </div>
        </div>

        <div class="dashboard-grid">
          <!-- Recent Orders -->
          <div class="recent-orders">
            <div class="recent-orders-header">
              <h2 class="recent-orders-title">Recent Orders</h2>
              <a href="orders.php" class="view-all">View All</a>
            </div>
            <div class="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while($row = mysqli_fetch_assoc($recentOrders)): ?>
                  <tr>
                    <td class="order-id">ORD-<?php echo str_pad($row['order_id'], 3, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><span class="status-badge status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
                    <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Recent Inquiries -->
          <div class="recent-inquiries">
            <div class="recent-orders-header">
              <h2 class="recent-orders-title">Recent Inquiries</h2>
              <a href="inquiries.php" class="view-all">View All</a>
            </div>
            <div class="inquiries-list">
              <?php while($row = mysqli_fetch_assoc($recentInquiries)): ?>
              <div class="inquiry-item">
                <div class="inquiry-header">
                  <div>
                    <p class="inquiry-title" style="font-weight:bold;"><?php echo $row['subject']; ?></p>
                    <p class="inquiry-customer">From: <?php echo $row['customer_name']; ?></p>
                  </div>
                  <span class="inquiry-time"><?php echo $row['dateSubmitted']; ?></span>
                </div>
                <p class="inquiry-preview" style="color:#555; margin:0;">
                  <?php
                    $preview = mb_strimwidth($row['msg'], 0, 50, '...');
                    echo htmlspecialchars($preview);
                  ?>
                </p>
              </div>
              <?php endwhile; ?>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Side Popup Container for Notifications -->
  <div id="sidePopupContainer"></div>

  <script src="dasbrd.js"></script>
</body>
</html>
