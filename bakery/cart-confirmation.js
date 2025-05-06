document.addEventListener("DOMContentLoaded", () => {
    console.log("Initializing cart confirmation...")
    
    // Get references to all necessary elements
    const paymentForm = document.getElementById("paymentForm")
    const modalOverlay = document.getElementById("modalOverlay")
    const checkoutModal = document.getElementById("checkoutModal")
    const orderConfirmationModal = document.getElementById("orderConfirmationModal")
    const closeCheckout = document.getElementById("closeCheckout")
    const backToCartBtn = document.querySelector(".back-to-cart")
    const orderItemsList = document.getElementById("orderItemsList")
    const orderTotal = document.getElementById("orderTotal")
    const cartCount = document.querySelector(".cart-count")
  
    // Close checkout modal handler
    if (closeCheckout) {
        closeCheckout.addEventListener("click", () => {
            if (modalOverlay && checkoutModal) {
                modalOverlay.classList.remove("active")
                checkoutModal.classList.remove("active")
                setTimeout(() => {
                    modalOverlay.style.display = "none"
                    checkoutModal.style.display = "none"
                    document.body.style.overflow = "auto"
                }, 300)
            }
        })
    }
  
    // Back to cart button handler
    if (backToCartBtn) {
        backToCartBtn.addEventListener("click", () => {
            // Close checkout modal
            if (modalOverlay && checkoutModal) {
                modalOverlay.classList.remove("active")
                checkoutModal.classList.remove("active")
                setTimeout(() => {
                    modalOverlay.style.display = "none"
                    checkoutModal.style.display = "none"
                }, 300)
            }
            
            // Open cart popup
            const cartPopup = document.getElementById("cartPopup")
            if (cartPopup) {
                cartPopup.style.display = "block"
                setTimeout(() => {
                    cartPopup.classList.add("active")
                }, 10)
            }
        })
    }
  
    // Function to update checkout items
    function updateCheckoutItems() {
        if (orderItemsList) {
            orderItemsList.innerHTML = ""

            const cart = window.getCart()
            if (!cart || cart.length === 0) {
                orderItemsList.innerHTML = '<p class="empty-cart-message">Your cart is empty</p>'
                return
            }

            cart.forEach((item) => {
                const cartItem = document.createElement("div")
                cartItem.className = "cart-item"
                cartItem.dataset.id = item.id

                cartItem.innerHTML = `
                    <div class="cart-item-image">
                        <img src="${item.image}" alt="${item.name}">
                    </div>
                    <div class="cart-item-details">
                        <h4>${item.name}</h4>
                        <p>Php ${item.price.toLocaleString()}</p>
                    </div>
                    <div class="cart-item-quantity">
                        <span class="cart-quantity">${item.quantity}</span>
                    </div>
                `

                orderItemsList.appendChild(cartItem)
            })
        }

        // Update order total
        if (orderTotal) {
            const total = window.calculateTotal()
            orderTotal.textContent = `Php ${total.toLocaleString()}`
        }
    }
  
    if (paymentForm) {
      paymentForm.addEventListener("submit", async function(e) {
        e.preventDefault()
        try {
          console.log("Payment form submitted")
          console.log("Initial window.cart state:", window.cart)
          console.log("Initial localStorage cart:", localStorage.getItem("cart"))
  
          // Get cart data from global cart object
          const cart = window.getCart()
          console.log('Final cart state during checkout:', cart)
          console.log('Final localStorage cart:', localStorage.getItem("cart"))
  
          // Validate cart
          if (!cart || !Array.isArray(cart) || cart.length === 0) {
              console.error('Invalid cart state:', cart)
              alert('Your cart is empty. Please add items before checking out.')
              return
          }
  
          // Get form values
          const email = document.getElementById("email").value.trim()
          const name = document.getElementById("name").value.trim()
          const address = document.getElementById("address").value.trim()
          const phone = document.getElementById("phone").value.trim()
          const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value
          
          // Validate form values
          if (!email || !name || !address || !phone || !paymentMethod) {
              alert("Please fill in all required fields")
              return
          }
          
          // Create order data
          const orderData = {
            email,
            name,
            address,
            phone,
            paymentMethod,
            items: cart,
            total: window.calculateTotal(),
            orderDate: new Date().toISOString()
          }
  
          // Send order to server
          const response = await fetch("process_order.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify(orderData)
          })
  
          if (!response.ok) {
            throw new Error("Failed to process order")
          }
  
          const result = await response.json()
  
          if (result.success) {
            // Clear cart after successful order
            window.clearCart()
            
            // Redirect to confirmation page
            window.location.href = "order-confirmation.php?orderId=" + result.orderId
          } else {
            throw new Error(result.message || "Failed to process order")
          }
        } catch (error) {
          console.error("Error processing order:", error)
          alert("Failed to process order. Please try again.")
        }
      })
    }
  
    // Initialize checkout items
    updateCheckoutItems()
  
    // Add click event listener to the "Check Order Status" button
    const checkStatusBtn = document.getElementById("checkStatusBtn")
    if (checkStatusBtn) {
      checkStatusBtn.addEventListener("click", () => {
        closeOrderConfirmationModal()
      })
    }
  
    // Add click event listener to the overlay to close when clicking outside
    if (modalOverlay) {
      modalOverlay.addEventListener("click", (e) => {
        // Only close if the overlay itself is clicked (not its children)
        if (e.target === modalOverlay) {
          closeOrderConfirmationModal()
        }
      })
    }
  
    // Function to close the order confirmation modal
    function closeOrderConfirmationModal() {
      if (orderConfirmationModal && modalOverlay) {
        // Remove active class for fade-out effect
        modalOverlay.classList.remove("active")
        orderConfirmationModal.classList.remove("active")
  
        // Hide the modal and overlay after the transition
        setTimeout(() => {
          modalOverlay.style.display = "none"
          orderConfirmationModal.style.display = "none"
          document.body.style.overflow = "auto"
        }, 300)
      }
    }
  
    // Find and remove any existing alert in the payment form submission
    const existingPaymentFormHandler = document.querySelector("script:not([src])")
    if (existingPaymentFormHandler) {
      const scriptContent = existingPaymentFormHandler.textContent
      if (scriptContent.includes('alert("Order confirmed!')) {
        // This is a more advanced approach that would require server-side modification
        // For now, we'll rely on our new event handler to take precedence
        console.log("Found alert in existing script. Our new handler will override it.")
      }
    }
  })
  
  