<?php
session_start();
require_once 'config.php';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Check if user is logged in and is admin
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin"){
    header("location: login.php");
    exit;
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Set CSRF token for AJAX requests
header('X-CSRF-Token: ' . $_SESSION['csrf_token']);

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total number of records
$total_sql = "SELECT COUNT(*) as count FROM login";
$total_result = mysqli_query($conn, $total_sql);
$total_records = mysqli_fetch_assoc($total_result)['count'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch accounts with pagination
$sql = "SELECT * FROM login ORDER BY created_at ASC LIMIT ? OFFSET ?";
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
  <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
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
              <option value="staff">Staff</option>
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
                        
                        $statusClass = $status === 'active' ? 'status-in-stock' : 'status-out-of-stock';
                        $roleClass = $role === 'admin' ? 'status-admin' : ($role === 'staff' ? 'status-low-stock' : 'status-customer');
                        
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
                                <button class='action-button edit-button edit-account-btn' title='Edit Account' data-user-id='$userId'>
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
                <span><?php echo $total_records; ?></span> accounts
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
  <div class="modal-overlay" id="modalOverlay">
    <!-- Customer Details Modal -->
    <div class="modal" id="customerDetailsModal">
      <div class="modal-container">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Customer Details</h3>
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
          </div>
          <div class="modal-body">
            <form id="addAccountForm">
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
              <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" class="form-control" required>

              </div>
              <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" class="form-control" required>

              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>

              </div>
              <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" class="form-select" required>
                  <option value="" disabled selected>Select a role</option>
                  <option value="customer">Customer</option>
                  <option value="staff">Staff</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required minlength="8">

              </div>
              <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                <small class="form-text text-muted">Password must be at least 8 characters long and contain both letters and numbers</small>
              </div>
              <div class="form-group form-check">
                <input type="checkbox" id="status" name="status" class="form-check-input" value="active" checked>
                <label class="form-check-label" for="status">Active Account</label>
              </div>
              <div class="modal-footer">
                <button type="button" class="modal-button modal-button-secondary" id="cancelAccount" onclick="window.closeModal()">Cancel</button>
                <button type="submit" id="submit" class="modal-button modal-button-primary">Save</button>
              </div>
            </form>
        </div>
      </div>
    </div>

    <!-- Edit Account Modal -->
    <div class="modal" id="accountEditModal">
      <div class="modal-container">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Edit Account</h3>
            <button type="button" class="close-button" id="closeEditModal">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="modal-body">
            <form id="accountEditForm">
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
              <input type="hidden" id="user_id" name="user_id" value="">
              <div class="form-group">
                <label for="editStatus">Status</label>
                <select id="editStatus" name="status" class="form-select" required>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="modal-button modal-button-secondary" id="cancelEdit" onclick="window.closeModal()">Cancel</button>
            <button type="submit" form="accountEditForm" class="modal-button modal-button-primary">Save Changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="accounts.js"></script>
</body>
</html>
