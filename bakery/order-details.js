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
    const modalOverlay = document.getElementById('modalOverlay')
  
    let isEditing = false
  
    // Initialize modal overlay
    if (modalOverlay) {
        modalOverlay.style.display = 'none';
        modalOverlay.classList.remove('active');
    }
    document.body.style.overflow = 'auto';
  
    // Edit button click handler
    if (editButton) {
        editButton.addEventListener("click", () => {
            isEditing = !isEditing;
    
            if (isEditing) {
                // Switch to editing mode
                editButton.innerHTML = '<i class="fas fa-times"></i> Cancel';
                editButton.classList.remove("edit-button-edit");
                editButton.classList.add("edit-button-cancel");
                if (updateStatusButton) {
                    updateStatusButton.style.display = "block";
                }
                // Show the status modal
                if (statusModal) {
                    statusModal.style.display = "block";
                    if (modalOverlay) {
                        modalOverlay.style.display = "flex";
                        modalOverlay.style.visibility = "visible";
                        setTimeout(() => {
                            statusModal.classList.add("active");
                            modalOverlay.classList.add("active");
                        }, 10);
                    }
                    document.body.style.overflow = "hidden";
                }
            } else {
                // Switch back to view mode
                editButton.innerHTML = '<i class="fas fa-edit"></i> Edit Order';
                editButton.classList.remove("edit-button-cancel");
                editButton.classList.add("edit-button-edit");
                if (updateStatusButton) {
                    updateStatusButton.style.display = "none";
                }
                // Close the status modal
                closeModal(statusModal);
            }
        });
    }
  
    // Update status button click handler
    if (updateStatusButton) {
        updateStatusButton.addEventListener("click", () => {
            if (statusModal) {
                statusModal.style.display = "block";
                if (modalOverlay) {
                    modalOverlay.style.display = "flex";
                    modalOverlay.style.visibility = "visible";
                    setTimeout(() => {
                        statusModal.classList.add("active");
                        modalOverlay.classList.add("active");
                    }, 10);
                }
                document.body.style.overflow = "hidden";
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
  
    // Update status confirm button click handler
    if (updateStatusConfirm) {
        updateStatusConfirm.addEventListener("click", () => {
            const newStatus = statusSelect.value;
            const message = messageTextarea.value;
    
            // Update the status badge
            if (statusContainer) {
                statusContainer.innerHTML = "";
                const statusBadge = document.createElement("span");
                statusBadge.className = `status-badge status-${newStatus.toLowerCase().replace(" ", "-")}`;
                statusBadge.textContent = newStatus;
                statusContainer.appendChild(statusBadge);
            }
    
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
    
            alert(`Order status updated to: ${newStatus}${message ? "\nMessage: " + message : ""}`);
        });
    }
  
    // Close modal when clicking outside
    if (modalOverlay) {
        modalOverlay.addEventListener("click", (event) => {
            if (event.target === modalOverlay) {
                const openModal = document.querySelector('.modal.active, .modal-container.active');
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
  
    function closeModal(modal) {
        if (modal) {
            modal.classList.remove("active");
            if (modalOverlay) {
                modalOverlay.classList.remove("active");
                setTimeout(() => {
                    modal.style.display = "none";
                    modalOverlay.style.display = "none";
                    modalOverlay.style.visibility = "hidden";
                    hideOverlayIfNoModal();
                }, 300);
            }
        }
    }
  
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
  
    function getOrderIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get("id");
    }
  
    // Fetch and populate order details
    async function loadOrderDetails() {
        const orderId = getOrderIdFromUrl();
        if (!orderId) return;
        
        try {
            const response = await fetch(`get_order.php?id=${orderId}`);
            const data = await response.json();
            
            if (!data.success) {
                alert(data.message || "Order not found.");
                return;
            }
            
            const order = data.order;
            const items = data.items;
            
            // Populate order info
            populateOrderInfo(order);
            populateCustomerInfo(order);
            populateShippingInfo(order);
            populatePaymentInfo(order);
            populateDeliveryInfo(order);
            populateOrderItems(items);
            
            // Hide custom cake details by default
            const customCakeDetails = document.getElementById("customCakeDetails");
            if (customCakeDetails) {
                customCakeDetails.style.display = "none";
            }
        } catch (err) {
            console.error("Error loading order details:", err);
            alert("Failed to load order details.");
        }
    }
  
    function populateOrderInfo(order) {
        const elements = {
            "orderId": order.order_id,
            "orderIdValue": `#ORD-${String(order.order_id).padStart(3, '0')}`,
            "orderDate": order.created_at,
            "orderStatus": order.status,
            "paymentMethod": order.payment_method || 'N/A',
            "deliveryDate": order.delivery_date || 'N/A',
            "deliveryMethod": order.delivery_method || 'N/A'
        };
        
        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                if (id === "orderStatus") {
                    element.textContent = value;
                    element.className = `status-badge status-${value.toLowerCase().replace(/\s/g, '-')}`;
                } else {
                    element.textContent = value;
                }
            }
        });
    }
  
    function populateCustomerInfo(order) {
        const elements = {
            "customerName": order.customer_name,
            "customerSince": order.customer_since ? `Customer since ${new Date(order.customer_since).toLocaleDateString()}` : '',
            "customerPhone": order.Pnum || 'N/A',
            "customerEmail": order.email || 'N/A'
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
            "shippingName": order.customer_name,
            "shippingAddress1": order.shipping_address1 || 'N/A',
            "shippingAddress2": order.shipping_address2 || '',
            "shippingCity": order.shipping_city || '',
            "shippingCountry": order.shipping_country || ''
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
            "paymentType": order.payment_method || 'N/A',
            "paymentTransaction": order.payment_transaction_id || '',
            "paymentDate": order.payment_date || '',
            "paymentStatus": order.payment_status || ''
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
            "deliveryType": order.delivery_method || 'N/A',
            "deliveryCarrier": order.delivery_carrier || '',
            "deliveryDateInfo": order.delivery_date || '',
            "deliveryTime": order.delivery_time || ''
        };
        
        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        });
    }
  
    function populateOrderItems(items) {
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
            `;
        }
    }
  
    // Load order details when the page loads
    loadOrderDetails();
});
  