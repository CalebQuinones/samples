document.addEventListener("DOMContentLoaded", () => {
    // Sample order data
    const ordersData = [
      {
        id: "ORD-001",
        customer: "Sarah Johnson",
        product: "Birthday Cake (Custom)",
        orderDate: "Apr 5, 2025",
        deliveryDate: "Apr 8, 2025",
        status: "Completed",
        payment: "Credit Card",
        total: "$89.99",
      },
      {
        id: "ORD-002",
        customer: "Michael Brown",
        product: "Wedding Cake (3-tier)",
        orderDate: "Apr 2, 2025",
        deliveryDate: "Apr 9, 2025",
        status: "In Progress",
        payment: "Bank Transfer",
        total: "$349.99",
      },
      {
        id: "ORD-003",
        customer: "Emily Davis",
        product: "Cupcakes (24 pcs)",
        orderDate: "Apr 7, 2025",
        deliveryDate: "Apr 9, 2025",
        status: "Pending",
        payment: "PayPal",
        total: "$72.00",
      },
      {
        id: "ORD-004",
        customer: "David Wilson",
        product: "Bread Assortment",
        orderDate: "Apr 8, 2025",
        deliveryDate: "Apr 10, 2025",
        status: "Cancelled",
        payment: "Credit Card",
        total: "$45.50",
      },
      {
        id: "ORD-005",
        customer: "Jessica Martinez",
        product: "Birthday Cake (Standard)",
        orderDate: "Apr 6, 2025",
        deliveryDate: "Apr 10, 2025",
        status: "In Progress",
        payment: "Credit Card",
        total: "$65.99",
      },
      {
        id: "ORD-006",
        customer: "Robert Taylor",
        product: "Assorted Pastries (12 pcs)",
        orderDate: "Apr 7, 2025",
        deliveryDate: "Apr 11, 2025",
        status: "Pending",
        payment: "PayPal",
        total: "$54.99",
      },
      {
        id: "ORD-007",
        customer: "Amanda Lee",
        product: "Baby Shower Cake",
        orderDate: "Apr 8, 2025",
        deliveryDate: "Apr 12, 2025",
        status: "In Progress",
        payment: "Credit Card",
        total: "$79.99",
      },
      {
        id: "ORD-008",
        customer: "Thomas Anderson",
        product: "Chocolate Chip Cookies (24 pcs)",
        orderDate: "Apr 9, 2025",
        deliveryDate: "Apr 12, 2025",
        status: "Pending",
        payment: "Cash on Delivery",
        total: "$36.50",
      },
    ]
  
    // DOM elements
    const ordersTableBody = document.getElementById("ordersTableBody")
    const searchInput = document.getElementById("searchInput")
    const statusFilter = document.getElementById("statusFilter")
    const productFilter = document.getElementById("productFilter")
    const dateFilter = document.getElementById("dateFilter")
    const selectAll = document.getElementById("selectAll")
    const bulkActions = document.getElementById("bulkActions")
    const selectedCount = document.getElementById("selectedCount")
    const clearSelection = document.getElementById("clearSelection")
    const prevPage = document.getElementById("prevPage")
    const nextPage = document.getElementById("nextPage")
    const prevPageMobile = document.getElementById("prevPageMobile")
    const nextPageMobile = document.getElementById("nextPageMobile")
    const paginationNav = document.getElementById("paginationNav")
    const startIndex = document.getElementById("startIndex")
    const endIndex = document.getElementById("endIndex")
    const totalItems = document.getElementById("totalItems")
  
    // Pagination state
    let currentPage = 1
    const itemsPerPage = 5
    let filteredOrders = [...ordersData]
    let selectedOrders = []
  
    // Initialize
    updateTable()
  
    // Event listeners
    searchInput.addEventListener("input", () => {
      currentPage = 1
      updateTable()
    })
  
    statusFilter.addEventListener("change", () => {
      currentPage = 1
      updateTable()
    })
  
    productFilter.addEventListener("change", () => {
      currentPage = 1
      updateTable()
    })
  
    dateFilter.addEventListener("change", () => {
      currentPage = 1
      updateTable()
    })
  
    selectAll.addEventListener("change", () => {
      const checkboxes = document.querySelectorAll(".order-checkbox")
      checkboxes.forEach((checkbox) => {
        checkbox.checked = selectAll.checked
        const orderId = checkbox.getAttribute("data-id")
        if (selectAll.checked) {
          if (!selectedOrders.includes(orderId)) {
            selectedOrders.push(orderId)
          }
        } else {
          selectedOrders = selectedOrders.filter((id) => id !== orderId)
        }
      })
      updateBulkActions()
    })
  
    clearSelection.addEventListener("click", () => {
      selectedOrders = []
      selectAll.checked = false
      const checkboxes = document.querySelectorAll(".order-checkbox")
      checkboxes.forEach((checkbox) => {
        checkbox.checked = false
      })
      updateBulkActions()
    })
  
    prevPage.addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--
        updateTable()
      }
    })
  
    nextPage.addEventListener("click", () => {
      const totalPages = Math.ceil(filteredOrders.length / itemsPerPage)
      if (currentPage < totalPages) {
        currentPage++
        updateTable()
      }
    })
  
    prevPageMobile.addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--
        updateTable()
      }
    })
  
    nextPageMobile.addEventListener("click", () => {
      const totalPages = Math.ceil(filteredOrders.length / itemsPerPage)
      if (currentPage < totalPages) {
        currentPage++
        updateTable()
      }
    })
  
    // Functions
    function updateTable() {
      // Filter orders
      filteredOrders = ordersData.filter((order) => {
        const searchTerm = searchInput.value.toLowerCase()
        const matchesSearch =
          order.id.toLowerCase().includes(searchTerm) ||
          order.customer.toLowerCase().includes(searchTerm) ||
          order.product.toLowerCase().includes(searchTerm)
  
        const matchesStatus = statusFilter.value === "All Statuses" || order.status === statusFilter.value
        const matchesProduct =
          productFilter.value === "All Products" || order.product.includes(productFilter.value.replace("All ", ""))
  
        return matchesSearch && matchesStatus && matchesProduct
      })
  
      // Update pagination info
      const totalPages = Math.ceil(filteredOrders.length / itemsPerPage)
      const start = (currentPage - 1) * itemsPerPage + 1
      const end = Math.min(start + itemsPerPage - 1, filteredOrders.length)
  
      startIndex.textContent = filteredOrders.length > 0 ? start : 0
      endIndex.textContent = end
      totalItems.textContent = filteredOrders.length
  
      // Update pagination buttons
      prevPage.disabled = currentPage === 1
      nextPage.disabled = currentPage === totalPages || totalPages === 0
      prevPageMobile.disabled = currentPage === 1
      nextPageMobile.disabled = currentPage === totalPages || totalPages === 0
  
      // Generate pagination numbers
      paginationNav.innerHTML = ""
      paginationNav.appendChild(prevPage)
  
      for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement("button")
        pageButton.className = `pagination-button pagination-button-page ${i === currentPage ? "active" : ""}`
        pageButton.textContent = i
        pageButton.addEventListener("click", () => {
          currentPage = i
          updateTable()
        })
        paginationNav.appendChild(pageButton)
      }
  
      paginationNav.appendChild(nextPage)
  
      // Render table rows
      ordersTableBody.innerHTML = ""
  
      const paginatedOrders = filteredOrders.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage)
  
      paginatedOrders.forEach((order) => {
        const row = document.createElement("tr")
  
        // Checkbox cell
        const checkboxCell = document.createElement("td")
        const checkbox = document.createElement("input")
        checkbox.type = "checkbox"
        checkbox.className = "order-checkbox"
        checkbox.setAttribute("data-id", order.id)
        checkbox.checked = selectedOrders.includes(order.id)
        checkbox.addEventListener("change", () => {
          if (checkbox.checked) {
            if (!selectedOrders.includes(order.id)) {
              selectedOrders.push(order.id)
            }
          } else {
            selectedOrders = selectedOrders.filter((id) => id !== order.id)
            selectAll.checked = false
          }
          updateBulkActions()
        })
        checkboxCell.appendChild(checkbox)
        row.appendChild(checkboxCell)
  
        // Order ID cell
        const idCell = document.createElement("td")
        idCell.className = "order-id"
        idCell.textContent = order.id
        row.appendChild(idCell)
  
        // Customer cell
        const customerCell = document.createElement("td")
        customerCell.textContent = order.customer
        row.appendChild(customerCell)
  
        // Product cell
        const productCell = document.createElement("td")
        productCell.textContent = order.product
        row.appendChild(productCell)
  
        // Order Date cell
        const orderDateCell = document.createElement("td")
        orderDateCell.textContent = order.orderDate
        row.appendChild(orderDateCell)
  
        // Delivery Date cell
        const deliveryDateCell = document.createElement("td")
        deliveryDateCell.textContent = order.deliveryDate
        row.appendChild(deliveryDateCell)
  
        // Status cell
        const statusCell = document.createElement("td")
        const statusBadge = document.createElement("span")
        statusBadge.className = `status-badge status-${order.status.toLowerCase().replace(" ", "-")}`
        statusBadge.textContent = order.status
        statusCell.appendChild(statusBadge)
        row.appendChild(statusCell)
  
        // Payment cell
        const paymentCell = document.createElement("td")
        paymentCell.textContent = order.payment
        row.appendChild(paymentCell)
  
        // Total cell
        const totalCell = document.createElement("td")
        totalCell.textContent = order.total
        row.appendChild(totalCell)
  
        // Actions cell
        const actionsCell = document.createElement("td")
        const editLink = document.createElement("a")
        editLink.href = `order-details.php?id=${order.id}`
        editLink.className = "text-pink-600 hover:text-pink-900 flex items-center"
        editLink.innerHTML = '<i class="fas fa-edit mr-1"></i> Edit'
        actionsCell.appendChild(editLink)
        row.appendChild(actionsCell)
  
        ordersTableBody.appendChild(row)
      })
  
      // If no orders match the filters
      if (paginatedOrders.length === 0) {
        const emptyRow = document.createElement("tr")
        const emptyCell = document.createElement("td")
        emptyCell.colSpan = 10
        emptyCell.textContent = "No orders found matching your filters."
        emptyCell.style.textAlign = "center"
        emptyCell.style.padding = "2rem 1rem"
        emptyCell.style.color = "var(--gray-500)"
        emptyRow.appendChild(emptyCell)
        ordersTableBody.appendChild(emptyRow)
      }
    }
  
    function updateBulkActions() {
      if (selectedOrders.length > 0) {
        bulkActions.style.display = "flex"
        selectedCount.textContent = selectedOrders.length
      } else {
        bulkActions.style.display = "none"
      }
    }
  })
  document.addEventListener('DOMContentLoaded', function() {
    // Sample orders data
    const orders = [
      {
        id: 'ORD-001',
        customer: 'Angelo Castro',
        product: 'Birthday Cake',
        orderDate: '2025-04-01',
        deliveryDate: '2025-04-05',
        status: 'Completed',
        payment: 'Credit Card',
        total: '$89.99',
      },
      {
        id: 'ORD-002',
        customer: 'James Cabelidad',
        product: 'Wedding Cake',
        orderDate: '2025-04-02',
        deliveryDate: '2025-04-09',
        status: 'In Progress',
        payment: 'Bank Transfer',
        total: '$349.99',
      },
      {
        id: 'ORD-003',
        customer: 'Masa Rack',
        product: 'Cupcakes (24)',
        orderDate: '2025-04-03',
        deliveryDate: '2025-04-06',
        status: 'Pending',
        payment: 'PayPal',
        total: '$72.00',
      },
      {
        id: 'ORD-004',
        customer: 'Alecs Pahayhay',
        product: 'Bread Assortment',
        orderDate: '2025-04-01',
        deliveryDate: '2025-04-02',
        status: 'Cancelled',
        payment: 'Credit Card',
        total: '$45.50',
      },
      {
        id: 'ORD-005',
        customer: 'Jessica Martinez',
        product: 'Anniversary Cake',
        orderDate: '2025-04-02',
        deliveryDate: '2025-04-10',
        status: 'In Progress',
        payment: 'Credit Card',
        total: '$65.99',
      },
      {
        id: 'ORD-006',
        customer: 'Robert Taylor',
        product: 'Birthday Cake',
        orderDate: '2025-04-03',
        deliveryDate: '2025-04-07',
        status: 'Pending',
        payment: 'PayPal',
        total: '$55.00',
      },
      {
        id: 'ORD-007',
        customer: 'Jennifer Anderson',
        product: 'Pastry Box',
        orderDate: '2025-04-01',
        deliveryDate: '2025-04-04',
        status: 'Completed',
        payment: 'Credit Card',
        total: '$38.50',
      },
      {
        id: 'ORD-008',
        customer: 'Christopher Thomas',
        product: 'Custom Cake',
        orderDate: '2025-04-02',
        deliveryDate: '2025-04-08',
        status: 'In Progress',
        payment: 'Bank Transfer',
        total: '$120.00',
      },
      {
        id: 'ORD-009',
        customer: 'Lisa Rodriguez',
        product: 'Cookies (36)',
        orderDate: '2025-04-03',
        deliveryDate: '2025-04-05',
        status: 'Pending',
        payment: 'PayPal',
        total: '$42.00',
      },
      {
        id: 'ORD-010',
        customer: 'Daniel Lee',
        product: 'Gluten-Free Cake',
        orderDate: '2025-04-01',
        deliveryDate: '2025-04-06',
        status: 'Completed',
        payment: 'Credit Card',
        total: '$75.99',
      },
      {
        id: 'ORD-011',
        customer: 'Michelle Clark',
        product: 'Vegan Cupcakes (12)',
        orderDate: '2025-04-02',
        deliveryDate: '2025-04-04',
        status: 'Completed',
        payment: 'PayPal',
        total: '$48.00',
      },
      {
        id: 'ORD-012',
        customer: 'James Walker',
        product: 'Birthday Cake',
        orderDate: '2025-04-03',
        deliveryDate: '2025-04-09',
        status: 'Pending',
        payment: 'Bank Transfer',
        total: '$59.99',
      }
    ];
  
    // Pagination variables
    let currentPage = 1;
    const itemsPerPage = 8;
    const totalPages = Math.ceil(orders.length / itemsPerPage);
  
    // Function to format date
    function formatDate(dateString) {
      const options = { year: 'numeric', month: 'short', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    }
  
    // Function to get status badge class
    function getStatusBadgeClass(status) {
      switch (status) {
        case 'Completed':
          return 'status-badge status-completed';
        case 'In Progress':
          return 'status-badge status-in-progress';
        case 'Pending':
          return 'status-badge status-pending';
        case 'Cancelled':
          return 'status-badge status-cancelled';
        default:
          return 'status-badge';
      }
    }
  
    // Function to render orders table
    function renderOrdersTable() {
      const tableBody = document.getElementById('ordersTableBody');
      tableBody.innerHTML = '';
  
      const startIndex = (currentPage - 1) * itemsPerPage;
      const endIndex = Math.min(startIndex + itemsPerPage, orders.length);
      
      document.getElementById('startIndex').textContent = startIndex + 1;
      document.getElementById('endIndex').textContent = endIndex;
      document.getElementById('totalItems').textContent = orders.length;
  
      for (let i = startIndex; i < endIndex; i++) {
        const order = orders[i];
        const row = document.createElement('tr');
        
        row.innerHTML = `
          <td>
            <input type="checkbox" class="order-checkbox" data-order-id="${order.id}">
          </td>
          <td>
            <a href="order-details.php?id=${order.id}" class="order-id">${order.id}</a>
          </td>
          <td>${order.customer}</td>
          <td>${order.product}</td>
          <td>${formatDate(order.orderDate)}</td>
          <td>${formatDate(order.deliveryDate)}</td>
          <td><span class="${getStatusBadgeClass(order.status)}">${order.status}</span></td>
          <td>${order.payment}</td>
          <td>${order.total}</td>
          <td>
            <div class="actions">
              <a href="order-details.php?id=${order.id}" title="View Details">
                <i class="fas fa-edit"></i>
              </a>
          
              <button title="Delete Order" class="delete-button" data-order-id="${order.id}">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        `;
        
        tableBody.appendChild(row);
      }
  
      // Update pagination buttons
      document.getElementById('prevPage').disabled = currentPage === 1;
      document.getElementById('prevPageMobile').disabled = currentPage === 1;
      document.getElementById('nextPage').disabled = currentPage === totalPages;
      document.getElementById('nextPageMobile').disabled = currentPage === totalPages;
  
      // Render pagination numbers
      renderPaginationNumbers();
    }
  
    // Function to render pagination numbers
    function renderPaginationNumbers() {
      const paginationNav = document.getElementById('paginationNav');
      const prevButton = document.getElementById('prevPage');
      const nextButton = document.getElementById('nextPage');
      
      // Remove existing page buttons
      const existingButtons = paginationNav.querySelectorAll('.pagination-button-page');
      existingButtons.forEach(button => button.remove());
      
      // Determine range of pages to show
      let startPage = Math.max(1, currentPage - 2);
      let endPage = Math.min(totalPages, startPage + 4);
      
      if (endPage - startPage < 4) {
        startPage = Math.max(1, endPage - 4);
      }
      
      // Insert page buttons
      for (let i = startPage; i <= endPage; i++) {
        const pageButton = document.createElement('button');
        pageButton.className = `pagination-button pagination-button-page ${i === currentPage ? 'active' : ''}`;
        pageButton.textContent = i;
        pageButton.addEventListener('click', () => {
          currentPage = i;
          renderOrdersTable();
        });
        
        paginationNav.insertBefore(pageButton, nextButton);
      }
    }
  
    // Event listeners for pagination
    document.getElementById('prevPage').addEventListener('click', () => {
      if (currentPage > 1) {
        currentPage--;
        renderOrdersTable();
      }
    });
  
    document.getElementById('nextPage').addEventListener('click', () => {
      if (currentPage < totalPages) {
        currentPage++;
        renderOrdersTable();
      }
    });
  
    document.getElementById('prevPageMobile').addEventListener('click', () => {
      if (currentPage > 1) {
        currentPage--;
        renderOrdersTable();
      }
    });
  
    document.getElementById('nextPageMobile').addEventListener('click', () => {
      if (currentPage < totalPages) {
        currentPage++;
        renderOrdersTable();
      }
    });
  
    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    selectAllCheckbox.addEventListener('change', () => {
      const checkboxes = document.querySelectorAll('.order-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
      });
      
      updateBulkActions();
    });
  
    // Function to update bulk actions visibility
    function updateBulkActions() {
      const checkboxes = document.querySelectorAll('.order-checkbox:checked');
      const bulkActions = document.getElementById('bulkActions');
      const selectedCount = document.getElementById('selectedCount');
      
      if (checkboxes.length > 0) {
        bulkActions.style.display = 'flex';
        selectedCount.textContent = checkboxes.length;
      } else {
        bulkActions.style.display = 'none';
      }
    }
  
    // Event delegation for checkbox changes
    document.getElementById('ordersTableBody').addEventListener('change', (e) => {
      if (e.target.classList.contains('order-checkbox')) {
        updateBulkActions();
        
        // Update "select all" checkbox
        const allCheckboxes = document.querySelectorAll('.order-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
        selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
      }
    });
  
    // Clear selection button
    document.getElementById('clearSelection').addEventListener('click', () => {
      const checkboxes = document.querySelectorAll('.order-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = false;
      });
      selectAllCheckbox.checked = false;
      updateBulkActions();
    });
  
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', (e) => {
      const searchTerm = e.target.value.toLowerCase();
      
      if (searchTerm.length > 0) {
        const filteredOrders = orders.filter(order => 
          order.id.toLowerCase().includes(searchTerm) ||
          order.customer.toLowerCase().includes(searchTerm) ||
          order.product.toLowerCase().includes(searchTerm)
        );
        
        // Render filtered orders
        renderFilteredOrders(filteredOrders);
      } else {
        // Reset to normal view
        currentPage = 1;
        renderOrdersTable();
      }
    });
  
    // Function to render filtered orders
    function renderFilteredOrders(filteredOrders) {
      const tableBody = document.getElementById('ordersTableBody');
      tableBody.innerHTML = '';
      
      document.getElementById('startIndex').textContent = '1';
      document.getElementById('endIndex').textContent = filteredOrders.length;
      document.getElementById('totalItems').textContent = filteredOrders.length;
      
      filteredOrders.forEach(order => {
        const row = document.createElement('tr');
        
        row.innerHTML = `
          <td>
            <input type="checkbox" class="order-checkbox" data-order-id="${order.id}">
          </td>
          <td>
            <a href="order-details.php?id=${order.id}" class="order-id">${order.id}</a>
          </td>
          <td>${order.customer}</td>
          <td>${order.product}</td>
          <td>${formatDate(order.orderDate)}</td>
          <td>${formatDate(order.deliveryDate)}</td>
          <td><span class="${getStatusBadgeClass(order.status)}">${order.status}</span></td>
          <td>${order.payment}</td>
          <td>${order.total}</td>
          <td>
            <div class="actions">
              <a href="order-details.php?id=${order.id}" title="View Details">
                <i class="fas fa-eye"></i>
              </a>
              <button title="Edit Order" class="edit-button" data-order-id="${order.id}">
                <i class="fas fa-edit"></i>
              </button>
              <button title="Delete Order" class="delete-button" data-order-id="${order.id}">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        `;
        
        tableBody.appendChild(row);
      });
      
      // Disable pagination for filtered results
      document.getElementById('prevPage').disabled = true;
      document.getElementById('prevPageMobile').disabled = true;
      document.getElementById('nextPage').disabled = true;
      document.getElementById('nextPageMobile').disabled = true;
      
      // Clear pagination numbers
      const paginationNav = document.getElementById('paginationNav');
      const existingButtons = paginationNav.querySelectorAll('.pagination-button-page');
      existingButtons.forEach(button => button.remove());
    }
  
    // Filter functionality
    document.getElementById('statusFilter').addEventListener('change', applyFilters);
    document.getElementById('productFilter').addEventListener('change', applyFilters);
    document.getElementById('dateFilter').addEventListener('change', applyFilters);
  
    function applyFilters() {
      const statusFilter = document.getElementById('statusFilter').value;
      const productFilter = document.getElementById('productFilter').value;
      const dateFilter = document.getElementById('dateFilter').value;
      
      let filteredOrders = [...orders];
      
      // Apply status filter
      if (statusFilter !== 'All Statuses') {
        filteredOrders = filteredOrders.filter(order => order.status === statusFilter);
      }
      
      // Apply product filter (simplified for demo)
      if (productFilter !== 'All Products') {
        filteredOrders = filteredOrders.filter(order => {
          if (productFilter === 'Cakes') {
            return order.product.includes('Cake');
          } else if (productFilter === 'Cupcakes') {
            return order.product.includes('Cupcake');
          } else if (productFilter === 'Breads') {
            return order.product.includes('Bread');
          } else if (productFilter === 'Custom Orders') {
            return order.product.includes('Custom');
          }
          return true;
        });
      }
      
      // Apply date filter (simplified for demo)
      if (dateFilter !== 'Last 30 Days') {
        const today = new Date();
        
        if (dateFilter === 'Last 7 Days') {
          const lastWeek = new Date(today);
          lastWeek.setDate(today.getDate() - 7);
          
          filteredOrders = filteredOrders.filter(order => {
            const orderDate = new Date(order.orderDate);
            return orderDate >= lastWeek && orderDate <= today;
          });
        } else if (dateFilter === 'Today') {
          const todayStr = today.toISOString().split('T')[0];
          filteredOrders = filteredOrders.filter(order => order.orderDate === todayStr);
        }
        // Additional date filters would be implemented here
      }
      
      if (filteredOrders.length > 0) {
        renderFilteredOrders(filteredOrders);
      } else {
        const tableBody = document.getElementById('ordersTableBody');
        tableBody.innerHTML = '<tr><td colspan="10" class="no-results">No orders match your filters</td></tr>';
        
        document.getElementById('startIndex').textContent = '0';
        document.getElementById('endIndex').textContent = '0';
        document.getElementById('totalItems').textContent = '0';
      }
    }
  
    // Initialize the table
    renderOrdersTable();
  });
  