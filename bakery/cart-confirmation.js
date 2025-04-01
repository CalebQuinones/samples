document.addEventListener("DOMContentLoaded", () => {
    // Get references to all necessary elements
    const paymentForm = document.getElementById("paymentForm")
    const modalOverlay = document.getElementById("modalOverlay")
    const checkoutModal = document.getElementById("checkoutModal")
    const orderConfirmationModal = document.getElementById("orderConfirmationModal")
  
    if (paymentForm) {
      paymentForm.addEventListener("submit", (e) => {
        e.preventDefault()
  
        // Close the checkout modal
        if (checkoutModal) {
          checkoutModal.classList.remove("active")
          setTimeout(() => {
            checkoutModal.style.display = "none"
          }, 300)
        }
  
        // Show the order confirmation modal
        if (orderConfirmationModal && modalOverlay) {
          // Set current date for the confirmation
          const currentDate = new Date().toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
          })
  
          const dateElement = document.getElementById("confirmationOrderDate")
          if (dateElement) {
            dateElement.textContent = currentDate
          }
  
          // Generate a random order ID
          const orderId = "TJR-" + Math.floor(10000 + Math.random() * 90000)
          const orderIdElement = document.getElementById("confirmationOrderId")
          if (orderIdElement) {
            orderIdElement.textContent = orderId
          }
  
          // Display the modal and overlay
          modalOverlay.style.display = "block"
          orderConfirmationModal.style.display = "block"
  
          // Force a reflow before adding the active class for the transition
          void modalOverlay.offsetWidth
          void orderConfirmationModal.offsetWidth
  
          // Add active class for fade-in effect
          modalOverlay.classList.add("active")
          orderConfirmationModal.classList.add("active")
  
          // Prevent scrolling when modal is open
          document.body.style.overflow = "hidden"
        }
      })
    }
  
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
  
        // Wait for transition to complete before hiding elements
        setTimeout(() => {
          modalOverlay.style.display = "none"
          orderConfirmationModal.style.display = "none"
  
          // Re-enable scrolling
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
  
  