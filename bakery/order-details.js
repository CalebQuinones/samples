document.addEventListener("DOMContentLoaded", () => {
    // DOM elements
    const editButton = document.getElementById("editButton")
    const updateStatusButton = document.getElementById("updateStatusButton")
    const statusContainer = document.getElementById("statusContainer")
    const statusModal = document.getElementById("statusModal")
    const statusSelect = document.getElementById("status")
    const messageTextarea = document.getElementById("message")
    const updateStatusConfirm = document.getElementById("updateStatusConfirm")
    const cancelStatusUpdate = document.getElementById("cancelStatusUpdate")
  
    let isEditing = false
  
    editButton.addEventListener("click", () => {
      isEditing = !isEditing
  
      if (isEditing) {
        // Switch to editing mode
        editButton.innerHTML = '<i class="fas fa-times"></i> Cancel'
        editButton.classList.remove("edit-button-edit")
        editButton.classList.add("edit-button-cancel")
        updateStatusButton.style.display = "block"
      } else {
        // Switch back to view mode
        editButton.innerHTML = '<i class="fas fa-edit"></i> Edit Order'
        editButton.classList.remove("edit-button-cancel")
        editButton.classList.add("edit-button-edit")
        updateStatusButton.style.display = "none"
      }
    })
  
    updateStatusButton.addEventListener("click", () => {
      statusModal.style.display = "flex"
    })
  
    cancelStatusUpdate.addEventListener("click", () => {
      statusModal.style.display = "none"
    })
  
    updateStatusConfirm.addEventListener("click", () => {
      const newStatus = statusSelect.value
      const message = messageTextarea.value
  
      // Update the status badge
      statusContainer.innerHTML = ""
      const statusBadge = document.createElement("span")
      statusBadge.className = `status-badge status-${newStatus.toLowerCase().replace(" ", "-")}`
      statusBadge.textContent = newStatus
      statusContainer.appendChild(statusBadge)
  
      // Close the modal
      statusModal.style.display = "none"
  
      alert(`Order status updated to: ${newStatus}${message ? "\nMessage: " + message : ""}`)
    })
  
    // Close modal when clicking outside
    window.addEventListener("click", (event) => {
      if (event.target === statusModal) {
        statusModal.style.display = "none"
      }
    })
  
    function getOrderIdFromUrl() {
      const urlParams = new URLSearchParams(window.location.search)
      return urlParams.get("id")
    }
  
    // Fetch and populate order details
    async function loadOrderDetails() {
      const orderId = getOrderIdFromUrl()
      if (!orderId) return
      try {
        const response = await fetch(`get_order.php?id=${orderId}`)
        const data = await response.json()
        if (!data.success) {
          alert(data.message || "Order not found.")
          return
        }
        const order = data.order
        const items = data.items
        // Populate order info
        document.getElementById("orderId").textContent = order.order_id
        document.getElementById("orderIdValue").textContent = `#ORD-${String(order.order_id).padStart(3, '0')}`
        document.getElementById("orderDate").textContent = order.created_at
        document.getElementById("orderStatus").textContent = order.status
        document.getElementById("orderStatus").className = `status-badge status-${order.status.toLowerCase().replace(/\s/g, '-')}`
        document.getElementById("paymentMethod").textContent = order.payment_method || 'N/A'
        document.getElementById("deliveryDate").textContent = order.delivery_date || 'N/A'
        document.getElementById("deliveryMethod").textContent = order.delivery_method || 'N/A'

        // Customer info
        document.getElementById("customerName").textContent = order.customer_name
        document.getElementById("customerSince").textContent = order.customer_since ? `Customer since ${new Date(order.customer_since).toLocaleDateString()}` : ''
        document.getElementById("customerPhone").textContent = order.Pnum || 'N/A'
        document.getElementById("customerEmail").textContent = order.email || 'N/A'

        // Shipping info (if available)
        document.getElementById("shippingName").textContent = order.customer_name
        document.getElementById("shippingAddress1").textContent = order.shipping_address1 || 'N/A'
        document.getElementById("shippingAddress2").textContent = order.shipping_address2 || ''
        document.getElementById("shippingCity").textContent = order.shipping_city || ''
        document.getElementById("shippingCountry").textContent = order.shipping_country || ''

        // Payment info (if available)
        document.getElementById("paymentType").textContent = order.payment_method || 'N/A'
        document.getElementById("paymentTransaction").textContent = order.payment_transaction_id || ''
        document.getElementById("paymentDate").textContent = order.payment_date || ''
        document.getElementById("paymentStatus").textContent = order.payment_status || ''

        // Delivery info (if available)
        document.getElementById("deliveryType").textContent = order.delivery_method || 'N/A'
        document.getElementById("deliveryCarrier").textContent = order.delivery_carrier || ''
        document.getElementById("deliveryDateInfo").textContent = order.delivery_date || ''
        document.getElementById("deliveryTime").textContent = order.delivery_time || ''

        // Order items
        const tbody = document.getElementById("orderItemsBody")
        tbody.innerHTML = ''
        let subtotal = 0
        items.forEach(item => {
          const tr = document.createElement("tr")
          const total = (item.price * item.quantity)
          subtotal += total
          tr.innerHTML = `
            <td>
              <div style="display: flex; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 6px; background-color: var(--pink-100); display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                  <i class="fas fa-birthday-cake" style="color: var(--pink-600);"></i>
                </div>
                <div>
                  <div style="font-size: 0.875rem; font-weight: 500; color: var(--gray-900);">${item.product_name}</div>
                  <div style="font-size: 0.875rem; color: var(--gray-500);">${item.category || ''}</div>
                </div>
              </div>
            </td>
            <td>₱${parseFloat(item.price).toFixed(2)}</td>
            <td>${item.quantity}</td>
            <td>₱${(total).toFixed(2)}</td>
          `
          tbody.appendChild(tr)
        })
        // Order totals
        const tfoot = document.getElementById("orderItemsFooter")
        tfoot.innerHTML = `
          <tr>
            <td colspan="2"></td>
            <td style="font-size: 0.875rem; font-weight: 500; color: var(--gray-700);">Subtotal</td>
            <td style="font-size: 0.875rem; font-weight: 500; color: var(--gray-700);">₱${subtotal.toFixed(2)}</td>
          </tr>
          <tr>
            <td colspan="2"></td>
            <td style="font-size: 0.875rem; font-weight: 500; color: var(--gray-700);">Tax</td>
            <td style="font-size: 0.875rem; font-weight: 500; color: var(--gray-700);">₱0.00</td>
          </tr>
          <tr>
            <td colspan="2"></td>
            <td style="font-size: 0.875rem; font-weight: 500; color: var(--gray-700);">Delivery Fee</td>
            <td style="font-size: 0.875rem; font-weight: 500; color: var(--gray-700);">₱0.00</td>
          </tr>
          <tr>
            <td colspan="2"></td>
            <td style="font-size: 0.875rem; font-weight: 700; color: var(--gray-900);">Total</td>
            <td style="font-size: 0.875rem; font-weight: 700; color: var(--gray-900);">₱${subtotal.toFixed(2)}</td>
          </tr>
        `

        // Custom Cake Details (if any)
        // You can add logic here to show/hide and populate #customCakeDetails and #customCakeContent
        // For now, hide by default
        document.getElementById("customCakeDetails").style.display = "none"
      } catch (err) {
        alert("Failed to load order details.")
      }
    }

    loadOrderDetails()
  })
  