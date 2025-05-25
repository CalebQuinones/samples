<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
    header("location: login.php");
    exit;
}

// Fetch all accounts from login table
$sql = "SELECT * FROM login ORDER BY created_at ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Accounts - Bakery Admin Dashboard</title>
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
            <a href="products.php">
              <i class="fas fa-birthday-cake"></i>
              <span>Products</span>
            </a>
          </li>
          <li>
            <a href="accounts.php" class="active">
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
            
            <h1>Accounts</h1>
          </div>
          <div>
            <button class="edit-button edit-button-edit" id="addAccountButton">
              <i class="fas fa-user-plus"></i>
              Add Account
            </button>
          </div>
        </div>
      </header>

      <div class="page-content">
        <!-- Bulk Actions (hidden by default) -->
        <div class="bulk-actions" id="bulkActions" style="display: none;">
          <div class="bulk-actions-count">
            <span id="selectedCount">0</span> accounts selected
          </div>
          <div class="bulk-actions-buttons">
            <button class="bulk-button bulk-button-complete">
              <i class="fas fa-check"></i>
              Activate
            </button>
            <button class="bulk-button bulk-button-print">
              Send Email
            </button>
            <button class="bulk-button bulk-button-cancel">
              <i class="fas fa-times"></i>
              Deactivate
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
            <input type="text" class="search-input" placeholder="Search accounts..." id="searchInput">
          </div>
          <div class="filter-buttons">
            <select class="filter-select" id="roleFilter">
              <option value="">All Roles</option>
              <option value="admin">Admin</option>
              <option value="customer">Customer</option>
            </select>
            <select class="filter-select" id="statusFilter">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>

        <!-- Accounts Table -->
        <div class="order-card">
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>
                    <input type="checkbox" id="selectAll" class="account-checkbox">
                  </th>
                  <th>User ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Created At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $userId = $row['user_id'];
                        $fullName = $row['Fname'] . ' ' . $row['Lname'];
                        $email = $row['email'];
                        $role = $row['role'];
                        $status = $row['status'];
                        $createdAt = date('M j, Y', strtotime($row['created_at']));
                        
                        $statusClass = $status === 'active' ? 'status-completed' : 'status-cancelled';
                        $roleClass = $role === 'admin' ? 'status-in-progress' : 'status-pending';
                        
                        echo "<tr>";
                        echo "<td><input type='checkbox' class='account-checkbox' data-id='$userId'></td>";
                        echo "<td>USR-" . str_pad($userId, 3, '0', STR_PAD_LEFT) . "</td>";
                        echo "<td>$fullName</td>";
                        echo "<td>$email</td>";
                        echo "<td><span class='status-badge $roleClass'>" . ucfirst($role) . "</span></td>";
                        echo "<td><span class='status-badge $statusClass'>" . ucfirst($status) . "</span></td>";
                        echo "<td>$createdAt</td>";
                        echo "<td>
                                <div class='action-buttons'>";
                        if ($role !== 'admin') {
                            echo "<button class='action-button view-button' title='View Details' onclick='showCustomerDetails($userId)'>
                                    <i class='fas fa-eye'></i>
                                </button>
                                <button class='action-button edit-button' title='Edit Account' onclick='showEditModal($userId)'>
                                    <i class='fas fa-pen'></i>
                                </button>
                                <button class='action-button archive-button' title='Archive Account' onclick='confirmArchiveAccount($userId)'>
                                    <i class='fas fa-archive'></i>
                                </button>";
                        }
                        echo "</div></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='no-accounts'>No accounts found</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal Overlay (single, shared) -->
  <div class="modal-overlay" id="modalOverlay">
    <!-- Customer Details Modal -->
    <div class="modal" id="customerDetailsModal">
      <div class="modal-container">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Customer Details</h3>
            <button class="close-modal" id="closeCustomerDetails">&times;</button>
          </div>
          <div class="modal-body">
            <div class="customer-info">
              <div class="info-group">
                <label>Phone Number</label>
                <p id="customerPhone">Loading...</p>
              </div>
              <div class="info-group">
                <label>Birthday</label>
                <p id="customerBirthday">Loading...</p>
              </div>
              <div class="info-group">
                <label>Address</label>
                <p id="customerAddress">Loading...</p>
              </div>
              <div class="info-group">
                <label>Payment Method</label>
                <p id="customerPayment">Loading...</p>
              </div>
              <div class="info-group">
                <label>Customer Since</label>
                <p id="customerCreatedAt">Loading...</p>
              </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button class="modal-button" id="cancelModal">Cancel</button>
            <button class="modal-button modal-button-primary" id="saveChanges">Save Changes</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Account Modal -->
    <div class="modal" id="addAccountModal">
      <div class="modal-container">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Add New Account</h3>
            <button class="close-modal" id="closeAccount">&times;</button>
          </div>
          <div class="modal-body">
            <form id="addAccountForm">
              <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName" required>
              </div>
              <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName" required>
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
              </div>
              <div class="form-group">
                  <label for="role" class="form-label">Role</label>
                  <select id="role" class="form-select">
                      <option>Customer</option>
                      <option>Staff</option>
                      <option>Admin</option>
                  </select>
              </div>
              <div class="form-group">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" id="password" class="form-textarea" style="min-height: auto;">
              </div>
              <div class="form-group">
                  <label for="confirm-password" class="form-label">Confirm Password</label>
                  <input type="password" id="confirm-password" class="form-textarea" style="min-height: auto;">
              </div>
              <div class="form-group" style="display: flex; align-items: center;">
                  <input type="checkbox" id="active" style="margin-right: 0.5rem;" checked="">
                  <label for="active" class="form-label" style="margin-bottom: 0;">Active Account</label>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button class="modal-button modal-button-secondary" id="cancelAccount">Cancel</button>
            <button class="modal-button modal-button-primary" id="saveAccount">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Account Modal -->
    <div class="modal" id="accountEditModal">
      <div class="modal-container">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Edit Account</h3>
            <button class="close-modal" id="closeEditModal">&times;</button>
          </div>
          <div class="modal-body">
              <div id="form-group">
                <label for="editStatus">Status</label>
                <select id="editStatus"  class="form-select" name="status" required>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button class="modal-button modal-button-secondary" id="cancelEdit">Cancel</button>
            <button class="modal-button modal-button-primary" id="saveChanges">Save Changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="accounts.js"></script>
</body>
</html>
