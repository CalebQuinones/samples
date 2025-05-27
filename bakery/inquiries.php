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
$total_sql = "SELECT COUNT(*) as count FROM inquiry";
$total_result = mysqli_query($conn, $total_sql);
$total_records = mysqli_fetch_assoc($total_result)['count'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch inquiries with pagination
$sql = "SELECT * FROM inquiry ORDER BY dateSubmitted DESC LIMIT ? OFFSET ?";
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
  <title>Inquiries - Bakery Admin Dashboard</title>
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
            <a href="accounts.php">
              <i class="fas fa-users"></i>
              <span>Accounts</span>
            </a>
          </li>
          <li>
            <a href="inquiries.php" class="active">
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
            <h1>Inquiries</h1>
          </div>
        </div>
      </header>

      <div class="page-content">
        <!-- Bulk Actions (hidden by default) -->
        <div class="bulk-actions" id="bulkActions" style="display: none;">
          <div class="bulk-actions-count">
            <span id="selectedCount">0</span> inquiries selected
          </div>
          <div class="bulk-actions-buttons">
            <button class="bulk-button bulk-button-complete">
              <i class="fas fa-check"></i>
              Mark as Resolved
            </button>
            <button class="bulk-button bulk-button-print">
              Mark as In Progress
            </button>
            <button class="bulk-button bulk-button-cancel">
              <i class="fas fa-times"></i>
              Delete
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
            <input type="text" class="search-input" placeholder="Search inquiries..." id="searchInput">
          </div>
          <div class="filter-buttons">
            <select class="filter-select" id="statusFilter">
              <option>All Status</option>
              <option>New</option>
              <option>In Progress</option>
              <option>Resolved</option>
            </select>
            <select class="filter-select" id="dateFilter">
              <option>Last 30 Days</option>
              <option>Last 7 Days</option>
              <option>Today</option>
              <option>This Month</option>
              <option>Last Month</option>
            </select>
          </div>
        </div>

        <!-- Inquiries Table -->
        <div class="order-card">
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>
                    <input type="checkbox" id="selectAll" class="account-checkbox">
                  </th>
                  <th>Subject</th>
                  <th>Customer</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="inquiriesTableBody">
                <?php
                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        // If you add a status column in the future, map it to a badge class
                        $status = 'New'; // Default for now
                        $statusClass = 'status-pending'; // Use status-pending for New, status-in-progress for In Progress, status-completed for Resolved

                        echo "<tr>";
                        echo "<td><input type='checkbox' class='account-checkbox' data-id='" . $row['ID'] . "'></td>";
                        echo "<td>"
                            . "<span style='font-weight:bold;'>" . htmlspecialchars($row['subject']) . "</span><br>"
                            . "<span style='color:#555;'>"
                            . htmlspecialchars(mb_strimwidth($row['msg'], 0, 50, '...'))
                            . "</span>"
                            . "</td>";
                        echo "<td>" . htmlspecialchars($row['Fname'] . " " . $row['Lname']) . "</td>";
                        echo "<td>" . date('M d, Y', strtotime($row['dateSubmitted'])) . "</td>";
                        echo "<td><span class='status-badge $statusClass'>" . htmlspecialchars($status) . "</span></td>";
                        echo "<td style='text-align:center; vertical-align:middle;'><div class='action-buttons'>
                                <button class='action-button view-button' data-id='" . $row['ID'] . "' title='View'>
                                    <i class='fas fa-eye'></i>
                                </button>
                                <button class='action-button delete-button' data-id='" . $row['ID'] . "' title='Archive'>
                                    <i class='fas fa-archive'></i>
                                </button>
                              </div></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No inquiries found</td></tr>";
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
                <span><?php echo $total_records; ?></span> inquiries
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
    <!-- View Inquiry Modal -->
    <div class="modal" id="inquiryModal">
      <div class="modal-container">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="inquirySubject">Wedding Cake Inquiry</h3>
            <span class="status-badge status-new" id="inquiryStatusBadge">New</span>
          </div>
          <div class="modal-body">
            <div class="bg-gray-50 p-3 rounded-lg">
              <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <div id="inquiryCustomer" style="font-size: 0.875rem; font-weight: 500; color: var(--gray-900);">Jennifer Smith</div>
                <div id="inquiryDate" style="font-size: 0.75rem; color: var(--gray-500);">Apr 9, 2025</div>
              </div>
              <p id="inquiryMessage" style="font-size: 0.875rem; color: var(--gray-700); white-space: pre-line;">I'm interested in ordering a 3-tier wedding cake for my upcoming wedding on June 15th. Do you offer tastings? I'd like to discuss design options and flavors. My fiancé and I are thinking of a semi-naked cake with fresh flowers. Please let me know what information you need from me to get started.</p>
            </div>

            <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem;">
              <div style="display: flex; align-items: center; font-size: 0.875rem; color: var(--gray-600);">
                <i class="fas fa-envelope" style="width: 1rem; height: 1rem; margin-right: 0.5rem;"></i>
                <span id="inquiryEmail">jennifer.smith@example.com</span>
              </div>
              <div style="display: flex; align-items: center; font-size: 0.875rem; color: var(--gray-600);">
                <i class="fas fa-phone" style="width: 1rem; height: 1rem; margin-right: 0.5rem;"></i>
                <span id="inquiryPhone">+1 (555) 123-4567</span>
              </div>
            </div>

            <div style="margin-top: 1rem;">
              <label for="reply" style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--gray-700); margin-bottom: 0.5rem;">
                Reply
              </label>
              <textarea
                style="width: 420px; height: 120px; border: 1px solid var(--pink-200); border-radius: 0.375rem; padding: 0.5rem 0.75rem;"
                id="replyText"
                rows="4"
                class="form-textarea"
                placeholder="Type your reply here..."
              ></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="modal-button modal-button-secondary" id="closeInquiry">Cancel</button>
            <button class="modal-button modal-button-primary" id="sendReply">Send Reply</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="inquiries.js"></script>
</body>
</html>
