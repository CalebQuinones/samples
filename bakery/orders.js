document.addEventListener("DOMContentLoaded", () => {
    var modalOverlay = document.getElementById('modalOverlay');
    if (modalOverlay) {
        modalOverlay.style.display = 'none';
        modalOverlay.classList.remove('active');
    }
    document.body.style.overflow = 'auto';
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
    const statusUpdateModal = document.getElementById('statusUpdateModal')
    const closeStatusModal = document.getElementById('closeStatusModal')
    const cancelStatusUpdate = document.getElementById('cancelStatusUpdate')
    const statusUpdateForm = document.getElementById('statusUpdateForm')
    const updateOrderId = document.getElementById('updateOrderId')
  
    // Pagination state
    let currentPage = 1
    const itemsPerPage = 5
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
      const checkboxes = document.querySelectorAll(".order-checkbox");
      checkboxes.forEach((checkbox) => {
        checkbox.checked = selectAll.checked;
      });
      updateBulkActions();
    });
  
    // Keep selectAll in sync with row checkboxes
    const rowOrderCheckboxes = document.querySelectorAll(".order-checkbox");
    rowOrderCheckboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", () => {
        if (!checkbox.checked) {
          selectAll.checked = false;
        } else {
          const allChecked = Array.from(document.querySelectorAll(".order-checkbox")).every(cb => cb.checked);
          selectAll.checked = allChecked;
        }
        updateBulkActions();
      });
    });
  
    clearSelection.addEventListener("click", () => {
      selectedOrders = []
      selectAll.checked = false
      const checkboxes = document.querySelectorAll(".order-checkbox")
      checkboxes.forEach((checkbox) => {
        checkbox.checked = false
      })
      updateBulkActions()
    })
  
    if (prevPage) {
    prevPage.addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--
        updateTable()
      }
    })
    }
  
    if (nextPage) {
    nextPage.addEventListener("click", () => {
        const totalPages = Math.ceil(getVisibleRows().length / itemsPerPage)
      if (currentPage < totalPages) {
        currentPage++
        updateTable()
      }
    })
    }
  
    if (prevPageMobile) {
    prevPageMobile.addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--
        updateTable()
      }
    })
    }
  
    if (nextPageMobile) {
    nextPageMobile.addEventListener("click", () => {
        const totalPages = Math.ceil(getVisibleRows().length / itemsPerPage)
      if (currentPage < totalPages) {
        currentPage++
        updateTable()
      }
    })
    }
  
    if (closeStatusModal) {
      closeStatusModal.addEventListener('click', () => closeModal(statusUpdateModal))
    }
    
    if (cancelStatusUpdate) {
      cancelStatusUpdate.addEventListener('click', () => closeModal(statusUpdateModal))
    }
    
    if (statusUpdateForm) {
      statusUpdateForm.addEventListener('submit', handleStatusUpdate)
    }
  
    // Add event listeners for action buttons
    if (ordersTableBody) {
        ordersTableBody.addEventListener('click', function(e) {
            const target = e.target.closest('.action-button');
            if (!target) return;

            const orderId = target.getAttribute('data-order-id');
            
            if (target.classList.contains('edit-button')) {
                openStatusModal(orderId);
            } else if (target.classList.contains('archive-button')) {
                if (confirm('Are you sure you want to archive this order?')) {
                    archiveOrder(orderId);
                }
            }
        });
    }
  
    // Close modal when clicking outside
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                const openModal = document.querySelector('.modal.active, .modal-container.active');
                if (openModal) {
                    closeModal(openModal);
                }
            }
        });
    }
  
    // Functions
    function updateTable() {
      // Get all rows
      const rows = getVisibleRows()
  
      // Update pagination info
      const totalPages = Math.ceil(rows.length / itemsPerPage)
      const start = (currentPage - 1) * itemsPerPage + 1
      const end = Math.min(start + itemsPerPage - 1, rows.length)
  
      if (startIndex) startIndex.textContent = rows.length > 0 ? start : 0
      if (endIndex) endIndex.textContent = end
      if (totalItems) totalItems.textContent = rows.length
  
      // Update pagination buttons
      if (prevPage) prevPage.disabled = currentPage === 1
      if (nextPage) nextPage.disabled = currentPage === totalPages || totalPages === 0
      if (prevPageMobile) prevPageMobile.disabled = currentPage === 1
      if (nextPageMobile) nextPageMobile.disabled = currentPage === totalPages || totalPages === 0
  
      // Generate pagination numbers
      if (paginationNav) {
      paginationNav.innerHTML = ""
        if (prevPage) paginationNav.appendChild(prevPage)
  
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
  
        if (nextPage) paginationNav.appendChild(nextPage)
      }
  
      // Show/hide rows based on pagination
      rows.forEach((row, index) => {
        const startIndex = (currentPage - 1) * itemsPerPage
        const endIndex = startIndex + itemsPerPage
        row.style.display = (index >= startIndex && index < endIndex) ? "" : "none"
      })
    }
  
    function getVisibleRows() {
      const searchTerm = searchInput.value.toLowerCase()
      const statusFilterValue = statusFilter.value
      const productFilterValue = productFilter.value
      
      // Get all rows
      const allRows = Array.from(ordersTableBody.querySelectorAll('tr'))
      
      // Filter rows based on search and filters
      return allRows.filter(row => {
        // Skip the "no orders found" row
        if (row.classList.contains('no-orders')) {
          return true
        }
        
        const orderId = row.querySelector('.order-id')?.textContent || ''
        const customer = row.querySelector('td:nth-child(3)')?.textContent || ''
        const status = row.querySelector('.status-badge')?.textContent || ''
        
        // Check if row matches search term
        const matchesSearch = 
          orderId.toLowerCase().includes(searchTerm) || 
          customer.toLowerCase().includes(searchTerm)
        
        // Check if row matches status filter
        const matchesStatus = 
          statusFilterValue === 'All Statuses' || 
          status === statusFilterValue
        
        // For now, we'll skip product filter as it's not in the current table structure
        const matchesProduct = true
        
        return matchesSearch && matchesStatus && matchesProduct
      })
    }
  
    function updateBulkActions() {
      const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
      if (bulkActions) {
        bulkActions.style.display = checkedCount > 0 ? 'flex' : 'none';
        if (selectedCount) selectedCount.textContent = checkedCount;
      }
    }
  
    // Helper to hide overlay if no modal is open
    function hideOverlayIfNoModal() {
        const anyOpen = Array.from(document.querySelectorAll('.modal, .modal-container'))
            .some(m => m.classList.contains('active') || m.style.display === 'block' || m.style.display === 'flex');
        if (modalOverlay) {
            if (!anyOpen) {
                modalOverlay.style.display = 'none';
                modalOverlay.style.visibility = 'hidden';
                modalOverlay.classList.remove('active');
                document.body.style.overflow = 'auto';
            } else {
                modalOverlay.style.display = 'flex';
                modalOverlay.style.visibility = 'visible';
                modalOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }
    }
  
    function openStatusModal(orderId) {
        if (statusUpdateModal && updateOrderId) {
            updateOrderId.value = orderId;
            // Get current order status and payment status
            const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
            const currentStatus = row.querySelector('.status-badge').textContent.toLowerCase();
            const currentPayment = row.querySelector('td:nth-child(8)').textContent;
            
            // Set current values in modal
            document.getElementById('newStatus').value = currentStatus;
            document.getElementById('newPaymentStatus').value = currentPayment;
            
            statusUpdateModal.style.display = 'block';
            if (modalOverlay) {
                modalOverlay.style.display = 'flex';
                modalOverlay.style.visibility = 'visible';
                setTimeout(() => {
                    statusUpdateModal.classList.add('active');
                    modalOverlay.classList.add('active');
                }, 10);
            }
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modal) {
        if (modal) {
            modal.classList.remove('active');
            if (modalOverlay) {
                modalOverlay.classList.remove('active');
                setTimeout(() => {
                    modal.style.display = 'none';
                    modalOverlay.style.display = 'none';
                    modalOverlay.style.visibility = 'hidden';
                    hideOverlayIfNoModal();
                }, 300);
            }
        }
    }
  
    function handleStatusUpdate(e) {
        e.preventDefault();
        
        const orderId = updateOrderId.value;
        const status = document.getElementById('newStatus').value.toLowerCase();
        const paymentStatus = document.getElementById('newPaymentStatus').value;
        const message = document.getElementById('message')?.value || '';
        
        fetch('update_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                order_id: orderId,
                status: status,
                payment_status: paymentStatus,
                message: message
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                if (row) {
                    const statusCell = row.querySelector('.status-badge');
                    if (statusCell) {
                        // Remove all possible status classes
                        statusCell.classList.remove(
                            'status-pending',
                            'status-processing',
                            'status-shipped',
                            'status-delivered',
                            'status-cancelled'
                        );
                        
                        // Map status values to display classes
                        const statusMappings = {
                            'pending': { class: 'status-pending', display: 'Pending' },
                            'processing': { class: 'status-processing', display: 'Processing' },
                            'shipped': { class: 'status-shipped', display: 'Shipped' },
                            'delivered': { class: 'status-delivered', display: 'Delivered' },
                            'cancelled': { class: 'status-cancelled', display: 'Cancelled' }
                        };
                        
                        const statusInfo = statusMappings[status] || statusMappings['pending'];
                        statusCell.classList.add(statusInfo.class);
                        statusCell.textContent = statusInfo.display;
                    }
                    
                    // Update payment status
                    const paymentCell = row.querySelector('td:nth-child(8)');
                    if (paymentCell) {
                        paymentCell.textContent = paymentStatus;
                    }
                }
                
                closeModal(statusUpdateModal);
                alert('Order status updated successfully!');
            } else {
                alert('Failed to update order status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the order status');
        });
    }
  
    // Replace deleteOrder function with archiveOrder
    function archiveOrder(orderId) {
        fetch('archive_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ order_id: orderId })
        })
        .then(response => response.json())
        .then(data => {  // Fixed missing parentheses
            if (data.success) {
                const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                if (row) {
                    row.remove();
                    // Get current visible rows after removal
                    const remainingRows = getVisibleRows();
                    // If current page is now empty but there are still rows, go to previous page
                    if (remainingRows.length > 0 && 
                        !document.querySelector(`tr[style=""]`)) {
                        currentPage = Math.max(1, currentPage - 1);
                    }
                    updateTable();
                }
                alert('Order archived successfully!');
            } else {
                alert('Error archiving order: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while archiving the order');
        });
    }
  
    // Add event listeners to order checkboxes
    document.addEventListener('change', function(e) {
      if (e.target.classList.contains('order-checkbox')) {
        const orderId = e.target.value
        
        if (e.target.checked) {
          if (!selectedOrders.includes(orderId)) {
            selectedOrders.push(orderId)
          }
        } else {
          selectedOrders = selectedOrders.filter(id => id !== orderId)
        }
        
        updateBulkActions()
      }
    })
})
