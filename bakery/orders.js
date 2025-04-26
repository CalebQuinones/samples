document.addEventListener("DOMContentLoaded", () => {
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
      const checkboxes = document.querySelectorAll(".order-checkbox")
      checkboxes.forEach((checkbox) => {
        checkbox.checked = selectAll.checked
        const orderId = checkbox.value
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
      closeStatusModal.addEventListener('click', closeModal)
    }
    
    if (cancelStatusUpdate) {
      cancelStatusUpdate.addEventListener('click', closeModal)
    }
    
    if (statusUpdateForm) {
      statusUpdateForm.addEventListener('submit', handleStatusUpdate)
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
      if (selectedOrders.length > 0) {
        bulkActions.style.display = "block"
        selectedCount.textContent = selectedOrders.length
      } else {
        bulkActions.style.display = "none"
      }
    }
  
    function openStatusModal(orderId) {
      if (statusUpdateModal && updateOrderId) {
        updateOrderId.value = orderId
        statusUpdateModal.style.display = "block"
      }
    }
  
    function closeModal() {
      if (statusUpdateModal) {
        statusUpdateModal.style.display = "none"
      }
    }
  
    function handleStatusUpdate(e) {
      e.preventDefault()
      
      const orderId = updateOrderId.value
      const status = document.getElementById('newStatus').value
      const paymentStatus = document.getElementById('newPaymentStatus').value
      
      // Send AJAX request to update order status
      fetch('update_order_status.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          order_id: orderId,
          status: status,
          payment_status: paymentStatus
        }),
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update the status in the table
          const row = document.querySelector(`tr[data-order-id="${orderId}"]`)
          if (row) {
            const statusCell = row.querySelector('.status-badge')
            if (statusCell) {
              // Remove old status class
              statusCell.classList.remove('status-completed', 'status-in-progress', 'status-pending', 'status-cancelled')
              
              // Add new status class
              let newStatusClass = 'status-pending'
              switch (status) {
                case 'Completed':
                  newStatusClass = 'status-completed'
                  break
                case 'In Progress':
                  newStatusClass = 'status-in-progress'
                  break
                case 'Pending':
                  newStatusClass = 'status-pending'
                  break
                case 'Cancelled':
                  newStatusClass = 'status-cancelled'
                  break
              }
              
              statusCell.classList.add(newStatusClass)
              statusCell.textContent = status
            }
            
            // Update payment status
            const paymentCell = row.querySelector('td:nth-child(7)')
            if (paymentCell) {
              paymentCell.textContent = paymentStatus
            }
          }
          
          // Close the modal
          closeModal()
          
          // Show success message
          alert('Order status updated successfully!')
      } else {
          alert('Failed to update order status: ' + data.message)
      }
      })
      .catch(error => {
        console.error('Error:', error)
        alert('An error occurred while updating the order status')
      })
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
    
    // Add event listeners to edit buttons
    document.addEventListener('click', function(e) {
      if (e.target.closest('.edit-button')) {
        const button = e.target.closest('.edit-button')
        const orderId = button.getAttribute('data-order-id')
        openStatusModal(orderId)
      }
    })
})
  