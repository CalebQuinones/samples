<?php
session_start();
require_once 'config.php';

// AJAX endpoint for dynamic sales data
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    $start = $_GET['start'] ?? '';
    $end = $_GET['end'] ?? '';
    $params = [];
    $where = "WHERE o.status != 'Cancelled'";
    if ($start && $end) {
        $where .= " AND DATE_FORMAT(o.created_at, '%Y-%m') BETWEEN ? AND ?";
        $params[] = $start;
        $params[] = $end;
    }
    $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(oi.quantity * oi.price) as total_sales
            FROM orders o
            LEFT JOIN order_items oi ON o.order_id = oi.order_id
            $where
            GROUP BY month
            ORDER BY month ASC";
    $stmt = mysqli_prepare($conn, $sql);
    if ($params) {
        mysqli_stmt_bind_param($stmt, "ss", $params[0], $params[1]);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $months = [];
    $salesData = [];
    $total = 0;
    while($row = mysqli_fetch_assoc($result)) {
        $months[] = $row['month'];
        $amount = $row['total_sales'] ? (float)$row['total_sales'] : 0;
        $salesData[] = $amount;
        $total += $amount;
    }
    echo json_encode(['months' => $months, 'salesData' => $salesData, 'total' => $total]);
    exit;
}

// Fetch sales data for the last 6 months
$salesData = [];
$months = [];
$totalSales = 0;
$sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(oi.quantity * oi.price) as total_sales
        FROM orders o
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.status != 'Cancelled'
        GROUP BY month
        ORDER BY month DESC
        LIMIT 6";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)) {
    $months[] = $row['month'];
    $amount = $row['total_sales'] ? (float)$row['total_sales'] : 0;
    $salesData[] = $amount;
    $totalSales += $amount;
}
$months = array_reverse($months);
$salesData = array_reverse($salesData);

// Fetch KPI data for current week
$weekStart = date('Y-m-d', strtotime('monday this week'));
$weekEnd = date('Y-m-d', strtotime('sunday this week'));

