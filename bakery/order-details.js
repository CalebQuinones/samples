document.addEventListener("DOMContentLoaded", () => {
    // DOM elements
    const editButton = document.getElementById("editButton")
    const updateStatusButton = document.getElementById("updateStatusButton")
    const updatePaymentButton = document.getElementById("updatePaymentButton")
    const statusContainer = document.getElementById("statusContainer")
    const statusModal = document.getElementById("statusModal")
    const statusSelect = document.getElementById("status")
    const messageTextarea = document.getElementById("message")
    const updateStatusConfirm = document.getElementById("updateStatusConfirm")
    const cancelStatusUpdate = document.getElementById("cancelStatusUpdate")
    const modalOverlay = document.getElementById('modalOverlay')
    const paymentModal = document.getElementById("paymentModal")
    const paymentStatusSelect = document.getElementById("paymentStatus")
    const paymentMethodSelect = document.getElementById("paymentMethod")
    const updatePaymentConfirm = document.getElementById("updatePaymentConfirm")
    const cancelPaymentUpdate = document.getElementById("cancelPaymentUpdate")
  
    let isEditing = false
  
    // Initialize modal overlay
    if (modalOverlay) {
        modalOverlay.style.display = 'none';
        modalOverlay.style.visibility = 'hidden';
    }
    document.body.style.overflow = 'auto';
  
    // Modal handling functions
    window.showModal = function(modal) {
        if (!modal) return;
        
        const modalOverlay = document.getElementById('modalOverlay');
        if (!modalOverlay) return;

        // Reset any existing modals
        document.querySelectorAll('.modal.active').forEach(m => {
            m.classList.remove('active');
        });

        // Show modal overlay first
        modalOverlay.style.display = 'flex';
        modalOverlay.style.visibility = 'visible';
        // Force reflow
        modalOverlay.offsetHeight;
        modalOverlay.classList.add('active');
        
        // Show modal
        modal.style.display = 'block';
        // Force reflow
        modal.offsetHeight;
        modal.classList.add('active');
        
        // Prevent body scrolling
        document.body.style.overflow = 'hidden';
    };

    window.closeModal = function(modal) {
        if (!modal) return;
        
        const modalOverlay = document.getElementById('modalOverlay');
        if (!modalOverlay) return;
        
        // Remove active classes
        modal.classList.remove('active');
        modalOverlay.classList.remove('active');
        
        // Hide modal after transition
        setTimeout(() => {
            modal.style.display = 'none';
            hideOverlayIfNoModal();
        }, 300);
        
        // Restore body scrolling
        document.body.style.overflow = '';
    };

    function hideOverlayIfNoModal() {
        const modalOverlay = document.getElementById('modalOverlay');
        if (!modalOverlay) return;
        
        const hasActiveModal = document.querySelector('.modal.active');
        if (!hasActiveModal) {
            modalOverlay.style.display = 'none';
            modalOverlay.style.visibility = 'hidden';
        }
    }

    // Close modal when clicking outside
    if (modalOverlay) {
        modalOverlay.addEventListener('click', (event) => {
            if (event.target === modalOverlay) {
                const openModal = document.querySelector('.modal.active');
                if (openModal) {
                    closeModal(openModal);
                    // Reset edit button state if status modal is closed
                    if (openModal === statusModal && editButton) {
                        isEditing = false;
                        editButton.innerHTML = '<i class="fas fa-edit"></i> Edit Order';
                        editButton.classList.remove("edit-button-cancel");
                        editButton.classList.add("edit-button-edit");
                        if (updateStatusButton) {
                            updateStatusButton.style.display = "none";
                        }
                    }
                }
            }
        });
    }
  
    // Edit button click handler
    if (editButton) {
        editButton.addEventListener("click", () => {
            isEditing = !isEditing;
    
            if (isEditing) {
                // Switch to editing mode
                editButton.innerHTML = '<i class="fas fa-times"></i> Cancel';
                editButton.classList.remove("edit-button-edit");
                editButton.classList.add("edit-button-cancel");
                if (updateStatusButton) updateStatusButton.style.display = "block";
                if (updatePaymentButton) updatePaymentButton.style.display = "block";
                // Show the status modal
                if (statusModal) {
                    showModal(statusModal);
                }
            } else {
                // Switch back to view mode
                editButton.innerHTML = '<i class="fas fa-edit"></i> Edit Order';
                editButton.classList.remove("edit-button-cancel");
                editButton.classList.add("edit-button-edit");
                if (updateStatusButton) updateStatusButton.style.display = "none";
                if (updatePaymentButton) updatePaymentButton.style.display = "none";
                // Close the status modal
                closeModal(statusModal);
                closeModal(paymentModal);
            }
        });
    }
  
    // Update status button click handler
    if (updateStatusButton) {
        updateStatusButton.addEventListener("click", () => {
            if (statusModal) {
                showModal(statusModal);
            }
        });
    }
  
    // Update payment button click handler
    if (updatePaymentButton) {
        updatePaymentButton.addEventListener("click", () => {
            if (paymentModal) {
                showModal(paymentModal);
            }
        });
    }
  
    // Cancel status update button click handler
    if (cancelStatusUpdate) {
        cancelStatusUpdate.addEventListener("click", () => {
            closeModal(statusModal);
            // Reset edit button state
            if (editButton) {
                isEditing = false;
                editButton.innerHTML = '<i class="fas fa-edit"></i> Edit Order';
                editButton.classList.remove("edit-button-cancel");
                editButton.classList.add("edit-button-edit");
                if (updateStatusButton) {
                    updateStatusButton.style.display = "none";
                }
            }
        });
    }
  
    // Cancel payment update button click handler
    if (cancelPaymentUpdate) {
        cancelPaymentUpdate.addEventListener("click", () => {
            closeModal(paymentModal);
        });
    }
  
    // Update status confirm button click handler
    if (updateStatusConfirm) {
        updateStatusConfirm.addEventListener("click", () => {
            const newStatus = statusSelect.value;
            const newPaymentStatus = paymentStatusSelect.value;
            const message = messageTextarea.value;
    
            // Update the status badge
            if (statusContainer) {
                statusContainer.innerHTML = "";
                const statusBadge = document.createElement("span");
                statusBadge.className = `status-badge status-${newStatus.toLowerCase().replace(" ", "-")}`;
                statusBadge.textContent = newStatus;
                statusContainer.appendChild(statusBadge);
            }

            // Update payment info
            const paymentStatusElement = document.getElementById("paymentStatus");
            if (paymentStatusElement) paymentStatusElement.textContent = newPaymentStatus;
    
            // Close the modal
            closeModal(statusModal);
            
            // Reset edit button state
            if (editButton) {
                isEditing = false;
                editButton.innerHTML = '<i class="fas fa-edit"></i> Edit Order';
                editButton.classList.remove("edit-button-cancel");
                editButton.classList.add("edit-button-edit");
                if (updateStatusButton) {
                    updateStatusButton.style.display = "none";
                }
            }
    
            alert(`Order Updated\nStatus: ${newStatus}\nPayment Status: ${newPaymentStatus}${message ? "\nMessage: " + message : ""}`);
        });
    }
  
    function getOrderIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get("id");
    }
  
    // Fetch and populate order details
    async function loadOrderDetails() {
        const orderId = getOrderIdFromUrl();
        if (!orderId) {
            alert("No order ID found in URL");
            return;
        }

        // Show loading states
        document.querySelectorAll('.order-info-value, #orderStatus, .customer-info-text').forEach(el => {
            el.textContent = 'Loading...';
        });

        try {
            const response = await fetch(`get_order.php?id=${orderId}`);
            
            if (!response.ok) {
                throw new Error(`Failed to load order (Status: ${response.status})`);
            }

            const responseText = await response.text();
            
            try {
                const data = JSON.parse(responseText);
                
                if (!data.success || !data.order || !data.items) {
                    throw new Error(data.message || "Invalid data received from server");
                }

                const { order, items } = data;
                
                // Clear loading states before populating
                document.querySelectorAll('.order-info-value, #orderStatus, .customer-info-text').forEach(el => {
                    el.textContent = '';
                });

                // Populate all sections
                populateOrderInfo(order);
                populateCustomerInfo(order);
                populateShippingInfo(order);
                populatePaymentInfo(order);
                populateDeliveryInfo(order);
                populateOrderItems(items, order);

            } catch (jsonError) {
                console.error('Server response:', responseText);
                throw new Error('Invalid response format from server');
            }
        } catch (err) {
            // Show error state
            document.querySelectorAll('.order-info-value, #orderStatus, .customer-info-text').forEach(el => {
                el.textContent = 'Error loading data';
                el.style.color = 'red';
            });
            console.error("Error loading order details:", err);
            alert("Failed to load order details: " + err.message);
        }
    }
  
    function populateOrderInfo(order) {
        const elements = {
            "orderId": order.order_id,
            "orderIdValue": `#${String(order.order_id).padStart(3, '0')}`,
            "orderDate": new Date(order.created_at).toLocaleDateString(),
            "orderStatus": order.status,
            "paymentMethod": order.payment_method || 'N/A',
            "deliveryDate": order.delivery_date ? new Date(order.delivery_date).toLocaleDateString() : 'N/A',
            "deliveryMethod": order.delivery_method || 'N/A'
        };
        
        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                if (id === "orderStatus") {
                    // Capitalize status value
                    element.textContent = value.charAt(0).toUpperCase() + value.slice(1);
                    element.className = `status-badge status-${value.toLowerCase().replace(/\s/g, '-')}`;
                } else {
                    // Capitalize other values if they're strings
                    element.textContent = typeof value === 'string' ? 
                        value.charAt(0).toUpperCase() + value.slice(1) : 
                        value;
                }
            }
        });
    }
  
    function populateCustomerInfo(order) {
        const elements = {
            "customerName": order.customer_name,
            "customerSince": 'Customer Since ' + new Date(order.created_at).toLocaleDateString(),
            "customerPhone": order.phone || 'N/A',
            "customerEmail": order.email || 'N/A',
            "customerAddress": order.address || 'N/A'
        };
        
        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        });
    }
  
    function populateShippingInfo(order) {
        const elements = {
            "shippingName": order.fullname || order.customer_name || 'N/A',
            "shippingAddress": order.delivery_address || 'N/A'
        };
        
        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        });
    }
  
    function populatePaymentInfo(order) {
        const elements = {
            "paymentType": (order.payment_method || 'N/A').charAt(0).toUpperCase() + (order.payment_method || 'N/A').slice(1),
            "paymentDate": new Date(order.created_at).toLocaleDateString(),
            "paymentStatus": (order.payment_status || 'Pending').charAt(0).toUpperCase() + (order.payment_status || 'Pending').slice(1)
        };
        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        });
    }
  
    function populateDeliveryInfo(order) {
        const elements = {
            "deliveryType": (order.delivery_method || 'N/A').charAt(0).toUpperCase() + (order.delivery_method || 'N/A').slice(1),
            "deliveryCarrier": 'Standard Delivery',
            "deliveryDateInfo": order.delivery_date ? new Date(order.delivery_date).toLocaleDateString() : 'N/A'
        };
        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        });
    }
  
    function populateOrderItems(items, order) {
        const tbody = document.getElementById("orderItemsBody");
        if (!tbody) return;
        tbody.innerHTML = '';
        let subtotal = 0;
        items.forEach(item => {
            const tr = document.createElement("tr");
            const total = (item.price * item.quantity);
            subtotal += total;
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
                <td>₱${total.toFixed(2)}</td>
            `;
            tbody.appendChild(tr);
        });
        // Update order totals
        const tfoot = document.getElementById("orderItemsFooter");
        if (tfoot) {
            const deliveryFee = parseFloat(order.delivery_fee).toFixed(2);

            const totalAmount = (subtotal + parseFloat(order.delivery_fee)).toFixed(2);
            tfoot.innerHTML = `
                <tr>
                    <td colspan="2"></td>
                    <td style="font-size: 0.875rem; font-weight: 500; color: var(--gray-700);">Subtotal</td>
                    <td style="font-size: 0.875rem; font-weight: 500; color: var(--gray-700);">₱${subtotal.toFixed(2)}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td style="font-size: 0.875rem; font-weight: 500; color: var(--gray-700);">Delivery Fee</td>
                    <td style="font-size: 0.875rem; font-weight: 500; color: var(--gray-700);">₱${deliveryFee}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td style="font-size: 0.875rem; font-weight: 700; color: var(--gray-900);">Total</td>
                    <td style="font-size: 0.875rem; font-weight: 700; color: var(--gray-900);">₱${totalAmount}</td>
                </tr>
            `;
        }
    }
    // Load order details when the page loads
    loadOrderDetails().catch(err => {
        console.error("Failed to initialize order details:", err);
    });
}); // End of DOMContentLoaded event listener