// Get weekly stats
$weeklyStats = mysqli_query($conn, "
    SELECT 
        SUM(oi.quantity * oi.price) as total_revenue,
        SUM(oi.quantity) as items_sold,
        COUNT(DISTINCT o.order_id) as total_orders,
        COUNT(DISTINCT o.user_id) as total_customers,
        SUM(oi.quantity * oi.price)/COUNT(DISTINCT o.order_id) as avg_order_value
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.created_at BETWEEN '$weekStart' AND '$weekEnd'
    AND o.status != 'Cancelled'
");
$stats = mysqli_fetch_assoc($weeklyStats);

// Get daily sales for current week  
$dailySales = mysqli_query($conn, "
    SELECT DATE_FORMAT(o.created_at, '%a') as day,
           SUM(total_amount) as daily_total
    FROM orders o 
    WHERE o.created_at BETWEEN '$weekStart' AND '$weekEnd'
    AND o.status != 'Cancelled'
    GROUP BY DATE(o.created_at)
    ORDER BY DATE(o.created_at)
");

// Get product distribution
$productDistribution = mysqli_query($conn, "
    SELECT 
        p.category,
        SUM(oi.quantity) as total_sold
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id 
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.created_at BETWEEN '$weekStart' AND '$weekEnd'
    AND o.status != 'Cancelled'
    AND p.category IN ('Cakes', 'Cookies', 'Breads', 'Pastries')
    GROUP BY p.category
    ORDER BY total_sold DESC
");

// Get top 5 sellers
$topSellers = mysqli_query($conn, "
    SELECT 
        p.name,
        SUM(oi.quantity) as units_sold,
        SUM(oi.quantity * oi.price) as revenue,
        ROUND(((SUM(oi.quantity) - LAG(SUM(oi.quantity)) OVER (ORDER BY SUM(oi.quantity))) 
        / LAG(SUM(oi.quantity)) OVER (ORDER BY SUM(oi.quantity)) * 100), 1) as growth
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    JOIN orders o ON oi.order_id = o.order_id
    WHERE o.created_at BETWEEN '$weekStart' AND '$weekEnd'
    AND o.status != 'Cancelled'
    GROUP BY p.product_id
    ORDER BY revenue DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sales - Bakery Admin</title>
  <link rel="stylesheet" href="adminstyles.css">
  <link rel="stylesheet" href="adminstyles2.css">
  <link rel="stylesheet" href="notification-styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
        .kpi-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .kpi-card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            padding: 1rem;
            border: 1px solid var(--pink-200);
        }
        .kpi-card h3 {
            
            font-family: Inter;
            color: #666;
            font-size: 0.9rem;
            font-weight: normal;
            margin: 0 0 0.5rem 0;
        }
        .kpi-card h2 {
            font-family: Madimi One;
            color: #333;
            font-size: 1.8rem;
            margin: 0 0 0.5rem 0;
        }
        .kpi-card h2 span {
            font-family: Inter;
        }
        .kpi-card p {
            font-family: Inter;
            color: #22c55e;
            font-size: 0.9rem;
            margin: 0;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .chart-card h3 {
            font-family: Madimi One;
            color: #333;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        .chart-card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            padding: 1rem;
            border: 1px solid var(--pink-200);
            margin-bottom: 2rem;
            height: auto; /* Add fixed height */
            width: 100%;   /* Control width */
        }
        .chart-card canvas {
            max-height: 250px !important; /* Force smaller chart height */
            width: 100% !important;
        }
        .top-sellers {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            padding: 1rem;
            border: 1px solid var(--pink-200);
        }
        .top-sellers h3 {
            font-family: Madimi One;
            color: #333;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        .top-sellers table {
            width: 100%;
            margin-top: 0.5rem;
            border-collapse: collapse;
        }
        .top-sellers th, .top-sellers td {
            padding: 0.5rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .top-sellers td.center-align {
          text-indent: 50px;
        }
        .growth-positive {
            color: #28a745;
        }
        .date-range-form {
            margin-bottom: 1rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .date-range-form input[type="month"] {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .date-range-form button {
            padding: 0.5rem 1rem;
            background: #ff6b9d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .date-range-form button:hover {
            background: #ff3d7f;
        }
    </style>
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
          <li>
            <a href="sales.php" class="active" id="salesSidebarLink">
              <i class="fas fa-chart-line"></i>
              Sales
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
            <h1>Sales</h1>
          </div>
        </div>
      </header>
      <div class="content">
        <div class="kpi-cards">
            <div class="kpi-card">
                <h3>Total Revenue</h3>
                <h2><span>₱</span> <?php echo number_format($stats['total_revenue'], 2); ?></h2>
                <p>+2.5% from last week</p>
            </div>
            <div class="kpi-card">
                <h3>Products Sold</h3>
                <h2><?php echo number_format($stats['items_sold']); ?></h2>
                <p>+8.2% from last week</p>
            </div>
            <div class="kpi-card">
                <h3>Avg Order Value</h3>
                <h2><span>₱</span> <?php echo number_format($stats['avg_order_value'], 2); ?></h2>
                <p>+5.1% from last week</p>
            </div>
            <div class="kpi-card">
                <h3>Customers</h3>
                <h2><?php echo number_format($stats['total_customers']); ?></h2>
                <p>+15.3% from last week</p>
            </div>
        </div>

        <div class="chart-grid">
            <div class="chart-card">
                <h3>Daily Sales This Week</h3>
                <canvas id="dailySalesChart"></canvas>
            </div>
            <div class="chart-card">
                <h3>Product Sales Distribution</h3>
                <canvas id="productDistributionChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h3>Monthly Revenue Trend</h3>
            <form id="salesRangeForm" class="date-range-form">
                <input type="month" id="startMonth" required>
                <input type="month" id="endMonth" required>
                <button type="submit">Update</button>
            </form>
            <canvas id="salesChart"></canvas>
        </div>

        <div class="top-sellers">
            <h3>Top 5 Best Selling This Week</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Products Sold</th>
                        <th>Revenue</th>
                        <th>Growth</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($product = mysqli_fetch_assoc($topSellers)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td class="center-align"><?php echo number_format($product['units_sold']); ?></td>
                        <td>₱<?php echo number_format($product['revenue'], 2); ?></td>
                        <td class="growth-positive">+<?php echo $product['growth']; ?>%</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
      </div>
    </main>
  </div>
  <!-- Logout Modal -->
  <div class="modal-overlay" id="logoutModal">
    <div class="modal">
      <div class="modal-container">
        <div class="modal-header">
          <h2 class="modal-title">Confirm Logout</h2>
          <button class="close-modal" id="closeLogoutModal">&times;</button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to logout?</p>
        </div>
        <div class="modal-footer">
          <button class="modal-button modal-button-secondary" id="cancelLogout">Cancel</button>
          <button class="modal-button modal-button-primary" id="confirmLogout">Logout</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Static test data for Monthly Sales
    const salesTestData = {
      months: ['2024-01', '2024-02', '2024-03', '2024-04', '2024-05', '2024-06'],
      sales: [25000, 30000, 27000, 32000, 29000, 35000]
    };

    // Set default date range (last 6 months)
    const today = new Date();
    const endMonth = today.toISOString().slice(0,7);
    const startMonth = new Date(today.setMonth(today.getMonth() - 5)).toISOString().slice(0,7);
    
    document.getElementById('startMonth').value = startMonth;
    document.getElementById('endMonth').value = endMonth;

    // Initialize sales chart
    let salesChart;
    
    function updateSalesChart(data) {
      if (salesChart) {
        salesChart.destroy();
      }
      
      const monthlyCtx = document.getElementById('salesChart').getContext('2d');
      salesChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
          labels: data.months.map(m => {
            const [year, month] = m.split('-');
            return new Date(year, month-1).toLocaleString('default', { month: 'short', year: '2-digit' });
          }),
          datasets: [{
            label: 'Monthly Sales',
            data: data.salesData || salesTestData.sales,
            borderColor: '#ff6b9d',
            backgroundColor: 'rgba(255, 107, 157, 0.1)',
            fill: true,
            tension: 0.4
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: (value) => '₱ ' + value.toLocaleString()
              }
            }
          }
        }
      });
    }

    // Initial chart load with test data
    updateSalesChart({ 
      months: salesTestData.months, 
      salesData: salesTestData.sales 
    });

    // Form submission handler
    document.getElementById('salesRangeForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const start = document.getElementById('startMonth').value;
      const end = document.getElementById('endMonth').value;
      
      // For demo, just use test data
      updateSalesChart({ 
        months: salesTestData.months, 
        salesData: salesTestData.sales 
      });
    });

    // Static test data for Daily Sales
    const dailyCtx = document.getElementById('dailySalesChart').getContext('2d');
    new Chart(dailyCtx, {
      type: 'bar',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Daily Sales',
          data: [4500, 5200, 4800, 5000, 5600, 6000, 5400],
          backgroundColor: '#ff6b9d'
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: (value) => '₱' + value.toLocaleString()
            }
          }
        }
      }
    });

    // Static test data for Product Distribution
    const distributionCtx = document.getElementById('productDistributionChart').getContext('2d');
    new Chart(distributionCtx, {
      type: 'pie',
      data: {
        labels: ['Cupcakes 19%', 'Croissants 23%', 'Cookies 17%', 'Bread Loaves 28%', 'Pastries 13%'],
        datasets: [{
          data: [19, 23, 17, 28, 13],
          backgroundColor: [
            '#ff9ec5', // Light pink for Cupcakes
            '#ff3d8a', // Dark pink for Croissants
            '#ff4b8d', // Medium pink for Cookies
            '#ffb5d6', // Lighter pink for Bread Loaves
            '#ffc7e0'  // Lightest pink for Pastries
          ],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'right',
            labels: {
              padding: 15,
              color: '#ff3d8a',
              font: {
                size: 12
              }
            }
          },

        }
      }
    });
  </script>
</body>
</html>
